<?php
require_once '_db.php'; //підключаємо кон
require_once 'types.php';

$stmt = $db->prepare("SELECT * FROM rooms ORDER BY name"); //запит для відбору всіх кімнат
$stmt->execute();
$rooms = $stmt->fetchAll();

$result = array();

foreach($rooms as $room) {
  $r = new Room();
  $r->id = $room['id'];
  $r->name = $room['name'];
  $r->capacity = $room['capacity'];
  $r->status = $room['status'];
  $result[] = $r;
  
}

header('Content-Type: application/json'); //цей рядок повинен бути першим у виводі інформації для передачі з сервера. Якщо до нього буде хоча б один пропуск, застосунок видасть помилку.
header("HTTP/1.1 200 OK", true, 200);
echo json_encode($result);

?>
