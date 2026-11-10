<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$sql = " SELECT * FROM users WHERE username = '$username' AND password = '$password' ";

$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    header("Location: /ALEPH-A-Class/");
    exit;
}

header("Location: login.php?error=1");
exit;
