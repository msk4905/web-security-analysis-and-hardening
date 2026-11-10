<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

if (isLoggedIn()) {
    header("Location: /ALEPH-A-Class/");
    exit;
}

$error = isset($_GET['error']);
?>

<?php require_once "../includes/header.php"; ?>

<section class="section">
    <div class="container">

        <div class="section-head">
            <div>
                <span class="eyebrow">ALEPH A CLASS</span>
                <h2>로그인</h2>
                <p>Cyber Security Class Portal에 로그인하세요.</p>
            </div>
        </div>

        <div style="max-width:520px;margin:0 auto;">

            <?php if ($error): ?>
                <div class="card"
                     style="margin-bottom:20px;border-color:#ff5c5c;">
                    아이디 또는 비밀번호가 올바르지 않습니다.
                </div>
            <?php endif; ?>

            <div class="card">

                <form action="login_process.php" method="post">

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
                        <label for="password">PASSWORD</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="비밀번호를 입력하세요"
                            required
                        >
                    </div>

                    <button
                        class="btn btn-primary full"
                        type="submit"
                    >
                        로그인
                    </button>

                </form>

                <div class="login-links">
                    <a href="find_id.php">ID 찾기</a>
                    <span>·</span>
                    <a href="reset_password.php">PW 재설정</a>
                    <span>·</span>
                    <a href="register.php">회원가입</a>
                </div>

            </div>
        </div>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>
