<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireAdmin();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = isset($_POST['title'])
        ? $_POST['title']
        : '';

    $content = isset($_POST['content'])
        ? $_POST['content']
        : '';

    if ($title === '' || $content === '') {

        $error = "제목과 내용을 입력해주세요.";

    } else {

        $user_id = $_SESSION['user_id'];

        $sql = "
            INSERT INTO posts
            (
                user_id,
                board_type,
                title,
                content
            )
            VALUES
            (
                $user_id,
                'notice',
                '$title',
                '$content'
            )
        ";

        if ($conn->query($sql)) {

            $post_id = $conn->insert_id;

            // 방금 작성한 게시글의 상세 페이지로 이동
            header(
                "Location: ../board/view.php?id=" . $post_id
            );

            exit;

        } else {

            $error = "공지사항 등록에 실패했습니다.";
        }
    }
}

?>

<?php require_once "../includes/header.php"; ?>

<section class="section">
    <div class="container">

        <div class="section-head">

            <div>

                <span class="eyebrow">
                    NOTICE WRITE
                </span>

                <h2>
                    수업 공지 작성
                </h2>

                <p>
                    관리자 전용 공지사항을 작성합니다.
                </p>

            </div>

        </div>

        <div class="panel">

            <?php if ($error !== ''): ?>

                <div style="
                    padding:14px;
                    margin-bottom:20px;
                    border-radius:12px;
                    background:#fff1f1;
                    color:var(--danger);
                ">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <form method="post">

                <div class="field">

                    <label for="title">
                        제목
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="<?php
                            echo isset($_POST['title'])
                                ? htmlspecialchars($_POST['title'])
                                : '';
                        ?>"
                        placeholder="공지사항 제목을 입력하세요"
                        required
                    >

                </div>

                <div class="field">

                    <label for="content">
                        내용
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        rows="14"
                        placeholder="공지사항 내용을 입력하세요"
                        required
                        style="
                            width:100%;
                            padding:14px 16px;
                            border:1px solid var(--line);
                            border-radius:12px;
                            font:inherit;
                            resize:vertical;
                        "
                    ><?php
                        echo isset($_POST['content'])
                            ? htmlspecialchars($_POST['content'])
                            : '';
                    ?></textarea>

                </div>

                <div style="
                    display:flex;
                    justify-content:space-between;
                    gap:10px;
                    margin-top:20px;
                ">

                    <a
                        class="btn btn-outline"
                        href="../board/notice.php"
                    >
                        취소
                    </a>

                    <button
                        class="btn btn-primary"
                        type="submit"
                    >
                        공지 등록
                    </button>

                </div>

            </form>

        </div>

    </div>
</section>

<?php require_once "../includes/footer.php"; ?>
