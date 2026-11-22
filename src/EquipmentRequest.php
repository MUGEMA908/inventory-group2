<?php
/** Equipment requests: created by Coordinator, decided by HoD. */
class EquipmentRequest
{
    public const PRIORITIES = ['Low', 'Medium', 'High'];

    public static function create(string $name, string $category, int $qty, string $priority, string $why, int $userId): void
    {
        Database::run(
            'INSERT INTO requests (equipment_name, category, quantity, priority, justification, requested_by)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$name, $category, $qty, $priority, $why, $userId]
        );
    }

    public static function countByUser(int $userId): int
    {
        return Database::fetchValue('SELECT COUNT(*) FROM requests WHERE requested_by = ?', [$userId]);
    }

    public static function paginateByUser(int $userId, int $limit, int $offset): array
    {
        return Database::fetchAll(
            'SELECT r.*, d.full_name AS decider FROM requests r
               LEFT JOIN users d ON d.id = r.decided_by
              WHERE r.requested_by = ? ORDER BY r.id DESC LIMIT ? OFFSET ?',
            [$userId, $limit, $offset]
        );
    }

    /** $status = '' means all statuses; $userId = 0 means all users. */
    public static function count(string $status = '', int $userId = 0): int
    {
        return Database::fetchValue(
            'SELECT COUNT(*) FROM requests WHERE (? = "" OR status = ?) AND (? = 0 OR requested_by = ?)',
            [$status, $status, $userId, $userId]
        );
    }

    public static function paginate(string $status, int $limit, int $offset): array
    {
        return Database::fetchAll(
            'SELECT r.*, u.full_name AS requester, d.full_name AS decider
               FROM requests r
               JOIN users u ON u.id = r.requested_by
               LEFT JOIN users d ON d.id = r.decided_by
              WHERE (? = "" OR r.status = ?)
              ORDER BY (r.status = "pending") DESC, r.id DESC LIMIT ? OFFSET ?',
            [$status, $status, $limit, $offset]
        );
    }

    public static function recentApproved(int $limit): array
    {
        return Database::fetchAll(
            'SELECT * FROM requests WHERE status = ? ORDER BY decided_at DESC LIMIT ?',
            ['approved', $limit]
        );
    }

    /** Only pending requests can be decided. Returns true if a row changed. */
    public static function decide(int $id, string $status, int $hodId): bool
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            return false;
        }
        $stmt = Database::run(
            'UPDATE requests SET status = ?, decided_by = ?, decided_at = NOW() WHERE id = ? AND status = ?',
            [$status, $hodId, $id, 'pending']
        );
        return $stmt->rowCount() === 1;
    }
}
