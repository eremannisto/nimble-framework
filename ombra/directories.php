<?php
// Dependancies:
if (!class_exists('Config')) require_once (__DIR__ . '/config.php');

/**
 * Directories class handles all directories related methods,
 * such as getting and setting the path of the directories.
 * 
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Request
 */
class Directories {

    /**
     * Gets the path of the requested directory.
     * 
     * @param string $directory
     * The directory name to get the path for.
     * 
     * @return string|null
     * The path of the requested directory, or null if the directory doesn't exist.
     */
    public static function get(string $directory): ?string {
        return Config::get("directories/$directory") ?? null;
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
        Config::set("directories/$directory", $path) 
        ? Report::success("Successfully set directory '$directory' to '$path'")
        : Report::error("Failed to set directory '$directory' to '$path'");
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
        return file_exists(dirname(__DIR__, 1) . '/' . Directories::get($directory));
    }
}