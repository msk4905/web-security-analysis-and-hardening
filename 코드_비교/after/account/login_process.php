<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

define('RECAPTCHA_SECRET_KEY', 'YOUR_RECAPTCHA_SECRET_KEY');
$stmt = $conn->prepare("SELECT failed_attempts FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$attemptRow = $stmt->get_result()->fetch_assoc();

$requireCaptcha = $attemptRow && $attemptRow['failed_attempts'] >= 5;

if ($requireCaptcha) {
    $captchaToken = $_POST['g-recaptcha-response'] ?? '';

    $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'secret'   => RECAPTCHA_SECRET_KEY,
        'response' => $captchaToken,
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

    $response = curl_exec($ch);
    $curlFailed = curl_errno($ch) !== 0;
    curl_close($ch);

    $verify = $curlFailed ? null : json_decode($response);

    if (!$verify || !$verify->success) {
        header("Location: login.php?error=1&captcha=1&username=" . urlencode($username));
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
session_regenerate_id(true);

if ($user && password_verify($password, $user['password'])) {

    $reset = $conn->prepare("UPDATE users SET failed_attempts = 0 WHERE username = ?");
    $reset->bind_param("s", $username);
    $reset->execute();
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    header("Location: /ALEPH-A-Class/");
    exit;
}

if ($attemptRow) {
    $fail = $conn->prepare("UPDATE users SET failed_attempts = failed_attempts + 1 WHERE username = ?");
    $fail->bind_param("s", $username);
    $fail->execute();
}

$nextRequiresCaptcha = $attemptRow && ($attemptRow['failed_attempts'] + 1) >= 5;

header("Location: login.php?error=1");
exit;
