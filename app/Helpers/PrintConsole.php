<?php
namespace App\Helpers;

class PrintConsole
{
    public static function info($msg)
    {
        echo "\e[32m{$msg} \e[39m\n";
    }
    
    public static function completed($msg = "Task")
    {
        self::clearLine();
        echo "{$msg}: \e[32m\xE2\x9C\x94 \e[39m\n";
    }
    
    public static function start($msg = "Task", $custom = 'starting...')
    {
        echo "{$msg}: {$custom}\n";
        // sleep(1);
    }
    
    public static function warning($msg)
    {
        echo "\e[38;5;166m{$msg} \e[39m\n";
    }
    
    public static function error($msg)
    {
        echo "\e[31m{$msg} \e[39m\n";
    }

    public static function failed($msg)
    {
        self::clearLine();
        echo "\e[31m{$msg}: \e[41mfailed\e[49m \e[39m\n";
    }
    
    public static function line($msg)
    {
        echo "\e[39m{$msg} \n";
    }
    
    public static function clearLine($n = 50)
    {
        $spaces = str_repeat(' ', $n);
        echo "\033[A{$spaces}\033[A";
        echo "\n\r";
    }
}

?>