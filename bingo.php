<?php
session_start();

/* ===== 初期化 ===== */
if (!isset($_SESSION['numbers'])) {
    $_SESSION['numbers'] = range(1, 75);
    shuffle($_SESSION['numbers']);
    $_SESSION['history'] = array();
}

/* ===== 抽選結果確定 ===== */
$fixedNumber = null;
if (isset($_POST['draw']) && count($_SESSION['numbers']) > 0) {
    $fixedNumber = array_shift($_SESSION['numbers']);
    $_SESSION['history'][] = $fixedNumber;
}

/* ===== リセット ===== */
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: roulette.php");
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
            text-align: center;
            font-family: sans-serif;
        }

        #display {
            font-size: 100px;
            margin: 30px;
            color: red;
        }

        .history span {
            display: inline-block;
            width: 40px;
            margin: 3px;
            padding: 5px;
            border: 1px solid #000;
        }

        button {
            font-size: 18px;
            padding: 10px 20px;
        }
    </style>
</head>

<body>

    <h1>🎯 ビンゴ ルーレット</h1>

    <div id="display">--</div>

    <form method="post" id="drawForm">
        <button type="submit" name="draw"
            <?php if (count($_SESSION['numbers']) == 0) echo 'disabled'; ?>>
            抽選
        </button>
        <button type="submit" name="reset">リセット</button>

        <?php if ($fixedNumber !== null): ?>
            <input type="hidden" id="result" value="<?php echo $fixedNumber; ?>">
        <?php endif; ?>
    </form>

    <h2>抽選済み番号</h2>
    <div class="history">
        <?php foreach ($_SESSION['history'] as $n): ?>
            <span><?php echo $n; ?></span>
        <?php endforeach; ?>
    </div>

    <script>
        <?php if ($fixedNumber !== null): ?>
            let count = 0;
            let interval = setInterval(() => {
                document.getElementById("display").textContent =
                    Math.floor(Math.random() * 75) + 1;
                count++;
                if (count > 40) { // 約2秒
                    clearInterval(interval);
                    document.getElementById("display").textContent =
                        document.getElementById("result").value;
                }
            }, 50);
        <?php endif; ?>
    </script>

</body>

</html>