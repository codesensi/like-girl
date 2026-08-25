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

> 说明：`app/`、`storage/`、`database/` 均位于 Web 根 `public/` 之外，无法被 URL 直接访问，解决了原 `ip.txt` 等敏感文件暴露的问题。

#### 启动方法
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
