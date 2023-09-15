<?php declare(strict_types=1);

/**
 * The Link class handles all link related methods, such as
 * generating stylesheets and scripts links.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Link
 */
class Link{

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
     * Method to check if the set file should be included on the current page.
     * 
     * @param string $condition
     * The condition to check.
     * 
     * @return bool
     * True if the file should be included, false otherwise.
     */
    public static function filterCondition(string $condition = null): bool {

        // If no condition is specified, return true (include on every page)
        if ($condition === null) return true;

        // If the condition starts with an exclamation point, call itself
        // again with the exclamation point removed (exclude on specific pages)
        if (strpos($condition, "!") === 0) return !Link::filterCondition(substr($condition, 1)); 

        // Return true if the current page matches the include condition; otherwise, 
        // return false (include on specific pages)
        return Request::current() === $condition || Request::current() === Config::get("application/router/index");
    }


    /**
     * Generates a link or script tag based on the given type and URL.
     *
     * @param string $type 
     * The type of the file to link to (e.g. 'text/css', 'text/javascript').
     * 
     * @param string $url 
     * The URL of the file to link to.
     * 
     * @return string 
     * The generated link or script tag.
     */
    private static function generate($type, $url): string {
        switch ($type) {
            case 'text/css':
                return sprintf('<link rel="stylesheet" type="text/css" href="%s">', $url);

            case 'text/javascript':
                return sprintf('<script defer type="text/javascript" src="%s"></script>', $url);

            default:
                Report::warning("Invalid type '$type' for file '$url'.");
                return '';
        }
    }

    /**
     * Generates a server link for a given path and type. This
     * will be used to generate the link to the a file outside
     * of the public directory.
     *
     * @param array $array 
     * An array containing the path and type of the link.
     * 
     * @return string 
     * The generated server link.
     */
    private static function server(array $array): string {
        $path     = $array['path'];
        $type     = $array['type'];
        $version  = File::modtime(Folder::getPath('src') . "/" . $path);
        $exploded = explode('/', $path);

        if (!in_array($exploded[0], ['pages', 'components'])) {
            return '';
        }

        $url = urldecode(URL::get([
            "PORT"  => TRUE,
            "QUERY" => [
                "mode" => "server",
                "path" => $path,
                "type" => $type,
                "version" => $version
            ]
        ]));

        return Link::generate($type, $url);
    }

    /**
     * Generates a client link based on the given array of parameters. 
     * This will be used to generate the link to the a file inside
     * of the public directory.
     *
     * @param array $array 
     * An array of parameters for generating the link.
     *
     * @return string 
     * The generated client link.
     */
    private static function client(array $array): string {
        $root       = Folder::getPath('root');
        $path       = $array['path'];
        $type       = $array['type'];
        $conditions = is_array($array['conditions']) ? $array['conditions'] : [$array['conditions']];
        $version    = File::version($root . $path);

        foreach ($conditions as $condition) {
            if (!Link::filterCondition($condition) || empty($version)) {
                continue;
            }

            // Remove the root path from the version:
            $version = str_replace("$root", '', $version);

            // Generate the link:
            return Link::generate($type, $version);
        }

        return '';
    }

    /**
     * Returns a string containing a link tag based on the given 
     * array of attributes.
     * 
     * @param array $array 
     * An array of attributes for the link tag.

     * @return string 
     * The link tag as a string.
     */
    public static function tag(array $array = []): string {
        if (empty($array) || empty($array['path']) || empty($array['type']) || empty($array['mode'])) {
            return '';
        }

        $mode = $array['mode'];

        if ($mode === 'client') {
            return Link::client($array);
        } 

        elseif ($mode === 'server') {
            return Link::server($array);
        } 

        else {
            Report::warning("Invalid mode '$mode' for file '{$array['path']}'.");
            return '';
        }
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
                Link::$styles[] = $data;
                break;

            case 'text/javascript':
                Link::$scripts[] = $data;
                break;

            default:
                Report::warning("Invalid file type: '$data[type]'.");
                break;
        }
    }
}