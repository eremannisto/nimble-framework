<?php declare(strict_types=1);

/**
 * The Request class provides methods to retrieve and 
 * manipulate HTTP request data.
 */
class Request {

    /**
     * Retrieves the value of a request parameter based on the
     * specified HTTP method.
     *
     * @param string $method 
     * The HTTP method used to make the request (GET or POST).
     * 
     * @param string $key
     * The name of the parameter to retrieve.
     *
     * @return string|null 
     * The value of the parameter if it exists, or NULL if it does not.
     */
    public static function req(string $method, string $key): ?string {
        switch(strtoupper($method)) {
            case 'GET':  return isset($_GET[$key])  ? $_GET[$key]  : NULL;
            case 'POST': return isset($_POST[$key]) ? $_POST[$key] : NULL;
            default:     return Report::error('Invalid request method: ' . $method);
        }
    }

    /**
     * Check if the specified key exists in the request array,
     * and optionally check if its value matches a given value.
     *
     * @param string $method 
     * The method to check for, can be GET or POST.
     * 
     * @param string $key 
     * The key to check for.
     * 
     * @param string $value 
     * The value to check for (optional).
     * 
     * @return bool 
     * True if the key exists in the request array and its value matches
     * the given value (if provided), otherwise false.
     * 
     * @example Request::has('GET',  'page');
     * @example Request::has('GET',  'page', 'home');
     * @example Request::has('POST', 'username');
     * @example Request::has('POST', 'username' 'admin');
     */
    public static function has(string $method, string $key, string $value = NULL): bool {
        return ($value !== NULL)                        // Check if the value is not null.
            ? (Request::req($method, $key) === $value)  // Check if the value matches the given value.
            : (Request::req($method, $key) !== NULL);   // Check if the key exists in the request array.
    }

    /**
     * Get the current request method.
     *
     * @return string|null 
     * The current request method, either 'GET', 'POST', 'PUT',
     * 'PATCH', 'DELETE', or null if it is not set.
     */
    public static function method(): ?string {
        return     isset($_SERVER['REQUEST_METHOD']) 
            ? strtoupper($_SERVER['REQUEST_METHOD']) : NULL;
    }

    /**
     * Get the current page name from the GET parameters,
     * or the default page name from the configuration file.
     *
     * @return string 
     * The current page name.
     */
    public static function current(): string {
        return htmlspecialchars(Request::req("GET", Config::get('application->router->parameter'))
            ??  Config::get('application->router->index'));
    }

    /**
     * Get the current base path, for example: https://example.com
     *
     * @return string 
     * The current base path.
     */
    public static function isClientFileFetch(): bool {
        return Request::has("GET", "mode", "server")
            && Request::has("GET", "path")
            && Request::has("GET", "type");
    }

}