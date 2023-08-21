<?php declare(strict_types=1);

/** 
 * This class provides file related methods, such as getting 
 * the file mod time, size, mime type, contents, and version.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  File
 */
class File {

    /**
     * Fetch a client file content, for example a component or page
     * stylesheet or a script, that is outside of the public root folder.
     * 
     * @param string $file
     * The file to fetch.
     * 
     * @param string $type
     * The type of the file to fetch. Valid values are 'components' and 'pages'.
     * 
     * @return string|null
     * The file contents, or null if the file does not exist.
     */
    public static function getClientFile(): void {

        $type  = Request::req("GET", "type");
        $path  = Request::req("GET", "path"); 

        // List of allowed files:
        $allowed = [
            "path" => ['components', 'pages'],
            "type" => ['text/css',   'text/javascript'],
            "ext"  => ['css',        'js']
        ];

        // Validate the type:
        if (!in_array($type, $allowed['type'])) {
            Report::warning("Invalid type '$type' for a file request.");
            return;
        }

        // Validate the source folder:
        $exploded = explode('/', $path);
        if (!in_array($exploded[0], $allowed['path'])) {
            Report::warning("Invalid path '$path' for a file request.");
            return;
        }

        // Get the extension from the path name by getting
        // the last word after the last dot, and validate it:
        $extension = explode('.', $path);
        if (!in_array($extension[count($extension) - 1], $allowed['ext'])) {
            Report::warning("Invalid extension for file '$path'.");
            return;
        }

        // Get the path to the file:
        $path = sprintf("%s/%s", Folder::getPath("src", Path::root()), $path);
        if (!file_exists($path)) {
            Report::warning("The file '$path' does not exist.");
            return;
        }

        // Set correct headers and echo the file contents:
        header("Content-Type: $type");
        echo(file_get_contents($path));
        return;
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
        $path = Path::root() . $path;
        return file_exists($path) ? filesize($path) : null;
    }

    /**
     * Get the mime type of a file.
     * 
     * @param string $path
     * The path to the file.
     * 
     * @return string|null
     * The file mime type, or null if the file does not exist.
     */
    public static function mime(string $path): ?string {
        $path = Path::root() . $path;
        return file_exists($path) ? mime_content_type($path) : null;
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
        $path = Path::root() . $path;
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
        $path = Path::root() . $path;
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
        return $modified !== null ? sprintf("%s?version=%d", $path, $modified) : null;
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
        $path = Path::root() . $path;
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
        $path = Path::root() . $path;

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

    /**
     * Load a file and get its contents. Works for images and
     * other files that are not PHP files. Restricts moving backwords
     * in the directory structure.
     */
    public static function load(): void {
        // Not implemented yet
    }
}


