<?php

// Dependencies:
if (!class_exists('Package')) {
    require_once (__DIR__ . '/package.php');
}


/**
 * Controller class handles all controller related methods,
 * such as getting and setting the controller host, base url, root
 * path, index name, and current page.
 * 
 * @version 1.0.0
 */
class Controller {

    /**
     * Get the front controller name
     * 
     * @return string|null
     * The controller name, or null if the file could not be read or decoded.
     */
    public static function getControllerName(): ?string {
        return Package::get()->controller->name ?? null;
    }

    /**
     * Set the front controller name.
     * 
     * @param string $name
     * The controller name.
     */
    public static function setControllerName(string $name): void {
        Package::get()->controller->name = $name;
        Package::set(Package::get());
    }

    /**
     * Get controller host.
     * 
     * @return string|null
     * The controller host, for example: example.com
     */
    public static function getHost(): ?string {
        return Package::get()->controller->host ?? null;
    }

    /**
     * Set controller host.
     * 
     * @param string $host
     * The controller host.
     */
    public static function setHost(string $host): void {
        Package::get()->controller->host = $host;
        Package::set(Package::get());   
    }

    /**
     * Get controller base url.
     * 
     * @return string|null
     * The controller base url, for example: https://example.com
     */
    public static function getBaseURL(): ?string {
        return Package::get()->controller->base ?? null;
    }

    /**
     * Set controller base url.
     * 
     * @param string $base
     * The controller base url.
     */
    public static function setBaseURL(string $base): void {
        Package::get()->controller->base = $base;
        Package::set(Package::get());   
    }

    /**
     * Get controller root folder.
     * 
     * @return string|null
     * The controller root, for example: /public
     */
    public static function getRootFolder(): ?string {
        return Package::get()->controller->root ?? null;
    }

    /**
     * Set controller root folder.
     * 
     * @param string $root
     * The controller root folder.
     */
    public static function setRootFolder(string $root): void {
        Package::get()->controller->root = $root;
        Package::set(Package::get());   
    }


    /**
     * Get controller index name.
     * 
     * @return string|null
     * The controller index, for example: home
     */
    public static function getIndexName(): ?string {
        return Package::get()->controller->index ?? null;
    }

    /**
     * Set controller index name.
     * 
     * @param string $index
     * The controller index name.
     */
    public static function setIndexName(string $index): void {
        Package::get()->controller->index = $index;
        Package::set(Package::get());   
    }

    /**
     * Get controller error name.
     * 
     * @return string|null
     * The controller error, for example: error
     */
    public static function getErrorName(): ?string {
        return Package::get()->controller->error ?? null;
    }

    /**
     * Set controller error name.
     * 
     * @param string $error
     * The controller error name.
     */
    public static function setErrorName(string $error): void {
        Package::get()->controller->error = $error;
        Package::set(Package::get());   
    }

     /**
     * Get controller page parameter name.
     * 
     * @return string|null
     * The controller page parameter name, for example: page
     */
    public static function getPageParameter(): ?string {
        return Package::get()->controller->parameter ?? null;
    }

    /**
     * Set controller GET parameter name.
     * 
     * @param string $parameter
     * The controller parameter name.
     */
    public static function setPageParameter(string $parameter): void {
        Package::get()->controller->parameter = $parameter;
        Package::set(Package::get());   
    }

    /** 
     * Gets the current page from the GET parameters, if
     * it is not available, the index page is returned.
     * 
     * @return string
     * The current page.
     * 
     * @deprecated
     */
    public static function getPageName(): string {

        $parameter  = Config::get("controller/parameter");  // Get parameter name
        $index      = Controller::getIndexName() ?? 'page'; // Get index name, or fallback to 'page'

        return isset($_GET[$parameter]) ? $_GET[$parameter] : $index;
    }


    /**
     * Sanitize GET parameters for the front-controller.
     * 
     * @return void
     * The sanitized parameters
     */
    public static function sanitize(): void {

        // Sanitize the GET parameters
        $sanitized = array_map(function ($value) {
            return filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        }, $_GET);

        // Set the sanitized GET parameters
        $_GET = $sanitized;
    }

    /**
     * Require the current page.
     * 
     * @return bool
     * True if the page was found and required, false otherwise.
     */
    public static function require(): bool {

        // Get the current page
        $page  = Controller::getPageName();
        $index = Controller::getIndexName();
        $error = Controller::getErrorName();

        // If the current page exists in the
        // pages list, require it.
        if(Pages::exists($page)) {
            Pages::require($page);
            Pages::load($page);
            return true;
        }

        // If page is empty or the index page, require the index page.
        else if(empty($page) || $page === $index) {
            Pages::require($index);
            Pages::load($index);
            return true;
        }

        // If the page does not exist, require the error page.
        else {
            Pages::require($error);
            Pages::load($error);
            return false;
        }
    }

    /**
     * Loads all the required files for the page.
     * 
     * @return bool
     * True if the page was found and loaded, false otherwise.
     */
    public static function load(): bool {

        // Get the current page
        $page  = Controller::getPageName();
        $index = Controller::getIndexName();
        $error = Controller::getErrorName();

        // If the current page exists in the
        // pages list, require it.
        if(Pages::exists($page)) {
            Pages::load($page);
            return true;
        }

        // If page is empty or the index page, require the index page.
        else if(empty($page) || $page === $index) {
            Pages::load($index);
            return true;
        }

        // If the page does not exist, require the error page.
        else {
            Pages::load($error);
            return false;
        }
    }
    


    /**
     * Initialize the controller. This will require all
     * the necessary files and require all the components, templates,
     * and pages. None of the pages will be rendered, but they will be
     * accessible after this method has been called.
     * 
     * @return bool
     * True if the controller was initialized, false otherwise.
     */
    public static function start(): void {

        // Set the default timezone:
        Time::setDefaultTimeZone();

        // Sanitize the GET parameters:
        Controller::sanitize();

        // Require the page:
        Controller::require();
    }
     

    /**
     * Redirect to a page.
     * 
     * @param string $page
     * The page to redirect to.
     * 
     * @return void
     */
    public static function redirect(string $page): void{
        Header('Location: /' . $page);
        exit;
    }
}
