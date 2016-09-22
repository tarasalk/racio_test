<?php

namespace Plp\Task;

class domain {
    public static function addzone($data) {
        $random = mt_rand(1, 3);

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