## 项目来源

本项目基于：[https://gitee.com/kiCode111/likegirl-stable](https://gitee.com/kiCode111/likegirl-stable) 项目，继续开发。

#### 更新记录
- 修复管理后台无法登录问题
- 删除了部分广告
- 优化登录状态提示
- 增加管理后台登录按钮
- 增加各类操作校验
- 优化留言功能
- 优化大部分文本显示
- 【目录重构】按行业规范重构目录结构（`app/` 应用代码 + `public/` Web 根 + `storage/` 运行时数据），见下方说明

#### 目录结构

```
like-girl/
├── app/                  # 应用代码（不可被 URL 直接访问，仅供内部 include）
│   ├── config/           # 统一配置与数据库连接（唯一入口）
│   ├── Core/             # 基础设施（SqliteCompat/helpers/Database/ipcheck）
│   ├── Models/           # 数据模型（每个表一个）
│   ├── Admin/            # 后台登录鉴权中间件等
│   └── View/             # 视图模板（前端页面迁移中）
├── public/               # Web 根目录（唯一可被 URL 访问的入口）
│   ├── index.php         # 前端入口（前端页面迁移中）
│   ├── admin/            # 后台入口
│   └── assets/           # 静态资源（原 Style/ + Botui/，迁移中）
├── storage/              # 运行时数据（日志 / SQLite / 上传）
├── database/             # 数据库初始化脚本 love_db.sql
├── docker/               # Docker 相关（Dockerfile/compose/entrypoint/apache 配置）
├── admin/                # (旧) 后台目录；迁移完整后移除
└── 根目录 php 页面        # (旧) 前端页面；迁移完整后移除
```

#### 启动方法
- 本地 PHP 开发服务器（不依赖 Docker）
    - 环境要求
        - 安装 PHP 7.4+（推荐 8.x），并确保 `php` 已加入系统 `PATH`，可在命令行执行 `php -v` 验证
        - 启用所需 PHP 扩展（编辑 `php.ini`，去掉对应 `;extension=...` 前的分号，保存后重启 PHP）
            - 默认 SQLite 模式（无需额外配置数据库）：启用 `pdo_sqlite`、`sqlite3`
            - 传统 MySQL 模式（可选）：启用 `mysqli`（或 `pdo_mysql`）
            - 常用依赖：`mbstring`、`curl`（如首页/接口依赖请一并启用）
        - 可用 `php -m` 查看已加载的扩展列表，确认上述扩展已启用
    - 启动命令（在项目根目录执行，Web 根为 `public/`）
        - `php -S 127.0.0.1:8080 -t public`
        - 其他端口示例：`php -S localhost:8000 -t public`
    - 访问地址：`http://localhost:8080`
    - 说明
        - 本地 SQLite 数据库默认在 `storage/data/likegirl.sqlite`（首次访问自动由 `database/love_db.sql` 播种）
        - 数据库、安全码等见 `app/config/config.php`；安全码默认 `Love`
        - 若在 PhpStorm 中运行：在 `Settings → PHP` 配置好 PHP 解释器后，可直接添加 `PHP Built-in Web Server` 运行配置，文档根目录选择 `public/`
- Docker + SQLite（推荐）
    - 构建并运行（文档根已指向 `public/`）：
      - `docker compose -f docker/docker-compose.yml up -d --build`
      - 或直接 `docker compose -f docker/docker-compose.yml up -d`
    - 访问地址：`http://localhost:8080`
    - 后台修改账号、密码等敏感信息时需要输入安全码；默认是 `Love`，可通过环境变量 `LIKEGIRL_SECURITY_CODE` 修改
    - SQLite 数据库默认保存在容器内 `/var/www/html/storage/data/likegirl.sqlite`，Compose 会用 `data` volume 持久化
- 传统 MySQL 方式（可选，数据库脚本已移至 `database/love_db.sql`）
    - 创建 mysql 数据库并导入 `database/love_db.sql`
        - create database 数据库名;
        - use 数据库名;
        - source database/love_db.sql;
    - 配置文件（`app/config/config.php`，旧 `admin/Config_DB.php` 亦可用）
        - 配置数据库、密码等
        - 请认真填写安全码 尽量设置的`复杂难以猜测` 修改密码等敏感信息需输入安全码
- 默认账号密码：`admin`/`love2026`
