<?php

declare(strict_types=1);

namespace VeloCMS\Modules\Contact\Models;

use VeloCMS\Core\Model;

class ContactModel extends Model
{
    protected string $table = 'velocms_contact_messages';

    /**
     * Count messages sent from a given IP in the last hour.
     */
    public function countRecentByIp(string $ip, int $windowMinutes = 60): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE ip_address = :ip
               AND created_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)
               AND deleted_at IS NULL"
        );
        $stmt->execute([':ip' => $ip, ':minutes' => $windowMinutes]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Store a new contact message.
     *
     * @return int Inserted record ID
     */
    public function create(
        string $name,
        string $email,
        string $subject,
        string $message,
        string $ip,
        string $userAgent
    ): int {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (name, email, subject, message, ip_address, user_agent)
             VALUES (:name, :email, :subject, :message, :ip, :ua)"
        );
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':subject' => $subject,
            ':message' => $message,
            ':ip'      => $ip,
            ':ua'      => mb_substr($userAgent, 0, 500),
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Mark a message as read.
     */
    public function markRead(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status = 'read' WHERE id = :id AND status = 'new'"
        );
        $stmt->execute([':id' => $id]);
    }

    /**
     * Mark a message as spam.
     */
    public function markSpam(int $id): void
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status = 'spam' WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
    }

    /**
     * Count unread messages.
     */
    public function countNew(): int
    {
        $stmt = $this->db->query(
            "SELECT COUNT(*) FROM {$this->table} WHERE status = 'new' AND deleted_at IS NULL"
        );
        return (int) $stmt->fetchColumn();
    }

    /**
     * Delete messages older than N days (DSGVO retention).
     */
    public function purgeOlderThan(int $days): int
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET deleted_at = NOW()
             WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
               AND deleted_at IS NULL"
        );
        $stmt->execute([':days' => $days]);
        return (int) $stmt->rowCount();
    }

    /**
     * Paginated list of non-deleted messages, newest first.
     *
     * @return array{rows: array[], total: int}
     */
    public function paginate(int $page = 1, int $perPage = 20, string $status = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = "deleted_at IS NULL" . ($status !== '' ? " AND status = :status" : '');

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$where}");
        $listStmt  = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );

        $params = [];
        if ($status !== '') {
            $params[':status'] = $status;
        }
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Bind LIMIT / OFFSET as integers (PDO requires explicit INT binding for these)
        $listStmt->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $listStmt->bindValue(':offset', $offset,  \PDO::PARAM_INT);
        if ($status !== '') {
            $listStmt->bindValue(':status', $status, \PDO::PARAM_STR);
        }
        $listStmt->execute();
        $rows = $listStmt->fetchAll();

        return ['rows' => $rows, 'total' => $total];
    }
}
