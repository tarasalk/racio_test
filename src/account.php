<?php

namespace Plp\Task;

class account {
    public static function bill($data) {
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