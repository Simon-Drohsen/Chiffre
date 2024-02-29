<?php
    error_reporting(0);
    session_start();
    session_unset();
    $_SESSION['chosen'] = $_POST['choose'];

    switch ($_SESSION['chosen']) {
        case 'caesar':
            header('location: ../../build/view/caesar.html');
            break;
        case 'aes256':
            header('location: ../../build/view/aes256.html');
            break;
        case 'md5':
            header('location: ../../build/view/md5.html');
            break;
        case 'sha1':
            header('location: ../../build/view/sha1.html');
            break;
        case 'polybios':
            header('location: ../../build/view/polybios.html');
            break;
        case 'rot13':
            header('location: ../../build/view/rot13.html');
            break;
        default:
            header('location: ../../build/view/404.php');
            break;
    }
