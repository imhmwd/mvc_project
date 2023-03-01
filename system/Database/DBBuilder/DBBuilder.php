<?php

namespace System\Database\ DBBuilder;

use System\Database\DBConnection\DBConnection;

class DBBuilder
{

    public function __construct()
    {
        $this->createTables();
        die("migrations run successfully!");
    }

    private function createTables()
    {
        $migrations = $this->getMigrations();
        $pdoInstance = DBConnection::getDBConnectionInstance();

        foreach ($migrations as $migration) {
            $statement = $pdoInstance->prepare($migration);
            $statement->execute();
        }
        return true;
    }

    private function getMigrations()
    {
        $oldMigrationsArray = $this->getOldMigrations();
        $migrationsDirectory = BASE_URL . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        $allMigrationsArray = glob($migrationsDirectory . "*.php");
        $newMigrationsArray = array_diff($allMigrationsArray,$oldMigrationsArray);
        $this->putOldMigrations($allMigrationsArray);

        $sqlCodeArray = [];

        foreach ($newMigrationsArray as $fileName){
            $sqlCode = require $fileName;
            $sqlCodeArray[] = $sqlCode[0];
        }

        return $sqlCodeArray;
    }

    private function getOldMigrations()
    {
        $data = file_get_contents(__DIR__ . '/oldTables.db');

        return empty($data) ? [] : unserialize($data);
    }

    private function putOldMigrations($value)
    {
        file_put_contents(__DIR__ . 'oldTables.db', serialize($value));
    }


}