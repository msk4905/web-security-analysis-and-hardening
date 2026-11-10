<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireLogin();

$board = isset($_POST['board'])
    ? $_POST['board']
    : 'free';

$action = isset($_POST['action'])
    ? $_POST['action']
    : 'open';


/* 게시판 종류 확인 */

if (
    $board !== 'free'
    && $board !== 'notice'
    && $board !== 'assignment'
) {
    die("잘못된 게시판입니다.");
}


/* 공지사항은 관리자만 작성 */

if (
    $board === 'notice'
    && !isAdmin()
) {
    http_response_code(403);
    die("관리자 권한이 필요합니다.");
}


/* 게시판 이름 */

$board_name = "자유게시판";

if ($board === 'notice') {

    $board_name = "공지사항";

} elseif ($board === 'assignment') {

    $board_name = "과제";
}


$error = "";


/*
 * 게시글 등록
 */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && $action === 'submit'
) {

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


        /*
         * 기존 게시글 등록 SQL
         *
         * 보안 실습용 웹사이트이므로
         * 기존 SQL 구조는 그대로 유지합니다.
         */

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
                '$board',
                '$title',
                '$content'
            )
        ";


        if ($conn->query($sql)) {

            $post_id = $conn->insert_id;


            /*
             * =========================================
             * 과제게시판 첨부파일 업로드
             * =========================================
             */

            if (
                $board === 'assignment'
                && isset($_FILES['attachment'])
                && $_FILES['attachment']['error'] === UPLOAD_ERR_OK
            ) {

                /*
                 * 실제 저장 폴더
                 */

                $upload_dir =
                    __DIR__
                    . "/../uploads/assignments/";


                /*
                 * 폴더가 없다면 생성
                 */

                if (!is_dir($upload_dir)) {

                    mkdir(
                        $upload_dir,
                        0755,
                        true
                    );
                }


                /*
                 * 사용자가 업로드한 원래 파일명
                 */

                $original_filename =
                    basename(
                        $_FILES['attachment']['name']
                    );


                /*
                 * 서버 저장 파일명
                 *
                 * 게시글번호_원래파일명
                 *
                 * 예:
                 * 35_report.pdf
                 */

                $stored_filename =
                    $post_id
                    . "_"
                    . $original_filename;


                /*
                 * 최종 저장 경로
                 */

                $destination =
                    $upload_dir
                    . $stored_filename;


                /*
                 * 임시 파일을 uploads 폴더로 이동
                 */

                if (
                    move_uploaded_file(
                        $_FILES['attachment']['tmp_name'],
                        $destination
                    )
                ) {

                    /*
                     * DB에 파일명 기록
                     */

                    $file_sql = "
                        UPDATE posts
                        SET
                            original_filename = '$original_filename',
                            stored_filename = '$stored_filename'
                        WHERE id = $post_id
                    ";

                    $conn->query($file_sql);
                }
            }


            /*
             * 등록된 게시글 상세 페이지로 이동
             */

            echo '
            <!DOCTYPE html>

            <html lang="ko">

            <head>

                <meta charset="UTF-8">

                <title>
                    게시글 등록
                </title>

            </head>

            <body>

                <form
                    id="viewForm"
                    method="post"
                    action="view.php"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="' . $post_id . '"
                    >

                </form>

                <script>

                    document
                        .getElementById("viewForm")
                        .submit();

                </script>

            </body>

            </html>
            ';

            exit;
        }


        $error =
            "게시글 등록에 실패했습니다.";
    }
}

?>


<?php
require_once "../includes/header.php";
?>


<section class="section">

    <div class="container">


        <div class="section-head">

            <div>

                <span class="eyebrow">
                    WRITE
                </span>

                <h2>

                    <?php
                    echo htmlspecialchars(
                        $board_name
                    );
                    ?>

                </h2>

                <p>
                    새로운 게시글을 작성합니다.
                </p>

            </div>

        </div>


        <div class="panel">


            <?php if ($error !== ''): ?>

                <div
                    style="
                        padding:14px;
                        margin-bottom:20px;
                        border-radius:12px;
                        background:#fff1f1;
                        color:var(--danger);
                    "
                >

                    <?php
                    echo htmlspecialchars(
                        $error
                    );
                    ?>

                </div>

            <?php endif; ?>


            <!--
                파일 전송을 위해
                enctype="multipart/form-data" 필요
            -->

            <form
                method="post"
                enctype="multipart/form-data"
            >


                <input
                    type="hidden"
                    name="board"
                    value="<?php
                        echo htmlspecialchars(
                            $board
                        );
                    ?>"
                >


                <input
                    type="hidden"
                    name="action"
                    value="submit"
                >


                <!-- 제목 -->

                <div class="field">

                    <label for="title">
                        제목
                    </label>

                    <input
                        id="title"
                        name="title"
                        type="text"

                        value="<?php

                            echo isset(
                                $_POST['title']
                            )
                                ? htmlspecialchars(
                                    $_POST['title']
                                )
                                : '';

                        ?>"

                        placeholder="제목을 입력하세요"
                        required
                    >

                </div>


                <!-- 내용 -->

                <div class="field">

                    <label for="content">
                        내용
                    </label>

                    <textarea
                        id="content"
                        name="content"
                        rows="14"
                        placeholder="내용을 입력하세요"
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

                        echo isset(
                            $_POST['content']
                        )
                            ? htmlspecialchars(
                                $_POST['content']
                            )
                            : '';

                    ?></textarea>

                </div>


                <!--
                    ====================================
                    과제게시판에서만 첨부파일 표시
                    ====================================
                -->

                <?php
                if ($board === 'assignment'):
                ?>

                    <div class="field">

                        <label for="attachment">
                            첨부파일
                        </label>

                        <input
                            id="attachment"
                            name="attachment"
                            type="file"
                        >

                        <p
                            style="
                                margin-top:8px;
                                color:var(--muted);
                                font-size:13px;
                            "
                        >
                            과제 파일을 첨부할 수 있습니다.
                        </p>

                    </div>

                <?php endif; ?>


                <!-- 버튼 -->

                <div
                    style="
                        display:flex;
                        justify-content:space-between;
                        gap:10px;
                        margin-top:20px;
                    "
                >

                    <button
                        class="btn btn-outline"
                        type="button"
                        onclick="
                            document
                                .getElementById('cancelForm')
                                .submit();
                        "
                    >
                        취소
                    </button>


                    <button
                        class="btn btn-primary"
                        type="submit"
                    >
                        게시글 등록
                    </button>

                </div>


            </form>


            <!-- 취소 -->

            <form
                id="cancelForm"
                method="post"
                action="<?php echo $board; ?>.php"
                style="display:none;"
            >
            </form>


        </div>

    </div>

</section>


<?php
require_once "../includes/footer.php";
?>
