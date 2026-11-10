<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

if (isLoggedIn()) {
    header("Location: /ALEPH-A-Class/");
    exit;
}

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    $sql = "
        SELECT id
        FROM users
        WHERE username = '$username'
        AND email = '$email'
    ";

    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {

        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        $update_sql = "
            UPDATE users
            SET password = '$new_password'
            WHERE id = $user_id
        ";

        if ($conn->query($update_sql)) {
            $message = "비밀번호가 변경되었습니다.";
        } else {
            $error = "비밀번호 변경에 실패했습니다.";
        }

    } else {
        $error = "회원 정보를 찾을 수 없습니다.";
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
                <p>회원 정보를 입력하고 새로운 비밀번호를 설정하세요.</p>
            </div>
        </div>

        <div style="max-width:520px;margin:0 auto;">

            <?php if ($message !== ""): ?>

                <div class="card"
                     style="margin-bottom:20px;">
                    <?php echo $message; ?>
                </div>

            <?php endif; ?>

            <?php if ($error !== ""): ?>

                <div class="card"
                     style="margin-bottom:20px;border-color:#ff5c5c;">
                    <?php echo $error; ?>
                </div>

            <?php endif; ?>

            <div class="card">

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
