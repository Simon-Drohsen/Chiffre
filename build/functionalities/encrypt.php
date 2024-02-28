<?php
session_start();

$finish = "";

switch ($_SESSION['chosen']) {
    case 'caesar':
        $finish = encrypt_caesar($_POST['encrypt-shift-caesar'], $_POST['encrypt-text-caesar']);
        $url = "../html/caesar.html";
        break;
    case 'aes256':
        $finish = encrypt_aes256($_POST['encrypt-text-aes256'], $_POST['encrypt-pass-aes256']);
        $url = "../html/aes256.html";
        break;
    case 'md5':
        $finish = encrypt_md5_sha1($_POST['encrypt-text-md5']);
        $url = "../html/md5.html";
        break;
    case 'sha1':
        $finish = encrypt_md5_sha1($_POST['encrypt-text-sha1']);
        $url = "../html/sha1.html";
        break;
    case 'polybios':
        $finish = encrypt_polybios_rot13($_POST['encrypt-text-polybios']);
        $url = "../html/polybios.html";
        break;
    case 'rot13':
        $finish = encrypt_polybios_rot13($_POST['encrypt-text-rot13']);
        $url = "../html/rot13.html";
        break;
    default:
        $finish = "Could not encrypt";
        break;
}

function encrypt_caesar ($shift, $text): string  {

    if(!isset($shift) || !isset($text)) {
        echo "Please fill in all fields";
        exit;
    }

    $encrypted = "";
    $alphabet = ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
                 "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
                 "u", "v", "w", "x", "y", "z"];

    for($i = 0; $i < strlen($text); $i++) {
        $letter = $text[$i];
        if (ctype_alpha($letter)) {
            $index = array_search($letter, $alphabet);
            $newIndex = ($index + $shift) % 26;
            $newLetter = $alphabet[$newIndex];
            $encrypted .= $newLetter;
        } else {
            $encrypted .= $letter;
        }
    }
    return $encrypted;
}
function encrypt_aes256 ($plaintext, $password): string {
    $method = "AES-256-CBC";
    $key = hash('sha256', $password, true);
    $iv = openssl_random_pseudo_bytes(16);

    $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
    $hash = hash_hmac('sha256', $ciphertext . $iv, $key, true);

    return base64_encode($iv . $hash . $ciphertext);}

function encrypt_md5_sha1 ($text): string {
    $exist = false;
    if ($_SESSION['chosen'] == 'md5') {
        $data = $_POST['encrypt-text-md5'];
        $file = fopen("../txt/md5.txt", "r");
    } else {
        $data = $_POST['encrypt-text-sha1'];
        $file = fopen("../txt/sha1.txt", "r");
    }

    if ($file) {
        while (($line = fgets($file)) !== false) {
            if (str_contains($line, $text)) {
                $exist = true;
            }
        }
        fclose($file);
    }

    if (!$exist && $_SESSION['chosen'] == 'md5') {
        file_put_contents("../txt/md5.txt", $data . " " . md5($text) . "\n", FILE_APPEND);
    } elseif (!$exist && $_SESSION['chosen'] == 'sha1') {
        file_put_contents("../txt/sha1.txt", $data . " " . sha1($text) . "\n", FILE_APPEND);
    }
    return ($_SESSION['chosen'] == 'md5'?md5($text):sha1($text));
}

function encrypt_polybios_rot13($text): string {
    $encrypted = "";
    $text = strtolower($text);
    if ($_SESSION['chosen'] == "polybios") {
        $alphabet = [11=>"a", 12=>"b", 13=>"c", 14=>"d", 15=>"e",
                21=>"f", 22=>"g", 23=>"h", 24=>"i", 25=>"j",
                31=>"k", 32=>"l", 33=>"m", 34=>"n", 35=>"o",
                41=>"p", 42=>"q", 43=>"r", 44=>"s", 45=>"t",
                51=>"u", 52=>"v", 53=>"w", 54=>"x", 55=>"y",
                61=>"z", 62=>" "];

        foreach (str_split($text) as $letter) {
            $encrypted .= array_search($letter, $alphabet) . " ";
        }
    } else {
        $alphabet = ['n'=>"a", 'o'=>"b", 'p'=>"c", 'q'=>"d", 'r'=>"e",
                's'=>"f", 't'=>"g", 'u'=>"h", 'v'=>"i", 'w'=>"j",
                'x'=>"k", 'y'=>"l", 'z'=>"m", 'a'=>"n", 'b'=>"o",
                'c'=>"p", 'd'=>"q", 'e'=>"r", 'f'=>"s", 'g'=>"t",
                'h'=>"u", 'i'=>"v", 'j'=>"w", 'k'=>"x", 'l'=>"y",
                'm'=>"z", ' '=>" "];

        foreach (str_split($text) as $letter) {
            $encrypted .= array_search($letter, $alphabet);
        }
    }
    return $encrypted;
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
                    <button class="rounded-full bg-lime-500 py-1 px-3"> <a href="../html/index.html">Home</a></button>
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

