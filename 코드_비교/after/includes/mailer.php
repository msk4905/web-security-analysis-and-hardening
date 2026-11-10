<?php

require_once __DIR__ . "/PHPMailer/Exception.php";
require_once __DIR__ . "/PHPMailer/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;

function createMailer()
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = '192.168.30.112'; // 실습용 메일 서버
    $mail->Port       = 25;
    $mail->SMTPAuth   = false;            // 인증 없음
    $mail->SMTPSecure = 'tls';            // 암호화 tls
    $mail->SMTPAutoTLS = true;

    $mail->SMTPOptions = [
        'ssl' => [
        'verify_peer'       => true,
        'verify_peer_name'  => true,
        'allow_self_signed' => false,
        'cafile'            => '/etc/pki/tls/certs/aleph-mail-ca.crt',
        'peer_name'         => '192.168.30.112',
        ],
    ];

    $mail->Timeout     = 5;               // 연결이 응답 없을 때 5초 만에 포기 (방화벽 등으로 막혀있으면 화면이 무한 로딩되는 것 방지)

    $mail->CharSet = 'UTF-8';

    return $mail;
}
