<?php declare(strict_types=1);

/**
 * This class handles debugging.
 * 
 * @version     0.0.1
 * @package     Ombra
 * @subpackage  Debug
 */
class Debug {

    /**
     * This method is used to log a debug message.
     * Its purely for better readability and uses
     * the Report::debug() method.
     * 
     * @param string $code
     * The debug code.
     * 
     * @param string $message
     * The debug message.
     * 
     * @return void
     * Returns nothing.
     */
    public static function log(string $message, bool $switch = true): void {
        Report::debug($message, $switch);
    }

    /**
     * This method is used to log a debug message that
     * is an array.
     * 
     * @param string $code
     * The debug code.
     * 
     * @param array $message
     * The debug message.
     * 
     * @return void
     * Returns nothing.
     */
    public static function array(array $message, bool $switch = true): void {
        JSON::encode($message);
        Report::debug($message, $switch);
    }

}