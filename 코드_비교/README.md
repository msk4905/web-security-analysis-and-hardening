# 보완 전후 코드 비교

애플리케이션 보안 보완에 사용한 파일을 `before/`와 `after/`에 수록했습니다. 파일별 변경 사항은 `diff/`에서 확인할 수 있습니다.

- 공개본의 `account/login_process.php`에서 reCAPTCHA 비밀키를 `YOUR_RECAPTCHA_SECRET_KEY`로 대체했습니다. 계정별 실패 횟수가 5회 이상일 때 캡차를 요구하는 조건은 시험 설정을 반영합니다.
- `uploads/.htaccess`와 `includes/mailer.php`는 보완 과정에서 추가한 파일입니다.

| 파일 | 주요 보완 내용 | diff |
|---|---|---|
| `account/login_process.php` | Prepared Statement, 비밀번호 해시 검증, 로그인 실패 횟수 기록, 세션 ID 재발급 | [보기](diff/account_login_process.php.diff) |
| `account/login.php` | 출력 이스케이프, reCAPTCHA 위젯 | [보기](diff/account_login.php.diff) |
| `account/register.php` | Prepared Statement, 비밀번호 해시 저장 | [보기](diff/account_register.php.diff) |
| `account/reset_password.php` | 이메일 인증코드 발급과 검증, 코드 만료 시간, Prepared Statement | [보기](diff/account_reset_password.php.diff) |
| `account/find_id.php` | Prepared Statement | [보기](diff/account_find_id.php.diff) |
| `account/edit.php` | 본인 확인(IDOR 방어), CSRF 토큰, Prepared Statement, 비밀번호 해시 | [보기](diff/account_edit.php.diff) |
| `account/delete_account.php` | 본인 확인, CSRF 토큰, Prepared Statement | [보기](diff/account_delete_account.php.diff) |
| `board/write.php` | 업로드 확장자·MIME 허용 목록, CSRF 토큰 | [보기](diff/board_write.php.diff) |
| `board/edit.php` | 소유자 검사, CSRF 토큰, Prepared Statement | [보기](diff/board_edit.php.diff) |
| `board/delete.php` | 소유자 검사, CSRF 토큰, Prepared Statement | [보기](diff/board_delete.php.diff) |
| `board/view.php` | Prepared Statement, 출력 이스케이프, CSRF 토큰 | [보기](diff/board_view.php.diff) |
| `board/free.php`, `board/notice.php`, `board/assignment.php` | 검색 조건 Prepared Statement | [자유게시판](diff/board_free.php.diff) · [공지](diff/board_notice.php.diff) · [과제](diff/board_assignment.php.diff) |
| `admin/notice_write.php` | Prepared Statement | [보기](diff/admin_notice_write.php.diff) |
| `includes/auth.php` | 세션 쿠키 옵션(`httponly`, `secure`, `samesite`), CSRF 토큰 발급 | [보기](diff/includes_auth.php.diff) |
| `includes/db.php` | DB 접속 정보를 환경변수에서 읽도록 변경 | [보기](diff/includes_db.php.diff) |
| `includes/header.php` | 출력 이스케이프 | [보기](diff/includes_header.php.diff) |
| `includes/mailer.php` (신규) | STARTTLS와 인증서 검증을 적용한 메일 발송 설정 | [코드](after/includes/mailer.php) |
| `uploads/.htaccess` (신규) | 업로드 폴더의 스크립트 실행 제한 | [설정](after/uploads/.htaccess) |
