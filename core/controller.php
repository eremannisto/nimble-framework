<?php

// Dependancies:


class Controller {


    public static function init($page): void {

        // Chec

        // Require the requested page
        Controller::require($page);
    }

    /**
     * require method handles the page request
     * and tries to load the requested page.
     *
     * @param string|null $page 
     * The name of the requested page.
     * 
     * @return void 
     * Returns nothing
     */
    public static function require(?string $page = NULL): void {

        // If parameter is empty, use the current page, otherwise
        // use the requested page
        $page  = ucfirst($page ?? Request::current());

        // Get the current HTTP response status code
        // from the GET parameter 'error'
        $status = Request::req("GET", "error") ?? Response::getStatus();

        // Get the path to the 'pages' folder and construct the full
        // path to the requested page file
        $folder = Folder::getPath('pages', Path::root());
        $file = sprintf("%s/%s/%s.php", $folder, $page, $page);
    
        // If the requested file does not exist, or status code is 
        // between 400 and 599, redirect to the error page:
        if (!file_exists($file) || ($status >= 400 && $status < 500) 
                                || ($status >= 500 && $status < 600)) {
            $file = sprintf("%s/Error/Error.php", $folder);
            Response::setStatus($status);
        }

        $stylesheet = sprintf("%s/%s/%s.css", $folder, $page, $page);
        $script     = sprintf("%s/%s/%s.js", $folder, $page, $page);

        // Add styles and scripts to their respective arrays
        File::add([
            "fetch"      => TRUE,
            "asset"      => $page,
            "type"       => "text/css",
            "src"        => "pages",
        ]);

        File::add([
            "fetch"      => TRUE,
            "asset"      => $page,
            "type"       => "text/javascript",
            "src"        => "pages"
            
        ]); 

        // Include the requested page file
        require_once $file;
    }


    /**
     * If page request fails, start a error handler
     */

}