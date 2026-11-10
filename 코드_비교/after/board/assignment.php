<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireLogin();

$search_type = isset($_POST['search_type'])
    ? $_POST['search_type']
    : 'title';

$keyword = isset($_POST['keyword'])
    ? $_POST['keyword']
    : '';

$sql = "
    SELECT posts.id, posts.title, posts.created_at, posts.views, users.name
    FROM posts JOIN users ON posts.user_id = users.id
    WHERE posts.board_type = ?
";
$params = ['assignment'];
$types = "s";

if ($keyword !== '') {
    $like = "%{$keyword}%";
    if ($search_type === 'writer') {
        $sql .= " AND users.name LIKE ?";
        $params[] = $like; $types .= "s";
    } elseif ($search_type === 'all') {
        $sql .= " AND (posts.title LIKE ? OR posts.content LIKE ?)";
        $params[] = $like; $params[] = $like; $types .= "ss";
    } else {
        $sql .= " AND posts.title LIKE ?";
        $params[] = $like; $types .= "s";
    }
}
$sql .= " ORDER BY posts.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

?>

<?php require_once "../includes/header.php"; ?>

<section class="board-page-section">

    <div class="container">

        <div class="board-page-head">

            <div>
                <span class="eyebrow">ASSIGNMENT</span>

                <h2>과제</h2>

                <p>과제 관련 게시글을 확인할 수 있습니다.</p>
            </div>

            <form class="board-write-form" action="write.php" method="post">
                <input type="hidden" name="board" value="assignment">
                <input type="hidden" name="action" value="open">
                <button class="btn btn-primary" type="submit">
                    <?php echo isAdmin() ? '과제 등록' : '과제 제출'; ?>
                </button>
            </form>

        </div>

        <div class="board-table-panel">

            <table class="board-table">

                <thead>
                    <tr>
                        <th class="col-num">번호</th>
                        <th class="col-title">제목</th>
                        <th class="col-writer">작성자</th>
                        <th class="col-date">작성일</th>
                        <th class="col-views">조회</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($post = $result->fetch_assoc()): ?>

                        <tr>

                            <td><?php echo $post['id']; ?></td>

                            <td class="board-title-cell">

                                <form
                                    class="board-post-form"
                                    method="post"
                                    action="view.php"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo $post['id']; ?>"
                                    >

                                    <button
                                        class="board-post-button"
                                        type="submit"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            $post['title']
                                        );
                                        ?>
                                    </button>
                                </form>

                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $post['name']
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    'm.d H:i',
                                    strtotime($post['created_at'])
                                );
                                ?>
                            </td>

                            <td>
                                <?php echo $post['views']; ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr class="board-empty">
                        <td colspan="5">과제 게시글이 없습니다.</td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="board-bottom">

            <form
                class="board-search"
                method="post"
                action="assignment.php"
            >

                <select name="search_type">
                    <option
                        value="title"
                        <?php if ($search_type === 'title') echo 'selected'; ?>
                    >
                        제목
                    </option>

                    <option
                        value="writer"
                        <?php if ($search_type === 'writer') echo 'selected'; ?>
                    >
                        작성자
                    </option>

                    <option
                        value="all"
                        <?php if ($search_type === 'all') echo 'selected'; ?>
                    >
                        제목 + 내용
                    </option>
                </select>

                <input
                    type="text"
                    name="keyword"
                    value="<?php echo htmlspecialchars($keyword); ?>"
                    placeholder="검색어를 입력하세요"
                >

                <button
                    class="btn board-search-reset"
                    type="button"
                >
                    초기화
                </button>

                <button
                    class="btn btn-dark"
                    type="submit"
                >
                    검색
                </button>

            </form>

        </div>

    </div>

</section>

<?php require_once "../includes/footer.php"; ?>

