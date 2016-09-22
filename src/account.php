<?php

namespace Plp\Task;

class account {
    public static function bill($data) {
        $random = mt_rand(1, 10);

        return ['result' => $random];
    }
}