<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireLogin();

$id = isset($_GET['id'])
    ? $_GET['id']
    : $_SESSION['user_id'];

$message = "";
$error = "";

$sql = "
    SELECT *
    FROM users
    WHERE id = $id
";

$result = $conn->query($sql);

if (!$result || $result->num_rows !== 1) {
    die("회원 정보를 찾을 수 없습니다.");
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($password !== "") {

        $sql = "
            UPDATE users
            SET
                name = '$name',
                email = '$email',
                password = '$password'
            WHERE id = $id
        ";

    } else {

        $sql = "
            UPDATE users
            SET
                name = '$name',
                email = '$email'
            WHERE id = $id
        ";
    }

    if ($conn->query($sql)) {

        $message = "회원 정보가 수정되었습니다.";

        $result = $conn->query("
            SELECT *
            FROM users
            WHERE id = $id
        ");

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
        }

        if ($id == $_SESSION['user_id']) {
            $_SESSION['name'] = $user['name'];
        }

    } else {

        $error = "회원 정보 수정에 실패했습니다.";
    }
}

?>

<?php require_once "../includes/header.php"; ?>

<section class="section">
    <div class="container">

        <div class="section-head">
            <div>
                <span class="eyebrow">ACCOUNT</span>
                <h2>회원 정보 수정</h2>
                <p>회원 정보를 수정하세요.</p>
            </div>
        </div>

        <div style="max-width:520px;margin:0 auto;">

            <?php if ($message !== ""): ?>

                <div class="card"
                     style="margin-bottom:20px;">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>

            <?php if ($error !== ""): ?>

                <div class="card"
                     style="margin-bottom:20px;border-color:#ff5c5c;">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <div class="card">

                <form method="post"
                      action="edit.php?id=<?php echo $id; ?>">

                    <div class="field">
                        <label for="username">ID</label>
                        <input
                            id="username"
                            type="text"
                            value="<?php echo htmlspecialchars($user['username']); ?>"
                            disabled
                        >
                    </div>

                    <div class="field">
                        <label for="name">이름</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="<?php echo htmlspecialchars($user['name']); ?>"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="email">EMAIL</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                        >
                    </div>

                    <div class="field">
                        <label for="password">
                            새로운 PASSWORD
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="변경하지 않으려면 비워두세요"
                        >
                    </div>

                    <button
                        class="btn btn-primary full"
                        type="submit"
                    >
                        정보 수정
                    </button>

                </form>

                <div class="login-links">
                    <a href="/ALEPH-A-Class/mypage/profile.php">
                        프로필
                    </a>
                    <span>·</span>
                    <a href="/ALEPH-A-Class/account/delete_account.php">
                        회원 탈퇴
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>

<?php require_once "../includes/footer.php"; ?>
