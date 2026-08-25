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
        // 局部刷新模式：登录后切换侧边菜单时，前端以 ?ajax=1 请求本页，仅返回内容片段
        // （不重复输出 layout_head/layout_foot），供前端 AJAX 替换 .content 区域，避免整页刷新闪烁。
        $isAjax = ($_GET['ajax'] ?? '') === '1';

        // 渲染页面主体
        extract($data, EXTR_SKIP);
        $pageFile = dirname(__DIR__, 2) . '/View/admin/pages/' . $page . '.php';

        if ($isAjax) {
            // 局部模式：只输出内容片段；需与完整模式一样注入全局变量
            // （$text/$diy/$connect 等由 Auth 声明为 global，页面模板依赖它们渲染数据值）
            extract($GLOBALS, EXTR_SKIP);
            include $pageFile;
            return;
        }

        // 完整布局模式：布局头（含头部/顶栏/侧边栏/样式）
        extract($GLOBALS, EXTR_SKIP);
        include dirname(__DIR__, 2) . '/View/admin/partials/layout_head.php';

        include $pageFile;

        // 布局尾（含 footer / 底部 JS / 菜单局部刷新脚本）
        include dirname(__DIR__, 2) . '/View/admin/partials/layout_foot.php';
    }
}
