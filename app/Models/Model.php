<?php
/*
 * Like Girl — 模型基类
 *
 * 每个业务表对应一个继承自本类的模型。约定：
 *   - 子类定义 $table 表名；
 *   - 通过 Database 封装访问全局连接；
 *   - 提供 all()/find()/findOne()/where() 等常用查询。
 */

require_once dirname(__DIR__) . '/Core/Database.php';

abstract class Model
{
    /** @var string 数据表名（子类覆盖） */
    protected $table = '';

    /** @var Database */
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * 返回表内全部行（按 id 倒序）。
     */
    public function all()
    {
        return $this->db->fetchAll("SELECT * FROM `{$this->table}` ORDER BY id DESC");
    }

    /**
     * 按主键获取一行，不存在返回 null。
     */
    public function find($id)
    {
        $rows = $this->where('id', (int) $id, 1);
        return $rows ? $rows[0] : null;
    }

    /**
     * 返回表中第一行（用于 text/diySet 等单行配置表）。
     */
    public function first()
    {
        $rows = $this->db->fetchAll("SELECT * FROM `{$this->table}` ORDER BY id ASC LIMIT 1");
        return $rows ? $rows[0] : null;
    }

    /**
     * 按指定列值过滤查询，$limit 为 null 时返回全部匹配行。
     */
    public function where($column, $value, $limit = null, $order = 'id DESC')
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$column}` = '"
            . $this->db->escape($value) . "' ORDER BY {$order}";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
        }
        return $this->db->fetchAll($sql);
    }

    /**
     * 统计行数。
     */
    public function count($where = '')
    {
        $row = $this->db->fetchOne("SELECT COUNT(*) AS c FROM `{$this->table}` {$where}");
        return $row ? (int) $row['c'] : 0;
    }
}
