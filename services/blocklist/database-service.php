<?php

namespace gfa_services;
class database_service
{

    public static function check_db_stats(): void
    {

        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $sql = " CREATE TABLE IF NOT EXISTS " . GFA_SUBMISSION_TABLE . " (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `date` datetime NOT NULL,
            `ip` varchar(45) NOT NULL,
            `user_agent` varchar(255) NOT NULL,
            `referrer` varchar(255) NOT NULL,
            `url` varchar(255) NOT NULL,
            `form_id` int(11) NOT NULL, 
            `form_data` longtext NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        dbDelta($sql);
    }
}