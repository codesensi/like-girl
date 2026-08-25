<?php
/*
 * Like Girl — 前端引导层（由 head.php 顶部 PHP 块抽离）
 *
 * 统一为前端页面准备运行环境：
 *   - 数据库连接（app/config/database.php，唯一入口，$connect/$conn）
 *   - 公共函数（app/Core/helpers.php）
 *   - 站点文案 $text（text 表）
 *   - 自定义设置 $diy（diySet 表）
 *   - $copy / $icp / $Animation 便捷变量
 *   - 头部/尾部自定义代码输出所需变量
 *
 * 前端页面只需 require 本文件，不再依赖旧 admin/ 目录。
 */

error_reporting(0);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Core/helpers.php';

/*
 * 资源路径归一化：把旧结构下以 Style/ 或 ../Style/ 开头的相对路径改写为
 * public/assets/ 对应的绝对化相对路径 assets/...（迁移后静态资源位于 public/assets）。
 * 传入普通字符串时按需改写；传入数组时递归处理文本值。
 * 注意：必须在本文件调用点之前定义（条件式函数在运行时才注册）。
 */
if (!function_exists('normalize_asset_paths')) {
    function normalize_asset_paths($value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = normalize_asset_paths($v);
            }
            return $value;
        }
        if (is_string($value)) {
            return preg_replace('#^(\.\./)?Style/#', 'assets/', $value);
        }
        return $value;
    }
}

/* IP 封禁检查（可选启用；CLI/后台冒烟时可跳过） */
if (!defined('LIKEGIRL_SKIP_IPCHECK')) {
    include __DIR__ . '/Core/ipcheck.php';
}

/* 站点文案：text 表单行配置 */
$sql = "select * from text";
$result = mysqli_query($connect, $sql);
if ($result && mysqli_num_rows($result)) {
    $text = mysqli_fetch_array($result);
} else {
    $text = [];
}
/* 迁移后静态资源位于 public/assets，统一把 Style/ 相对路径改写为 assets/ */
$text = normalize_asset_paths($text);
$copy = isset($text['Copyright']) ? $text['Copyright'] : '';
$icp = isset($text['icp']) ? $text['icp'] : '';
$Animation = isset($text['Animation']) ? $text['Animation'] : '';

/* 自定义设置：diySet 表单行配置 */
$sql = "select * from diySet";
$result = mysqli_query($connect, $sql);
if ($result && mysqli_num_rows($result)) {
    $diy = mysqli_fetch_array($result);
} else {
    $diy = [];
}
$diy = normalize_asset_paths($diy);

/* 视图渲染辅助：$view() 将全局状态与传入数据注入模板后渲染。
 * EXTR_SKIP 保证 $data 中的键优先于全局同名变量。 */
if (!function_exists('view')) {
    function view($tpl, array $data = [])
    {
        extract($GLOBALS, EXTR_SKIP);
        extract($data, EXTR_SKIP);
        include dirname(__DIR__) . '/app/View/' . $tpl . '.php';
    }
}


