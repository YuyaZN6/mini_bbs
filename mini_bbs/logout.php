<?php
session_start();

$_SESSION = array();
if (ini_set('session.use_cookie')) {
    $parmas = session_get_cookie_parmas();
    setcookie(session_name() . '', time() - 42000, $parmas['path'], $parmas['domain'], $parmas['secure'], $parmas['httponly']);
}

session_destroy();

setcookie('email', '', time() - 3600);

header('Location: login.php');
exit();
