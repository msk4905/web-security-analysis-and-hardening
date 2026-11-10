<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

if (isLoggedIn()) {
    header("Location: /ALEPH-A-Class/");
    exit;
}

$error = isset($_GET['error']);
$requireCaptcha = isset($_GET['captcha']) && $_GET['captcha'] === '1';
$prefillUsername = isset($_GET['username']) ? $_GET['username'] : '';
$resetSuccess = isset($_GET['reset']) && $_GET['reset'] === 'success';

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
        
<?php if ($resetSuccess): ?>
    <div class="card"
         style="margin-bottom:20px;border-color:#2ecc71;">
        비밀번호가 변경되었습니다. 새 비밀번호로 로그인해주세요.
    </div>
    
<?php endif; ?>
            <?php if ($error): ?>
                <div class="card"
                     style="margin-bottom:20px;border-color:#ff5c5c;">
                    <?php echo $requireCaptcha ? "캡차를 완료해야 로그인할 수 있습니다." : "아이디 또는 비밀번호가 올바르지 않습니다."; ?>
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
                            value="<?php echo htmlspecialchars($prefillUsername); ?>"
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
                    <?php if ($requireCaptcha): ?>
                        <div class="field">
                            <div class="g-recaptcha" data-sitekey="6LcTi9gtAAAAAC0P8Gc52VK0_9KaFA_6h-iSyQSh"></div>
                        </div>
                        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                    <?php endif; ?>                    

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
