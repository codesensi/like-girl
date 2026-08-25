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
    // ============ 后台菜单 AJAX 局部刷新（避免点击菜单整页刷新闪烁） ============
    // 点击侧边菜单时，仅请求 ?ajax=1 返回的内容片段并替换 .content 区域，
    // 顶部栏 / 侧边栏 / 加载动画不再重新加载；页面内部（增删改、删除后重载）仍按原整页跳转。
    (function () {
        var $content = $('.content-page .content');
        var $menu = $('.side-nav');
        // 非后台布局（如登录页）无侧边菜单，直接跳过，保持原行为
        if (!$menu.length || !$content.length) return;

        // 判断 URL 是否为后台页面（*.php）
        function isAdminPage(url) {
            return /\/admin\/.+\.php/i.test(url.split('?')[0]);
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
                $content.html(html);
                highlight(bare);
                window.scrollTo(0, 0);
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
