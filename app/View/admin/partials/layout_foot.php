</div> <!-- content End -->
<?php
// 后台通用 AJAX 前端处理（$("#xxxPost").click 等回调）
include __DIR__ . '/ajax.php';
?>
<!-- Footer Start -->
<footer class="footer">
    <div class="row footer_center">
        <div class="col-md-6">
            Copyright © 2022-<?php echo date("Y")  ?> <a href="https://blog.kikiw.cn/index.php/archives/52/" target="_blank">Ki</a> All Rights Reserved.
        </div>
    </div>
</footer>
<!-- end Footer -->
</div> <!-- content-page End -->
</div> <!-- container-fluid End -->
</div> <!-- wrapper End -->

<script>
    console.log("%c Like Girl v5.2.1-Stable | Powered by Ki", "color:#fff;background:linear-gradient(270deg,#986fee,#8695e6,#68b7dd,#18d7d3);padding:8px 15px;border-radius:15px");
    console.log("%c Q | 3439780232", "color:#fff;background:#000;padding:8px 15px;border-radius:15px");
    
</script>


<script src="../assets/toastr/toastr.js"></script>
<!-- App js -->
<script src="/admin/assets/js/app.min.js"></script>

<!-- end demo js-->
<script src="/admin/assets/js/pages/demo.toastr.js"></script>

<script>
    // ============ 后台 AJAX 局部刷新（避免切换菜单 / 页面内按钮整页刷新闪烁） ============
    // 点击侧边菜单或内容区内的后台页面链接时，仅请求 ?ajax=1 返回的内容片段并替换 .content 区域，
    // 顶部栏 / 侧边栏 / 加载动画不再重新加载。删除、提交、登出等副作用操作仍按原整页跳转。
    (function () {
        var $content = $('.content-page .content');
        var $menu = $('.side-nav');
        // 非后台布局（如登录页）无侧边菜单，直接跳过，保持原行为
        if (!$menu.length || !$content.length) return;

        // 判断 URL 是否为后台页面（*.php）
        function isAdminPage(url) {
            return /\/admin\/.+\.php/i.test(url.split('?')[0]);
        }

        // 副作用页：删除 / 提交接口 / 登出等，不做局部刷新
        function isSideEffect(bare) {
            var f = (bare.split(/[\\/]/).pop() || '').toLowerCase();
            return /post\.php$/.test(f) || /^del/.test(f) || /logout|logino?u?t/.test(f);
        }

        // 同步侧边菜单高亮（仅本地视觉，无需服务端重载）
        function highlight(path) {
            var file = (path.split('?')[0] || '').split('/').pop().toLowerCase();
            $menu.find('a.side-nav-link').each(function () {
                var href = $(this).attr('href') || '';
                var on = file && href && href.split('?')[0].split('/').pop().toLowerCase() === file;
                $(this).closest('li').toggleClass('active', on);
            });
        }

        // 按文档顺序执行内容片段内的脚本：外部库串行加载后再执行内联脚本，
        // 避免 jQuery .html() 默认"内联先于外部库"执行导致 editormd / DataTable 等初始化失败。
        // navSeq 传入用于中断判断：若期间又发起了新导航，则停止执行过期片段脚本。
        function execFragment(html, mySeq, done) {
            // 先注入去除 <script> 的 HTML，避免 jQuery 自动抢先执行内联脚本
            $content.html(html.replace(/<script[\s\S]*?<\/script>/gi, ''));

            // 收集脚本（保持文档顺序）
            var scripts = [];
            var re = /<script([^>]*)>([\s\S]*?)<\/script>/gi, m;
            while ((m = re.exec(html)) !== null) {
                var src = ((m[1] || '').match(/src=["']([^"']+)["']/i) || [])[1] || '';
                scripts.push({ src: src, code: m[2] });
            }

            var i = 0;
            function step() {
                // 已发起更新的导航，停止执行过期片段的脚本
                if (mySeq !== navSeq) { done && done(); return; }
                if (i >= scripts.length) { done && done(); return; }
                var s = scripts[i++];
                if (s.src) {
                    // 串行加载外部脚本，保证依赖顺序
                    $.getScript(s.src).always(step);
                } else {
                    try { $.globalEval(s.code); } catch (e) { console.error('admin fragment script error:', e); }
                    step();
                }
            }
            step();
        }

        // 拉取目标页面内容片段并替换 .content
        // navSeq 用于丢弃过期响应（快速连点菜单时只认最后一个请求）
        var navSeq = 0;
        function navigate(url) {
            var mySeq = ++navSeq;
            var bare = url.split('?')[0];
            // 构造 ?ajax=1 请求；去掉 URL 中可能已存在的 ajax 参数避免重复
            var query = (url.split('?')[1] || '').split('&').filter(function (k) {
                return k.indexOf('ajax=') !== 0;
            });
            query.push('ajax=1');
            var ajaxUrl = bare + '?' + query.join('&');

            $.ajax({
                url: ajaxUrl,
                dataType: 'html'
            }).done(function (html) {
                // 已有更新的请求发出，丢弃本次过期响应
                if (mySeq !== navSeq) return;
                // 若返回的是完整 HTML（如未登录被重定向到 login 页），退回整页跳转
                if (/^\s*<!DOCTYPE/i.test(html) || /<html[\s>]/i.test(html) || /<body[\s>]/i.test(html)) {
                    window.location.href = url;
                    return;
                }
                // 先同步地址栏（保留原 query），保证片段内相对路径（assets/js 等）按目标页面正确解析
                history.pushState(null, '', url);
                execFragment(html, mySeq, function () {
                    highlight(bare);
                    window.scrollTo(0, 0);
                });
            }).fail(function () {
                // 网络异常时退化——仅当自己仍是最新请求时整页跳转
                if (mySeq !== navSeq) return;
                window.location.href = url;
            });
        }

        // 拦截侧边菜单链接点击
        $menu.on('click', 'a.side-nav-link', function (e) {
            var href = $(this).attr('href');
            if (!href || !isAdminPage(href) || !/\.php/i.test(href)) return;
            // 当前就在目标页时不做任何事
            if (location.pathname.toLowerCase() === href.split('?')[0].toLowerCase()) return;
            e.preventDefault();
            navigate(href);
        });

        // 拦截内容区内的后台页面链接（新增 / 修改 / 设置等），使页面内按钮跳转同样局部刷新。
        // 委托绑定在 .content 容器上，替换内容后依然生效；删除 / 提交 / 登出等副作用链接除外。
        // 用 new URL 把相对路径（如 modlist.php?id=1）解析为绝对后判断，保证列表里"修改"等相对链接也生效。
        $content.on('click', 'a[href]', function (e) {
            var raw = $(this).attr('href');
            if (!raw || !/\.php/i.test(raw)) return;
            var abs;
            try { abs = new URL(raw, location.href); } catch (err) { return; }
            if (abs.origin !== location.origin) return;          // 仅同源，放行外链
            if (!/\/admin\/.+\.php/i.test(abs.pathname)) return; // 仅后台 admin 页
            if (isSideEffect(abs.pathname)) return;
            if (location.pathname.toLowerCase() === abs.pathname.toLowerCase()) return;
            e.preventDefault();
            navigate(abs.pathname + abs.search);
        });

        // 浏览器前进 / 后退时恢复对应内容（用完整 href，保留 query）
        $(window).on('popstate', function () {
            if (isAdminPage(location.href)) {
                navigate(location.href);
            }
        });

        highlight(location.href);
    })();
</script>

</body>

</html>
