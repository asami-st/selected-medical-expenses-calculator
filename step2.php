<?php
// 入力値を取得
$drug_count = filter_input(INPUT_POST, 'drug_count', FILTER_VALIDATE_INT);

if ($drug_count && $drug_count > 0): ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>薬代計算アプリ - 入力フォーム</title>
</head>
<body>
    <h1>薬の詳細を入力してください</h1>

    <!-- 保険負担割合を入力 -->
    <form action="calculate.php" method="POST">
        <h2>保険負担割合</h2>
        <label for="insurance_rate">保険負担の割合（%）:</label>
        <input type="number" id="insurance_rate" name="insurance_rate" min="0" max="100" step="0.01" value="30" required>
        <p>※通常の保険負担割合は30%です。必要に応じて変更してください。</p>
        <hr>

        <!-- 各薬剤の詳細を入力 -->
        <?php for ($i = 1; $i <= $drug_count; $i++): ?>
            <h2>薬 <?php echo $i; ?></h2>
            <label for="drug_<?php echo $i; ?>_brand_price">先発品の金額:</label>
            <input type="number" id="drug_<?php echo $i; ?>_brand_price" name="drugs[<?php echo $i; ?>][brand_price]" step="0.01" required><br>

            <label for="drug_<?php echo $i; ?>_generic_price">後発品の金額:</label>
            <input type="number" id="drug_<?php echo $i; ?>_generic_price" name="drugs[<?php echo $i; ?>][generic_price]" step="0.01" required><br>

            <label for="drug_<?php echo $i; ?>_form">剤形:</label>
            <select id="drug_<?php echo $i; ?>_form" name="drugs[<?php echo $i; ?>][form]" required>
                <option value="" disabled selected>選択してください</option>
                <option value="内服薬">内服薬</option>
                <option value="外用薬">外用薬</option>
            </select><br>

            <label for="drug_<?php echo $i; ?>_daily_dose">1日量:</label>
            <input type="number" id="drug_<?php echo $i; ?>_daily_dose" name="drugs[<?php echo $i; ?>][daily_dose]" min="1" step="0.01" required><br>

            <label for="drug_<?php echo $i; ?>_days">処方日数:</label>
            <input type="number" id="drug_<?php echo $i; ?>_days" name="drugs[<?php echo $i; ?>][days]" min="1" step="1" required><br><br>
        <?php endfor; ?>

        <button type="submit">計算する</button>
    </form>
</body>
</html>
<?php
else:
    echo "薬の数が正しく入力されていません。";
endif;
?>

