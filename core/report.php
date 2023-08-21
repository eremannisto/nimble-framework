<?php declare(strict_types=1);

/**
 * Report class handles all logging related methods, such as writing
 * to log files, handling exceptions, errors, warnings and notices.
 * Note that this file can't use any dependencies, as it is used
 * by the dependencies themselves.
 * 
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Reports
 */
class Report {

    /**
     * Stores the report data, such as file name, line number,
     * error message and error code...
     */
    public static array $options = [];

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
        Report::write('success', $message, 'success', 0);
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
        Report::write('report', $message, 'notice', 3);
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
        Report::write('report', $message, 'warning', 2);
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
        Report::write('report', $message, 'error', 4);
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
        Report::write('report', $exception->getMessage(), 'exception', 0);
    }
    
    /**
     * Debug handler will be used to trigger a debug
     * when a debug occurs.
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
    public static function debug(string $message, bool $switch = true): void {
        $switch === true ? Report::write('debug', $message, 'debug', 0) : null;
    }

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
    private static function write(string $file, string $message, string $level, int $tab = 0): bool {

        // Get the reports file and directory and ensure the directory exists
        $directory = __DIR__ . "/reports";
        is_dir($directory) ? NULL : mkdir($directory, 0777, TRUE);
        $file = "$directory/$file.log";
        
        // Validate the log level:
        $tabulation = str_repeat(' ', $tab);
        $level      = Report::level($level);

        Report::$options = [
            "LEVEL"     => "[$level]" . $tabulation,
            "COLOR"     => Report::color($level),
            "MESSAGE"   => Report::message($message),
            "TIMESTAMP" => Report::timestamp(),
            "FILE"      => Report::file(Report::backtrace()),
            "LINE"      => Report::line(Report::backtrace()),
        ];

        // Write the report:
        $report = sprintf(
            "%s %s [%s] [%s] [Line %d]: %s", 
            Report::$options['COLOR'],
            Report::$options['LEVEL'], 
            Report::$options['TIMESTAMP'], 
            Report::$options['FILE'], 
            Report::$options['LINE'], 
            Report::$options['MESSAGE']
        );

        // Write the message to the log file, including the file name and line number
        if (!file_put_contents($file, $report . PHP_EOL, FILE_APPEND)) {
            return FALSE;
        }
        
        // Stop script execution if log level is "ERROR"
        if (Report::$options['LEVEL'] === 'ERROR') {
            exit;
        }
        
        // Return true if the message was written successfully
        return TRUE;
    }

    /**
     * Returns the current timestamp in the format 'd.m.Y H:i:s'.
     *
     * @return string The current timestamp.
     */
    private static function timestamp(): string {
        return date('d.m.Y H:i:s');
    }

    /**
     * Returns the corresponding color emoji for a given log level.
     *
     * @param string $level 
     * The log level to get the color for.
     * 
     * @return string 
     * The corresponding color emoji.
     */
    private static function color(string $level): string {
        $colors = [
            "SUCCESS"   => "🟢",
            "NOTICE"    => "🔵",
            "WARNING"   => "🟠",
            "ERROR"     => "🔴",
            "EXCEPTION" => "🟤",
            "DEBUG"     => "⚪",
        ];
        return isset($colors[$level]) ? $colors[$level] : "⚪";
    }

    /**
     * Returns the current file name and line number.
     *
     * @param int $depth 
     * The depth of the backtrace.
     * 
     * @return array 
     * The current file name and line number.
     */
    private static function backtrace(int $depth = 3): array {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $depth);
    }

    /**
     * Returns the current file name
     *
     * @param array $backtrace 
     * The backtrace array.
     * 
     * @return string
     * The current file name.
     */
    private static function file(array $backtrace): string {
        $file     = $backtrace[2]['file'];
        $project  = basename(dirname(__DIR__, 1));
        $position = strpos($file, $project);
        return $position !== FALSE ? '' . substr($file, $position) : $file;
    }

    /**
     * Returns the current line number.
     *
     * @param array $backtrace 
     * The backtrace array.
     * 
     * @return int
     * The current line number.
     */
    private static function line(array $backtrace): int {
        $project = dirname(__DIR__, 1);
        return $backtrace[2]['line'];
    }

    /**
     * Returns the current level.
     * 
     * @return string
     * The current level.
     */
    private static function level($level): string {
        $level  = strtoupper($level);
        $levels = ["SUCCESS", "NOTICE", "WARNING", "ERROR", "EXCEPTION", "DEBUG"];
        return in_array($level, $levels) ? $level : "DEBUG";
    }

    /**
     * Returns the current message.
     * 
     * @return string
     * The current message.
     */
    private static function message($message): string {
        return(ucfirst($message));
    }
}
