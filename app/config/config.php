<?php
/*
 * Like Girl — 统一配置
 * 由原 admin/Config_DB.php 抽取，路径已适配 app/ + storage/ + database/ 结构。
 */

header("Content-Type:text/html; charset=utf8");

// ---------- 数据库（MySQL 传统模式） ----------
// 启用了 MYSQL 扩展时使用；否则（无 mysqli 扩展）自动回退到 SQLite 兼容层。
$db_address  = "localhost";
$db_username = "root";
$db_password = "123456";
$db_name     = "love_db";

// ---------- SQLite 兼容模式（Docker 默认） ----------
// 数据库文件与播种脚本均位于文档根 public/ 之外。
$sqlite_path      = getenv('LIKEGIRL_SQLITE_PATH')
    ?: dirname(__DIR__) . '/../storage/data/likegirl.sqlite';
$sqlite_seed_file = getenv('LIKEGIRL_SQLITE_SEED')
    ?: dirname(__DIR__) . '/../database/love_db.sql';

// ---------- 安全码（敏感信息修改需输入） ----------
// Docker 可通过环境变量 LIKEGIRL_SECURITY_CODE 覆盖。
$Like_Code = getenv('LIKEGIRL_SECURITY_CODE') ?: "Love";

// ---------- 版本号 ----------
$version = 20260601;
