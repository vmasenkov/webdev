<?php
require_once '_db.php';
require_once 'types.php';

$stmt = $db->prepare("UPDATE reservations SET name = :name, start = :start, end = :end, room_id = :room, status = :status, paid = :paid WHERE id = :id");
$stmt->bindParam(':id', $_POST['id']);
$stmt->bindParam(':name', $_POST['name']);
$stmt->bindParam(':start', $_POST['start']);
$stmt->bindParam(':end', $_POST['end']);
$stmt->bindParam(':room', $_POST['room']);
$stmt->bindParam(':status', $_POST['status']);
$stmt->bindParam(':paid', $_POST['paid']);
$stmt->execute();

$response = new Results();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
header("HTTP/1.1 200 OK", true, 200);
echo json_encode($response);

?>
