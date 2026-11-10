<?php

require_once "../includes/db.php";
require_once "../includes/auth.php";

requireLogin();

if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die("잘못된 요청입니다.");
}

$id = isset($_POST['id'])
    ? $_POST['id']
    : 0;

$stmt = $conn->prepare("SELECT id, board_type FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    die("게시글을 찾을 수 없습니다.");
}

if (
    $post['board_type'] === 'notice'
    && !isAdmin()
) {
    http_response_code(403);
    die("관리자 권한이 필요합니다.");
}
if (!isAdmin() && (!isLoggedIn() || $_SESSION['user_id'] != $post['user_id'])) {
    http_response_code(403);
    die("삭제 권한이 없습니다.");
}
$stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    $board_link = $post['board_type'] . ".php";

    header("Location: " . $board_link);
    exit;

} else {

    die("게시글 삭제에 실패했습니다.");
}
?>
