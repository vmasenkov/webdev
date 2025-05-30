<?php
require_once '_db.php';
require_once 'types.php';

$capacity = isset($_POST['capacity']) ? $_POST['capacity'] : 0;
if (!$capacity || !is_numeric($capacity) || $capacity <= 0) {
    $response = new Results();
    $response->result = 'Error';
    $response->message = 'Invalid capacity value';
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$stmt = $db->prepare("INSERT INTO rooms (name, capacity, status) VALUES (:name, :capacity, 'Ready')");
$stmt->bindParam(':name', $_POST['name']);
$stmt->bindParam(':capacity', $_POST['capacity']);
$stmt->execute();

$response = new Results();
$response->result = 'OK';
$response->message = 'Created with id: '.$db->lastInsertId();
$response->id = $db->lastInsertId();

header("HTTP/1.1 200 OK", true, 200);
header('Content-Type: application/json');
echo json_encode($response);

?>
