<?php
/**
 * Database - single PDO connection (Singleton) + prepared statement helper.
 * Every query uses positional placeholders:  VALUES (?, ?, ?, ?)
 */
class Database
{
    private static ?PDO $pdo = null;

    private function __construct() {}

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                exit('Database connection failed. Check config/config.php.');
            }
        }
        return self::$pdo;
    }

    /** Prepare + bind (?) placeholders + execute. */
    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connect()->prepare($sql);
        foreach (array_values($params) as $i => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : (is_null($value) ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue($i + 1, $value, $type);
        }
        $stmt->execute();
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row ?: null;
    }

    public static function fetchValue(string $sql, array $params = []): int
    {
        return (int) self::run($sql, $params)->fetchColumn();
    }
}
