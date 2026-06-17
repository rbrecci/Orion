<?php
defined('ORION') || exit('Acesso negado.');

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    public function log($action, $userId = null, $entityType = null, $entityId = null, $description = null)
    {
        $this->run(
            "INSERT INTO activity_logs
                (user_id, action, entity_type, entity_id, description, ip_address, user_agent)
             VALUES (?,?,?,?,?,?,?)",
            [
                $userId,
                $action,
                $entityType,
                $entityId,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]
        );
    }

    public function recent($limit = 8)
    {
        $limit = (int) $limit;
        return $this->select(
            "SELECT l.action, l.description, l.created_at, u.username
             FROM activity_logs l
             LEFT JOIN users u ON u.id = l.user_id
             ORDER BY l.created_at DESC
             LIMIT {$limit}"
        );
    }
}
