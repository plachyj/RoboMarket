<?php
/**
 * Created by PhpStorm.
 * User: Jarda
 * Date: 1.11.2015
 * Time: 19:38
 */



class Logger {

    public static function debug($message) {
        self::log('DEBUG', $message);
    }

    public static function info($message) {
        self::log('INFO', $message);
    }

    public static function warn($message) {
        self::log('WARN', $message);
    }

    public static function error($message) {
        self::log('ERROR', $message);
    }

    private static function log($level, $message) {
        // jm�no souboru ve form�tu YYYY-mm-dd.log
        $fileName = date('Y-m-d').'.log';
        // otev�en� logu; pokud neexistuje, bude automaticky vytvo�en
        $file = fopen("../Logs/". $fileName, "a");
        // text pro z�pis do logu
        $text = date('d.m.Y H:i:s') . ' ' . $level . ' ' . $message;
        // jm�no skriptu a ��slo ��dku
        $backtrace = debug_backtrace();
        if (count($backtrace)>1) {
            $text .= "\r\n";
            $text .= '('.$backtrace[1]['file'] . ':' . $backtrace[1]['line'] . ')';
        }
        // od��dkov�n� na konci zpr�vy
        $text .= "\r\n";
        fwrite($file, $text);
        fclose($file);
    }
}