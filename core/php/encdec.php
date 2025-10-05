<?php
// RSA暗号化
function rsa_encryption(
    $a // 暗号化対象,平文
    ,$n // 公開鍵1
    ,$e // 公開鍵2
){
    $b = [];
    foreach ($a as $value) {
        // aの一つをe乗する
        $a_e = gmp_pow($value, $e); // pow-> べき乗
        // e乗した値をnで割った余りを格納
        $b[] = (string)gmp_div_r($a_e, $n); // div_r-> 剰余
    }
    return $b;
}
// RSA復号
function rsa_composite(
    $b // 暗号文
    ,$d // 秘密鍵
    ,$n // 公開鍵n
){
    $a = [];
    foreach ($b as $value) {
        // bの一つをd乗する
        $b_d = gmp_pow($value, $d); // pow-> べき乗
        // d乗した値をnで割った余りを格納
        $a[] = (string)gmp_div_r($b_d, $n); // div_r-> 剰余
    }
    return $a;
}

// 平文を数値配列に変換
function convert_string_to_integer(
    $str
){
    // 初期配列
    $ord_array = [];
    // 文字列を全て数値配列に
    for ($i = 0; $i < mb_strlen($str); $i++) {
        // 一文字取得
        $value = mb_substr($str, $i, 1);
        // 文字を数値に
        $ord_array[] = mb_ord($value);
    }
    return $ord_array;
}

// 数値配列を文字列に変換
function convert_integer_to_string(
    $int_array
){
    // 初期文字列
    $chr = '';
    // 配列の数値を全て文字列に変換
    foreach ($int_array as $value) {
        // 数値を文字列に
        $chr .=  mb_chr($value);
    }
    return $chr;
}
//================================
//暗号化
function encrypt(
    $str // 暗号化対象
){
    // 鍵読み込み
    include('../.config/server_key.php');
    $data = key_get();
    $n = $data['pub1'];
    $e = $data['pub2'];
    // 文字列を数値配列に変換
    $a = convert_string_to_integer($str);
    // 数値配列を暗号化
    $b = rsa_encryption($a, $n, $e);
    $c = implode('.', $b);
    return $c;
}

//複合化
function decrypt(
    $str // 複合化対象
){
    // 鍵読み込み
    include('../.config/server_key.php');
    $data = key_get();
    $d = $data['pri'];
    $n = $data['pub1'];
    // 文字列を数値配列に変換
    $b = explode('.', $str);
    // 数値配列を複合化
    $a = rsa_composite($b, $d, $n);
    // 数値配列を文字列に変換
    $chr = convert_integer_to_string($a);
    return $chr;
}
?>