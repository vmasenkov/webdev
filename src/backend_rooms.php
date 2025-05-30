<?php
require_once '_db.php'; //підключаємо кон
require_once 'types.php';

ini_set('display_errors', 1);

$query_where = '';
$filterCapacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 0;
if ($filterCapacity > 0) {
  $query_where = "WHERE capacity = :capacity"; //якщо передано параметр capacity, то додаємо до запиту умову
}

$stmt = $db->prepare("SELECT * FROM rooms {$query_where} ORDER BY name"); //запит для відбору всіх кімнат
if ($filterCapacity > 0) {
  //якщо передано параметр capacity, то прив'язуємо його до запиту
  $stmt->bindParam(':capacity', $filterCapacity, PDO::PARAM_INT);
}
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
