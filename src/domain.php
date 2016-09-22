<?php

namespace Plp\Task;

class domain {
    public static function addzone($data) {
        $random = mt_rand(1, 10);

        return ['result' => $random];
    }
}