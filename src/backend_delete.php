<?php
require_once '_db.php';
require_once 'types.php';

ini_set('display_errors', 1);

$stmt = $db->prepare("DELETE FROM reservations WHERE id = :id");
$stmt->bindParam(':id', $_POST['id']);
$stmt->execute();

$response = new Results();
$response->result = 'OK';
$response->message = 'Deleted successful';

header('Content-Type: application/json');
header("HTTP/1.1 200 OK", true, 200);
echo json_encode($response);

?>
