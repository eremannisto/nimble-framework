<?php declare(strict_types=1);

/**
 * The Controller class is responsible for initializing the application 
 * by setting the timezone and locale. It also checks if the requested page
 * is a client file and fetches it if it is.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Controller
 */
class Controller {

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
        date_default_timezone_set(Config::get('application/time/zone'));
        setlocale(LC_ALL, Config::get('application/time/locale'));

        // Check if the requested page is a client file
        if (Request::isClientFileFetch()){
            File::getClientFile();
            exit();
        }   
    }
}