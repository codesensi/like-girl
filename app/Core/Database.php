<?php
/*
 * Like Girl — 数据库访问封装（轻量）
 *
 * 统一封装底层 mysqli（原生或 SQLite 兼容层），提供：
 *   - fetchAll / fetchOne：查询多行 / 单行
 *   - execute：写操作（INSERT/UPDATE/DELETE），返回影响行数与自增 ID
 * 供 Models 层使用。业务代码建议通过 Models 访问数据，避免直接拼 SQL。
 */

class Database
{
    /** @var mysqli|LikeGirlSqliteConnection 全局数据库连接 */
    protected $db;

    public function __construct()
    {
        global $connect;
        $this->db = $connect;
    }

    /**
     * 执行查询并返回全部行（关联数组）。
     */
    public function fetchAll($sql)
    {
        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            return [];
        }
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * 查询单行（无结果时返回 null）。
     */
    public function fetchOne($sql)
    {
        $rows = $this->fetchAll($sql);
        return $rows ? $rows[0] : null;
    }

    /**
     * 执行写操作；成功返回 true，失败返回 false。
     */
    public function execute($sql)
    {
        return mysqli_query($this->db, $sql) !== false;
    }

    /**
     * 返回最近一次 INSERT 的自增 ID。
     */
    public function lastInsertId()
    {
        return $this->db->insert_id ?? 0;
    }

    /**
     * 转义字符串。
     */
    public function escape($value)
    {
        return mysqli_real_escape_string($this->db, $value);
    }
}
