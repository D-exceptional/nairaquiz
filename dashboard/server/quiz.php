<?php
require 'session.php';
require 'mailer.php';
date_default_timezone_set("Africa/Lagos");

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $input = file_get_contents('php://input');

    // Decode the JSON data
    $data = json_decode($input, true);

    // Check if data is decoded successfully
    if (json_last_error() === JSON_ERROR_NONE) {
        if (!isset($data['action']) || empty($data['action']) || !isset($data['payload']) || empty($data['payload'])) {
            echo json_encode(['status' => 'error', 'message' => 'Empty field(s)', 'data' => []]);
            exit;
        } else {
            switch ($data['action']) {
                case 'Init Quiz':
                    $response = handleQuizInit($conn, $userID, $data['payload']);
                    break;

                case 'Check Answer':
                    $response = handleAnswerCheck($conn, $userID, $data['payload']);
                    break;

                case 'Save Trial':
                    $response = handleTrialSave($conn, $userID, $data['payload']);
                    break;
            }

            // Send JSON response
            echo json_encode($response);
            //mysqli_close($conn);
        }

    } else {
        // JSON decoding error
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data', 'data' => []]);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method', 'data' => []]);
    exit();
}

// Helper functions
function generateSessionCode($conn) {
    do {
        $hash = bin2hex(random_bytes(16));
        $sessionName = 'Session_' . $hash;

        $stmt = $conn->prepare("SELECT COUNT(*) FROM quiz_sessions WHERE session_token = ?");
        if (!$stmt) return null;

        $stmt->bind_param("s", $sessionName);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);

    return $sessionName;
}

function fetchRandomQuestions($conn, $limit) {
    $questions = [];

    $stmt = $conn->prepare("SELECT question_details, option_one, option_two, option_three, option_four, correct_option FROM questions ORDER BY RAND() LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $questions[] = [
            'question' => $row['question_details'],
            'answers' => [
                'A' => $row['option_one'],
                'B' => $row['option_two'],
                'C' => $row['option_three'],
                'D' => $row['option_four'],
            ],
            'correctAnswer' => strtoupper(trim($row['correct_option'])),
            'answered' => false
        ];
    }

    return $questions;
}

 // Helper function to remove correctAnswer when sending to client
function formatClientQuestion($q, $index) {
    return [
        'index' => $index,
        'question' => $q['question'],
        'answers' => $q['answers']
    ];
}

function decodePayload($payload) {
    if (!isset($payload)) {
       throw new Exception("Invalid data payload", 1);
    }
    return $payload;
}

function handleQuizInit($conn, $userID, $payload) {
    $payloadData = decodePayload($payload);
    $limit = $payloadData[0]['limit'];

    $questionSet = fetchRandomQuestions($conn, $limit);
    $sessionToken = generateSessionCode($conn);
    $jsonSet = json_encode($questionSet);
    $startTime = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO quiz_sessions (userID, session_token, question_set, start_time, status, score) VALUES (?, ?, ?, ?, 'in_progress', 0)");
    $stmt->bind_param("isss", $userID, $sessionToken, $jsonSet, $startTime);

    if ($stmt->execute()) {
        // For test, only show answer to dev
        $isDev = intval($userID) === 7;
        $packetData = $isDev ? $questionSet[0]['correctAnswer'] : '';
            
        return [
            'status' => 'success',
            'message' => 'Quiz session initialized',
            'data' => [
                'token' => $sessionToken,
                'question' => formatClientQuestion($questionSet[0], 0), // Send the first question,
                'packet' => $packetData,
            ],
        ];
    } else {
        return ['status' => 'error', 'message' => 'Error initializing quiz session', 'data' => []];
    }
    $stmt->close();
    $conn->close();
}

function handleTokenCheck($conn, $token, $userID) {
    $stmt = $conn->prepare("SELECT id FROM quiz_sessions WHERE session_token = ? AND userID = ?");
    $stmt->bind_param("si", $token, $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result->num_rows) {
        return ['status' => 'error', 'message' => 'Invalid or expired token', 'data' => []];
    }
}

function handleSessionDetails($conn, $token) {
    $sql = $conn->prepare("SELECT * FROM quiz_sessions WHERE session_token = ?");
    $sql->bind_param("s", $token);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    return $row;
}

function handleUserDetails($conn, $userID) {
    $sql = $conn->prepare("SELECT * FROM users WHERE userID = ? AND user_type = 'User'");
    $sql->bind_param("i", $userID);
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    return $row;
}

function handleAnswerCheck($conn, $userID, $payload) {
    // Process inputs
    $payloadData = decodePayload($payload);
    $sessionToken = $payloadData[0]['token'];
    $questionIndex = intval($payloadData[0]['index']);
    $selectedOption = $payloadData[0]['answer'] ?? '';
    $questionType = $payloadData[0]['limit'];
    $quizStake = intval($payloadData[0]['stake']);
    
    // Validate
    if (!isset($questionIndex) || !isset($sessionToken) || !isset($selectedOption) || !isset($questionType) || !isset($quizStake)) {
        return ['status' => 'error', 'message' => 'Missing or invalid input', 'data' => []];
    }
    
    // Validate token with the database here
    handleTokenCheck($conn, $sessionToken, $userID);

    // Get session details
    $sessionDetails = handleSessionDetails($conn, $sessionToken);
    $sessionId = $sessionDetails['id'];
    $questions = json_decode($sessionDetails['question_set'], true);
    $score = intval($sessionDetails['score']);
    $status = $sessionDetails['status'];

    // Check if session is valid
    if ($status !== 'in_progress') {
        return ['status' => 'error', 'message' => 'Session already completed or terminated', 'data' => []];
    }

    // Check if question is valid
    if (!isset($questions[$questionIndex])) {
        return ['status' => 'error', 'message' => 'Invalid question', 'data' => []];
    }

    // Check if question is already answered
    /*
    if ($questions[$questionIndex]['answered'] === true) {
        return ['status' => 'error', 'message' => 'Question already answered', 'data' => []];
    }*/

    // Mark as answered
    $questions[$questionIndex]['answered'] = true;

    // Update DB
    $updatedSet = json_encode($questions);
    $stmt = $conn->prepare("UPDATE quiz_sessions SET question_set = ? WHERE id = ?");
    $stmt->bind_param("si", $updatedSet, $sessionId);
    $stmt->execute();

    // Check answer
    $isCorrect = $questions[$questionIndex]['correctAnswer'] === $selectedOption;
    if ($isCorrect) {
        $score += 1;

        // Update DB
        $stmt = $conn->prepare("UPDATE quiz_sessions SET score = ? WHERE id = ?");
        $stmt->bind_param("ii", $score, $sessionId);
        $stmt->execute();

        // Find next unanswered question
        $nextIndex = -1;
        foreach ($questions as $i => $q) {
            if (!$q['answered']) {
                $nextIndex = $i;
                break;
            }
        }

        // If none available
        if ($nextIndex === -1) {
            // All answered — quiz complete
            $stmt = $conn->prepare("UPDATE quiz_sessions SET status = 'completed' WHERE id = ?");
            $stmt->bind_param("i", $sessionId);
            $stmt->execute();

            $rewardMultipliers = [5 => 50, 7 => 200, 10 => 500, 14 => 1000];
            $multiplier = $rewardMultipliers[$questionType] ?? 0;

            if ($multiplier <= 0) {
                return ['status' => 'error', 'message' => 'Invalid quiz type', 'data' => []];
            }

            try {
                $conn->begin_transaction();

                // ----------------------------
                // 1. Insert into quiz_trials
                // ----------------------------
                $trialPoints = 1;
                $insertTrial = $conn->prepare("INSERT INTO quiz_trials (trial_type, trial_stake, trial_points, trial_date, userID) VALUES (?, ?, ?, NOW(), ?)");
                $insertTrial->bind_param("idii", $questionType, $quizStake, $trialPoints, $userID);
                if (!$insertTrial->execute()) throw new Exception('Failed to save quiz trial');
                $insertTrial->close();

                // ----------------------------
                // 2. Fetch wallet and currency
                // ----------------------------
                $currency = '₦';
                $wallet_amount = 0.0;

                $walletQuery = $conn->prepare("SELECT wallet_currency, wallet_amount FROM wallet WHERE userID = ?");
                $walletQuery->bind_param("i", $userID);
                $walletQuery->execute();
                $walletQuery->bind_result($currency_fetched, $wallet_amount);
                if ($walletQuery->fetch()) {
                    $currency = $currency_fetched ?? '₦';
                } else {
                    $walletQuery->close();
                    throw new Exception('Wallet not found');
                }
                $walletQuery->close();

                // ----------------------------
                // 3. Final balance validation and atomic deduction
                // ----------------------------
                $walletValidation = $conn->prepare("UPDATE wallet SET wallet_amount = wallet_amount - ? WHERE userID = ? AND wallet_amount >= ?");
                $walletValidation->bind_param("did", $quizStake, $userID, $quizStake);
                $walletValidation->execute();

                if ($walletValidation->affected_rows === 0) {
                    $walletValidation->close();
                    throw new Exception('Insufficient funds. Please refresh and try again.');
                }
                $walletValidation->close();

                // ----------------------------
                // 4. Update wallet savings
                // ----------------------------
                $rewardFloat = floatval($quizStake * $multiplier);
                $savingsCheck = $conn->prepare("SELECT wallet_amount FROM wallet_savings WHERE userID = ?");
                $savingsCheck->bind_param("i", $userID);
                $savingsCheck->execute();
                $savingsCheck->bind_result($savings_amount);

                if ($savingsCheck->fetch()) {
                    $new_savings = $savings_amount + $rewardFloat;
                    $savingsCheck->close();

                    $updateSavings = $conn->prepare("UPDATE wallet_savings SET wallet_amount = ? WHERE userID = ?");
                    $updateSavings->bind_param("di", $new_savings, $userID);
                    if (!$updateSavings->execute()) throw new Exception('Failed to update wallet savings');
                    $updateSavings->close();
                } else {
                    $savingsCheck->close();
                    $insertSavings = $conn->prepare("INSERT INTO wallet_savings (wallet_amount, userID) VALUES (?, ?)");
                    $insertSavings->bind_param("di", $rewardFloat, $userID);
                    if (!$insertSavings->execute()) throw new Exception('Failed to insert wallet savings');
                    $insertSavings->close();
                }

                // ----------------------------
                // 5. Send Email to User
                // ----------------------------
                $userDetails = handleUserDetails($conn, $userID);
                $fullname = $userDetails['fullname'];
                $email = $userDetails['email'];
                
                $formatted_staked = $currency . number_format($quizStake, 2);
                $earned_amount = $currency . number_format($rewardFloat, 2);
                $subject = "Quiz Win";
                $message = "
                    Hi <b>$fullname</b>,<br>
                    You staked <b>$formatted_staked</b> and won <b>$earned_amount</b> for completing a $questionType-question quiz.<br>
                    Please ensure your bank details are added for payments.<br>
                    Keep playing and winning!";
                send_email($subject, $email, $message);

                // ----------------------------
                // 6. Notify Admins
                // ----------------------------
                $adminQuery = $conn->prepare("SELECT email FROM users WHERE user_type = 'Admin'");
                $adminQuery->execute();
                $adminQuery->bind_result($admin_email);
                $admin_emails = [];

                while ($adminQuery->fetch()) {
                    $admin_emails[] = $admin_email;
                }
                $adminQuery->close();
            
                // Admin Notification
                $now = date('Y-m-d H:i:s');
                $notif_title = 'won a quiz format';
                $notif_details = "<b>$fullname</b> won a $questionType-question quiz, staking <b>$formatted_staked</b> and earning <b>$earned_amount</b>";
                $notif_type = 'quiz_win';
                $notif_status = 'Unseen';
                
                // Admin Message
                $adminSubject = "Quiz Win";
                $adminMessage = "
                    Hello Admin,<br>
                    A user just won a quiz.<br>
                    <b>$fullname</b><br>
                    Format: $questionType Questions<br>
                    Stake: $formatted_staked<br>
                    Reward: $earned_amount<br>
                    Date: $now<br>
                    <a href='https://nairaquiz.com/admin/'><b>View Dashboard</b></a>
                ";

                foreach ($admin_emails as $admin_email) {
                    // Prevent duplicate notifications within 5s
                    $insertNotif = $conn->prepare("INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_receiver, notification_date, notification_status) VALUES (?, ?, ?, ?, ?, ?)");
                    $insertNotif->bind_param("ssssss", $notif_title, $notif_details, $notif_type, $admin_email, $now, $notif_status);
                    $insertNotif->execute();
                    $insertNotif->close();

                    // Send email to admin
                    send_email($adminSubject, $admin_email, $adminMessage);
                }

                // If everything passed
                $conn->commit();
                return ['status' => 'success', 'message' => 'Quiz completed successfully', 'data' => [ 'score' => $score ]];

            } catch (Exception $e) {
                $conn->rollback();
                //echo json_encode(['Info' => $e->getMessage()]);
                return ['status' => 'error', 'message' => 'Error occured while processing your request', 'data' => []];
            }
        } else {
            // Process next question
            $nextQ = $questions[$nextIndex];
            
            // For test, only show answer to dev
            $isDev = intval($userID) === 7;
            $packetData = $isDev ? $nextQ['correctAnswer'] : '';
            
            // Format response
            unset($nextQ['correctAnswer']);
            unset($nextQ['answered']);
            
            // Return final response
            return [
                'status' => 'success',
                'message' => 'Next quiz fetched successfully',
                'data' => [
                    'index' => $nextIndex,
                    'question' => $nextQ,
                    'packet' => $packetData,
                ]
            ];
        }
    }
    else{
        // Wrong answer — quiz terminate
        $terminateStmt = $conn->prepare("UPDATE quiz_sessions SET status = 'terminated' WHERE id = ?");
        $terminateStmt->bind_param("i", $sessionId);
        $terminateStmt->execute();

        // Save Trial
        $trialPoints = 0;
        $insertTrial = $conn->prepare("INSERT INTO quiz_trials (trial_type, trial_stake, trial_points, trial_date, userID) VALUES (?, ?, ?, NOW(), ?)");
        $insertTrial->bind_param("idii", $questionType, $quizStake, $trialPoints, $userID);
        if (!$insertTrial->execute()) throw new Exception('Failed to save quiz trial');
        $insertTrial->close();

        // Update Wallet
        $walletValidation = $conn->prepare("UPDATE wallet SET wallet_amount = wallet_amount - ? WHERE userID = ? AND wallet_amount >= ?");
        $walletValidation->bind_param("did", $quizStake, $userID, $quizStake);
        $walletValidation->execute();

        if ($walletValidation->affected_rows === 0) {
            $walletValidation->close();
            throw new Exception('Insufficient funds. Please refresh and try again.');
        }
        $walletValidation->close();

        // Send Feedback
        return ['status' => 'error', 'message' => 'Quiz session terminated due to wrong answer', 'data' => []];
    }

    $conn->close();
}

function handleTrialSave($conn, $userID, $payload) {
    // Process inputs
    $payloadData = decodePayload($payload);
    $questionType = $payloadData[0]['limit'];
    $quizStake = intval($payloadData[0]['stake']);
    $sessionToken = $payloadData[0]['token'];
    
    // Validate
    if (!isset($sessionToken) || !isset($questionType) || !isset($quizStake)) {
        return ['status' => 'error', 'message' => 'Missing or invalid input', 'data' => []];
    }
    
    // Validate token with the database here
    handleTokenCheck($conn, $sessionToken, $userID);

    // Get session details
    $sessionDetails = handleSessionDetails($conn, $sessionToken);
    $sessionId = $sessionDetails['id'];
    $sessionStatus = $sessionDetails['status'];

    // Check if session is valid
    if ($sessionStatus !== 'in_progress') {
        return ['status' => 'error', 'message' => 'Session already completed or terminated', 'data' => []];
    }

    // Wrong answer — quiz terminate
    $terminateStmt = $conn->prepare("UPDATE quiz_sessions SET status = 'terminated' WHERE id = ?");
    $terminateStmt->bind_param("i", $sessionId);
    $terminateStmt->execute();

    // Save Trial
    $trialPoints = 0;
    $insertTrial = $conn->prepare("INSERT INTO quiz_trials (trial_type, trial_stake, trial_points, trial_date, userID) VALUES (?, ?, ?, NOW(), ?)");
    $insertTrial->bind_param("idii", $questionType, $quizStake, $trialPoints, $userID);
    if (!$insertTrial->execute()) throw new Exception('Failed to save quiz trial');
    $insertTrial->close();

    // Update Wallet
    $walletValidation = $conn->prepare("UPDATE wallet SET wallet_amount = wallet_amount - ? WHERE userID = ? AND wallet_amount >= ?");
    $walletValidation->bind_param("did", $quizStake, $userID, $quizStake);
    $walletValidation->execute();

    if ($walletValidation->affected_rows === 0) {
        $walletValidation->close();
        throw new Exception('Insufficient funds. Please refresh and try again.');
    }
    $walletValidation->close();

    // Send Feedback
    return ['status' => 'error', 'message' => 'Quiz session terminated due to unethical practices detetcted', 'data' => []];
}
?>
