<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireLogin();

$id = isset($_GET['id'])
    ? $_GET['id']
    : $_SESSION['user_id'];

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sql = "
        DELETE FROM users
        WHERE id = $id
    ";

    if ($conn->query($sql)) {

        if ($id == $_SESSION['user_id']) {

            session_destroy();

            header("Location: /ALEPH-A-Class/");
            exit;
        }

        header("Location: /ALEPH-A-Class/");
        exit;

    } else {

        $error = "회원 탈퇴 처리에 실패했습니다.";
    }
}

?>

<?php require_once "../includes/header.php"; ?>

<section class="section">
    <div class="container">

        <div class="section-head">
            <div>
                <span class="eyebrow">ACCOUNT</span>
                <h2>회원 탈퇴</h2>
                <p>회원 탈퇴를 진행합니다.</p>
            </div>
        </div>

        <div style="max-width:520px;margin:0 auto;">

            <?php if ($error !== ""): ?>

                <div class="card"
                     style="margin-bottom:20px;border-color:#ff5c5c;">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <div class="card">

                <div style="margin-bottom:24px;">
                    <h3 style="margin-bottom:10px;">
                        정말 탈퇴하시겠습니까?
                    </h3>

                    <p>
                        회원 탈퇴 시 작성한 게시글,
                        출석 정보 및 과제 정보도 삭제될 수 있습니다.
                    </p>
                </div>

                <form method="post"
                      action="delete_account.php?id=<?php echo $id; ?>">

                    <button
                        class="btn btn-primary full"
                        type="submit"
                    >
                        회원 탈퇴
                    </button>

                </form>

                <div class="login-links">
                    <a href="/ALEPH-A-Class/mypage/profile.php">
                        취소
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>

<?php require_once "../includes/footer.php"; ?>
