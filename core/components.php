<?php declare(strict_types=1);
 
/**
 * Components class handles all component related methods,
 * such as loading components and their associated stylesheets
 * and scripts.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Components
 */
class Components {

    /**
     * Loads the specified components and their associated 
     * stylesheets and scripts.
     *
     * @param mixed $components 
     * The components to load. Can be a string or an array of strings.
     * 
     * @return void
     * Returns nothing.
     */
    public static function require(string $page = null): void {
    
        // If parameter is empty, use the current page, otherwise
        // use the requested page. Get also the current HTTP response
        // status code in case the requested page does not exist
        $status         = Request::req("GET", "error") ?? Response::getStatus();
        $pageRequest    = ucfirst($pageRequest ?? Request::current());
        $pageParts      = explode('/', $pageRequest);
        $pageName       = end($pageParts);

        // Get the path to the 'pages' folder and construct the full
        // path to the requested page file
        $folder = Folder::getPath('pages', Path::root());
        $file = "$folder/$pageRequest/$pageName";

        Debug::log("Current page requested: $file");

        // If page parameter is empty, use the index page
        if (empty($pageRequest)) {
            $pageRequest = ucfirst(Config::get("application/router/index"));
            $file = "$folder/$pageRequest/$pageRequest";
        }

        // Check if status code is between 400 and 599. If it is,
        // redirect to the error page with the same status code:
        if (($status >= 400 && $status < 600)){
            $errorPage = ucfirst(Config::get("application/router/error"));
            $file = "$folder/$errorPage/$errorPage";
            Response::setStatus($status);
        }
        
        // Get pages object:

        // If the requested file does not exist or is not part of the
        // pages.json, redirect to the error page with a 404 status code:
        if (!file_exists("$file.php") || !array_key_exists($pageRequest, (array)Pages::get())) {
            $errorPage = ucfirst(Config::get("application/router/error"));
            $file = "$folder/$errorPage/$errorPage";
            Response::setStatus(404);
        }

        // Add the component stylesheet to the master stylesheet array
        if (file_exists("$file.css")) {
            Link::add([
                "mode"       => "server",                               // Get the file from the server 
                "path"       => "pages/$pageRequest/$pageName.css",  // Automatically will add from src/->
                "type"       => "text/css",                             // Type is CSS
                "conditions" => null,                                   // No conditions
            ]);
        }   

        // Add the component script to the master scripts array
        if (file_exists("$file.js")) {
            Link::add([
                "mode"       => "server",                               // Get the file from the server 
                "path"       => "pages/$pageRequest/$pageName.js",   // Automatically will add from src/->
                "type"       => "text/javascript",                      // Type is JS
                "conditions" => null,                                   // No conditions
            ]);
        }

        // Include the requested component file
        require_once "$file.php";
    }


    public static function requireGlobal(): void {
        if (!empty(Controller::$cache['components']) && is_array(Controller::$cache['components'])) {
            foreach (Controller::$cache['components'] as $class => $components) {
                foreach ($components as $component => $condition) {
                    if (Link::filterCondition($condition)) {
                        Components::require(ucfirst($component));
                    }
                }
            }
        }
    }

    public static function renderGlobal(string $class){
        if (!empty(Controller::$cache['components']) && is_array(Controller::$cache['components'])) {
            foreach (Controller::$cache['components'] as $componentClass => $components) {      
                if ($componentClass !== $class) continue;
                foreach ($components as $component => $condition) {
                    if (Link::filterCondition($condition)) {
                        $component::render();
                    }
                }
            }
        }
    }
}