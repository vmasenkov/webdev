<?php
session_start();

// Скидання гри при натисканні "Почати спочатку"
if (isset($_GET['reset'])) {
    unset($_SESSION['board']);
    unset($_SESSION['turn']);
    unset($_SESSION['message']);
}

// Ініціалізація стану гри
if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = array_fill(0, 9, ' ');
    $_SESSION['turn'] = 'X';
    $_SESSION['message'] = "Гра почалася! Хід: X";
}

// Отримуємо стан гри з сесії
$board = $_SESSION['board'];
$turn = $_SESSION['turn'];
$message = $_SESSION['message'];

// Обробка ходу
if (isset($_GET['move']) && $turn != '-') {
    $move = (int)$_GET['move'];
    if ($board[$move] === ' ') {
        $board[$move] = $turn;
        $winner = checkWinner($board);
        if ($winner) {
            $message = "Переможець: $winner!";
            $turn = '-'; // Гру завершено
        } elseif (!in_array(' ', $board)) {
            $message = "Нічия!";
            $turn = '-'; // Гру завершено
        } else {
            $turn = $turn === 'X' ? 'O' : 'X'; // Зміна гравця
            $message = "Хід: $turn";
        }
    } else {
        $message = "Клітинка вже зайнята! Хід: $turn";
    }

    // Зберігаємо стан гри в сесії
    $_SESSION['board'] = $board;
    $_SESSION['turn'] = $turn;
    $_SESSION['message'] = $message;
}

// Функція перевірки переможця
function checkWinner($b) {
    $lines = [
        [0,1,2], [3,4,5], [6,7,8], // рядки
        [0,3,6], [1,4,7], [2,5,8], // стовпці
        [0,4,8], [2,4,6]           // діагоналі
    ];
    foreach ($lines as $line) {
        if ($b[$line[0]] != ' ' && $b[$line[0]] == $b[$line[1]] && $b[$line[1]] == $b[$line[2]]) {
            return $b[$line[0]];
        }
    }
    return null;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Хрестики-нулики (SESSION)</title>
    <style>
        table { margin: 20px auto; border-collapse: collapse; }
        td { width: 60px; height: 60px; text-align: center; font-size: 2em; border: 1px solid black; }
        a { display: block; width: 100%; height: 100%; text-decoration: none; color: black; }
        .message { text-align: center; font-size: 1.5em; }
        .restart { text-align: center; margin-top: 20px; }
        .x { color: blue; font-weight: bold; }
        .o { color: red; font-weight: bold; }
    </style>
</head>
<body>

<h1 style="text-align:center;">Гра: Хрестики-нулики</h1>
<h2 style="text-align:center;">Masenkov Vladyslav (ПЗІС-24005м)</h2>
<div class="message"><?= htmlspecialchars($message) ?></div>

<table>
    <?php for($i=0; $i<3; $i++): ?>
        <tr>
            <?php for($j=0; $j<3; $j++): 
                $index = $i*3 + $j;
                ?>
                <td>
                    <?php if ($board[$index] === ' ' && $turn != '-'): ?>
                        <a href="?move=<?= $index ?>">-</a>
                    <?php elseif ($board[$index] === 'X'): ?>
                        <span class="x">X</span>
                    <?php elseif ($board[$index] === 'O'): ?>
                        <span class="o">O</span>
                    <?php else: ?>
                        <?= htmlspecialchars($board[$index]) ?>
                    <?php endif; ?>
                </td>
            <?php endfor; ?>
        </tr>
    <?php endfor; ?>
</table>

<div class="restart">
    <a href="?reset=1">🔄 Почати спочатку</a>
</div>

</body>
</html>
