<?php

namespace Plp\Task;

class message {
    public static function sms($data) {
        $random = mt_rand(1, 10);

        switch ($random) {
            case 1:
                throw new UserException('userException');
                break;
            case 2:
                throw new FatalException('fatalException');
        }

        return ['result' => $random];
    }
}