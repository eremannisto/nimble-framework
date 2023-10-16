<?php declare(strict_types=1);

/** 
 * This class provides file related methods, such as getting 
 * the file mod time, size, mime type, contents, and version.
 */
class File {

    public static function exists(string $path): bool{
        $path = Path::root($path);
        return file_exists($path);
    }

    /**
     * Get the size of a file.
     * 
     * @param string $path
     * The path to the file.
     * 
     * @return int|null
     * The file size, or null if the file does not exist.
     */
    public static function size(string $path): ?int {
        $path = Path::root($path);
        return file_exists($path) ? filesize($path) : null;
    }

    /**
     * Get the contents of a file.
     * 
     * @param string $path
     * The path to the file.
     * 
     * @return string|null
     * The file contents, or null if the file does not exist.
     */
    public static function contents(string $path): ?string {
        $path = Path::root($path);
        return file_exists($path) ? file_get_contents($path) : null;
    }

    /**
     * Get the mod time of a file.
     * 
     * @param string $path
     * The path to the file.
     * 
     * @return int|null
     * The file mod time, or null if the file does not exist.
     */
    public static function modtime(string $path): ?int {
        $path = Path::root($path);
        return file_exists($path) ? filemtime($path) : null;
    }

    /**
     * Get the version of a file.
     * 
     * @param string $path
     * The path to the file.
     * 
     * @return string|null
     * The file version, or null if the file does not exist.
     */
    public static function version(string $path): ?string {
        $modified = File::modtime($path);
        $path     = str_replace(Path::public(), '', Path::root($path));
        return $modified !== null ? sprintf("%s?v=%d", $path, $modified) : null;
    }

    /**
     * Get the file extension.
     * 
     * @param string $path
     * The path to the file.
     * 
     * @return string|null
     * The file extension, or null if the file does not exist.
     */
    public static function extension(string $path): ?string {
        $path = Path::root($path);
        return file_exists($path) ? pathinfo($path, PATHINFO_EXTENSION) : null;
    }

    /**
     * Set a file parameter.
     *
     * @param string $parameter 
     * The parameter to set. Valid values are 'modtime' and 'contents'.
     * 
     * @param string|null $path
     * The path to the file.
     * 
     * @param mixed $value
     * The value to set the parameter to.
     * 
     * @return bool|null 
     * Returns true if the parameter was set successfully, false if the file does
     * not exist, or null if the parameter is not valid.
     */
    public static function set(string $parameter, ?string $path, mixed $value): bool{

        // Get the path to the file:
        $path = Path::root($path);

        switch ($parameter) {

            // Set the file modtime:
            case 'modtime':
                return file_exists($path) ? touch($path, $value) : null;

            // Set the file contents:
            case 'contents':
                return file_exists($path) ? file_put_contents($path, $value) : null;

            default:
                Report::warning("The parameter '$parameter' is not valid.");
                return null;
        }

    }

}


