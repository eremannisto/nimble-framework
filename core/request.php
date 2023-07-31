<?php

// Dependancies:
if (!class_exists('Config')) {
    require_once(__DIR__ . '/config.php');
}

class Request {

    /**
     * Get the GET value of the specified key.
     * 
     * @param string $key
     * The key to get the value for.
     * 
     * @return string|null
     * The value of the specified key, or null if the key doesn't exist.
     */
    public static function get(string $key): ?string {
        return htmlspecialchars($_GET[$key]) ?? null;
    }

    /**
     * Get the POST value of the specified key.
     * 
     * @param string $key
     * The key to get the value for.
     * 
     * @return string|null
     * The value of the specified key, or null if the key doesn't exist.
     */
    public static function post(string $key): ?string {
        return htmlspecialchars($_POST[$key]) ?? null;
    }

    /**
     * Check if the specified key exists in the array, can be
     * used to check if a GET or POST value exists.
     * 
     * @param string $key
     * The key to check for.
     * 
     * @param string $method
     * The method to check for, can be GET or POST.
     * 
     * @return bool
     * True if the key exists in the array, otherwise false.
     */
    public static function has(string $key, string $method = "GET"): bool {
        return in_array(strtoupper($method), ['GET', 'POST'])   // Check if the method is GET or POST.
        ? isset($_REQUEST[$key])                                // Check if the key exists in the request array.
        : false;                                                // Return false otherwise.
    }

    /**
     * Get the current protocol.
     * 
     * @return string
     * The current protocol.
     */
    public static function protocol(): string {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' 
        ? 'https' 
        : 'http';
    }
    
    /**
     * Get the current request method.
     * 
     * @return string
     * The current request method.
     */
    public static function method(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get the current page.
     * 
     * @return string
     * The current page.
     */
    public static function page(): string {
        return sprintf('%s/%s', Config::get('host'), $_GET['page'] ?? '');
    }

    /**
     * Get the current url.
     * 
     * @return string
     * The current url.
     */
    public static function url(bool $protocol = TRUE): string {
        return sprintf('%s://%s%s', Request::protocol(), Config::get('host'), $_SERVER['REQUEST_URI']);
    }   

}