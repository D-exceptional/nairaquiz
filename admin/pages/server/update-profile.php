<?php
require 'session.php'; // Assumes $userID and $email are defined here

$response = ['Info' => 'Unknown error occurred'];

// Validate user ID
if (empty($userID)) {
    $response['Info'] = 'All fields must be filled up';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Check if a file was uploaded
if (!isset($_FILES['profile']) || $_FILES['profile']['error'] !== UPLOAD_ERR_OK) {
    $response['Info'] = 'Upload a valid image';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

$targetDir = "../../../uploads/";
$imgFile = $_FILES['profile'];
$imgName = basename($imgFile['name']); // sanitize name
$tmpName = $imgFile['tmp_name'];
$imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
$allowedExts = ["jpeg", "png", "jpg"];

// Validate image extension
if (!in_array($imgExt, $allowedExts)) {
    $response['Info'] = 'Image must have either .jpeg, .png or .jpg extension';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Generate a unique image name
$newImgName = uniqid('profile_', true) . '.' . $imgExt;

// Ensure the upload directory exists
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Fetch existing profile image filename
$stmt = $conn->prepare("SELECT user_profile FROM users WHERE userID = ? AND user_type = 'Admin'");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($existingProfile);
$stmt->fetch();
$stmt->close();

// Delete the old profile image if it exists
if (!empty($existingProfile) && file_exists($targetDir . $existingProfile)) {
    unlink($targetDir . $existingProfile);
}

// Move the uploaded file to the target directory
if (!move_uploaded_file($tmpName, $targetDir . $newImgName)) {
    $response['Info'] = 'Failed to upload image';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Update the user's profile image in the database
$updateStmt = $conn->prepare("UPDATE users SET user_profile = ? WHERE userID = ? AND user_type = 'Admin'");
$updateStmt->bind_param("si", $newImgName, $userID);

if ($updateStmt->execute()) {
    $response['Info'] = 'Profile updated successfully';
} else {
    $response['Info'] = 'Something went wrong';
}

$updateStmt->close();
$conn->close();

echo json_encode($response, JSON_FORCE_OBJECT);
exit;
?>
