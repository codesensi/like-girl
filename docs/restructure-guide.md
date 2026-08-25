# Like Girl 目录重构 — 第 4~8 步迁移指南

> 本文档是重构的后续推进计划。**第 0~3 步已完成并验证**（见文末「已完成」），
> 第 4~8 步涉及大规模文件搬移与路径重写，作为独立阶段推进。
> 迁移完成前，旧的根目录页面与 `admin/` 目录仍可正常使用，不破坏现有功能。

---

## 目标结构（最终）

```
like-girl/
├── app/                 # 应用代码（不可被 URL 访问）
│   ├── config/          # config.php + database.php（唯一连接）✅已建
│   ├── Core/            # SqliteCompat/helpers/Database/ipcheck ✅已建
│   ├── Models/          # 9 个业务模型 + Model 基类 ✅已建
│   ├── Admin/           # middleware/Auth.php ✅已建（Controllers 待建）
│   ├── Controllers/     # 前端页面控制器（待建）
│   ├── View/
│   │   ├── partials/    # header / footer / nav（待建）
│   │   └── pages/       # 各页面模板（待建）
├── public/              # Web 根（唯一 URL 入口）
│   ├── index.php 等前端页面
│   ├── admin/           # 后台入口
│   └── assets/          # 静态资源（原 Style/ + Botui/ + admin/assets/）
├── storage/             # data / logs / uploads ✅已建
├── database/love_db.sql ✅已迁移
├── docker/              # Docker 相关 ✅已迁移并改文档根
└──（迁移完成后移除）admin/ 、根目录 php 页面 、Style/ 、Botui/、data/
```

---

## 第 4 步：前端页面 MVC 化并迁入 public/

### 4.1 建立前端引导层

✅ 已落地：`app/bootstrap.php`（前端引导层，CLI 冒烟验证 text/diy/view 正常加载）

新建 `app/bootstrap.php`，统一前端页面所需的环境（连接数据库、加载 text/diy、ipjc/ip 副作用、公共函数、全局变量）：

```php
require __DIR__ . '/config/database.php';
require __DIR__ . '/Core/helpers.php';
include __DIR__ . '/Core/ipcheck.php';
$sql = "select * from text";   // $text
$sql = "select * from diySet"; // $diy
// $copy / $icp / $Animation / $version 等在 config.php 提供
```

作用：让前端页面不再依赖 `admin/connect.php` / `admin/Function.php`，而依赖 `app/`。

### 4.2 拆分部视图

- `head.php` → `app/View/partials/header.php`（纯 HTML head + 导航，变量由调用方注入）
  - 顶部 PHP 块（查询 text/diy、include ipjc/ip）**移出**，放入控制器/引导层；
  - 资源引用 `../Style/...`、`../Botui/...` 改为 `assets/...`（配合第 6 步）；
  - 内嵌 JS 保留在视图，PHP 变量（`$text['startTime']` 等）由注入变量提供。
- `footer.php` → `app/View/partials/footer.php`
- `admin/Nav.php` 的渲染部分 → `app/View/admin/partials/nav.php`（鉴权已由 `Auth.php` 承担）

### 4.3 页面迁移顺序（dependency 少 → 多，逐个搬移并回归）

| 页面 | 控制器 | 视图 | 原文件 |
|---|---|---|---|
| 关于 | `Controllers/AboutController.php` | `pages/about.php` | `about.php` |
| 恋爱清单 | `Controllers/ListController.php` | `pages/list.php` | `list.php` |
| 点点滴滴 | `Controllers/LittleController.php` | `pages/little.php` | `little.php` |
| 恋爱相册 | `Controllers/LoveImgController.php` | `pages/loveImg.php` | `loveImg.php` |
| 相册分页接口 | `Controllers/LoveImgController@photos` | — | `getPhotos.php` |
| 留言板 | `Controllers/LeavingController.php` | `pages/leaving.php` | `leaving.php` |
| 首页 | `Controllers/HomeController.php` | `pages/index.php` | `index.php` |
| 文章详情 | `Controllers/PageController.php` | `pages/page.php` | `page.php` |
| 错误页 | `Controllers/ErrorController.php` | `pages/error.php` | `error.php` |

控制器范式：`class XController { public function index() { $model = new X(); return view('pages/x', ['rows'=>$model->all()]); } }`
`view()` 辅助函数：`function view($tpl,$data){ extract($data); include dirname(__DIR__).'/app/View/'.$tpl.'.php'; }`

### 4.4 搬移后移除根目录 php 页面（第 8 步统一）

`index.php / list.php / little.php / loveImg.php / leaving.php / about.php / page.php / error.php / getPhotos.php / head.php / footer.php / ip.php / ipjc.php` → 全部由 `app/` + `public/` 取代后删除。

---

## 第 5 步：后台 CRUD 控制器化与归并

将 `admin/` 下 55 个平铺 php 文件按实体归并为控制器。**命名统一为 REST 风格 action**：`index/create/store/edit/update/destroy`。

### 控制器映射

| 实体 | 合并到的控制器 | 原文件（归并） |
|---|---|---|
| 登录/登出 | `Admin/Controllers/AuthController` | `login.php loginPost.php loginOut.php` |
| 后台首页/仪表盘 | `Admin/Controllers/DashboardController` | `index.php LG_Info.php Like_Girl.php Footer.php` |
| 站点设置 | `Admin/Controllers/SettingController` | `Set.php adminPost.php` |
| 用户管理 | `Admin/Controllers/UserController` | `user.php userPost.php` |
| 恋爱清单(列表/卡) | `Admin/Controllers/LoveListController` | `lovelist.php lovelistAdd.php listaddPost.php modlist.php listupda.php dellist.php loveadminPost.php CardadminPost.php CopyadminPost.php` |
| 点点滴滴 | `Admin/Controllers/LittleController` | `littleSet.php littleAdd.php littleAddPost.php littleupda.php dellitt.php modlitt.php` |
| 留言板 | `Admin/Controllers/LeavingController` | `leavSet.php leavP.php leavPPost.php leavingPost.php delleav.php` |
| 恋爱相册 | `Admin/Controllers/LoveImgController` | `loveImgSet.php loveImgAdd.php ImgAddPost.php ImgUpdaPost.php delImg.php modImg.php` |
| 关于 | `Admin/Controllers/AboutController` | `aboutSet.php aboutPost.php` |
| IP 封禁 | `Admin/Controllers/IpController` | `ipList.php ipSet.php ipAddPost.php delip.php` |
| 警告/非法操作 | `Admin/Controllers/WarningController` | `warning.php feifa.php` |
| Ajax 异步处理 | `Admin/Controllers/AjaxController` | `ajax.php` |

### 公共后台层（已建/待建）

- ✅ `app/Admin/middleware/Auth.php`（登录校验 + 全局数据加载）
- ⬜ `app/Admin/Controllers/AbstractAdminController.php`（统一：require Auth、view 渲染）
- ⬜ `app/View/admin/partials/nav.php` + `footer.php`（后台布局）

### 注意
- 所有 `*Post.php` 的 `$_POST` 处理与 HTML 表单 `action=` 指向需同步更新为新 URL。
- `admin/ajax.php` 校验 `$_SESSION['loginadmin']`，迁移后保持该校验。
- 页面中 `header("Location: /admin/login.php")` 等绝对 URL 保持不变（文档根仍为 public/，/admin/... 相对 public 根解析正确）。

---

## 第 6 步：静态资源迁移与路径更新

| 原目录 | 目标 | 说明 |
|---|---|---|
| `Style/`（css/js/jquery/Font/img/pagelir/toastr/cur） | `public/assets/` | 目录小写：`css js fonts images lib plugins` |
| `Botui/` | `public/assets/vendor/botui/` | |
| `admin/assets/` | `public/admin/assets/` | |
| `admin/editormd/` | `public/admin/assets/vendor/editormd/` | |

### 路径重写清单（迁移时逐一处理）
- `head.php` 中 `../Style/...`、`../Botui/...` → `assets/...`
- 各前端页面中 `Style/...`（相对）→ `assets/...`
- `footer.php` 中 `../Style/toastr/toastr.js` → `assets/toastr/toastr.js`
- 后台 `admin/*.php` 中 `assets/...`、`editormd/...` 相对路径 → `admin/assets/...`
- `Style/Font` → `assets/fonts`；`Style/cur` → `assets/cur`（若被引用）
- 检查 favicon：`/favicon.ico` 需复制到 `public/favicon.ico`

> 建议用正则批量替换后逐页人工抽查，避免遗漏导致 404。

---

## 第 7 步：入口与容器收尾

- 根目录「旧」Docker 文件已删，统一使用 `docker/`（✅完成）：`docker/Dockerfile`、`docker/docker-compose.yml`、`docker/docker-entrypoint.sh`、`docker/apache-vhost.conf`、`docker/.dockerignore`。
- `public/.htaccess` ✅已建（禁止列目录、禁止访问隐藏文件）。
- 前端入口搬入 `public/` 后，Apache 文档根（已指向 `/var/www/html/public`）即可服务。
- 核对 `$_SERVER['DOCUMENT_ROOT']` 依赖：原 `Nav.php`、入口用 `DOCUMENT_ROOT` 拼绝对路径，新结构中一律改为基于 `__DIR__` 或 `public/` 的相对引入（`Auth.php` 已示范）。
- 若无特殊需求可省略 `routes.php`（保持"直接访问 .php 文件"的原有工作方式，减少回归面）。

---

## 第 8 步：回归与清理

1. **语法回归**：全仓 `php -l`（对所有 `app/`、`public/`、`admin/` php 文件）。
2. **数据层回归**：CLI 用 SQLite 兼容层 + `database/love_db.sql` 播种，跑通全部 Model 查询（方法与 `storage/` 冒烟一致）。
3. **接口回归**：CLI 模拟 `$_GET/$_POST` 冒烟 `public/getPhotos.php` 分页、留言提交、后台登录。
4. **容器冒烟（需 Docker 环境）**：`docker compose -f docker/docker-compose.yml up -d --build`，验证首页、相册、留言、后台 CRUD、安全码校验。
5. **清理旧目录**：删除 `admin/`、根目录 php 页面、`Style/`、`Botui/`、`data/`（确认已全部迁移）。
6. **项目级 review**：整体 diff 审查，确认无遗漏引用、无残留临时文件。

---

## 已完成（第 0~3 步，✅ 已落地并验证）

1. **第 0 步 安全基线**：新分支 `refactor/restructure`，全量 include/require 引用清单扫描。
2. **第 1 步 Docker 文档根**：`docker/apache-vhost.conf` 文档根指向 `public/`，拦截 `app|storage|database|docker`；`docker/Dockerfile` / `docker-compose.yml` / `docker-entrypoint.sh` / `.dockerignore` 迁移至 `docker/`，路径指向 `storage/data` 与 `database/love_db.sql`。
3. **第 2 步 数据库层抽离**：`app/config/config.php`（统一配置）、`app/config/database.php`（唯一连接，`$connect` 与 `$conn` 同源，消除双连接）、`app/Core/Database.php`（查询封装）、`app/Core/SqliteCompat.php`（迁移）、`app/Models/`（9 模型 + 基类）。
4. **第 3 步 公共层抽离**：`app/Core/helpers.php`（原 Function.php）、`app/Core/ipcheck.php`（原 ipjc.php）、`app/Admin/middleware/Auth.php`（原 Nav.php 登录校验）；`database/love_db.sql` 迁移并同步旧 `admin/Config_DB.php` 引用。
5. **收尾加固**：`.gitignore` / `.dockerignore` 适配 `storage/`；README 更新结构说明与新 Docker 用法；根目录旧 Docker 文件清理。

> 验证情况：`app/` 全部 `php -l` 通过；CLI 用 SQLite 兼容层 + `database/love_db.sql` 成功播种并跑通 7 个核心 Model 查询；现有旧功能文件未被动到、旧连接链仍可用。
