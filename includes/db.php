<?php
/**
 * Good Car Imports — Database Connection (PDO)
 */

require_once __DIR__ . '/config.php';

/**
 * Get PDO database connection (singleton pattern)
 */
function getDB(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('Database connection failed. Please check configuration.');
            }
        }
    }

    return $pdo;
}

/**
 * Execute a query and return all rows
 */
function dbFetchAll(string $sql, array $params = []): array {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Execute a query and return a single row
 */
function dbFetchOne(string $sql, array $params = []): ?array {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Execute a query and return the number of affected rows
 */
function dbExecute(string $sql, array $params = []): int {
    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Insert a row and return the last insert ID
 */
function dbInsert(string $table, array $data): int {
    $columns = implode(', ', array_map(fn($col) => "`$col`", array_keys($data)));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
    $stmt = getDB()->prepare($sql);
    $stmt->execute(array_values($data));
    
    return (int) getDB()->lastInsertId();
}

/**
 * Update rows in a table
 */
function dbUpdate(string $table, array $data, string $where, array $whereParams = []): int {
    $setClauses = implode(', ', array_map(fn($col) => "`$col` = ?", array_keys($data)));
    
    $sql = "UPDATE `$table` SET $setClauses WHERE $where";
    $stmt = getDB()->prepare($sql);
    $stmt->execute(array_merge(array_values($data), $whereParams));
    
    return $stmt->rowCount();
}

/**
 * Get total count for pagination
 */
function dbCount(string $table, string $where = '1=1', array $params = []): int {
    $sql = "SELECT COUNT(*) as total FROM `$table` WHERE $where";
    $result = dbFetchOne($sql, $params);
    return (int) ($result['total'] ?? 0);
}

/**
 * Get a setting value from the settings table
 */
function getSetting(string $key, $default = null) {
    $result = dbFetchOne(
        "SELECT setting_value, setting_type FROM settings WHERE setting_key = ?",
        [$key]
    );

    if (!$result) return $default;

    return match ($result['setting_type']) {
        'number'  => (float) $result['setting_value'],
        'boolean' => (bool) $result['setting_value'],
        'json'    => json_decode($result['setting_value'], true),
        default   => $result['setting_value'],
    };
}

/**
 * Update a setting value
 */
function setSetting(string $key, $value): void {
    dbExecute(
        "UPDATE settings SET setting_value = ? WHERE setting_key = ?",
        [(string) $value, $key]
    );
}
