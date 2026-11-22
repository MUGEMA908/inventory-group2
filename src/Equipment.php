<?php
/** Equipment inventory (registered by the Logistic officer). */
class Equipment
{
    public const CATEGORIES = ['Computers','Printers','Networking','Audio-Visual','Electronics','Electrical','Furniture','Other'];
    public const CONDITIONS = ['New','Good','Fair','Needs repair'];

    public static function serialExists(string $serial): bool
    {
        return Database::fetchValue('SELECT COUNT(*) FROM equipment WHERE serial_number = ?', [$serial]) > 0;
    }

    public static function register(string $name, string $category, string $serial, int $qty, string $cond, string $location, string $desc, int $userId): void
    {
        Database::run(
            'INSERT INTO equipment (name, category, serial_number, quantity, condition_status, location, description, registered_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$name, $category, $serial, $qty, $cond, $location, $desc, $userId]
        );
    }

    public static function count(string $search = ''): int
    {
        $like = '%' . $search . '%';
        return Database::fetchValue(
            'SELECT COUNT(*) FROM equipment WHERE name LIKE ? OR serial_number LIKE ? OR category LIKE ?',
            [$like, $like, $like]
        );
    }

    public static function totalUnits(): int
    {
        return Database::fetchValue('SELECT COALESCE(SUM(quantity), 0) FROM equipment');
    }

    public static function paginate(string $search, int $limit, int $offset): array
    {
        $like = '%' . $search . '%';
        return Database::fetchAll(
            'SELECT e.*, u.full_name AS registrar
               FROM equipment e JOIN users u ON u.id = e.registered_by
              WHERE e.name LIKE ? OR e.serial_number LIKE ? OR e.category LIKE ?
              ORDER BY e.id DESC LIMIT ? OFFSET ?',
            [$like, $like, $like, $limit, $offset]
        );
    }
}
