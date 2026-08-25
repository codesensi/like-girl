<?php
/*
 * Like Girl — 后台登录鉴权中间件
 * 由 admin/Nav.php 顶部逻辑抽离而来。
 *
 * 职责：
 *   - 启动会话；
 *   - 校验是否已登录（$_SESSION['loginadmin']）；
 *   - 校验登录用户仍存在于 login 表；
 *   - 加载后台全局数据（$login、$text、$diy）；
 *   - 未通过校验时跳转到 /admin/login.php。
 *
 * 依赖：app/config/database.php（提供 $connect）与 app/Core/helpers.php。
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $connect, $login, $text, $diy;

if (empty($_SESSION['loginadmin'])) {
    header("Location: /admin/login.php");
    exit;
}

// 连接与公共函数
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__) . '/../Core/helpers.php';
require_once dirname(__DIR__) . '/../Core/ipcheck.php';

$sql = "select * from login where user = '" . mysqli_real_escape_string($connect, $_SESSION['loginadmin']) . "' ";
$loginresult = mysqli_query($connect, $sql);
if (mysqli_num_rows($loginresult)) {
    $login = mysqli_fetch_array($loginresult);
} else {
    unset($_SESSION['loginadmin']);
    header("Location: /admin/login.php");
    exit;
}

$sql = "select * from login";
$result = mysqli_query($connect, $sql);
if (mysqli_num_rows($result)) {
    $login = mysqli_fetch_array($result);
}

$sql = "select * from text";
$result = mysqli_query($connect, $sql);
if (mysqli_num_rows($result)) {
    $text = mysqli_fetch_array($result);
}

$sql = "select * from diySet";
$result = mysqli_query($connect, $sql);
if (mysqli_num_rows($result)) {
    $diy = mysqli_fetch_array($result);
}
