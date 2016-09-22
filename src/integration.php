<?php

namespace Plp\Task;

class integration {
    public static function process($data) {
        $random = mt_rand(2, 4);

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