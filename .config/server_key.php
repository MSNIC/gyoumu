<?php
function rsa_key_generate(
    $p // 素数１
    ,$q // 素数2
){
    // 素数が一緒だとfalse
    if($p === $q){
        return false;
    }

    // 鍵生成
    $n = (string)gmp_mul($p, $q); // mul-> 乗算
    $n_ = gmp_mul( gmp_sub($p, '1'), gmp_sub($q, '1')); //sub-> 減算 (p-1)(q-1)
    $rand = gmp_random_range( 0, gmp_sub($n_, '1')); // random_range-> ランダムな数 0 ~ (n')

    // 互いに素な数が見つかるまで
    while (true) {
        $coprime_numbers = (string)gmp_gcd($rand, $n_); // gcd-> 最大公約数を返す

        // 最大公約数が1なら互いに素な数
        if ($coprime_numbers === '1') {
            $e = (string)$rand;
            break;
        }
        // 非互いに素な数減算し再計算
        $rand = gmp_sub($rand, '1'); // rand--;
    }

    $d = (string)gmp_invert($e, $n_); // n'を法としたeの逆数

    // 秘密鍵:d 公開鍵:n,e
    return [$d, $n, $e];
}

function getSosu(){
    while (true) {
        // 10^6
        $min = gmp_pow('10', 3);
        // 10^6 - 1
        $max = gmp_sub(gmp_pow('10',4), '1');
        // ランダムな数を生成
        $candidate = gmp_random_range($min, $max);
        // 奇数にする
        if (gmp_mod($candidate, '2') == 0) {
            $candidate = gmp_add($candidate, '1');
        }
        // 素数判定
        if (gmp_prob_prime($candidate, 25) > 0) {
            return $candidate;
        }
    }
}

function key_gen(){
    $p = getSosu();
    $q = getSosu();
    $keys = rsa_key_generate($p, $q);
    $pub1 = $keys[1];
    $pub2 = $keys[2];
    $pri = $keys[0];
    $data = [
        'pub1' => floatval($pub1),
        'pub2' => floatval($pub2),
        'pri' => floatval($pri)
    ];
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/.config/keys.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function key_get(){
    $json = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/.config/keys.json');
    $data = json_decode($json, true);
    return $data;
}
?>