<?php
// Запускаємо сесію для збереження стану гри між кліками
session_start();

// Функція для перевірки переможця
function checkWinner($board) {
    // Виграшні комбінації (індекси масиву 3x3)
    $winningCombos = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8], // Рядки
        [0, 3, 6], [1, 4, 7], [2, 5, 8], // Стовпці
        [0, 4, 8], [2, 4, 6]             // Діагоналі
    ];

    foreach ($winningCombos as $combo) {
        if ($board[$combo[0]] !== '' &&
            $board[$combo[0]] === $board[$combo[1]] &&
            $board[$combo[1]] === $board[$combo[2]]) {
            return $board[$combo[0]]; // Повертаємо 'X' або 'O'
        }
    }

    // Перевірка на нічию (якщо немає порожніх клітинок)
    if (!in_array('', $board)) {
        return 'Tie';
    }

    return null; // Гра продовжується
}

// Скидання гри
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ініціалізація або відновлення ігрового поля
if (!isset($_SESSION['board'])) {
    $_SESSION['board'] = array_fill(0, 9, ''); // Масив з 9 порожніх елементів
    $_SESSION['turn'] = 'X'; // Першим ходить Х
    $_SESSION['winner'] = null;
}

// Обробка ходу гравця
if (isset($_GET['move']) && $_SESSION['winner'] === null) {
    $move = (int)$_GET['move'];
    
    // Перевіряємо, чи клітинка в межах поля і чи вона порожня
    if ($move >= 0 && $move <= 8 && $_SESSION['board'][$move] === '') {
        // Робимо хід
        $_SESSION['board'][$move] = $_SESSION['turn'];
        
        // Перевіряємо, чи є переможець
        $_SESSION['winner'] = checkWinner($_SESSION['board']);
        
        // Передаємо хід іншому гравцю, якщо гра не закінчена
        if ($_SESSION['winner'] === null) {
            $_SESSION['turn'] = ($_SESSION['turn'] === 'X') ? 'O' : 'X';
        }
    }
    // Перенаправляємо, щоб уникнути повторного відправлення форми при оновленні сторінки
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Хрестики-нулики на PHP</title>
    <style>
        body { font-family: sans-serif; text-align: center; margin-top: 50px; }
        .board { display: grid; grid-template-columns: repeat(3, 100px); gap: 5px; justify-content: center; margin: 20px auto; }
        .cell {
            width: 100px; height: 100px;
            font-size: 3em; display: flex;
            align-items: center; justify-content: center;
            background-color: #f0f0f0; color: #333;
            text-decoration: none; border-radius: 5px;
        }
        .cell:hover { background-color: #ddd; }
        .status { font-size: 1.5em; margin-bottom: 20px; font-weight: bold; }
        .reset-btn { padding: 10px 20px; font-size: 1em; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 5px; text-decoration: none; }
        .reset-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>

    <h1>Хрестики-нулики</h1>

    <div class="status">
        <?php
        if ($_SESSION['winner'] === 'Tie') {
            echo "Нічия!";
        } elseif ($_SESSION['winner']) {
            echo "Переможець: " . $_SESSION['winner'] . " 🎉";
        } else {
            echo "Зараз ходить: " . $_SESSION['turn'];
        }
        ?>
    </div>

    <div class="board">
        <?php foreach ($_SESSION['board'] as $index => $value): ?>
            <?php if ($value === '' && $_SESSION['winner'] === null): ?>
                <a href="?move=<?php echo $index; ?>" class="cell"></a>
            <?php else: ?>
                <div class="cell" style="cursor: default;"><?php echo $value; ?></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <br>
    <a href="?reset=1" class="reset-btn">Почати заново</a>

</body>
</html>l