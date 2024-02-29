<?php
error_reporting(0);
session_start();

$finish = "";

switch ($_SESSION['chosen']) {
    case 'caesar':
        $finish = decrypt_caesar($_POST['decrypt-shift-caesar'], $_POST['decrypt-text-caesar']);
        $url = "../view/caesar.html";
        break;
    case 'aes256':
        $finish = decrypt_aes256($_POST['decrypt-text-aes256'], $_POST['decrypt-pass-aes256']);
        $url = "../view/aes256.html";
        break;
    case 'md5':
        $finish = decrypt_md5_sha1($_POST['decrypt-text-md5']);
        $url = "../view/md5.html";
        break;
    case 'sha1':
        $finish = decrypt_md5_sha1($_POST['decrypt-text-sha1']);
        $url = "../view/sha1.html";
        break;
    case 'polybios':
        $finish = decrypt_polybios_rot13($_POST['decrypt-text-polybios']);
        $url = "../view/polybios.html";
        break;
    case 'rot13':
        $finish = decrypt_polybios_rot13($_POST['decrypt-text-rot13']);
        $url = "../view/rot13.html";
        break;
    default:
        $finish = "Could not decrypt";
        break;
}

function decrypt_caesar ($shift, $text): string  {

    if(!isset($shift) || !isset($text)) {
        echo "Please fill in all fields";
        exit;
    }

    $decrypted = "";
    $alphabet = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
                 "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
                 "u", "v", "w", "x", "y", "z"];

    for ($i = 0; $i < strlen($text); $i++) {
        $letter = $text[$i];
        if (ctype_alpha($letter)) {
            $index = array_search($letter, $alphabet);
            while ($index < $shift) {
                $index += 26;
            }
            $newIndex = ($index - $shift + 26) % 26;
            $newLetter = $alphabet[$newIndex];
            $decrypted .= $newLetter;
        } else {
            $decrypted .= $letter;
        }
    }
    return $decrypted;
}

function decrypt_md5_sha1($text): string {

    $str = "";
    if ($_SESSION['chosen'] == "md5") {
        $file = fopen("../txt/md5.txt", "r");
    } else {
        $file = fopen("../txt/sha1.txt", "r");
    }

    if ($file) {
        while (($line = fgets($file)) !== false) {
            if (str_contains($line, $text)) {
                $arr = explode(" ", $line);
            }
        }
        fclose($file);
    }

    if (count($arr) > 2) {
        for ($i = 0; count($arr) > $i+1; $i++) {
            $str .= $arr[$i] . " ";
        }
    } else {
        $str = $arr[0];
    }
    return $str;
}

function decrypt_aes256 ($encryptedText, $password): string | null {

    $encryptedText = base64_decode($encryptedText);

    $method = "AES-256-CBC";
    $iv = substr($encryptedText, 0, 16);
    $hash = substr($encryptedText, 16, 32);
    $ciphertext = substr($encryptedText, 48);
    $key = hash('sha256', $password, true);

    if (!hash_equals(hash_hmac('sha256', $ciphertext . $iv, $key, true), $hash)) return 'Invalid password';

    return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
}

function decrypt_polybios_rot13($text): string {
    $decrypted = "";
    $text = strtolower($text);
    if ($_SESSION['chosen'] == "polybios") {
        $alphabet = [11=>"a", 12=>"b", 13=>"c", 14=>"d", 15=>"e",
                21=>"f", 22=>"g", 23=>"h", 24=>"i", 25=>"j",
                31=>"k", 32=>"l", 33=>"m", 34=>"n", 35=>"o",
                41=>"p", 42=>"q", 43=>"r", 44=>"s", 45=>"t",
                51=>"u", 52=>"v", 53=>"w", 54=>"x", 55=>"y",
                61=>"z", 62=>" "];
    } else {
        $alphabet = ['n'=>"a", 'o'=>"b", 'p'=>"c", 'q'=>"d", 'r'=>"e",
                's'=>"f", 't'=>"g", 'u'=>"h", 'v'=>"i", 'w'=>"j",
                'x'=>"k", 'y'=>"l", 'z'=>"m", 'a'=>"n", 'b'=>"o",
                'c'=>"p", 'd'=>"q", 'e'=>"r", 'f'=>"s", 'g'=>"t",
                'h'=>"u", 'i'=>"v", 'j'=>"w", 'k'=>"x", 'l'=>"y",
                'm'=>"z", ' '=>" "];
    }
    foreach (str_split($text) as $let) {
        $decrypted .= $alphabet[$let];
    }
    if ($decrypted == "") {
        return "Error: Invalid input";
    }
    return $decrypted;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Cypher</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../../dist/output.css">
    </head>
    <body class="overflow-hidden">
        <nav class="bg-zinc-900 py-3">
            <ul class="flex justify-center items-center">
                <li class="mr-4">
                    <button class="rounded-full bg-lime-500 py-1 px-3"> <a href="../view/index.html">Home</a></button>
                    <button class="rounded-full bg-lime-500 py-1 px-3"> <a href=<?= $url?>>Back</a></button>
                </li>
            </ul>
        </nav>
        <section class="mx-auto sm:w-fit flex justify-center items-center h-screen">
            <div class="overflow-x-auto w-screen opacity-80">
                <div class="w-fit px-3 mx-auto bg-slate-100 rounded-lg pb-4">
                    <h1 class="text-9xl"><?= $finish ?></h1>
                </div>
            </div>
        </section>
    </body>
</html>
