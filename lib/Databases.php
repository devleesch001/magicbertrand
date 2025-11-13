<?php
declare(strict_types=1);

namespace lib;

use RuntimeException;
use SQLite3;

class Databases
{
    private static function mb_get_db_path(): string
    {
        $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';
        if (!is_dir($baseDir)) {
            @mkdir($baseDir, 0775, true);
        }

        if (is_dir($baseDir) && is_writable($baseDir)) {
            return $baseDir . DIRECTORY_SEPARATOR . 'db.sqlite3';
        }

        // Fallback: /tmp
        $tmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
        return $tmp . DIRECTORY_SEPARATOR . 'magicbertrand_visits.sqlite3';
    }

    private static function mb_open_db(): SQLite3
    {
        if (!class_exists('SQLite3')) {
            throw new RuntimeException("L'extension SQLite3 n'est pas installée sur ce serveur.");
        }

        $dbPath = self::mb_get_db_path();

        $db = new SQLite3($dbPath, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);


        // Réglages perfs/concurrence
        $db->busyTimeout(3000);
        $db->exec('PRAGMA journal_mode = WAL;');
        $db->exec('PRAGMA synchronous = NORMAL;');
        $db->exec('PRAGMA foreign_keys = ON;');

        // Schéma minimal (une seule ligne)
        $db->exec('CREATE TABLE IF NOT EXISTS counter (
        name VARCHAR(32) PRIMARY KEY,
        count INTEGER NOT NULL DEFAULT 0
        );');

        $db->exec("INSERT OR IGNORE INTO counter (name, count) VALUES ('visite', 0);");


        $db->exec('CREATE TABLE IF NOT EXISTS unique_visite (
    hkey   CHAR(128) PRIMARY KEY,
    count INTEGER NOT NULL DEFAULT 0
);');

        return $db;
    }

    public static function increment_visit_count(): int
    {
        $db = self::mb_open_db();

        // Incrémente le compteur
        $db->exec("UPDATE counter SET count = count + 1 WHERE name = 'visite';");

        // Récupère le compteur actuel
        $res = $db->query("SELECT count FROM counter WHERE name = 'visite';");
        $row = $res ? $res->fetchArray(SQLITE3_NUM) : [0];

        return (int)($row[0] ?? 0);
    }

    /**
     * Incrémente le compteur pour une clé unique
     *
     * @param string $hkey
     * @return int Nouveau compteur
     */
    public static function increment_unique_visit_count(string $hkey): int
    {
        $db = self::mb_open_db();
        // INSERT ou UPDATE atomique
        $stmt = $db->prepare("
            INSERT INTO unique_visite (hkey, count)
            VALUES (:hkey, 1)
            ON CONFLICT(hkey) DO UPDATE SET count = count + 1
        ");
        $stmt->bindValue(':hkey', $hkey, SQLITE3_TEXT);
        $stmt->execute();

        // Récupère le compteur actuel
        $stmt = $db->prepare("SELECT count FROM unique_visite WHERE hkey = :hkey");
        $stmt->bindValue(':hkey', $hkey, SQLITE3_TEXT);
        $res = $stmt->execute();
        $row = $res ? $res->fetchArray(SQLITE3_NUM) : [0];

        return (int)($row[0] ?? 0);
    }

    public static function get_visit_count(): int
    {
        $db = self::mb_open_db();
        $res = $db->query('SELECT count FROM counter WHERE id = 1;');
        $row = $res ? $res->fetchArray(SQLITE3_NUM) : [0];
        return (int)($row[0] ?? 0);
    }

    public static function get_unique_visit_count()
    {
        $db = self::mb_open_db();
        $res = $db->query('SELECT COUNT(*) FROM unique_visite;');
        $row = $res ? $res->fetchArray(SQLITE3_NUM) : [0];
        return (int)($row[0] ?? 0);
    }

    public static function get_unique_visit_sum()
    {
        $db = self::mb_open_db();
        $res = $db->query('SELECT SUM(count) FROM unique_visite;');
        $row = $res ? $res->fetchArray(SQLITE3_NUM) : [0];
        return (int)($row[0] ?? 0);
    }

}
