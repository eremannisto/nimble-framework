<?php

/**
 * Report class handles all logging related methods, such as writing
 * to log files, handling exceptions, errors, warnings and notices.
 * 
 * @version 1.0.0
 */
class Report {

    /**
     * Write into a custom log file.
     * 
     * @param string $file
     * The path to the log file.
     * 
     * @param string $message
     * The message to log.
     * 
     * @param string $level
     * The log level, can be "warning", "notice" or "error".
     * 
     * @return void
     * Returns nothing.
     */
    private static function write(string $file, string $message, string $level = 'notice'): bool {

        $extension  = '.log';
        $directory  = dirname(__DIR__, 1) . '/reports';
        $file       = sprintf('%s/%s%s', $directory, $file, $extension);
        $levels     = ['warning', 'notice', 'error', 'exception', "success"];

        // Ensure log directory exists, if not, create it
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        
        // TODO: Regex the file name

        // Validate log level
        if (!in_array(strtolower($level), $levels)) {
            return false;
        }
        
        // Set the color code:
        if     ($level === "success")   { $color = "🟢"; }  // Success
        elseif ($level === "notice")    { $color = "🔵"; }  // Notice
        elseif ($level === "warning")   { $color = "🟠"; }  // Warning
        elseif ($level === "error")     { $color = "🔴"; }  // Error
        elseif ($level === "exception") { $color = "🟣"; }  // Exception

        // Get the file name and line number where the error occurred
        $backtrace  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $line       = $backtrace[1]['line'];
        $fname      = $backtrace[1]['file'];
        
        // Write the message to the log file, including the file name and line number
        $message = sprintf('%s %s [%s] %s Line %d: %s', $color, date('d-m-Y H:i:s'), strtoupper($level), $fname, $line, $message);
        if (!file_put_contents($file, $message . PHP_EOL, FILE_APPEND)) {
            return false;
        }
        
        // Stop script execution if log level is "error"
        if (strtolower($level) === 'error') {
            exit;
        }
        
        return true;
    }

    /**
     * Success handler will be used to trigger a success
     * when a success occurs.
     * 
     * @param string $code
     * The success code.
     * 
     * @param string $message
     * The success message.
     * 
     * @return void
     * Returns nothing.
     */
    public static function success(string $message): void {
        Report::write('report', $message, 'success');
    }

    /** 
     * Notice handler will be used to trigger a notice
     * when a notice occurs.
     * 
     * @param string $code
     * The notice code.
     * 
     * @param string $message
     * The notice message.
     * 
     * @return void
     * Returns nothing.
     */
    public static function notice(string $message): void {
        Report::write('report', $message, 'notice');
    }

    /**
     * Warning handler will be used to trigger a warning
     * when a warning occurs.
     * 
     * @param string $code
     * The warning code.
     * 
     * @param string $message
     * The warning message.
     * 
     * @return void
     * Returns nothing.
     */
    public static function warning(string $message): void {
        Report::write('report', $message, 'warning');
    }

    /** 
     * Error handler will be used to trigger an error
     * when an error occurs.
     * 
     * @param string $code
     * The error code.
     * 
     * @param string $message
     * The error message.
     * 
     * @return void
     * Returns nothing.
     */
    public static function error(string $message): void {
        Report::write('report', $message, 'error');
    }

    /** 
     * Exception handler will be used to trigger an error
     * when an exception occurs.
     * 
     * @param object $exception
     * The exception object.
     * 
     * @return void
     * Returns nothing.
     */
    public static function exception(object $exception): void {

        // Put the code and the message together and log it
        $message = $exception->getMessage();
        Report::write('report', $message, 'exception');
    }
    
    /**
     * Custom log handler will be used to trigger a custom log
     * when a custom log occurs.
     * 
     * @param string $file
     * The log file.
     * 
     * @param string $code
     * The log code.
     * 
     * @param string $message
     * The log message.
     * 
     * @param string $level
     * The log level.
     * 
     * @return void 
     * Returns nothing.
     */
    public static function custom(string $file, string $message, string $level = "notice"): void {

        // Validate levels:
        $level  = strtolower($level);
        $levels = ["warning", "notice", "error", "exception", "success"];
        if (!in_array($level, $levels)) { $level = "notice"; }

        // Write the message to the log file
        Report::write($file, $message, $level);
    }
}