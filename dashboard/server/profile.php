<?php
require 'session.php'; // Assumes $userID and $email are defined here

date_default_timezone_set("Africa/Lagos");

$response = ['Info' => 'Unknown error occurred'];

if (empty($userID)) {
    $response['Info'] = 'All fields must be filled up';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

if (empty($_FILES['profile'])) {
    $response['Info'] = 'Upload a valid image';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

$targetDir = "../../uploads/";
$imgFile = $_FILES['profile'];
$imgName = $imgFile['name'];
$tmpName = $imgFile['tmp_name'];
$imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
$allowedExts = ["jpeg", "png", "jpg"];

if (!in_array($imgExt, $allowedExts)) {
    $response['Info'] = 'Image must have either .jpeg, .png or .jpg extension';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Generate a unique image name
$newImgName = pathinfo($imgName, PATHINFO_FILENAME) . time() . '.' . $imgExt;

// Fetch existing profile image filename safely
$stmt = $conn->prepare("SELECT user_profile FROM users WHERE userID = ? AND user_type = 'User'");
$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($existingProfile);
$stmt->fetch();
$stmt->close();

// Delete existing image if present
if ($existingProfile && file_exists($targetDir . $existingProfile)) {
    unlink($targetDir . $existingProfile);
}

// Move uploaded file
if (!move_uploaded_file($tmpName, $targetDir . $newImgName)) {
    $response['Info'] = 'Failed to upload image';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Update database with new image name
$updateStmt = $conn->prepare("UPDATE users SET user_profile = ? WHERE userID = ? AND user_type = 'User'");
$updateStmt->bind_param("si", $newImgName, $userID);

if ($updateStmt->execute()) {
    $response['Info'] = 'Profile updated successfully';
} else {
    $response['Info'] = 'Something went wrong';
}

$updateStmt->close();

echo json_encode($response, JSON_FORCE_OBJECT);
$conn->close();
exit;
?>
