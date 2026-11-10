<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";
require_once "../includes/mailer.php";

if (isLoggedIn()) {
    header("Location: /ALEPH-A-Class/");
    exit;
}

$message = "";
$error = "";

// 세션에 발급받은 아이디/이메일이 남아있으면 2단계, 없으면 1단계
$stage = isset($_SESSION['reset_username']) ? 2 : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restart'])) {
    unset($_SESSION['reset_username'], $_SESSION['reset_email']);
    $stage = 1;
}

// 1단계: 인증 코드 발급 + 이메일 발송
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $stage === 1 && isset($_POST['username'], $_POST['email'])) {

    $username = $_POST['username'];
    $email = $_POST['email'];

    $code = random_int(100000, 999999);

    // 만료시간을 PHP의 date()가 아니라 MySQL의 NOW()로 계산합니다.
    // 저장(이 UPDATE)과 검증(SELECT ... reset_expires > NOW())이 둘 다 MySQL 서버 시간 하나만 기준으로 삼게 되어,
    // PHP와 MySQL의 시간대가 서로 달라도(예: PHP는 UTC, MySQL은 KST) 어긋날 일이 없습니다.
    $stmt = $conn->prepare(
        "UPDATE users SET reset_code = ?, reset_expires = NOW() + INTERVAL 1 MINUTE WHERE username = ? AND email = ?" // 테스트용 1분, 10분 정도로 설정하길 권장함.
    );
    $stmt->bind_param("sss", $code, $username, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {

        try {
            $mail = createMailer();
            $mail->setFrom('noreply@aleph-a-class.local', 'ALEPH-A-Class');
            $mail->addAddress($email);
            $mail->Subject = '비밀번호 재설정 인증코드';
            $mail->Body = "인증코드: $code (1분 이내 유효)";
            $mail->send();

            $message = "입력하신 이메일로 인증코드를 보냈습니다.";

        } catch (Exception $e) {
            // 메일 서버 연결 문제로 발송이 안 되더라도 학습은 이어갈 수 있게, 코드를 화면에 표시
            $message = "메일 발송에 실패했습니다. (테스트용) 인증코드: $code";
        }

        $_SESSION['reset_username'] = $username;
        $_SESSION['reset_email'] = $email;
        $stage = 2;

    } else {
        // 이름/이메일이 실제로 일치했는지는 화면에서 구분해 보여주지 않습니다
        // (구분하면 공격자가 존재하는 계정을 하나씩 확인할 수 있게 됩니다)
        $error = "회원 정보를 확인해주세요.";
    }
}

// 2단계: 인증 코드 확인 후 비밀번호 변경
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $stage === 2 && isset($_POST['code'], $_POST['new_password'])) {

    $username = $_SESSION['reset_username'];
    $email = $_SESSION['reset_email'];
    $inputCode = $_POST['code'];
    $new_password = $_POST['new_password'];

    $stmt = $conn->prepare(
        "SELECT id FROM users WHERE username = ? AND email = ? AND reset_code = ? AND reset_expires > NOW()"
    );
    $stmt->bind_param("sss", $username, $email, $inputCode);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 1) {

        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $update = $conn->prepare(
            "UPDATE users SET password = ?, reset_code = NULL, reset_expires = NULL WHERE username = ?"
        );
        $update->bind_param("ss", $hashed, $username);
        $update->execute();

        unset($_SESSION['reset_username'], $_SESSION['reset_email']);

        header("Location: login.php?reset=success");
        exit;

    } else {
        $error = "인증코드가 올바르지 않거나 만료되었습니다.";
    }
}

?>

<?php require_once "../includes/header.php"; ?>

<section class="section">
    <div class="container">

        <div class="section-head">
            <div>
                <span class="eyebrow">ACCOUNT</span>
                <h2>비밀번호 재설정</h2>
                <p>
                    <?php echo $stage === 1
                        ? "회원 정보를 입력하면 이메일로 인증코드를 보내드립니다."
                        : "받으신 인증코드와 새 비밀번호를 입력하세요.";
                    ?>
                </p>
            </div>
        </div>

        <div style="max-width:520px;margin:0 auto;">

            <?php if ($message !== ""): ?>
                <div class="card" style="margin-bottom:20px;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ""): ?>
                <div class="card" style="margin-bottom:20px;border-color:#ff5c5c;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="card">

                <?php if ($stage === 1): ?>

                    <form method="post">

                        <div class="field">
                            <label for="username">ID</label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                placeholder="아이디를 입력하세요"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="email">EMAIL</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="이메일을 입력하세요"
                                required
                            >
                        </div>

                        <button
                            class="btn btn-primary full"
                            type="submit"
                        >
                            인증코드 받기
                        </button>

                    </form>

                <?php else: ?>

                    <form method="post">

                        <div class="field">
                            <label for="code">인증코드</label>
                            <input
                                id="code"
                                name="code"
                                type="text"
                                placeholder="이메일로 받은 6자리 코드"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="new_password">
                                새로운 PASSWORD
                            </label>
                            <input
                                id="new_password"
                                name="new_password"
                                type="password"
                                placeholder="새 비밀번호를 입력하세요"
                                required
                            >
                        </div>

                        <button
                            class="btn btn-primary full"
                            type="submit"
                        >
                            비밀번호 변경
                        </button>

                    </form>

                    <form method="post" style="margin-top:10px;">
                        <input type="hidden" name="restart" value="1">
                        <button
                            class="btn btn-outline full"
                            type="submit"
                        >
                            처음부터 다시
                        </button>
                    </form>

                <?php endif; ?>

                <div class="login-links">
                    <a href="login.php">로그인</a>
                    <span>·</span>
                    <a href="find_id.php">ID 찾기</a>
                </div>

            </div>

        </div>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>
