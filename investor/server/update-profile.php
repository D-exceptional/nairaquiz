<?php
require 'session.php'; // Assumes $userID and $email are defined here

$response = ['Info' => 'Unknown error occurred'];

// Validate user ID
if (empty($investorID)) {
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

$targetDir = "../../uploads/";
$imgFile = $_FILES['profile'];
$imgName = basename($imgFile['name']); // sanitize name
$tmpName = $imgFile['tmp_name'];
$imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
$allowedExts = ["jpeg", "png", "jpg"];

// Generate a unique image name
$newImgName = uniqid('profile_', true) . '.' . $imgExt;

// Validate image extension
if (!in_array($imgExt, $allowedExts)) {
    $response['Info'] = 'Image must have either .jpeg, .png or .jpg extension';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Ensure the upload directory exists
if (!is_dir($targetDir)) {
    $response['Info'] = 'Uploads directory not found';
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

// Fetch existing profile image filename
$stmt = $conn->prepare("SELECT investor_profile FROM investors WHERE investorID = ?");
$stmt->bind_param("i", $investorID);
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
$updateStmt = $conn->prepare("UPDATE investors SET investor_profile = ? WHERE investorID = ?");
$updateStmt->bind_param("si", $newImgName, $investorID);

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
