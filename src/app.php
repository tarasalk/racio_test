<?php

namespace Plp\Task;

use medoo;

class App {

    /** @var array */
    private $config;

    /** @var $db medoo */
    private $db;

    const CLASS_NAMESPACE = '\Plp\Task\\';

    const TASK_STATUS_FAIL = -1;
    const TASK_STATUS_QUEUE = 0;
    const TASK_STATUS_RUN = 1;
    const TASK_STATUS_COMPLETE = 2;

    const TABLE_NAME = 'task';

    public function __construct(array $config) {
        $this->config = $config;
    }

    private function initConnection() {
        if (empty($this->db)) {
            $this->db = new medoo($this->config);
        }
    }

    public function run() {
        $this->initConnection();

        while (true) {
            $aTask = $this->getTask();

            if (!empty($aTask)) {
                $this->runTask($aTask);
            }
            else {
                echo date('d.m.Y H:i:s') . " no tasks available\n";
                sleep(10);
            }
        }
    }

    private function getTask() {
        return $this->db->get(self::TABLE_NAME, '*', [
            'AND' => [
                'status' => self::TASK_STATUS_QUEUE,
                'OR' => [
                    '#deffer[<]' => 'NOW()',
                    'deffer' => null
                ],
            ],
            'ORDER' => 'id'
        ]);
    }

    private function runTask(array $aTask) {
        $result = '';

        try {
            $this->db->update(self::TABLE_NAME, [
                'status' => self::TASK_STATUS_RUN
            ], [
                'id' => $aTask['id']
            ]);

            $class =  self::CLASS_NAMESPACE . $aTask['task'];
            $method = $aTask['action'];
            $data = json_decode($aTask['data'], true);

            $result = $class::$method($data);

            $this->db->update(self::TABLE_NAME, [
                'status' => self::TASK_STATUS_COMPLETE,
                'result' => json_encode($result)
            ], [
                'id' => $aTask['id']
            ]);
        }
        catch (UserException $e) {
            $result = ['message' => $e->getMessage()];

            if ($aTask['retries'] < 2) {
                $this->taskDeffered($aTask, $result);
            }
            else {
                $this->taskFailed($aTask, $result);
            }
        }
        catch (FatalException $e) {
            $result = [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];

            $this->taskFailed($aTask, $result);
        }

        printf("%s id: %s, task: %s, action: %s, result: %s\n",
            date('d.m.Y H:i:s'), $aTask['id'], $aTask['task'], $aTask['action'], print_r($result, true));
    }

    private function taskDeffered(array $aTask, $result) {
        $this->db->update(self::TABLE_NAME, [
            'retries[+]' => 1,
            'result' => json_encode($result),
            "deffer" => date('Y-m-d H:i:s', time() + 60*60),
            'status' => self::TASK_STATUS_QUEUE
        ],[
            'id' => $aTask['id']
        ]);
    }

    private function taskFailed(array $aTask, $result) {
        $this->db->update(self::TABLE_NAME, [
            'status' => self::TASK_STATUS_FAIL,
            'result' => json_encode($result)
        ],[
            'id' => $aTask['id']
        ]);

        error_log(implode(', ', $result), 0);
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