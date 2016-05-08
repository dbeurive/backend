<?php

return call_user_func(function(\PDO $inPdo) {

    // -----------------------------------------------------------------------------------------------------------------
    // Configuration.
    // -----------------------------------------------------------------------------------------------------------------

    $USER_COUNT = 10;

    // -----------------------------------------------------------------------------------------------------------------
    // Initialization.
    // -----------------------------------------------------------------------------------------------------------------

    $ids = ['users' => [], 'profiles' => []];

    $inPdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    $inPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    \dbeurive\BackendTest\Utils\Pdo::setPdo($inPdo);

    // -----------------------------------------------------------------------------------------------------------------
    // Clean the database.
    // ----------------------------------------------------------------------------------------------------------------

    \dbeurive\BackendTest\Utils\Pdo::delete("DELETE FROM profile");
    \dbeurive\BackendTest\Utils\Pdo::delete("DELETE FROM user");

    // -----------------------------------------------------------------------------------------------------------------
    // Creating users.
    // Please note that we create a special user with no profile.
    // This special user is the one the the last ID (the greatest ID's value).
    // -----------------------------------------------------------------------------------------------------------------

    for ($i=1; $i<=$USER_COUNT+1; $i++) {
        $v = array(
            'login'       => "login${i}",
            'password'    => "password${i}",
            'description' => "description${i}"
        );

        $sql = "INSERT INTO user (login, password, description) " .
            "VALUES " .
            "(:login, :password, :description)";
        \dbeurive\BackendTest\Utils\Pdo::insert($sql, $v);
    }

    $ids['users'] = array_map(function($v) { return $v[0]; },
        \dbeurive\BackendTest\Utils\Pdo::select("SELECT id FROM user ORDER BY id", array(), \PDO::FETCH_NUM));

    // -----------------------------------------------------------------------------------------------------------------
    // Creating users' profiles.
    // -----------------------------------------------------------------------------------------------------------------

    for ($i=0; $i<$USER_COUNT; $i++) {
        $v = array(
            'first_name' => "firstName${i}",
            'last_name'  => "name${i}",
            'fk_user_id' => $ids['users'][$i]
        );

        $sql = "INSERT INTO profile (first_name, last_name, fk_user_id) " .
            "VALUES " .
            "(:first_name, :last_name, :fk_user_id)";
        \dbeurive\BackendTest\Utils\Pdo::insert($sql, $v);
    }

    $ids['profiles'] = array_map(function($v) { return $v[0]; },
        \dbeurive\BackendTest\Utils\Pdo::select("SELECT id FROM profile", array(), \PDO::FETCH_NUM));

    return true;

}, $pdo);


