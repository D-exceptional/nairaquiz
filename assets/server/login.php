<?php

session_start();

require 'conn.php';

header('Content-Type: application/json');

$data = [];

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($email) && !empty($password)) {
    $sql = "SELECT userID, email, user_password, user_status FROM users WHERE email = ? AND user_type = 'User' LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $data = ["Info" => "Server error: failed to prepare statement"];
        echo json_encode($data, JSON_FORCE_OBJECT);
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $userID = $row['userID'];
        $databaseEmail = $row['email'];
        $databasePassword = $row['user_password'];
        $user_status = $row['user_status'];

        if ($user_status === 'Pending') {
            $data = ["Info" => "Cannot login at this time!"];
        } else {
            // Verify password
            if (password_verify($password, $databasePassword) && $email === $databaseEmail) {
                // Set session variables
                $_SESSION['userID'] = $userID;
                $_SESSION['userType'] = 'User';

                $data = [
                    "Info" => "You have successfully logged in",
                    "user" => ["link" => "https://nairaquiz.com/dashboard/index.php"]
                ];
            } else {
                $data = ["Info" => "Check your email or password again"];
            }
        }
    } else {
        $data = ["Info" => "No record found"];
    }

    $stmt->close();
} else {
    $data = ["Info" => "Some fields are empty"];
}

echo json_encode($data, JSON_FORCE_OBJECT);

$conn->close();
exit();
?>
