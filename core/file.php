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
     * The array of stylesheet files to include.
     * Key is the path to the file, and value is the condition under which the file
     * should be included.
     * 
     * @var array
     */
    public static array $styles = [];

    /**
     * The array of javascript files to include.
     * Key is the path to the file, and value is the condition under which the file
     * should be included.
     * 
     * @var array
     */
    public static array $scripts = [];

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

        $asset = ucfirst(Request::req("GET", "asset"));
        $type  = Request::req("GET", "type");
        $src   = Request::req("GET", "src");

        // List of allowed files:
        $allowed = [
            "src"  => ['components', 'pages'],
            "type" => ['text/css',   'text/javascript']
        ];

        // Validate the asset:
        if (empty($asset) || strpos($asset, ' ') !== FALSE) {
            Report::warning("Invalid asset '$asset' for a client file request.");
            return;
        }

        // Validate the type:
        if (!in_array($type, $allowed['type'])) {
            Report::warning("Invalid type '$type' for a file request.");
            return;
        }

        // Validate the source:
        if (!in_array($src, $allowed['src'])) {
            Report::warning("Invalid source '$src' for a file request.");
            return;
        }

        // Set the file extension based on the type and get the file name:
        $extension = ($type === 'text/css') ? 'css' : 'js';
        $file = sprintf("%s.%s", $asset, $extension);

        // Get the path to the file:
        $path = Folder::getPath($src, Path::root());
        $path = sprintf("%s/%s/%s", $path, $asset, $file);

        // If the file does not exist, return null:
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
        return file_exists($path) ? filemtime($path) : NULL;
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
        return file_exists($path) ? filesize($path) : NULL;
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
        return file_exists($path) ? mime_content_type($path) : NULL;
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
        return file_exists($path) ? file_get_contents($path) : NULL;
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
        return $modified !== NULL ? sprintf("%s?version=%d", $path, $modified) : NULL;
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
        return file_exists($path) ? pathinfo($path, PATHINFO_EXTENSION) : NULL;
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
     * Method to check if the set file should be included on the current page.
     * 
     * @param string $condition
     * The condition to check.
     * 
     * @return bool
     * True if the file should be included, false otherwise.
     */
    private static function filter(string $condition = NULL): bool {

        // If no condition is specified, return true (include on every page)
        if ($condition === NULL) return TRUE;

        // If the condition starts with an exclamation point, call itself
        // again with the exclamation point removed (exclude on specific pages)
        if (strpos($condition, "!") === 0) return !File::filter(substr($condition, 1)); 

        // Return true if the current page matches the include condition; otherwise, 
        // return false (include on specific pages)
        return Request::current() === $condition;
    }

    /**
     * Method to generate a file reference based on the specified file name, type,
     * and conditions.
     * 
     * @param string $file
     * The file name.
     * 
     * @param string $type
     * The file type.
     * 
     * @param string|null $conditions
     * The conditions under which the file should be included.
     * 
     * @return string
     * The HTML code for the file reference.
     */
    private static function link(string $file, string $type, ?string $conditions = NULL): mixed {
        
        // Get the file version and filter the conditions, this also makes sure the
        // file exists:
        $version    = File::version($file);
        $conditions = is_array($conditions) ? $conditions : [$conditions];

        // Loop through the conditions and check if the file should be included:
        foreach ($conditions as $condition) {
            if (!File::filter($condition) || $version === NULL) continue; 
        
            // Check the file type and generate the link:
            // if($type == "text/css"){
            //     if($method = "client"){
            //         $output = sprintf('<link rel="stylesheet" type="text/css" href="%s" media="all">', $version);
            //     }
            //     else{
            //         $output = sprintf('<link rel="stylesheet" type="text/css" href="%s" media="all">', $version);
            //     }
            // }
            switch ($type) {
                case $type == 'css' || $type == 'text/css'::
                    $output = sprintf('<link rel="stylesheet" type="text/css" href="%s" media="all">', $version);
                    break;

                case $type == 'js' || $type == 'text/javascript':
                    $output = sprintf('<script defer type="text/javascript" src="%s"></script>', $version);
                    break;

                default:
                    Report::warning("Invalid type '$type' for file '$file'.");
                    break;
            }
        
            return $output;
        }
        
        return NULL;
    }

    /**
     * Add a key/value pair to the either the stylesheets, scripts array variables.
     * 
     * @param string $file
     * The file to add.
     * 
     * @param array|null $condition
     * The condition under which the file should be included.
     * 
     * @param bool $public
     * Whether the file is public or not.
     */
    public static function add(array $data): void {

        switch ($data['type']) {
            case 'text/css':
                File::$styles[] = $data;
                break;

            case 'text/javascript':
                File::$scripts[] = $data;
                break;

            default:
                Report::warning("Invalid file type: '$data[type]'.");
                break;
        }
    }

}

    // /**
    //  * Open a requested file.
    //  * 
    //  * @param string $file
    //  * The file to open.
    //  * 
    //  * @param string $folder
    //  * The folder where the file is located.
    //  * 
    //  * @return bool
    //  * True if the file was opened successfully, false otherwise.
    //  */
    // public static function open(string $file, string $folder = ''): bool {

    //     // Path to the file, this currently only picks
    //     // files from the uploads folder:
    //     $path = dirname(__DIR__, 2) . "/uploads/$folder/$file";
    //     Report::notice("Opening the file '$file' from the '$folder' folder.");

    //     // If the file does not exist, return false:
    //     if (!file_exists($path)) {
    //         Report::warning("The file '$file' does not exist in the '$folder' folder.");
    //         return false;
    //     }

    //     // Get the file extension, this is used to set the mime type:
    //     $extension = pathinfo($path, PATHINFO_EXTENSION);

    //     // Get the mime type:
    //     $mime = mime_content_type($path);

    //     // Set the headers:
    //     header("Content-Type: $mime");
    //     header("Content-Disposition: inline; filename=$file");
    //     header("Content-Length: " . filesize($path));

    //     // Read the file:
    //     readfile($path);

    //     // Return true:
    //     return true;
    // }

