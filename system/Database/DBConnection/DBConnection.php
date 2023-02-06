<?php

namespace System\Database\DBConnection;

use PDO;
use PDOException;

class DBConnection
{
    private static $dbConnectionInstance = null;

    private function __construct()
    {
    }

    public static function getDbConnectionInstance()
    {
        if (self::$dbConnectionInstance == null) {
            $DbConnectionInstance = new DBConnection();
            self::$dbConnectionInstance = $DbConnectionInstance->dbConnection();
        }
        return self::$dbConnectionInstance;
    }

    private function dbConnection()
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            return new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSERNAME, DBPASSWORD, $options);
        } catch (PDOException $exception) {
            echo "Error in database connection : " . $exception->getMessage();
            return false;
        }
    }

    public static function newInsertId()
    {
        return self::getDbConnectionInstance()->lastInsertId();
    }

}