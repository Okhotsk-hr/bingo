<?php
session_start();

include("card_db.php");
/* ===== リセット処理（最優先） ===== */
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: card.php");
    exit;
}

/* ===== 初期化 ===== */
if (!isset($_SESSION['card'])) {

    // 各列の番号範囲
    $ranges = [
        range(1, 15),    // B
        range(16, 30),   // I
        range(31, 45),   // N
        range(46, 60),   // G
        range(61, 75)    // O
    ];

    $card = [];

    // 各列ごとに処理
    for ($col = 0; $col < 5; $col++) {

        shuffle($ranges[$col]);
        $columnNumbers = array_slice($ranges[$col], 0, 5);
        sort($columnNumbers);   // 昇順

        for ($row = 0; $row < 5; $row++) {
            $card[$row][$col] = [
                'num'  => $columnNumbers[$row],
                'open' => false
            ];
        }
    }

    // FREEマス
    $card[2][2]['num']  = 'FREE';
    $card[2][2]['open'] = true;

    $_SESSION['card'] = $card;
}

/* ===== マスを開ける ===== */
if (isset($_GET['open'])) {
    list($x, $y) = explode(',', $_GET['open']);
    $_SESSION['card'][$x][$y]['open'] = true;
}

/* ===== ビンゴ判定 ===== */
function checkBingo($card)
{
    // 横・縦
    for ($i = 0; $i < 5; $i++) {
        $row = $col = true;
        for ($j = 0; $j < 5; $j++) {
            if (!$card[$i][$j]['open']) $row = false;
            if (!$card[$j][$i]['open']) $col = false;
        }
        if ($row || $col) return true;
    }

    // 斜め
    $diag1 = $diag2 = true;
    for ($i = 0; $i < 5; $i++) {
        if (!$card[$i][$i]['open']) $diag1 = false;
        if (!$card[$i][4 - $i]['open']) $diag2 = false;
    }

    return $diag1 || $diag2;
}

$bingo = checkBingo($_SESSION['card']);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>PHP ビンゴゲーム</title>
    <style>
        table {
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            width: 60px;
            height: 60px;
            text-align: center;
            border: 1px solid #000;
            font-size: 20px;
        }

        .open {
            background: #ffcccc;
        }

        a {
            text-decoration: none;
            color: #000;
            display: block;
        }
    </style>
</head>

<body>

    <h1>ビンゴゲーム</h1>

    <table>
        <tr>
            <th>B</th>
            <th>I</th>
            <th>N</th>
            <th>G</th>
            <th>O</th>
        </tr>

        <?php foreach ($_SESSION['card'] as $i => $row): ?>
            <tr>
                <?php foreach ($row as $j => $cell): ?>
                    <td class="<?php echo $cell['open'] ? 'open' : ''; ?>">
                        <?php if ($cell['open']): ?>
                            <?php echo $cell['num']; ?>
                        <?php else: ?>
                            <a href="?open=<?php echo $i . ',' . $j; ?>">
                                <?php echo $cell['num']; ?>
                            </a>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php if ($bingo): ?>
        <h2>🎉 BINGO!! 🎉</h2>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="reset">リセット</button>
    </form>

</body>

</html>