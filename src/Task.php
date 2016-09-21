<?php

namespace Plp\Task;

use medoo;

class Task {

    private $config;

    /** @var $db medoo */
    private $db;

    public function __construct(array $config) {
        $this->config = $config;
    }

    private function initConnection() {
        if (empty($this->db)) {
            $this->db = new medoo($this->config);
        }
    }

    public function run() {
        echo 'end';
    }

    public function migrate() {
        $this->initConnection();

        $sql = '
            SET NAMES utf8;

            CREATE TABLE `task` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `account_id` int(10) unsigned DEFAULT NULL,
            `created` datetime DEFAULT NULL,
            `deffer` datetime DEFAULT NULL,
            `type` tinyint(2) DEFAULT NULL,
            `task` varchar(45) DEFAULT NULL,
            `action` varchar(45) DEFAULT NULL,
            `data` text,
            `status` tinyint(2) DEFAULT NULL,
            `retries` tinyint(2) DEFAULT NULL,
            `finished` datetime DEFAULT NULL,
            `result` text,
            PRIMARY KEY (`id`),
            KEY `status` (`status`),
            KEY `deffer` (`deffer`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            INSERT INTO `task` (`id`, `account_id`, `created`, `deffer`, `type`, `task`, `action`, `data`, `status`, `retries`, `finished`, `result`) VALUES
            (2971220, 70748,\'2016­02­14 13:09:15\', NULL, NULL, \'integration\', \'process\', \'{\"integration_id\":3312,\"lead_id\":\"2999670\"}\', 0, 0, NULL, NULL),
            (2971206, 80034,\'2016­02­14 13:08:16\', NULL, NULL, \'message\', \'sms\', \'{\"number\":\"89111111119\",\"message\":\"Заявка с ru.ru\\nвячеслав \\n\"}\', 0, 0, NULL, NULL),
            (2971187, 81259,\'2016­02­14 13:06:42\', NULL, NULL, \'account\', \'bill\', \'{\"bill_id\":\"82029\"}\',0, 0, NULL, NULL),
            (2971123, 9608, \'2016­02­14 13:01:58\', NULL, NULL, \'integration\', \'process\', \'{\"integration_id\":2845,\"lead_id\":\"2999571\"}\', 0, 0, NULL, NULL),
            (2971122, 9608, \'2016­02­14 13:01:53\', NULL, NULL, \'integration\', \'process\', \'{\"integration_id\":2987,\"lead_id\":\"2999570\"}\', 0, 0, NULL, NULL),
            (2971107, 83992,\'2016­02­14 13:01:03\', NULL, NULL, \'domain\', \'addzone\', \'{\"domain\":\"mydomain.ru\"}\', 0, 0, NULL, NULL);';

        $this->db->query($sql);
    }
}