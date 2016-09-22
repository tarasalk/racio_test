<?php

namespace Plp\Task;

class message {
    public static function sms($data) {
        $random = mt_rand(1, 10);

        return ['result' => $random];
    }
}