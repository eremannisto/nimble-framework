<?php

// Dependancies:
if(!class_exists('Config')) require_once(__DIR__ . '/config.php');
if(!class_exists('Report')) require_once(__DIR__ . '/report.php');

/**
 * The Request class provides methods to retrieve and 
 * manipulate HTTP request data.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Request
 */
class Request {

    /**
     * Retrieve a value from the request.
     *
     * @param string $method 
     * The HTTP method to use (GET or POST).
     * 
     * @param string $key 
     * The key to retrieve from the request.
     *
     * @return string|null 
     * The value of the key in the request, or null if it doesn't exist.
     * 
     * Get the page key (GET)
     * @example Request::request('GET', 'page');
     * 
     * Get the username key (POST)
     * @example Request::request('POST', 'username');
     */
    public static function request(string $method, string $key): ?string {

        // Convert the method to uppercase:
        $method = strtoupper($method);

        switch ($method) {
            case 'GET':
                return isset($_GET[$key]) 
                ? $_GET[$key] : null;

            case 'POST':
                return isset($_POST[$key]) 
                ? $_POST[$key] : null;

            default:
                Report::warning('Invalid request method.');
                return null;
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
     * Check if the page key exists (GET)
     * @example Request::has('GET',  'page');
     * 
     * Check if the page key exists and its value is home (GET) 
     * @example Request::has('GET', 'page', 'home');
     * 
     * Check if the username key exists (POST)
     * @example Request::has('POST', 'username');
     * 
     * Check if the username key exists and its value is admin (POST)
     * @example Request::has('POST', 'username' 'admin');
     */
    public static function has(string $method, string $key, string $value = null): bool {
        return ($value !== null)                        // Check if the value is not null.
        ? (Request::request($method, $key) === $value)  // Check if the value matches the given value.
        : (Request::request($method, $key) !== null);   // Check if the key exists in the request array.
    }

    /**
     * Get the current request method.
     *
     * @return string|null 
     * The current request method, either 'GET', 'POST', 'PUT',
     * 'PATCH', 'DELETE', or null if it is not set.
     */
    public static function method(): ?string {
        return isset($_SERVER['REQUEST_METHOD']) 
        ? strtoupper($_SERVER['REQUEST_METHOD']) 
        : null;
    }

    /**
     * Get the current page name from the GET parameters,
     * or the default page name from the configuration file.
     *
     * @return string 
     * The current page name.
     */
    public static function page(): string {
        return $_GET[Config::get('app/router/parameter')] 
        ?? Config::get('app/router/index');
    }

    /**
     * Get the current base path, for example: https://example.com
     *
     * @return string 
     * The current base path.
     */
    public static function base(): string {
        return Config::get('app/router/base')               // Get the base path from the configuration file.
        ?: Request::scheme() . '://' . Request::host();     // Otherwise, get it from the current protocol and host.
    }

    /**
     * Get the current scheme.
     *
     * @return string 
     * The current scheme, either 'http' or 'https'.
     */
    public static function scheme(): string {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' 
        ? 'https' 
        : 'http';
    }

    /**
     * Get the current host name, for example: example.com.
     *
     * @return string 
     * The current host name.
     */
    public static function host(): string {
        return Config::get('app/router/host')   // Get the host from the configuration file.
        ?: $_SERVER['HTTP_HOST'];               // Otherwise, get it from the $_SERVER superglobal array.
    }

    /**
     * Get the current port.
     * 
     * @return string
     * The current port.
     */
    public static function port(): string {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * Get the current path from the $_SERVER superglobal array.
     *
     * @return string 
     * The current path.
     */
    public static function path(): string {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns the query string portion of the URL.
     *
     * @return string 
     * The query string portion of the URL.
     */
    public static function query(): string {
        return $_SERVER['QUERY_STRING'];
    }


    /**
     * Builds a URL string based on the given options.
     *
     * @param array 
     * $options An array of options to build the URL string.
     *      - scheme:   Whether to use the current scheme or not. Default is TRUE.
     *      - host:     Whether to use the current host or not. Default is TRUE.
     *      - port:     Whether to use the current port or not. Default is FALSE.
     *      - path:     Whether to use the current path or not. Default is TRUE.
     *      - query:    Whether to use the current query string or not. Default is TRUE.
     *
     * @return string 
     * The generated URL string.
     */
    public static function url(array $options = []) {
        
        $defaults = [
            'scheme' => TRUE,  // By default, use the current scheme
            'host'   => TRUE,  // By default, use the current host
            'port'   => FALSE, // By default, use current port
            'path'   => TRUE,  // By default, use the current path
            'query'  => TRUE   // By default, use the current query string
        ];

        // Merge the given options with the defaults:
        $options = array_merge($defaults, $options);

        // Process and set options: Validate each option and assign to $options[$key].
        foreach ($options as $key => $value) {
            $options[$key] = Request::validate($key, $value);
        }

        // Set the new options to variables:
        $scheme = $options['scheme'];
        $host   = $options['host'];
        $port   = $options['port'];
        $path   = $options['path'];
        $query  = $options['query'];

        // Build the url:
        $url = '';
        $url = '';
        if ($scheme !== '' && $scheme !== FALSE) { $url .= $scheme . '://';         }
        if ($host   !== '' && $host   !== FALSE) { $url .= $host;                   }
        if ($port   !== '' && $port   !== FALSE) { $url .= ':' . $port;             } 
        if ($path   !== '' && $path   !== FALSE) { $url .= '/' . ltrim($path, '/'); }
        if ($query  !== '' && $query  !== FALSE) { $url .= '?' . $query;            }

        // Validate the URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            Report::notice("Generated URL is not valid: $url");
        }

        return $url;
    }

    /**
     * Validates the given key-value pair for a request option.
     *
     * @param string $key 
     * The option key to validate.
     * 
     * @param mixed $value 
     * The option value to validate.
     * 
     * @return mixed 
     * The validated option value, or an empty string if invalid.
     */
    private static function validate(string $key, mixed $value): mixed {

        switch ($key) {
            case 'scheme':
                return ($value === TRUE) ? Request::scheme() : (
                    ($value === FALSE) ? '' : (
                        (in_array($value, ['http', 'https'], true)) ? $value : ''
                    )
                );

            case 'host':
                return ($value === TRUE) ? Request::host() : (
                    (is_string($value)) ? $value : ''
                );

            case 'port':
                return ($value === TRUE) ? Request::port() : (
                    (is_int($value) && $value >= 0 && $value <= 65535) ? $value : ''
                );

            case 'path':
                return ($value === TRUE) ? Request::path() : (
                    (is_string($value)) ? $value : ''
                );

            case 'query':
                return ($value === TRUE) ? Request::query() : (
                    (is_array($value)) ? http_build_query($value) : (
                        (is_string($value)) ? $value : ''
                    )
                );

            default:
                Report::warning("Invalid option: $option");
                return '';
        }
    }

}