<?php declare(strict_types=1);

/**
 * The Server class provides methods to retrieve information about the
 * current server, such as the current base path, scheme, host, port, path,
 * and query string.
 */
class Server {

    /**
     * Get the current base path, for example: https://example.com
     *
     * @return string 
     * The current base path.
     */
    public static function base(): string {
        return Config::get('application->router->base')       // Get the base path from the configuration file.
        ?: Server::scheme() . '://' . Server::host();     // Otherwise, get it from the current protocol and host.
    }

    /**
     * Get the current scheme.
     *
     * @param mixed $value
     * The value to validate. If set to true, the current
     * scheme will be returned. If a custom value is provided,
     * it will be validated and returned if valid ('http' or 'https').
     * Otherwise, an empty string will be returned.
     *
     * @return string
     * The current scheme, either 'http' or 'https'.
     */
    public static function scheme(mixed $value = true): string {

        // Make sure scheme is either 'http' or 'https'.
        $validate = function($scheme) {
            return in_array($scheme, ['http', 'https'], true) 
                ?: (Report::warning('URL validation failed: Invalid SCHEME') && false);

        };

        // If value is set to true, return the current scheme.
        if ($value === true) {
            return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
            ? 'https' : 'http';
        }

        // If value is not false and it is valid, return it.
        else if ($value !== false) {
            return $validate($value) ? (string)$value : '';
        }

        // Otherwise the value is set to false, so we
        // return an empty string.
        return '';
    }

    /**
     * Get the current host name, for example: example.com.
     *
     * @param mixed $value
     * The value to validate. If set to true, the current
     * host name will be returned. If a custom value is
     * provided, it will be validated and returned if valid.
     * Otherwise, an empty string will be returned.
     * 
     * @return string 
     * The current host name.
     */
    public static function host(mixed $value = true): string {
        
        // Define a regular expression pattern for a valid host name.
        $pattern = "/^([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,}$/";

        // Make sure host is a valid host name.
        $validate = function($host) use ($pattern) {
            return ($host === 'localhost' || 'localhost:'.Server::port() || preg_match($pattern, $host) === 1) 
                ?: (Report::warning("URL validation failed: Invalid HOST: $host") && false);
        };

        // If value is set to true, return the current host name.
        if ($value === true) 
            return $validate(Config::get('application->router->host')) 
                ? Config::get('application->router->host')
                : ($_SERVER['HTTP_HOST'] ?? '');
            
        // If value is not false and it is valid, return it.
        else if ($value !== false) {
            return $validate($value) ? (string)$value : '';
        }
        
        // Otherwise the value is set to false, so we
        // return an empty string.
        return '';
    }

    /**
     * Get the current port.
     * 
     * @param mixed $value
     * The value to validate. If set to true, the current
     * port will be returned. If a custom value is provided,
     * it will be validated and returned if valid. Otherwise,
     * an empty string will be returned.
     * 
     * @return string
     * The current port.
     */
    public static function port(mixed $value = true): string {

        // Make sure port is a valid integer between 0 and 65535.
        $validate = function($port) {
            if (is_numeric($port) && $port >= 0 && $port <= 65535) {
                return true;
            } 
            else {
                Report::warning("URL validation failed: Invalid PORT: $port");
                return false;
            }
        };

        // If value is set to true, return the current port.
        if ($value === true) {
            return $validate($_SERVER['SERVER_PORT']) 
                ? $_SERVER['SERVER_PORT'] : '';
        }
    
        // If value is not false and it is valid, return it.
        else if ($value !== false) {
            return $validate($value) ? (string)$value : '';
        }

        // Otherwise the value is set to false, so we 
        // return an empty string.
        return '';
    }

    /**
     * Get the current path from the $_SERVER superglobal array.
     *
     * @param mixed $value
     * The value to validate. If set to true, the current
     * path will be returned. If a custom value is provided,
     * it will be validated and returned if valid. Otherwise,
     * an empty string will be returned.
     *
     * @return string
     * The current path.
     */
    public static function path(mixed $value = true): string {

        // Define a regular expression pattern for a valid path segment.
        $pattern = '/^[-_a-zA-Z0-9\/\.]*$/';

        // Validate the path against the pattern.
        $validate = function($path) use ($pattern) {
            return (preg_match($pattern, $path) === 1) 
                ?: (Report::warning('URL validation failed: Invalid PATH.') && false);
        };

        // If value is set to true, return the current path.
        if ($value === true) {
            $path = $_SERVER['REQUEST_URI'];
            return $validate($path) ? $path : '';
        }

        // If value is not false and it is valid, return it.
        else if ($value !== false) {
            return $validate($value) ? (string)$value : '';
        }

        // Otherwise the value is set to false, so we
        // return an empty string.
        return '';
    }

    /**
     * Returns the query string portion of the URL.
     *
     * @param mixed $value
     * The value to validate. If set to true, the current
     * query string will be returned. If a custom value is provided,
     * it will be validated and returned if valid. Otherwise,
     * an empty string will be returned.
     *
     * @return string
     * The query string portion of the URL.
     */
    public static function query(mixed $value = true): string {

        // Validation function for query strings, simple REGEX pattern.
        $validate = function($query) {
            $pattern = '/^[^#]*$/';
            return preg_match($pattern, $query) === 1;
        };

        $build = function($query) {
            return(is_array($query) ? http_build_query($query) : $query);
        };

        // If value is set to true, return the current query string.
        if ($value === true) {
            $query = $_SERVER['QUERY_STRING'];

            // Remove tha page parameter from the query string.
            $parameter = Config::get('application->router->parameter');
            $query = preg_replace("/{$parameter}=[^&]+&?/", '', $query);

            return $validate($query) ? $query : '';
        }
    
        // If value is not false and it is valid, return it.
        else if ($value !== false) {
            $query = $build($value);
            return $validate($query) ? $query : '';
        }
    
        // Otherwise the value is set to false, so we
        // return an empty string.  
        return '';
    }

    /**
     * Validates the given key-value pair for a url option.
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
    public static function validate(string $key, mixed $value): mixed {

        switch (strtoupper($key)) {
            case 'SCHEME':
                return Server::scheme($value);

            case 'HOST':
                return Server::host($value);

            case 'PORT':
                return Server::port($value);
    
            case 'PATH':
                return Server::path($value);

            case 'QUERY':
                return Server::query($value);

            default:
                Report::warning("Invalid option: $key");
                return '';
        }
    }
}