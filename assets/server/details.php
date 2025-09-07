<?php

require 'conn.php';

header('Content-Type: application/json');

$data = [];

$package = $_GET['package'] ?? 'Starter';

if (!empty($package)) {
    // Prepare SQL to check for exact or similar questions using LIKE
    $sql = "SELECT plan_amount, plan_roi FROM investment_plans WHERE plan_name = ? OR plan_name LIKE ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $data = ['Info' => 'Server error: failed to prepare statement'];
        echo json_encode($data, JSON_FORCE_OBJECT);
        exit();
    }
    
    $likePackage = "%{$package}%";
    $stmt->bind_param("ss", $package, $likePackage);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $data = [
            'Info' => 'Details fetched successfully',
            'data' => [
                'amount' => floatval($row['plan_amount']),
                'roi' => $row['plan_roi']
            ]
        ];
    } else {
        $data = ['Info' => 'Package details not found'];
    }

    $stmt->close();
} else {
    $data = ['Info' => 'Package name is required'];
}

echo json_encode($data, JSON_FORCE_OBJECT);
$conn->close();
exit();

?>
