<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireLogin();

$id = isset($_POST['id'])
    ? $_POST['id']
    : 0;

$sql = "
    SELECT
        posts.*,
        users.name
    FROM posts
    JOIN users
        ON posts.user_id = users.id
    WHERE posts.id = $id
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("게시글을 찾을 수 없습니다.");
}

$post = $result->fetch_assoc();

if (
    $post['board_type'] === 'notice'
    && !isAdmin()
) {
    http_response_code(403);
    die("관리자 권한이 필요합니다.");
}

$error = "";

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['title'])
    && isset($_POST['content'])
) {

    $title = $_POST['title'];
    $content = $_POST['content'];

    if ($title === '' || $content === '') {

        $error = "제목과 내용을 입력해주세요.";

    } else {

        $sql = "
            UPDATE posts
            SET
                title = '$title',
                content = '$content'
            WHERE id = $id
        ";

        if ($conn->query($sql)) {

            echo '
            <!DOCTYPE html>
            <html lang="ko">
            <head>
                <meta charset="UTF-8">
                <title>게시글 수정</title>
            </head>
            <body>
                <form id="viewForm" method="post" action="view.php">
                    <input type="hidden" name="id" value="' . $id . '">
                </form>

                <script>
                    document.getElementById("viewForm").submit();
                </script>
            </body>
            </html>
            ';

            exit;

        } else {

            $error = "게시글 수정에 실패했습니다.";
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
                    EDIT
                </span>

                <h2>
                    게시글 수정
                </h2>

                <p>
                    게시글 내용을 수정합니다.
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

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $post['id']; ?>"
                >

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
                                : htmlspecialchars($post['title']);
                        ?>"
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
                            : htmlspecialchars($post['content']);
                    ?></textarea>

                </div>

                <div style="
                    display:flex;
                    justify-content:space-between;
                    gap:10px;
                    margin-top:20px;
                ">

                    <button
                        class="btn btn-outline"
                        type="button"
                        onclick="
                            document.getElementById('cancelForm').submit();
                        "
                    >
                        취소
                    </button>

                    <button
                        class="btn btn-primary"
                        type="submit"
                    >
                        수정 완료
                    </button>

                </div>

            </form>

            <form
                id="cancelForm"
                method="post"
                action="view.php"
                style="display:none;"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $post['id']; ?>"
                >

            </form>

        </div>

    </div>

</section>

<?php require_once "../includes/footer.php"; ?>
