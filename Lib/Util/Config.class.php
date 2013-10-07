<?php

class Config {

    private static $c;

    public static function get($key) {
        if (!isset($c)) {
            include 'config.php';
            $c = $config;
        }
        return $c[$key];
    }

}

?>