<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireLogin();

$id = $_SESSION['user_id'];

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        die("잘못된 요청입니다.");
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id); // "i" = 정수. 숫자가 아닌 값은 바인딩 단계에서 걸러짐

    if ($stmt->execute()) {

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
                      <input 
                          type="hidden" 
                          name="csrf_token" 
                          value="<?php echo $_SESSION['csrf_token']; ?>"
                      >
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
