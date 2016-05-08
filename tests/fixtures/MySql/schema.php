<?php

/**
 * This file returns the schema of the database in plain text.
 */

return [
    "DROP DATABASE IF EXISTS `phptools`",
    "CREATE DATABASE IF NOT EXISTS `phptools`",
    "USE `phptools`",

    "CREATE TABLE IF NOT EXISTS `user` (
        `id`          INT UNSIGNED       NOT NULL AUTO_INCREMENT,
        `login`       VARCHAR(255)       NOT NULL,
        `password`    VARCHAR(255)       NOT NULL,
        `description` VARCHAR(255)       NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE INDEX `login_idx` (`login`)
    ) ENGINE = InnoDB
      CHARACTER SET utf8;",

    "CREATE TABLE IF NOT EXISTS `profile` (
        `id`         INT UNSIGNED       NOT NULL AUTO_INCREMENT,
        `fk_user_id` INT UNSIGNED       NOT NULL,
        `first_name` VARCHAR(255)       NOT NULL,
        `last_name`  VARCHAR(255)       NOT NULL,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`fk_user_id`) REFERENCES `user`(`id`)
    ) ENGINE = InnoDB
      CHARACTER SET utf8;"
];
