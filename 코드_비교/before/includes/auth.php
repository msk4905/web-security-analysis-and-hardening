<?php

session_start();

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role'])
        && $_SESSION['role'] === 'admin';
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: /ALEPH-A-Class/account/login.php");
        exit;
    }
}

function requireAdmin()
{
    if (!isLoggedIn() || !isAdmin()) {
        http_response_code(403);
        die("관리자 권한이 필요합니다.");
    }
}
?>
