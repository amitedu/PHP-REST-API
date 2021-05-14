<?php

include "config/config.php";

class Database
{
    private $DB;

    private $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    public function getConnect()
    {
        if (!$this->DB) {
            try {
                $this->DB = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
                    DB_USER, DB_PASS, $this->options);
            } catch (PDOException $exception) {
                die('something wrong happened' . $exception->getMessage());
            }
        }

        return $this->DB;
    }
}