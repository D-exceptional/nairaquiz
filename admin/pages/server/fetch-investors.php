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
        i.investorID,
        i.fullname,
        i.email,
        i.contact,
        i.country,
        i.created_on,
        i.investor_status
    FROM investors i
    ORDER BY i.fullname ASC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'investorID' => $row['investorID'],
            'fullname' => $row['fullname'],
            'email' => $row['email'],
            'contact' => $row['contact'],
            'country' => $row['country'],
            'date' => $row['created_on'],
            'status' => $row['investor_status'],
        ];
    }
} else {
    $data = ['Info' => 'No record found'];
}

echo json_encode($data);
$conn->close();
exit();
?>
