<?php declare(strict_types=1);

/**
 * This class handles how the pages are displayed,
 * and also how the pages are generated.
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
        $folder = Directories::get('pages') . self::$file;
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
        $status         = Request::req("GET", "res") ?? Response::getStatus();
        $pageRequest    = $pageRequest ?? Request::current();
        $pageParts      = explode('/', $pageRequest);
        $pageName       = end($pageParts);

        // Get the path to the 'pages' folder and construct the full
        // path to the requested page file
        $folder = Path::pages();

        // If page parameter is empty, use the index page
        if (empty($pageRequest)) {
            $pageRequest = Config::get("application->router->index");
            $pageName    = $pageRequest;
        }

        // Check if status code is between 400 and 599. If it is,
        // redirect to the error page with the same status code:
        if (($status >= 400 && $status < 600)){
            $errorPage = Config::get("application->router->error");
            $pageRequest = $errorPage;
            $pageName    = $errorPage;
            Response::setStatus($status);
        }
        
        // If the requested file does not exist or is not part of the
        // pages.json, redirect to the error page with a 404 status code:
        if (!file_exists("$folder/$pageRequest/$pageName.php") || !array_key_exists($pageRequest, (array)Pages::get())) {
            $errorPage   = Config::get("application->router->error");
            $pageRequest = $errorPage;
            $pageName    = $errorPage;
            Response::setStatus(404);
        }

        // Add the component stylesheet to the master stylesheet array
        if (file_exists("$folder/$pageRequest/$pageName.css")) {
            Assets::add([
                "mode"       => "server",                               // Get the file from the server 
                "path"       => "/pages/$pageRequest/$pageName.css",    // Automatically will add from src/->
                "type"       => "style",                                // Type is CSS
            ]);
        }   

        // Add the component script to the master scripts array
        if (file_exists("$folder/$pageRequest/$pageName.js")) {
            Assets::add([
                "mode"       => "server",                               // Get the file from the server 
                "path"       => "/pages/$pageRequest/$pageName.js",     // Automatically will add from src/->
                "type"       => "script",                               // Type is JS
            ]);
        }

        // Include the requested component file
        require_once "$folder/$pageRequest/$pageName.php";
    }
    

}