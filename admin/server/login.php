<?php 

session_start();
require 'conn.php';

$data = [];

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!empty($email) && !empty($password)) {
    // Prepare statement to select user info
    $stmt = $conn->prepare("SELECT userID, email, user_password FROM users WHERE email = ? AND user_type = 'Admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userID, $databaseEmail, $databasePassword);
        $stmt->fetch();

        if (password_verify($password, $databasePassword) && $email === $databaseEmail) {
            // Successful login
            $_SESSION['userID'] = $userID;
            $_SESSION['userType'] = 'Admin';

            $data = [
                'Info' => 'You have successfully logged in',
                'admin' => ['link' => "https://nairaquiz.com/admin/pages/index.php"]
            ];
        } else {
            $data = ['Info' => 'Invalid credentials. Check your email or password again'];
        }
    } else {
        $data = ['Info' => 'No record found'];
    }
    $stmt->close();
} else {
    $data = ['Info' => 'All inputs must be filled out'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
