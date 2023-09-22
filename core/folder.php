<?php declare(strict_types=1);

/**
 * Directories class handles all directories related methods,
 * such as getting and setting the path of the directories.
 */
class Folder {

    /**
     * Base path in the configuratio file, where
     * all the folder paths are stored.
     * 
     * @var string $base
     */
    private static string $base = 'application->folders->';

    /**
     * Returns the path of a directory based on its name and an optional subpath.
     *
     * @param string $folder
     * The key of the folder path to retrieve.
     * 
     * @param string|null $path (optional)
     * An optional subpath to append to the directory path.
     * 
     * @return string|null 
     * The full path of the directory, including the optional subpath, 
     * or null if the directory is not found.
     */
    public static function getPath(string $folder, ?string $path = NULL): ?string {
        $folder = Config::get(Folder::$base . $folder);
        return $path === NULL ? $folder : sprintf("%s%s", $path, $folder);
    }

    /**
     * Sets the path of the requested directory.
     * 
     * @param string $folder
     * The folder key to set the path for.
     * 
     * @param string $path
     * The path to set for the requested folder.
     */
    public static function setPath(string $folder, string $path): void {
        Config::set(Folder::$base . $folder, $path);
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
    public static function exists(string $folder): bool {
        return (Folder::getPath($folder) === NULL) 
        ? FALSE : is_dir(Folder::getPath($folder, Path::root()));
    }

}