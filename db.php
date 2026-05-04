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

    $count = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    if ($count === 0) {
        $seed = $pdo->prepare(
            "INSERT INTO events (title, description, category, location, event_date, image)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $rows = [
            ['ملتقى الابتكار الطلابي', 'جلسات عرض مشاريع طلابية وحلول تقنية مبتكرة.', 'ابتكار', 'الجامعة الافتراضية - القاعة الرئيسية', '2026-05-20 10:00:00', 'assets/img/event1.jpg'],
            ['ورشة تطوير الويب', 'تدريب عملي على PHP وواجهات المستخدم الحديثة.', 'تدريب', 'مخبر الحاسوب 2', '2026-05-22 12:00:00', 'assets/img/event2.jpg'],
            ['ندوة الذكاء الاصطناعي', 'محاضرة عن تطبيقات الذكاء الاصطناعي في التعليم.', 'تقني', 'مدرج الهندسة', '2026-05-25 14:00:00', 'assets/img/event3.jpg'],
            ['فعالية رياضية جامعية', 'منافسات ودية بين الفرق الطلابية.', 'رياضة', 'الملعب الجامعي', '2026-05-27 16:00:00', 'assets/img/1759749743_event2.jpg'],
            ['أمسية ثقافية', 'قراءات أدبية وفقرات موسيقية من طلاب الجامعة.', 'ثقافي', 'المسرح الجامعي', '2026-05-29 18:00:00', 'assets/img/1759749733_event3.jpg'],
            ['معرض مشاريع التخرج', 'عرض نماذج من مشاريع تخرج مميزة.', 'أكاديمي', 'مبنى العمادات', '2026-06-01 11:00:00', 'assets/img/1759749565_event1.jpg'],
        ];

        foreach ($rows as $row) {
            $seed->execute($row);
        }
    }
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
