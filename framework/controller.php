<?php declare(strict_types=1);

/**
 * The Controller class is responsible for initializing the application 
 * by setting the timezone and locale. It also checks if the requested page
 * is a client file and fetches it if it is.
 */
class Controller {

    public static array $cache = [];

    /**
     * Initializes the controller by setting the timezone and locale,
     * and checking if the requested page is a client file.
     *
     * @return void
     *
     * @throws Exception If the timezone is invalid.
     */
    public static function init(): void {

        // Set timezone and locale:
        date_default_timezone_set(Config::get('application->time->zone'));
        setlocale(LC_ALL, Config::get('application->time->locale'));

        // If the request is for a file, fetch it and exit:
        $page = Request::current();
        if(strpos($page, 'fetch') === 0){
            Assets::fetch($page);           
            exit();
        }

        // Check if the requested page is a client file
        // if (Request::isClientFileFetch()){
        //     File::getClientFile();
        //     exit();
        // }   

        // If page supports multiple languages and user goes to the landing page,
        // redirect to the correct language. Otherwise, redirect to the index page.
        $language = Language::current();
        if(Language::supported()) {
            Language::redirect($language);
        }
    }
}