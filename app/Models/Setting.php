<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM {$this->table}");
        $rows = $stmt->fetchAll();

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM {$this->table} WHERE setting_key = :key");
        $stmt->execute(['key' => $key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? $value : $default;
    }

    public function set(string $key, string $value): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (setting_key, setting_value)
             VALUES (:key, :value)
             ON DUPLICATE KEY UPDATE setting_value = :value2"
        );
        return $stmt->execute(['key' => $key, 'value' => $value, 'value2' => $value]);
    }
}
