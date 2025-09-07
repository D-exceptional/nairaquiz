<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['investorID'])) {
    header("Location: /access");
    exit();
}

$investorID = $_SESSION['investorID'];

// Cache buster helper
function getCacheBustedUrl($filePath) {
    return file_exists($filePath) ? $filePath . '?v=' . filemtime($filePath) : $filePath;
}

// Get user details
function getInvestorDetails($conn, int $investorID): ?array {
    $stmt = $conn->prepare("SELECT email, fullname, investor_profile, contact, country FROM investors WHERE investorID = ?");
    $stmt->bind_param("i", $investorID);
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

function getProfile($profile) {
    return $profile === 'None' ? "../assets/img/user.png" : "../uploads/$profile";
}

// Fetch user details
$investorData = getInvestorDetails($conn, $investorID);

if (!$investorData) {
    // User not found or wrong role
    header("Location: /access");
    exit();
}

$email = $investorData['email'];
$fullname = $investorData['fullname'];
$profile = getProfile($investorData['profile']);
$contact = $investorData['contact'];
$country = $investorData['country'];

//$conn->close();
?>
