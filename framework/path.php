<?php declare(strict_types=1);

/**
 * Path class handles path related operations, such as
 * building a path to a directory or file, getting the
 * root folder of the project, and getting the relative
 * path to the caller's location.
 * 
 * @version B4.0.0
 */
class Path {

    /**
     * The variable determining how many levels to traverse
     * in the call stack to retrieve the path to the caller
     * is currently set to 2. This value is chosen because the
     * backtrace is initiated from the Path::relative() method,
     * which, in turn, is invoked from the Path::root() method.
     * 
     * @var int
     * The number of levels to traverse in the call stack
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
        return Path::root() . $directory . $file;
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
        if ($remove > 0) $directory = Path::move($remove, $directory);
        
        // Return the path with the optional file name appended
        return $directory . $file;
    }

    /**
     * Get the root folder of the project.
     * 
     * @return string
     * The root folder of the project
     */
    public static function root($file = ""): string {

        // Get the root path from the project folder
        $root = Path::relative(1);
        
        // If the $file already contains the root path, then
        // return the $file as is.
        if (strpos($file, $root) === 0) return $file;

        // Otherwise, return the root path with the $file appended
        return "{$root}{$file}";
    }

    /**
     * Get the path to the caller's directory.
     * 
     * @param string $directory
     * The directory to add to the path.
     * 
     * @return string
     * The path to the caller's directory
     */
    public static function get(string $directory, string $file = ""): string {

        // Get the directory path from the project folder
        $directory = Directories::get($directory);
        if(empty($directory)) {
            Report::warning("Directory '$directory' not found.");
            return "";
        }

        // Return the path to the directory
        return Path::build($directory, $file) ?? "";
    }

    /**
     * Get the framework folder.
     * 
     * @param $file 
     * The file to append to the path.
     * 
     * @return string
     * The framework folder
     */
    public static function framework(string $file = ""): string {
        return Path::build(Directories::get('framework'), $file);
    }

    /**
     * Get the public folder.
     * 
     * @return string
     * The public folder
     */
    public static function public(string $file = ""): string {
        return Path::build(Directories::get('public'), $file);
    }

    /**
     * Preset: Get the assets folder.
     * 
     * @return string
     * The assets folder
     */
    public static function assets(string $file = ""): string {
        return Path::build(Directories::get('assets'), $file);
    }

    /**
     * Preset: Get the styles folder.
     * 
     * @return string
     * The styles folder
     */
    public static function styles(string $file = ""): string {
        return Path::build(Directories::get('styles'), $file);
    }

    /**
     * Preset: Get the scripts folder.
     * 
     * @return string
     * The scripts folder
     */
    public static function scripts(string $file = ""): string {
        return Path::build(Directories::get('scripts'), $file);
    }

    /**
     * Preset: Get the images folder.
     * 
     * @return string
     * The images folder
     */
    public static function images(string $file = ""): string {
        return Path::build(Directories::get('images'), $file);
    }

    /**
     * Preset: Get favicon folder.
     * 
     * @return string
     * The favicon folder
     */
    public static function favicon(string $file = ""): string {
        return Path::build(Directories::get('favicon'), $file);
    }

    /**
     * Preset: Get the src folder.
     * 
     * @return string
     * The src folder
     */
    public static function src(string $file = ""): string {
        return Path::build(Directories::get('src'), $file);
    }

    /**
     * Preset: Get the pages folder.
     * 
     * @return string
     * The pages folder
     */
    public static function pages(string $file = ""): string {
        return Path::build(Directories::get('pages'), $file);
    }

    /**
     * Preset: Get the components folder.
     * 
     * @return string
     * The components folder
     */
    public static function components(string $file = ""): string {
        return Path::build(Directories::get('components'), $file);
    }

    /**
     * Preset: Get the snippets folder.
     * 
     * @return string
     * The snippets folder
     */
    public static function snippets(string $file = ""): string {
        return Path::build(Directories::get('snippets'), $file);
    }

    /**
     * Strip a string from the beginning of a path.
     *
     * @param string $remove 
     * The string to remove from the beginning of the path.
     * 
     * @param string $path 
     * The path to strip from.
     * 
     * @return string 
     * The path with the string removed, or the original path if the
     * remove string was not found.
     */
    public static function strip(string $remove, string $path): string {
        $position = strpos($path, $remove);
        return ($position !== false) ? substr($path, $position + strlen($remove)) : $path;
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