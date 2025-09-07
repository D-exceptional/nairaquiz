<?php

require 'conn.php';
header('Content-Type: application/json');

$data = [];

// Get offset and limit from POST request, default to 0 and 25
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;

if ($limit <= 0) $limit = 25;
if ($offset < 0) $offset = 0;

// Updated SQL query with LIMIT and OFFSET
$sql = "
    SELECT 
        u.userID,
        u.email,
        u.fullname,
        u.user_profile,
        u.country,
        u.contact,
        u.user_type,
        u.user_status,
        u.created_on,
        w.account_number,
        w.bank,
        w.wallet_currency,
        w.wallet_amount AS wallet_balance,
        s.wallet_amount AS savings_balance
    FROM users u
    LEFT JOIN wallet w ON u.userID = w.userID
    LEFT JOIN wallet_savings s ON u.userID = s.userID
    ORDER BY u.userID
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $currency = $row['wallet_currency'] ?? 'NGN';

        $walletAmount = number_format((float)($row['wallet_balance'] ?? 0), 2, '.', ',');
        $withdrawalAmount = number_format((float)($row['savings_balance'] ?? 0), 2, '.', ',');

        $data[] = [
            'userID' => $row['userID'],
            'fullname' => $row['fullname'],
            'email' => $row['email'],
            'contact' => $row['contact'],
            'country' => $row['country'],
            'user_type' => $row['user_type'],
            'created_on' => $row['created_on'],
            'user_status' => $row['user_status'],
            'account_number' => $row['account_number'] ?? 'Not available',
            'bank' => $row['bank'] ?? 'Not available',
            'wallet' => $currency . $walletAmount,
            'withdraw' => $currency . $withdrawalAmount
        ];
    }
} else {
    $data = ['Info' => 'No record found'];
}

echo json_encode($data);
$conn->close();
exit();
?>
