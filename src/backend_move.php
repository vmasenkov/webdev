<?php
require_once '_db.php';
require_once 'types.php';

$stmt = $db->prepare("SELECT * FROM reservations WHERE NOT ((end <= :start) OR (start >= :end)) AND id <> :id AND room_id = :resource");
$stmt->bindParam(':start', $_POST['newStart']);
$stmt->bindParam(':end', $_POST['newEnd']);
$stmt->bindParam(':id', $_POST['id']);
$stmt->bindParam(':resource', $_POST['newResource']);
$stmt->execute();
$overlaps = $stmt->rowCount() > 0;

if ($overlaps) {
    $response = new Results();
    $response->result = 'Error';
    $response->message = 'This reservation overlaps with an existing reservation.';

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$stmt = $db->prepare("UPDATE reservations SET start = :start, end = :end, room_id = :room WHERE id = :id");
$stmt->bindParam(':id', $_POST['id']);
$stmt->bindParam(':start', $_POST['newStart']);
$stmt->bindParam(':end', $_POST['newEnd']);
$stmt->bindParam(':room', $_POST['room']);
$stmt->execute();

$response = new Results();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
header("HTTP/1.1 200 OK", true, 200);
echo json_encode($response);

?>
