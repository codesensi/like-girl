<?php

if (!defined('MYSQLI_ASSOC')) {
    define('MYSQLI_ASSOC', 1);
}

if (!defined('MYSQLI_NUM')) {
    define('MYSQLI_NUM', 2);
}

if (!defined('MYSQLI_BOTH')) {
    define('MYSQLI_BOTH', 3);
}

class LikeGirlSqliteConnection
{
    public $connect_error = '';
    public $error = '';
    public $affected_rows = 0;
    public $insert_id = 0;

    private $pdo = null;

    public function __construct($host = null, $username = null, $password = null, $database = null)
    {
        try {
            $this->pdo = $this->openPdo();
            $this->initializeDatabase();
        } catch (Throwable $exception) {
            $this->connect_error = $exception->getMessage();
            $this->error = $exception->getMessage();
            $this->pdo = null;
        }
    }

    public function set_charset($charset)
    {
        return true;
    }

    public function query($sql)
    {
        if (!$this->pdo) {
            return false;
        }

        try {
            $statement = $this->pdo->query($sql);
            $this->affected_rows = $statement->rowCount();

            if ($statement->columnCount() > 0) {
                return new LikeGirlSqliteResult($statement->fetchAll(PDO::FETCH_BOTH));
            }

            $this->insert_id = $this->pdo->lastInsertId();
            return true;
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function prepare($sql)
    {
        if (!$this->pdo) {
            return false;
        }

        return new LikeGirlSqliteStatement($this, $this->pdo, $sql);
    }

    public function real_escape_string($value)
    {
        return str_replace(array("\x00", "'", '"', "\\"), array("\\0", "''", '\"', "\\\\"), $value);
    }

    public function escape_string($value)
    {
        return $this->real_escape_string($value);
    }

    public function close()
    {
        $this->pdo = null;
        return true;
    }

    private function openPdo()
    {
        $databasePath = $this->databasePath();
        $databaseDir = dirname($databasePath);

        if (!is_dir($databaseDir) && !mkdir($databaseDir, 0775, true) && !is_dir($databaseDir)) {
            throw new RuntimeException("Unable to create SQLite data directory: {$databaseDir}");
        }

        $pdo = new PDO('sqlite:' . $databasePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA busy_timeout = 5000');
        $pdo->exec('PRAGMA journal_mode = WAL');

        return $pdo;
    }

    private function initializeDatabase()
    {
        if (!$this->pdo || $this->hasApplicationTables()) {
            return;
        }

        $seedFile = $this->seedFile();
        if (!is_readable($seedFile)) {
            throw new RuntimeException("SQLite seed file is not readable: {$seedFile}");
        }

        $dump = file_get_contents($seedFile);
        if ($dump === false) {
            throw new RuntimeException("Unable to read SQLite seed file: {$seedFile}");
        }

        $this->pdo->beginTransaction();

        try {
            foreach ($this->buildCreateTableStatements($dump) as $statement) {
                $this->pdo->exec($statement);
            }

            foreach ($this->extractInsertStatements($dump) as $statement) {
                $this->pdo->exec($this->convertMysqlStringsToSqlite($statement));
            }

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    private function hasApplicationTables()
    {
        $statement = $this->pdo->query(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' LIMIT 1"
        );

        return (bool)$statement->fetchColumn();
    }

    private function databasePath()
    {
        global $sqlite_path;

        return getenv('LIKEGIRL_SQLITE_PATH')
            ?: (isset($sqlite_path) ? $sqlite_path : dirname(__DIR__) . '/data/likegirl.sqlite');
    }

    private function seedFile()
    {
        global $sqlite_seed_file;

        return getenv('LIKEGIRL_SQLITE_SEED')
            ?: (isset($sqlite_seed_file) ? $sqlite_seed_file : dirname(__DIR__) . '/love20240612.sql');
    }

    private function buildCreateTableStatements($dump)
    {
        preg_match_all(
            '/CREATE\s+TABLE\s+`([^`]+)`\s*\((.*?)\)\s*ENGINE\s*=/is',
            $dump,
            $matches,
            PREG_SET_ORDER
        );

        $statements = array();
        foreach ($matches as $match) {
            $table = $match[1];
            $columns = array();

            foreach (preg_split('/\R/', $match[2]) as $line) {
                $line = trim(rtrim(trim($line), ','));
                if (!preg_match('/^`([^`]+)`\s+([^\s,]+)(.*)$/is', $line, $columnMatch)) {
                    continue;
                }

                $name = $columnMatch[1];
                $mysqlType = strtolower($columnMatch[2]);
                $rest = $columnMatch[3];

                if ($name === 'id') {
                    $columns[] = $this->quoteIdentifier($name) . ' INTEGER PRIMARY KEY AUTOINCREMENT';
                    continue;
                }

                $sqliteType = strpos($mysqlType, 'int') === 0 ? 'INTEGER' : 'TEXT';
                $notNull = stripos($rest, 'NOT NULL') !== false ? ' NOT NULL' : '';
                $columns[] = $this->quoteIdentifier($name) . ' ' . $sqliteType . $notNull;
            }

            if ($columns) {
                $statements[] = sprintf(
                    "CREATE TABLE IF NOT EXISTS %s (\n  %s\n)",
                    $this->quoteIdentifier($table),
                    implode(",\n  ", $columns)
                );
            }
        }

        return $statements;
    }

    private function extractInsertStatements($dump)
    {
        $inserts = array();

        foreach ($this->splitSqlStatements($dump) as $statement) {
            $statement = trim(preg_replace('/^\s*--.*(?:\R|$)/m', '', $statement));
            if (preg_match('/^\s*INSERT\s+INTO\s+/i', $statement) === 1) {
                $inserts[] = $statement;
            }
        }

        return $inserts;
    }

    private function splitSqlStatements($sql)
    {
        $statements = array();
        $buffer = '';
        $inSingle = false;
        $inDouble = false;
        $escaped = false;
        $length = strlen($sql);

        for ($index = 0; $index < $length; $index++) {
            $char = $sql[$index];
            $buffer .= $char;

            if ($inSingle) {
                if ($escaped) {
                    $escaped = false;
                } elseif ($char === '\\') {
                    $escaped = true;
                } elseif ($char === "'") {
                    $inSingle = false;
                }
                continue;
            }

            if ($inDouble) {
                if ($escaped) {
                    $escaped = false;
                } elseif ($char === '\\') {
                    $escaped = true;
                } elseif ($char === '"') {
                    $inDouble = false;
                }
                continue;
            }

            if ($char === "'") {
                $inSingle = true;
                continue;
            }

            if ($char === '"') {
                $inDouble = true;
                continue;
            }

            if ($char === ';') {
                $statement = trim($buffer);
                if ($statement !== '') {
                    $statements[] = $statement;
                }
                $buffer = '';
            }
        }

        $statement = trim($buffer);
        if ($statement !== '') {
            $statements[] = $statement;
        }

        return $statements;
    }

    private function convertMysqlStringsToSqlite($sql)
    {
        $output = '';
        $length = strlen($sql);

        for ($index = 0; $index < $length; $index++) {
            $char = $sql[$index];

            if ($char !== "'") {
                $output .= $char;
                continue;
            }

            $value = '';
            $index++;

            for (; $index < $length; $index++) {
                $inner = $sql[$index];

                if ($inner === '\\' && $index + 1 < $length) {
                    $index++;
                    $value .= $this->mysqlEscapedCharacter($sql[$index]);
                    continue;
                }

                if ($inner === "'") {
                    break;
                }

                $value .= $inner;
            }

            $output .= "'" . str_replace("'", "''", $value) . "'";
        }

        return $output;
    }

    private function mysqlEscapedCharacter($escaped)
    {
        switch ($escaped) {
            case 'n':
                return "\n";
            case 'r':
                return "\r";
            case 't':
                return "\t";
            case '0':
                return "\0";
            default:
                return $escaped;
        }
    }

    private function quoteIdentifier($identifier)
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
}

class LikeGirlSqliteResult
{
    private $rows;
    private $cursor = 0;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function fetch_array($resultType = MYSQLI_BOTH)
    {
        if (!array_key_exists($this->cursor, $this->rows)) {
            return null;
        }

        $row = $this->rows[$this->cursor++];

        if ($resultType === MYSQLI_ASSOC) {
            return array_filter($row, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        if ($resultType === MYSQLI_NUM) {
            return array_filter($row, 'is_int', ARRAY_FILTER_USE_KEY);
        }

        return $row;
    }

    public function num_rows()
    {
        return count($this->rows);
    }
}

class LikeGirlSqliteStatement
{
    public $error = '';

    private $connection;
    private $pdo;
    private $sql;
    private $boundParams = array();
    private $boundTypes = array();
    private $boundResults = array();
    private $rows = array();
    private $cursor = 0;

    public function __construct(LikeGirlSqliteConnection $connection, PDO $pdo, $sql)
    {
        $this->connection = $connection;
        $this->pdo = $pdo;
        $this->sql = $sql;
    }

    public function bind_param($types, &...$variables)
    {
        $this->boundTypes = str_split($types);
        $this->boundParams = array();

        foreach ($variables as &$variable) {
            $this->boundParams[] = &$variable;
        }

        return true;
    }

    public function bind_result(&...$variables)
    {
        $this->boundResults = array();

        foreach ($variables as &$variable) {
            $this->boundResults[] = &$variable;
        }

        return true;
    }

    public function execute()
    {
        try {
            $statement = $this->pdo->prepare($this->sql);
            foreach ($this->boundParams as $index => &$value) {
                $statement->bindValue(
                    $index + 1,
                    $value,
                    $this->pdoType(isset($this->boundTypes[$index]) ? $this->boundTypes[$index] : 's')
                );
            }

            $statement->execute();
            $this->connection->affected_rows = $statement->rowCount();
            $this->connection->insert_id = $this->pdo->lastInsertId();
            $this->rows = $statement->columnCount() > 0 ? $statement->fetchAll(PDO::FETCH_NUM) : array();
            $this->cursor = 0;

            return true;
        } catch (Throwable $exception) {
            $this->error = $exception->getMessage();
            $this->connection->error = $exception->getMessage();
            return false;
        }
    }

    public function fetch()
    {
        if (!array_key_exists($this->cursor, $this->rows)) {
            return null;
        }

        $row = $this->rows[$this->cursor++];
        foreach ($this->boundResults as $index => &$variable) {
            $variable = isset($row[$index]) ? $row[$index] : null;
        }

        return true;
    }

    public function close()
    {
        $this->rows = array();
        $this->boundParams = array();
        $this->boundResults = array();

        return true;
    }

    private function pdoType($type)
    {
        return $type === 'i' ? PDO::PARAM_INT : PDO::PARAM_STR;
    }
}

if (!class_exists('mysqli')) {
    class mysqli extends LikeGirlSqliteConnection
    {
    }
}

if (!function_exists('mysqli_connect')) {
    function mysqli_connect($host = null, $username = null, $password = null, $database = null)
    {
        $connection = new mysqli($host, $username, $password, $database);

        return $connection->connect_error ? false : $connection;
    }
}

if (!function_exists('mysqli_query')) {
    function mysqli_query(LikeGirlSqliteConnection $connection, $sql)
    {
        return $connection->query($sql);
    }
}

if (!function_exists('mysqli_fetch_array')) {
    function mysqli_fetch_array(LikeGirlSqliteResult $result, $resultType = MYSQLI_BOTH)
    {
        return $result->fetch_array($resultType);
    }
}

if (!function_exists('mysqli_fetch_assoc')) {
    function mysqli_fetch_assoc(LikeGirlSqliteResult $result)
    {
        return $result->fetch_array(MYSQLI_ASSOC);
    }
}

if (!function_exists('mysqli_num_rows')) {
    function mysqli_num_rows($result)
    {
        return $result instanceof LikeGirlSqliteResult ? $result->num_rows() : 0;
    }
}

if (!function_exists('mysqli_real_escape_string')) {
    function mysqli_real_escape_string(LikeGirlSqliteConnection $connection, $value)
    {
        return $connection->real_escape_string($value);
    }
}

if (!function_exists('mysqli_error')) {
    function mysqli_error(LikeGirlSqliteConnection $connection)
    {
        return $connection->error;
    }
}

if (!function_exists('mysqli_close')) {
    function mysqli_close(LikeGirlSqliteConnection $connection)
    {
        return $connection->close();
    }
}
