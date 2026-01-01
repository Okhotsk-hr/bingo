<?php
session_start();

/* 初期化 */
if (!isset($_SESSION['drawn'])) {
    $_SESSION['drawn'] = [];
}
if (!isset($_SESSION['pending'])) {
    $_SESSION['pending'] = null;
}

/* 抽選ボタンが押された */
if (isset($_POST['draw'])) {

    // 前回の pending を確定
    if ($_SESSION['pending'] !== null) {
        $_SESSION['drawn'][] = $_SESSION['pending'];
        $_SESSION['pending'] = null;
    }

    // 未抽選の数字を抽選
    $numbers = range(1, 75);
    $remaining = array_values(array_diff($numbers, $_SESSION['drawn']));

    if (!empty($remaining)) {
        $_SESSION['pending'] = $remaining[array_rand($remaining)];
    }
}

/* 最新確定番号（表示用） */
$fixedNumber = $_SESSION['pending'];

/* BINGO列分類 */
$columns = ['B' => [], 'I' => [], 'N' => [], 'G' => [], 'O' => []];
foreach ($_SESSION['drawn'] as $n) {
    if ($n <= 15) $columns['B'][] = $n;
    elseif ($n <= 30) $columns['I'][] = $n;
    elseif ($n <= 45) $columns['N'][] = $n;
    elseif ($n <= 60) $columns['G'][] = $n;
    else $columns['O'][] = $n;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ビンゴ ルーレット</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
        }

        #display {
            font-size: 80px;
            font-weight: bold;
            margin: 20px;
        }

        table {
            margin: auto;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            width: 80px;
            height: 40px;
        }

        th {
            font-size: 24px;
        }

        .latest {
            font-size: 48px;
            margin: 20px;
            color: red;
        }
    </style>
</head>

<body>

    <h1>B I N G O</h1>

    <div id="display">--</div>

    <?php if ($fixedNumber !== null): ?>
        <div class="latest">
            前回の番号：<span id="latest"></span>
        </div>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="draw">抽選</button>
    </form>

    <h2>抽選済み番号</h2>
    <table>
        <tr>
            <th>B</th>
            <th>I</th>
            <th>N</th>
            <th>G</th>
            <th>O</th>
        </tr>
        <tr>
            <?php foreach ($columns as $col): ?>
                <td><?= implode(', ', $col) ?></td>
            <?php endforeach; ?>
        </tr>
    </table>

    <?php if ($fixedNumber !== null): ?>
        <input type="hidden" id="result" value="<?= $fixedNumber ?>">

        <script>
            let roulette = setInterval(() => {
                document.getElementById("display").textContent =
                    Math.floor(Math.random() * 75) + 1;
            }, 50);

            // 2秒後に停止
            setTimeout(() => {
                clearInterval(roulette);

                // 最新番号を表示
                document.getElementById("display").textContent =
                    document.getElementById("result").value;

                document.getElementById("latest").textContent =
                    document.getElementById("result").value;
            }, 2000);
        </script>
    <?php endif; ?>

</body>

</html>