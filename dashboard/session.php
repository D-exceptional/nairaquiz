<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: /login");
    exit();
}

$userID = $_SESSION['userID'];

// Cache buster helper
function getCacheBustedUrl($filePath) {
    return file_exists($filePath) ? $filePath . '?v=' . filemtime($filePath) : $filePath;
}

// Get user details
function getUserDetails($conn, int $userID, string $userType = 'User'): ?array {
    $stmt = $conn->prepare("SELECT email, fullname, user_profile, contact, country FROM users WHERE userID = ? AND user_type = ?");
    $stmt->bind_param("is", $userID, $userType);
    $stmt->execute();
    $stmt->bind_result($email, $fullname, $profile, $contact, $country);

    if ($stmt->fetch()) {
        $stmt->close();
        return [
            'email' => $email,
            'fullname' => $fullname,
            'profile' => $profile,
            'contact' => $contact,
            'country' => $country
        ];
    }

    $stmt->close();
    return null;
}

// Fetch user details
$userData = getUserDetails($conn, $userID);

if (!$userData) {
    // User not found or wrong role
    header("Location: /login");
    exit();
}

$email = $userData['email'];
$fullname = $userData['fullname'];
$profile = $userData['profile'];
$contact = $userData['contact'];
$country = $userData['country'];

//$conn->close();
?>
