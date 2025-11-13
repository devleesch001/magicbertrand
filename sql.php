<?php
declare(strict_types=1);

// Compteur de visites basé sur SQLite3 (PHP 8+)
// - Crée automatiquement la base et la table au premier lancement
// - Incrément atomique via transaction BEGIN IMMEDIATE
// - Tolérant: fallback vers un dossier temporaire si ./data n'est pas accessible

function mb_get_db_path(): string {
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($baseDir)) {
        @mkdir($baseDir, 0775, true);
    }

    if (is_dir($baseDir) && is_writable($baseDir)) {
        return $baseDir . DIRECTORY_SEPARATOR . 'db.sqlite3';
    }

    // Fallback: /tmp
    $tmp = sys_get_temp_dir();
    return rtrim($tmp, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'magicbertrand_visits.sqlite3';
}

function mb_open_db(): SQLite3 {
    if (!class_exists('SQLite3')) {
        throw new RuntimeException("L'extension SQLite3 n'est pas installée sur ce serveur.");
    }

    $dbPath = mb_get_db_path();
    $db = new SQLite3($dbPath, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    // Réglages perfs/concurrence
    $db->busyTimeout(3000);
    $db->exec('PRAGMA journal_mode = WAL;');
    $db->exec('PRAGMA synchronous = NORMAL;');
    $db->exec('PRAGMA foreign_keys = ON;');

    // Schéma minimal (une seule ligne)
    $db->exec('CREATE TABLE IF NOT EXISTS counter (
        id INTEGER PRIMARY KEY CHECK (id = 1),
        count INTEGER NOT NULL DEFAULT 0
    );');
    $db->exec('INSERT OR IGNORE INTO counter (id, count) VALUES (1, 0);');

    return $db;
}

function increment_visit_count(): int {
    $db = mb_open_db();

    // Incrément atomique
    $ok = $db->exec('BEGIN IMMEDIATE;');
    if (!$ok) {
        // En cas d'échec du lock immédiat, réessayer en simple BEGIN
        $db->exec('BEGIN;');
    }

    $updateOk = $db->exec('UPDATE counter SET count = count + 1 WHERE id = 1;');
    if (!$updateOk) {
        $db->exec('ROLLBACK;');
        // Dernier recours: lire la valeur existante sans incrémenter
        $res = $db->query('SELECT count FROM counter WHERE id = 1;');
        $row = $res ? $res->fetchArray(SQLITE3_NUM) : [0];
        return (int)($row[0] ?? 0);
    }

    $res = $db->query('SELECT count FROM counter WHERE id = 1;');
    $row = $res ? $res->fetchArray(SQLITE3_NUM) : [0];
    $count = (int)($row[0] ?? 0);

    $db->exec('COMMIT;');

    return $count;
}

function get_visit_count(): int {
    $db = mb_open_db();
    $res = $db->query('SELECT count FROM counter WHERE id = 1;');
    $row = $res ? $res->fetchArray(SQLITE3_NUM) : [0];
    return (int)($row[0] ?? 0);
}
