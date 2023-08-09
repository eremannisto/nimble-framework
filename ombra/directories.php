<?php
// Dependancies:
if (!class_exists('Config')) require_once (__DIR__ . '/config.php');

/**
 * Directories class handles all directories related methods,
 * such as getting and setting the path of the directories.
 * 
 * @version     0.0.1
 * @package     Ombra
 * @subpackage  Directories
 */
class Directories {

    /**
     * Returns the path of a directory based on its name and an optional subpath.
     *
     * @param string $directory
     * The name of the directory to retrieve.
     * 
     * @param string|null $path (optional)
     * An optional subpath to append to the directory path.
     * 
     * @return string|null 
     * The full path of the directory, including the optional subpath, 
     * or null if the directory is not found.
     */
    public static function get(string $directory, ?string $path = null): ?string {
        $directory = Config::get("application/directories/$directory");
        return $path === null ? $directory : sprintf("%s%s", $path, $directory);
    }

    /**
     * Sets the path of the requested directory.
     * 
     * @param string $directory
     * The directory name to set the path for.
     * 
     * @param string $path
     * The path to set for the requested directory.
     */
    public static function set(string $directory, string $path): void {
        Config::set("application/directories/$directory", $path);
    }

    /**
     * Check if the requested directory exists.
     * 
     * @param string $directory
     * The directory name to check for.
     * 
     * @return bool
     * True if the directory exists, false otherwise.
     */
    public static function exists(string $directory): bool {
        return is_dir(dirname(__DIR__, 1) . '/' . Directories::get($directory));
    }

    /**
     * Get a directory from framework folder.
     * 
     * @param int $level
     * The level of the directory to retrieve.
     * 
     * @return string
     * The directory path.
     */
    public static function DIR(int $level): string {
        return dirname(__DIR__, $level);
    }
}