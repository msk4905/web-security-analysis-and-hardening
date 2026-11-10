<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireLogin();

$id = isset($_POST['id'])
    ? $_POST['id']
    : 0;

$sql = "
    SELECT
        id,
        board_type
    FROM posts
    WHERE id = $id
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

$sql = "
    DELETE FROM posts
    WHERE id = $id
";

if ($conn->query($sql)) {

    $board_link = $post['board_type'] . ".php";

    header("Location: " . $board_link);
    exit;

} else {

    die("게시글 삭제에 실패했습니다.");
}
?>
