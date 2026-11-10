<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";


$id = isset($_POST['id'])
    ? $_POST['id']
    : 0;


/*
 * 조회수 증가
 */

$sql = "
    UPDATE posts
    SET views = views + 1
    WHERE id = $id
";

$conn->query($sql);


/*
 * 게시글 조회
 */

$sql = "
    SELECT
        posts.*,
        users.username,
        users.name
    FROM posts
    JOIN users
        ON posts.user_id = users.id
    WHERE posts.id = $id
";


$result =
    $conn->query($sql);


if (
    !$result
    || $result->num_rows === 0
) {

    die(
        "게시글을 찾을 수 없습니다."
    );
}


$post =
    $result->fetch_assoc();


/*
 * 게시판 이름
 */

$board_name =
    "자유게시판";


if (
    $post['board_type']
    === 'notice'
) {

    $board_name =
        "공지사항";

} elseif (
    $post['board_type']
    === 'assignment'
) {

    $board_name =
        "과제";
}


/*
 * 수정/삭제 권한
 */

$can_manage = false;


if (isAdmin()) {

    $can_manage = true;

} elseif (
    isLoggedIn()
    && $_SESSION['user_id']
        == $post['user_id']
) {

    $can_manage = true;
}


/*
 * 목록으로 돌아가기
 */

$board_link =
    "free.php";


if (
    $post['board_type']
    === 'notice'
) {

    $board_link =
        "notice.php";

} elseif (
    $post['board_type']
    === 'assignment'
) {

    $board_link =
        "assignment.php";
}

?>


<?php
require_once "../includes/header.php";
?>


<section class="board-page-section">

    <div class="container">


        <div class="board-page-head">

            <div>

                <span class="eyebrow">

                    <?php
                    echo strtoupper(
                        $post['board_type']
                    );
                    ?>

                </span>


                <h2>

                    <?php
                    echo $board_name;
                    ?>

                </h2>


                <p>
                    게시글 내용을 확인합니다.
                </p>

            </div>

        </div>


        <div class="board-view-panel">


            <!-- 게시글 제목 -->

            <div class="board-view-head">

                <span class="badge">

                    <?php
                    echo $board_name;
                    ?>

                </span>


                <h3>

                    <?php
                    echo $post['title'];
                    ?>

                </h3>


                <div class="board-view-meta">


                    <?php
                    echo $post['name'];
                    ?>


                    ·


                    <?php

                    echo date(
                        'm.d H:i',
                        strtotime(
                            $post['created_at']
                        )
                    );

                    ?>


                    · 조회

                    <?php
                    echo $post['views'];
                    ?>


                    <?php

                    if (
                        $post['updated_at']
                        !==
                        $post['created_at']
                    ):

                    ?>

                        · 수정됨

                    <?php endif; ?>


                </div>

            </div>


            <!-- 게시글 내용 -->

            <div class="board-view-content">

                <?php

                echo nl2br(
                    $post['content']
                );

                ?>

            </div>


            <!--
                ==================================
                과제 첨부파일
                ==================================
            -->

            <?php

            if (
                $post['board_type']
                    === 'assignment'
                &&
                !empty(
                    $post['stored_filename']
                )
            ):

            ?>


                <div
                    style="
                        margin-top:24px;
                        padding:16px;
                        border:1px solid var(--line);
                        border-radius:12px;
                    "
                >


                    <strong>
                        첨부파일
                    </strong>


                    <div
                        style="
                            margin-top:8px;
                        "
                    >


                        <a
                            href="../uploads/assignments/<?php

                                echo rawurlencode(
                                    $post[
                                        'stored_filename'
                                    ]
                                );

                            ?>"
                            download
                        >

                            <?php

                            echo htmlspecialchars(
                                $post[
                                    'original_filename'
                                ]
                            );

                            ?>

                        </a>


                    </div>


                </div>


            <?php endif; ?>


            <!-- 하단 버튼 -->

            <div class="board-view-actions">


                <form
                    class="board-back-form"
                    method="post"
                    action="<?php
                        echo $board_link;
                    ?>"
                >

                    <button
                        class="btn btn-outline"
                        type="submit"
                    >
                        ← 목록으로
                    </button>

                </form>


                <?php
                if ($can_manage):
                ?>


                    <div class="board-view-manage">


                        <!-- 수정 -->

                        <form
                            class="board-action-form"
                            method="post"
                            action="edit.php"
                        >

                            <input
                                type="hidden"
                                name="id"
                                value="<?php
                                    echo $post['id'];
                                ?>"
                            >


                            <button
                                class="btn btn-outline"
                                type="submit"
                            >
                                수정
                            </button>

                        </form>


                        <!-- 삭제 -->

                        <form
                            class="board-action-form"
                            method="post"
                            action="delete.php"

                            onsubmit="
                                return confirm(
                                    '정말 삭제하시겠습니까?'
                                );
                            "
                        >

                            <input
                                type="hidden"
                                name="id"
                                value="<?php
                                    echo $post['id'];
                                ?>"
                            >


                            <button
                                class="btn btn-primary"
                                type="submit"
                            >
                                삭제
                            </button>

                        </form>


                    </div>


                <?php endif; ?>


            </div>


        </div>

    </div>

</section>


<?php
require_once "../includes/footer.php";
?>
