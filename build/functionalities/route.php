<?php
session_start();
session_unset();
$_SESSION['chosen'] = $_POST['choose'];

switch ($_SESSION['chosen']) {
    case 'caesar':
        header('location: ../../build/html/caesar.html');
        break;
    case 'aes256':
        header('location: ../../build/html/aes256.html');
        break;
    case 'md5':
        header('location: ../../build/html/md5.html');
        break;
    case 'sha1':
        header('location: ../../build/html/sha1.html');
        break;
    case 'polybios':
        header('location: ../../build/html/polybios.html');
        break;
    case 'rot13':
        header('location: ../../build/html/rot13.html');
        break;
    default:
        echo "Please choose a cypher";
        break;
}
