<?php
/*
 * Like Girl — 数据库连接初始化（唯一入口）
 * 由原 admin/connect.php + admin/Database.php 统一而来。
 *
 * 统一约定：
 *   - 全局 $connect 为单一 mysqli 连接（面向过程 mysqli_query 兼容）；
 *   - 全局 $conn    为同一连接的别名（prepared statement 风格兼容）；
 *   - 优先加载 app/Core/SqliteCompat.php（无 mysqli 扩展时模拟 mysqli API）。
 */
error_reporting(0);
header("Content-Type:text/html; charset=utf8");

require_once __DIR__ . '/config.php';
require_once dirname(__DIR__) . '/Core/SqliteCompat.php';

$createConnection = function () use ($db_address, $db_username, $db_password, $db_name) {
    if (class_exists('mysqli')) {
        // 原生（MySQL）或 SQLite 兼容层的 mysqli
        $c = mysqli_connect($db_address, $db_username, $db_password, $db_name);
        if ($c && method_exists($c, 'set_charset')) {
            $c->set_charset("utf8mb4");
        }
        return $c;
    }
    return false;
};

$connect = $createConnection();

if (!$connect) {
    die("<script>location.href = '../admin/connectDie.php';</script>");
}

$LikeGirl_Code = $Like_Code;
$conn = $connect; // 兼容原有 $conn 变量（prepared statement 入口）
