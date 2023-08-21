<?php declare(strict_types=1);

/**
 * This class handles how the pages are displayed,
 * and also how the pages are generated.
 * 
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Pages
 */
class Pages {
    
    /**
     * The cached pages object, this is updated when
     * the JSON::get method is called.
     */
    public static mixed $cache = null;

    /**
     * The path to the pages.json file from the
     * root directory of the project.
     */
    private static string $file = "/pages.json";

    /**
     * Retrieves favicons data from the specified location.
     *
     * @param string|null $location 
     * The location of the pages.json file. Defaults to an empty string.
     * 
     * @return mixed 
     * The pages data.
     */
    public static function get(?string $location = ""): mixed {
        $folder = Folder::getPath("pages") . self::$file;
        return JSON::get($location, $folder, get_called_class());
    }

    /**
     * Sets the given data at the specified location in the json file.
     *
     * @param string $location 
     * The location where the data should be set.
     * 
     * @param mixed $data 
     * The data to be set.
     * 
     * @return bool 
     * Returns true if the data was successfully set, false otherwise.
     */
    public static function set(string $location, mixed $data): bool {
        return JSON::set($location, $data, self::$file, get_called_class());
    }

    /**
     * Deletes the data at the specified location in the json file.
     * 
     * @param string $location
     * The location where the data should be deleted.
     * 
     * @return bool
     * Returns true if the data was successfully deleted, false otherwise.
     */
    public static function remove(string $location): bool {
        return JSON::remove($location, self::$file, get_called_class());
    }

    /**
     * This method is used to require a page file and add its
     * stylesheet and script to the master stylesheet and scripts array.
     * 
     * @param string $page 
     * The name of the page file to require.
     * 
     * @return void
     */

    public static function require(string $page = null): void {
    
        // If parameter is empty, use the current page, otherwise
        // use the requested page. Get also the current HTTP response
        // status code in case the requested page does not exist
        $status = Request::req("GET", "error") ?? Response::getStatus();
        $page   = ucfirst($page ?? Request::current());
        $pages  = Pages::get();

        // Get the path to the 'pages' folder and construct the full
        // path to the requested page file
        $folder = Folder::getPath('pages', Path::root());
        $file = "$folder/$page/$page";

        // If page parameter is empty, use the index page
        if (empty($page)) {
            $page = ucfirst(Config::get("application/router/index"));
            $file = "$folder/$page/$page";
        }

        // Check if status code is between 400 and 599. If it is,
        // redirect to the error page with the same status code:
        if (($status >= 400 && $status < 600)){
            $page = ucfirst(Config::get("application/router/error"));
            $file = "$folder/$page/$page";
            Response::setStatus($status);
        }
        
        // Get pages object:

        // If the requested file does not exist or is not part of the
        // pages.json, redirect to the error page with a 404 status code:
        if (!file_exists("$file.php") || !array_key_exists($page, (array)$pages)) {
            $page = ucfirst(Config::get("application/router/error"));
            $file = "$folder/$page/$page";
            Response::setStatus(404);
        }

        // Add the component stylesheet to the master stylesheet array
        if (file_exists("$file.css")) {
            Link::add([
                "mode"       => "server",                 // Get the file from the server 
                "path"       => "pages/$page/$page.css",  // Automatically will add from src/->
                "type"       => "text/css",               // Type is CSS
                "conditions" => null,                     // No conditions
            ]);
        }   

        // Add the component script to the master scripts array
        if (file_exists("$file.js")) {
            Link::add([
                "mode"       => "server",                // Get the file from the server 
                "path"       => "pages/$page/$page.js",  // Automatically will add from src/->
                "type"       => "text/javascript",       // Type is JS
                "conditions" => null,                    // No conditions
            ]);
        }

        // Include the requested component file
        require_once "$file.php";
    }
    

}