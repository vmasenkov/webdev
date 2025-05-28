<?php
require_once '_db.php'; //підключаємо конект до mysql
require_once 'types.php';

$stmt = $db->prepare("SELECT * FROM reservations WHERE NOT ((end <= :start) OR (start >= :end))");
$stmt->bindParam(':start', $_POST['start']);
$stmt->bindParam(':end', $_POST['end']);
$stmt->execute();
$result = $stmt->fetchAll();

$events = array();

date_default_timezone_set("UTC");
$now = new DateTime("now");
$today = $now->setTime(0, 0, 0);

foreach($result as $row) {
    $e = new Event();
    $e->id = $row['id'];
    $e->text = $row['name'];
    $e->start = $row['start'];
    $e->end = $row['end'];
    $e->resource = $row['room_id'];
    $e->bubbleHtml = "Reservation details: ".$e->text;
    
    // additional properties
    $e->status = $row['status'];
    $e->paid = $row['paid'];
    $events[] = $e;
}

header('Content-Type: application/json');
header("HTTP/1.1 200 OK", true, 200);
echo json_encode($events);

?>
