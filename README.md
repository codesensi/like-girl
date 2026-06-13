## 项目来源

本项目基于：[https://gitee.com/kiCode111/likegirl-stable](https://gitee.com/kiCode111/likegirl-stable) 项目，继续开发。

#### 更新记录
- 修复管理后台无法登录问题
- 删除了部分广告
- 优化登录状态提示

#### 启动方法
- Docker + SQLite（推荐）
    - 拉取镜像：`docker pull codesensi/like-girl:latest`
      - 运行容器：`docker run -d --name like-girl -p 8080:80 -e LIKEGIRL_SECURITY_CODE=你的安全码 -v /docker/like-girl/data:/var/www/html/data codesensi/like-girl:latest`
      - 或使用 Compose：`docker compose up -d`
    - 访问地址：`http://localhost:8080`
    - 后台修改账号、密码等敏感信息时需要输入安全码；默认是 `Love`，Docker 可通过环境变量 `LIKEGIRL_SECURITY_CODE` 修改
    - SQLite 数据库默认保存在容器内 `/var/www/html/data/likegirl.sqlite`，Compose 会用 `data` volume 持久化
- 传统 MySQL 方式（可选）
    - 作者是采用ngnix+php反向代理
    - 创建mysql数据库并导入`lova_db.sql`文件
        - create database 数据库名;
        - use 数据库名;
        - source sql文件;
    - 配置文件（`admin/Config_DB.php`）
        - 配置数据库、密码等
        - 请认真填写安全码 尽量设置的`复杂难以猜测` 修改密码等敏感信息需输入安全码
- 默认账号密码：`admin`/`love2026`
