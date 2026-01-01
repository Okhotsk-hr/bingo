<?php
session_start();

/* ===== リセット ===== */
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: bingo.php");
    exit;
}

/* ===== 初期化 ===== */
if (!isset($_SESSION['drawn'])) {
    $_SESSION['drawn'] = [];
}
if (!isset($_SESSION['pending'])) {
    $_SESSION['pending'] = null;
}

/* ===== 抽選 ===== */
if (isset($_POST['draw'])) {

    // 前回 pending を確定
    if ($_SESSION['pending'] !== null) {
        $_SESSION['drawn'][] = $_SESSION['pending'];
        $_SESSION['pending'] = null;
    }

    // 未抽選数字
    $numbers = range(1, 75);
    $remaining = array_values(array_diff($numbers, $_SESSION['drawn']));

    if (!empty($remaining)) {
        $_SESSION['pending'] = $remaining[array_rand($remaining)];
    }
}

/* 最新番号 */
$fixedNumber = $_SESSION['pending'];

/* ===== BINGO列分類 ===== */
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

        .latest {
            font-size: 48px;
            margin: 20px;
            color: red;
        }

        .start {
            font-size: 28px;
            color: green;
            margin: 20px;
        }

        .bingo {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .column {
            margin: 0 10px;
        }

        .column table {
            border-collapse: collapse;
        }

        .column th,
        .column td {
            border: 1px solid #000;
            width: 60px;
            height: 35px;
        }

        .column th {
            font-size: 28px;
        }

        button {
            font-size: 18px;
            padding: 10px 20px;
            margin: 5px;
        }
    </style>
</head>

<body>

    <h1>B I N G O</h1>

    <div id="display">--</div>

    <?php
    // ★ ビンゴ開始時の表示
    if (empty($_SESSION['drawn']) && $fixedNumber === null) {
        echo '<div class="start">ビンゴを開始してください</div>';
        $info = "start";
        $str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPUQRSTUVWXYZ';
        $_SESSION["str_r"] = substr(str_shuffle($str), 0, 7);
        $info .= "-" . $_SESSION["str_r"];
        include("db.php");
    } else {
        $info = "drop";
        $info .= "-" . $_SESSION["str_r"];
        include("db.php");
    }
    ?>

    <?php if ($fixedNumber !== null): ?>
        <div class="latest">
            最新番号：<span id="latest"></span>
        </div>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="draw">抽選</button>
        <button type="submit" name="reset">リセット</button>
    </form>

    <h2>抽選済み番号</h2>

    <div class="bingo">
        <?php foreach ($columns as $label => $nums): ?>
            <div class="column">
                <table>
                    <tr>
                        <th><?= $label ?></th>
                    </tr>
                    <?php foreach ($nums as $n): ?>
                        <tr>
                            <td><?= $n ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($fixedNumber !== null): ?>
        <input type="hidden" id="result" value="<?= $fixedNumber ?>">

        <script>
            // ルーレット演出
            let roulette = setInterval(() => {
                document.getElementById("display").textContent =
                    Math.floor(Math.random() * 75) + 1;
            }, 50);

            // 停止 → 最新番号表示
            setTimeout(() => {
                clearInterval(roulette);

                let result = document.getElementById("result").value;
                document.getElementById("display").textContent = result;
                document.getElementById("latest").textContent = result;
            }, 2000);
        </script>
    <?php endif; ?>

</body>

</html>