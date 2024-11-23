<?php
$insurance_rate = filter_input(INPUT_POST, 'insurance_rate', FILTER_VALIDATE_FLOAT); // 自己負担割合
$drugs = isset($_POST['drugs']) ? $_POST['drugs'] : [];

if ($insurance_rate !== false && !empty($drugs)) {
    echo "<h1>計算結果</h1>";
    echo "<h2>自己負担割合: {$insurance_rate}%</h2><hr>";

    $total_special_fee = 0; // 特別料金合計
    $total_insurance_fee_with_special = 0; // 保険対象費用（特別料金を含む）
    $total_insurance_fee_without_special = 0; // 保険対象費用（特別料金なし）
    $total_out_of_pocket_with_special = 0; // 自己負担総額（特別料金あり）
    $total_out_of_pocket_without_special = 0; // 自己負担総額（特別料金なし）

    foreach ($drugs as $index => $drug) {
        $brand_price = floatval($drug['brand_price']); // 先発品の金額
        $generic_price = floatval($drug['generic_price']); // 後発品の金額
        $daily_dose = floatval($drug['daily_dose']); // 1日量
        $days = intval($drug['days']); // 処方日数
        $form = htmlspecialchars($drug['form'], ENT_QUOTES, 'UTF-8'); // 剤形

        // 価格差計算
        $price_diff = $brand_price - $generic_price;

        // 価格差の1/4を小数点第2位で四捨五入
        $quarter_price_diff = round($price_diff / 4, 2);

        // 基準金額計算（特別料金あり）
        $insurance_base_price_with_special = $brand_price - $quarter_price_diff;

        // 基準金額計算（特別料金なし：ジェネリックのみ）
        $insurance_base_price_without_special = $generic_price;

        // 特別料金計算
        $special_fee_per_day = $quarter_price_diff * $daily_dose;
        $special_fee_points = ($special_fee_per_day <= 15) ? 1 : round($special_fee_per_day / 10); // 点数化
        $special_fee_total = $special_fee_points * $days * 10; // 処方日数分
        $total_special_fee += $special_fee_total;

        // 保険対象費用（特別料金あり）
        $insurance_fee_per_day_with_special = $insurance_base_price_with_special * $daily_dose;
        $insurance_fee_points_with_special = ($insurance_fee_per_day_with_special <= 15) ? 1 : round($insurance_fee_per_day_with_special / 10);
        $insurance_fee_total_with_special = $insurance_fee_points_with_special * $days * 10;
        $total_insurance_fee_with_special += $insurance_fee_total_with_special;

        // 保険対象費用（特別料金なし）
        $insurance_fee_per_day_without_special = $insurance_base_price_without_special * $daily_dose;
        $insurance_fee_points_without_special = ($insurance_fee_per_day_without_special <= 15) ? 1 : round($insurance_fee_per_day_without_special / 10);
        $insurance_fee_total_without_special = $insurance_fee_points_without_special * $days * 10;
        $total_insurance_fee_without_special += $insurance_fee_total_without_special;

        // 自己負担額を計算
        $self_payment_with_special = round($insurance_fee_total_with_special * ($insurance_rate / 100)) + round($special_fee_total * ($insurance_rate / 100));
        $self_payment_without_special = round($insurance_fee_total_without_special * ($insurance_rate / 100));
        $total_out_of_pocket_with_special += $self_payment_with_special;
        $total_out_of_pocket_without_special += $self_payment_without_special;

        // 表示
        echo "<h2>薬 {$index}</h2>";
        echo "先発品の金額: {$brand_price} 円<br>";
        echo "後発品の金額: {$generic_price} 円<br>";
        echo "価格差: {$price_diff} 円<br>";
        echo "価格差の1/4（四捨五入）: {$quarter_price_diff} 円<br>";
        echo "特別料金: {$special_fee_total} 円<br>";
        echo "保険対象費用（特別料金あり）: {$insurance_fee_total_with_special} 円<br>";
        echo "保険対象費用（特別料金なし）: {$insurance_fee_total_without_special} 円<br>";
        echo "自己負担額（特別料金あり）: {$self_payment_with_special} 円<br>";
        echo "自己負担額（特別料金なし）: {$self_payment_without_special} 円<br>";
        echo "<hr>";
    }

    // 差額計算
    $difference = $total_out_of_pocket_with_special - $total_out_of_pocket_without_special;

    // 合計表示
    echo "<h2>合計</h2>";
    echo "特別料金合計: {$total_special_fee} 円<br>";
    echo "自己負担額（特別料金あり）: {$total_out_of_pocket_with_special} 円<br>";
    echo "自己負担額（特別料金なし）: {$total_out_of_pocket_without_special} 円<br>";
    echo "特別料金の有無による差額: {$difference} 円<br>";
} else {
    echo "データが正しく入力されていません。";
}
?>

