<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

if (isLoggedIn()) {
    header("Location: /ALEPH-A-Class/");
    exit;
}

$result_id = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT username FROM users WHERE name = ? AND email = ?");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $result_id = $user['username'];
    }
}
?>

<?php require_once "../includes/header.php"; ?>

<section class="section">
    <div class="container">

        <div class="section-head">
            <div>
                <span class="eyebrow">ACCOUNT</span>
                <h2>ID 찾기</h2>
                <p>이름과 이메일을 입력하세요.</p>
            </div>
        </div>

        <div style="max-width:520px;margin:0 auto;">

            <div class="card">

                <form method="post">

                    <div class="field">
                        <label for="name">이름</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            placeholder="이름을 입력하세요"
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
                        ID 찾기
                    </button>

                </form>

                <?php if ($result_id !== ""): ?>

                    <div class="card"
                         style="margin-top:20px;">
                        회원님의 ID는
                        <strong>
                            <?php echo htmlspecialchars($result_id); ?>
                        </strong>
                        입니다.
                    </div>

                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>

                    <div class="card"
                         style="margin-top:20px;">
                        일치하는 회원 정보를 찾을 수 없습니다.
                    </div>

                <?php endif; ?>

                <div class="login-links">
                    <a href="login.php">로그인</a>
                    <span>·</span>
                    <a href="reset_password.php">PW 재설정</a>
                </div>

            </div>

        </div>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>
