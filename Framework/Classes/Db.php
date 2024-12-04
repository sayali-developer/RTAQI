<?php
/**
 * TO USE THIS CLASS SECRETS.PHP FILE MUST HAVE BEEN SETUP PROPERLY
 */

namespace RTAQI\Framework\Classes;

use PDO;

final class Db
{
    /**
     * @return PDO
     */
    private $connection;

    function __construct()
    {
        $this->connection = null;
    }

    function __destruct()
    {
        $this->connection = null;
    }

    function connect(): PDO
    {

        $this->connection = new PDO(
            "mysql:host=" . DB_HOST . "; dbname=" . DB_NAME . "; charset=utf8;",
            DB_USER,
            DB_PASS,
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+05:30'",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );

        return $this->connection;
    }
}