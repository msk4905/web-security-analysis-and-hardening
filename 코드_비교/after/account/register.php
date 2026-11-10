<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

if (isLoggedIn()) {
    header("Location: /ALEPH-A-Class/");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (
        $username === "" ||
        $password === "" ||
        $password_confirm === "" ||
        $name === ""
    ) {
        header("Location: register.php?error=empty");
        exit;
    }

    if ($password !== $password_confirm) {
        header("Location: register.php?error=password");
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        header("Location: register.php?error=duplicate");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare(
        "INSERT INTO users (username, password, name, email, role) VALUES (?, ?, ?, ?, 'student')"
    );
    $stmt->bind_param("ssss", $username, $hashed, $name, $email);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    }

    die("회원가입 처리 중 오류가 발생했습니다.");
}

$error = isset($_GET['error']) ? $_GET['error'] : "";

?>

<?php require_once "../includes/header.php"; ?>

<section class="section">
    <div class="container">

        <div class="section-head">
            <div>
                <span class="eyebrow">ALEPH A CLASS</span>
                <h2>회원가입</h2>
                <p>새로운 계정을 생성하세요.</p>
            </div>
        </div>

        <div style="max-width:520px;margin:0 auto;">

            <?php if ($error === "duplicate"): ?>

                <div class="card"
                     style="margin-bottom:20px;border-color:#ff5c5c;">
                    이미 사용 중인 아이디입니다.
                </div>

            <?php elseif ($error === "empty"): ?>

                <div class="card"
                     style="margin-bottom:20px;border-color:#ff5c5c;">
                    모든 항목을 입력해주세요.
                </div>

            <?php elseif ($error === "password"): ?>

                <div class="card"
                     style="margin-bottom:20px;border-color:#ff5c5c;">
                    비밀번호가 일치하지 않습니다.
                </div>

            <?php endif; ?>

            <div class="card">

                <form action="register.php" method="post">

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

                    <div class="field">
                        <label for="password_confirm">
                            PASSWORD 확인
                        </label>
                        <input
                            id="password_confirm"
                            name="password_confirm"
                            type="password"
                            placeholder="비밀번호를 다시 입력하세요"
                            required
                        >
                    </div>

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
                        >
                    </div>

                    <button
                        class="btn btn-primary full"
                        type="submit"
                    >
                        회원가입
                    </button>

                </form>

                <div class="login-links">
                    <a href="login.php">로그인</a>
                </div>

            </div>
        </div>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>
