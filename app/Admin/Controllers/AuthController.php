<?php
/*
 * Like Girl — 后台「登录/登出」控制器
 *
 * 归并原文件：
 *   login.php    → login（登录页，公开）
 *   loginPost.php→ loginPost（登录提交，公开）
 *   loginOut.php → loginOut（登出）
 *
 * 说明：本控制器不继承 AbstractAdminController，登录流程需在鉴权之前执行。
 */

require_once dirname(__DIR__, 2) . '/config/database.php';

class AuthController
{
    /* 登录页（独立页面，不套后台布局） */
    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $errorMsg = '';
        if (isset($_SESSION['login_error'])) {
            $errorMsg = $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        }
        extract($GLOBALS, EXTR_SKIP);
        include dirname(__DIR__, 2) . '/View/admin/pages/login.php';
    }

    /* 登录提交 */
    public function loginPost()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $_POST['adminName'] ?? '';
        $pw   = $_POST['pw'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sql = "select * from login where user =?";
            $stmt = $this->conn()->prepare($sql);
            $stmt->bind_param("s", $USER);
            $USER = mysqli_real_escape_string($this->conn(), $user);
            $PW = md5($pw);
            $stmt->bind_result($id, $Login_user, $Login_pw);
            $result = $stmt->execute();
            if (!$result) {
                echo "错误信息：" . $stmt->error;
            }
            $stmt->fetch();
        }

        if (isset($USER) && $USER == $Login_user) {
            if ($PW == $Login_pw) {
                $_SESSION['loginadmin'] = $USER;
                $_SESSION['login_success'] = true;
                echo "<script>location.href = 'index.php';</script>";
            } else {
                $_SESSION['login_error'] = '用户名或密码错误！！！';
                die("<script>location.href = 'login.php';</script>");
            }
        } else {
            $_SESSION['login_error'] = '用户名或密码错误！！！';
            die("<script>location.href = 'login.php';</script>");
        }
    }

    /* 登出 */
    public function loginOut()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location:login.php");
    }

    /**
     * 返回全局连接（$conn 与 $connect 同源）。
     */
    private function conn()
    {
        global $conn;
        return $conn;
    }
}
