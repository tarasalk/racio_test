<?php

namespace Plp\Task;

class integration {
    public static function process($data) {
        $random = mt_rand(1, 10);

        return ['result' => $random];
    }
}