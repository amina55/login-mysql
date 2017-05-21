<?php

function generateRandomSalt($length = 15) {
    $alphabets = range('A','Z');
    $small_alphabets = range('a','z');
    $numbers = range('0','9');
    $additional_characters = array('_','.','!');
    $final_array = array_merge($alphabets,$numbers,$additional_characters, $small_alphabets);

    $password = '';

    while($length--) {
        $key = array_rand($final_array);
        $password .= $final_array[$key];
    }
    return $password;
}


function encrypt_decrypt($action, $string, $secret_key = null) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_iv = 'This is my secret iv';

    $secret_key = ($secret_key) ? $secret_key : 'ABF4EE085C7BF9CDB25BC83BA4764F60AC80215A6FE095BB2AA6B46417DFF9696232495E2ECFEF18C9C38A50D88FE3821495D99B1839003BA1197D4A4F5F8D6B';
    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}
/*
$plain_txt = "amina555";
$secret_key = generateRandomSalt(15);

echo "Plain Text = $plain_txt\n";
echo "Secret Key = $secret_key\n";

$encrypted_txt = encrypt_decrypt('encrypt', $plain_txt, $secret_key);
echo "Encrypted Text = $encrypted_txt\n";

$decrypted_txt = encrypt_decrypt('decrypt', $encrypted_txt, $secret_key);
echo "Decrypted Text = $decrypted_txt\n";

if( $plain_txt === $decrypted_txt ) echo "SUCCESS";
else echo "FAILED";*/
?>


