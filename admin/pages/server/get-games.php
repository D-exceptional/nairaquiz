<?php 

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'session.php';

$response = [];
$games = [];

$count = isset($_GET['count']) ? $_GET['count'] : null;

if ($count) {
    function fetchGames($conn, $limit) {
        $games = [];

        // Fetch pending sessions with > 0 players
        $sql = $conn->prepare("
            SELECT 
                sg.sessionID,
                sg.session_name,
                sg.session_question,
                sg.session_answer,
                sg.session_date,
                sg.session_status,
                COUNT(DISTINCT sp.userID) AS total_players
            FROM session_game sg
            LEFT JOIN session_players sp ON sg.session_name = sp.session_name
            WHERE sg.session_status = 'Pending'
            GROUP BY sg.sessionID
            HAVING total_players > 0
            ORDER BY sg.sessionID DESC
            LIMIT ?
        ");
        $sql->bind_param("i", $limit);
        $sql->execute();
        $result = $sql->get_result();

        while ($row = $result->fetch_assoc()) {
            $games[] = [
                'id' => (int) $row['sessionID'],
                'name' => $row['session_name'],
                'question' => $row['session_question'],
                'answer' => $row['session_answer'],
                'date' => $row['session_date'],
                'status' => $row['session_status'],
                'players' => (int) $row['total_players'],
                'amount' => '₦' . number_format((int) $row['total_players'] * 1000, 2, '.', ',')
            ];
        }

        return $games;
    }

    // Determine limit based on 'count' value
    switch ($count) {
        case '25':
        case '50':
        case '100':
        case '250':
        case '500':
        case '1000':
            $games = fetchGames($conn, (int) $count);
            break;
        case 'All':
            $games = fetchGames($conn, 10000); // Effectively all
            break;
        default:
            $response = ["Info" => "Invalid or missing 'count' parameter"];
            echo json_encode($response);
            exit();
    }

    $response = !empty($games)
        ? ['Info' => 'Games fetched', 'games' => $games]
        : ['Info' => 'No pending session with players found', 'games' => []];
} else {
    $response = ["Info" => "Count parameter is empty"];
}

echo json_encode($response);
mysqli_close($conn);
exit();
?>
