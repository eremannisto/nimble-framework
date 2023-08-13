<?php

// Dependencies:
if (!class_exists('Config')) require_once(__DIR__ . '/config.php');

/**
 * The Server class provides methods to retrieve information about the
 * current server, such as the current base path, scheme, host, port, path,
 * and query string.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Server
 */
class Server {

    /**
     * Get the current base path, for example: https://example.com
     *
     * @return string 
     * The current base path.
     */
    public static function base(): string {
        return Config::get('application/router/base')       // Get the base path from the configuration file.
        ?: Server::scheme() . '://' . Server::host();     // Otherwise, get it from the current protocol and host.
    }

    /**
     * Get the current scheme.
     *
     * @return string 
     * The current scheme, either 'http' or 'https'.
     */
    public static function scheme(): string {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' 
        ? 'https' : 'http';
    }

    /**
     * Get the current host name, for example: example.com.
     *
     * @return string 
     * The current host name.
     */
    public static function host(): string {
        return Config::get('application/router/host')   // Get the host from the configuration file.
        ?: $_SERVER['HTTP_HOST'];                       // Otherwise, get it from the $_SERVER superglobal array.
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

        switch ($key) {
            case 'scheme':
                return ($value === TRUE) ? Server::scheme() : (
                    ($value === FALSE) ? '' : (
                        (in_array($value, ['http', 'https'], TRUE)) ? $value : '' ));

            case 'host':
                return ($value === TRUE) ? Server::host() : (
                    (is_string($value)) ? $value : '' );

            case 'port':
                return ($value === TRUE) ? Server::port() : (
                    (is_int($value) && $value >= 0 && $value <= 65535) ? $value : '' );

            case 'path':
                return ($value === TRUE) ? Server::path() : (
                    (is_string($value)) ? $value : '' );

            case 'query':
                return ($value === TRUE) ? Server::query() : (
                    (is_array($value)) ? http_build_query($value) : (
                        (is_string($value)) ? $value : '' ));

            default:
                Report::warning("Invalid option: $option");
                return '';
        }
    }
}