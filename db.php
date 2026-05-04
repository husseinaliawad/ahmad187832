<?php
/*
 * SQLite-backed mysqli compatibility layer.
 * Keeps the existing app code unchanged while replacing MySQL dependency.
 */

class DbResultCompat
{
    public int $num_rows = 0;
    private array $rows = [];
    private int $index = 0;

    public function __construct(array $rows)
    {
        $this->rows = array_values($rows);
        $this->num_rows = count($this->rows);
    }

    public function fetch_assoc(): ?array
    {
        if ($this->index >= $this->num_rows) {
            return null;
        }
        return $this->rows[$this->index++];
    }
}

class DbStmtCompat
{
    private PDO $pdo;
    private PDOStatement $stmt;
    private array $boundValues = [];

    public function __construct(PDO $pdo, string $sql)
    {
        $this->pdo = $pdo;
        $this->stmt = $this->pdo->prepare($sql);
    }

    public function bind_param(string $types, ...$vars): bool
    {
        $this->boundValues = array_values($vars);
        return true;
    }

    public function execute(): bool
    {
        return $this->stmt->execute($this->boundValues);
    }

    public function get_result(): DbResultCompat
    {
        $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        return new DbResultCompat($rows ?: []);
    }
}

class DbCompat
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function prepare(string $sql): DbStmtCompat
    {
        return new DbStmtCompat($this->pdo, $sql);
    }

    public function query(string $sql): DbResultCompat|false
    {
        $stmt = $this->pdo->query($sql);
        if ($stmt === false) {
            return false;
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return new DbResultCompat($rows ?: []);
    }
}

function init_sqlite(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL
        )"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            category TEXT,
            location TEXT,
            event_date TEXT NOT NULL,
            image TEXT
        )"
    );
}

$dbPath = getenv('SQLITE_PATH') ?: (__DIR__ . '/database.sqlite');
$dbDir = dirname($dbPath);

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    init_sqlite($pdo);
    $conn = new DbCompat($pdo);
} catch (Throwable $e) {
    http_response_code(500);
    die('فشل الاتصال بقاعدة البيانات SQLite: ' . $e->getMessage());
}
?>
