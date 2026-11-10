<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALEPH A Class | Cyber Security</title>

    <link rel="stylesheet"
          href="/ALEPH-A-Class/css/style.css">

    <style>
        /* 드롭다운 메뉴 스타일 */
        nav .dropdown {
            position: relative;
            display: inline-block;
        }

        nav .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #1e222d;
            min-width: 140px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border-radius: 6px;
            padding: 8px 0;
            list-style: none;
            margin: 0;
            z-index: 9999;
        }

        nav .dropdown-menu li {
            width: 100%;
        }

        nav .dropdown-menu a {
            display: block;
            padding: 8px 16px;
            color: #ccc;
            text-decoration: none;
            white-space: nowrap;
            font-size: 14px;
        }

        nav .dropdown-menu a:hover {
            background-color: #2a303c;
            color: #00e5ff;
        }

        /* 마우스 호버 시 드롭다운 표시 */
        nav .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>

<body>

<div class="topbar">
    <div class="container">
        <span>ALEPH Cyber Security Team 404 found </span>
        <span>Class A · 09:00 — 18:00</span>
    </div>
</div>

<header>
    <div class="container nav">

        <a class="brand"
           href="/ALEPH-A-Class/">
            <span class="brand-mark">A</span>

            <span>
                ALEPH A CLASS
                <small>CYBER SECURITY</small>
            </span>
        </a>

        <nav>
            <a href="/ALEPH-A-Class/">홈</a>

            <!-- 게시판 드롭다운 메뉴 -->
            <div class="dropdown">
                <a href="#">게시판 ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="/ALEPH-A-Class/board/notice.php">공지게시판</a></li>
                    <li><a href="/ALEPH-A-Class/board/free.php">자유게시판</a></li>
                    <li><a href="/ALEPH-A-Class/board/assignment.php">과제게시판</a></li>
                </ul>
            </div>

            <!-- Class 항목 삭제됨 -->

            <a href="/ALEPH-A-Class/mypage/index.php">마이페이지</a>

            <?php if (isAdmin()): ?>
                <a href="/ALEPH-A-Class/admin/index.php">
                    관리자
                </a>
            <?php endif; ?>
        </nav>

        <div class="nav-actions">

            <?php if (isLoggedIn()): ?>

                <a class="btn btn-outline"
                   href="/ALEPH-A-Class/mypage/index.php">
                    <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>님
                </a>

                <a class="btn btn-primary"
                   href="/ALEPH-A-Class/account/logout.php">
                    로그아웃
                </a>

            <?php else: ?>

                <button class="btn btn-outline"
                        onclick="openLogin()">
                    로그인
                </button>

                <a class="btn btn-primary"
                   href="/ALEPH-A-Class/account/register.php">
                    회원가입
                </a>

            <?php endif; ?>

        </div>

    </div>
</header>

<main>
