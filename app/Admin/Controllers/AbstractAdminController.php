<?php
/*
 * Like Girl — 后台控制器抽象基类
 *
 * 统一职责：
 *   - 加载 app/Admin/middleware/Auth.php（登录鉴权 + 全局 $connect/$login/$text/$diy）；
 *   - 提供 render() 渲染后台布局（layout_head + 页面内容 + layout_foot）。
 *
 * 渲染变量：参数 $data 注入到视图，$GLOBALS 中的全局（$text/$diy/$connect 等）注入到页面视图。
 */

require_once dirname(__DIR__) . '/middleware/Auth.php';

abstract class AbstractAdminController
{
    /**
     * 渲染后台页面：布局头（含头部/侧边栏） + 页面主体 + 布局尾。
     *
     * @param string $page 相对 app/View/admin/pages/ 的模板名（不含 .php）
     * @param array  $data 注入视图的局部变量
     */
    protected function render($page, array $data = [])
    {
        // 渲染布局头（含头部/顶栏/侧边栏/样式）
        extract($GLOBALS, EXTR_SKIP);
        include dirname(__DIR__, 2) . '/View/admin/partials/layout_head.php';

        // 渲染页面主体
        extract($data, EXTR_SKIP);
        include dirname(__DIR__, 2) . '/View/admin/pages/' . $page . '.php';

        // 渲染布局尾（含 footer / 底部 JS）
        include dirname(__DIR__, 2) . '/View/admin/partials/layout_foot.php';
    }
}
