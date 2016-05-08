<?php

namespace dbeurive\BackendTest\Utils;

/**
 * Class Pdo
 *
 * This class contains tools that are used to work with PDO.
 *
 * @package dbeurive\BackendTest\Utils
 */

class Pdo
{
    /* @var \PDO $__pdo */
    static $__pdo = null;

    /**
     * Set the PDO handler.
     * @param \PDO $inPdo The PDO handler.
     */
    static public function setPdo(\PDO $inPdo) {
        self::$__pdo = $inPdo;
    }

    /**
     * Execute a request that should return an ID.
     * @param string $inSql Request to execute.
     * @param array $inOptParams Request's parameters.
     * @return bool|int If the ID is found, then the method returns its value.
     *         Otherwise, it returns the value false.
     */
    static public function getId($inSql, $inOptParams=array()) {
        $req = self::$__pdo->prepare($inSql);
        $req->execute($inOptParams);
        $result = $req->fetchAll(\PDO::FETCH_ASSOC);
        if (count($result) < 1) {
            return false;
        }
        return $result[0]['id'];
    }

    /**
     * Execute any request that does not return any data.
     * @param string $inSql Request to execute.
     * @param array $inParams Request's parameters.
     */
    static public function exec($inSql, $inParams=array()) {
        $req = self::$__pdo->prepare($inSql);
        $req->execute($inParams);
    }

    /**
     * Execute a request INSERT
     * @param string $inSql Request to execute.
     * @param array $inParams Request's parameters.
     * return int The method returns the last inserted ID.
     */
    static public function insert($inSql, $inParams=array()) {
        self::exec($inSql, $inParams);
        return self::$__pdo->lastInsertId();
    }

    /**
     * Execute a request SELECT
     * @param string $inSql Request to execute.
     * @param array $inParams Request's parameters.
     * @param int $inOptSelectType This parameter sets the result's format.
     * return array The method returns selected data.
     */
    static public function select($inSql, $inParams=array(), $inOptSelectType=\PDO::FETCH_ASSOC) {
        $req = self::$__pdo->prepare($inSql);
        $req->execute($inParams);
        return $req->fetchAll($inOptSelectType);
    }

    /**
     * Execute a request DELETE
     * @param string $inSql Request to execute.
     * @param array $inParams Request's parameters.
     */
    static public function delete($inSql, $inParams=array()) {
        self::exec($inSql, $inParams);
    }
}