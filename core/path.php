<?php
/**
 * Path class handles path related operations, such as
 * building a path to a directory or file, getting the
 * root folder of the project, and getting the relative
 * path to the caller's location.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Path
 */
class Path {

    /**
     * Number of call stack levels to go back
     * to get the path to the caller.
     * 
     * @var int
     */
    static private int $levels = 2;

    /**
     * Builds a path from root folder of the project to
     * the specified directory and file if provided.
     * 
     * @param string $directory
     * The directory to append to the path, often used
     * with Directory::get() function.
     * 
     * @param string $file 
     * The file name to append to the path
     * 
     * @return string
     * The path to the root folder of the project
     */
    public static function build(string $directory, string $file = ""): string {
        return Path::root() . $directory . (!empty($file) ? '/' . $file : '');
    }

    /**
     * Get the relative path from the caller's location.
     *
     * @param int $remove 
     * The number of directories to remove from the path (default: 0)
     * 
     * @param string $file 
     * Optional file name to append to the path (default: '')
     *
     * @return string 
     * The calculated relative path
     */
    public static function relative(int $remove = 0, string $file = ''): string {

        // Get the call stack and extract the path to the caller
        // directory. Replace backslashes with forward slashes.
        $stack     = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, Path::$levels);
        $path      = str_replace('\\', '/', $stack[0]['file']);
        $directory = substr($path, 0, strrpos($path, '/'));

        // Remove the specified number of directories from the path
        if ($remove > 0) {
            $directory = Path::move($remove, $directory);
        }

        // Return the path with the optional file name appended
        return $directory . (!empty($file) ? '/' . $file : '');
    }

    /**
     * Get the root folder of the project.
     * 
     * @return string
     * The root folder of the project
     */
    public static function root(): string {
        return Path::relative(1);
    }

    /**
     * Get the framework folder.
     * 
     * @return string
     * The framework folder
     */
    public static function framework(): string {
        return Path::build(Folder::getPath('framework'));
    }

    /**
     * Get the path to the caller's directory.
     * 
     * @param int $remove 
     * The number of directories to remove from the path (default: 0)
     * 
     * @return string
     * The path to the caller's directory
     */
    private static function move(int $remove, string $directory): string {
        for ($i = 0; $i < $remove; $i++) {
            $directory = substr($directory, 0, strrpos($directory, '/'));
        }
        return $directory;
    }

}