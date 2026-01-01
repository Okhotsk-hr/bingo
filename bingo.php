<?php
session_start();

/* ===== 初期化 ===== */
if (!isset($_SESSION['numbers'])) {
    $_SESSION['numbers'] = range(1, 75); // 抽選対象
    shuffle($_SESSION['numbers']);
    $_SESSION['history'] = array();     // 抽選履歴
}

/* ===== 抽選処理 ===== */
$current = null;
if (isset($_POST['draw']) && count($_SESSION['numbers']) > 0) {
    $current = array_shift($_SESSION['numbers']);
    $_SESSION['history'][] = $current;
}

/* ===== リセット ===== */
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: bingo.php");
    exit;
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

        .current {
            font-size: 80px;
            margin: 20px;
            color: red;
        }

        .history span {
            display: inline-block;
            width: 40px;
            margin: 3px;
            padding: 5px;
            border: 1px solid #000;
        }
    </style>
</head>

<body>

    <h1>🎯 ビンゴ ルーレット</h1>

    <?php if ($current !== null): ?>
        <div class="current">
            <?php echo $current; ?>
        </div>
    <?php else: ?>
        <div class="current">--</div>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="draw" <?php if (count($_SESSION['numbers']) == 0) echo 'disabled'; ?>>
            抽選
        </button>
        <button type="submit" name="reset">リセット</button>
    </form>

    <h2>抽選済み番号</h2>
    <div class="history">
        <?php foreach ($_SESSION['history'] as $n): ?>
            <span><?php echo $n; ?></span>
        <?php endforeach; ?>
    </div>

    <?php if (count($_SESSION['numbers']) == 0): ?>
        <h2>🎉 全ての数字が出ました！</h2>
    <?php endif; ?>

</body>

</html>