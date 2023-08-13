<?php

// Dependencies:
if (!class_exists('Report')) require_once(__DIR__ . '/report.php');

/**
 * URL class provides a method to build a URL string based on the given 
 * options.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  URL
 */
class URL {

    /**
     * Builds a URL string based on the given options.
     *
     * @param array 
     * $options An array of options to build the URL string.
     *
     *
     * @return string 
     * The generated URL string.
     */
    public static function get(array $options = []) {
        
        $defaults = [
            'SCHEME' => TRUE,  // By default, use the scheme
            'HOST'   => TRUE,  // By default, use the host
            'PORT'   => FALSE, // By default, don't use port
            'PATH'   => TRUE,  // By default, use the path
            'QUERY'  => TRUE   // By default, use the query string
        ];

        // Merge the given options with the defaults:
        $options = array_merge($defaults, $options);

        // Process and set options: Validate each option and assign to $options[$key].
        foreach ($options as $key => $value) {
            $options[$key] = Server::validate($key, $value);
        }

        // Set the new options to variables:
        $scheme = $options['SCHEME'];
        $host   = $options['HOST'];
        $port   = $options['PORT'];
        $path   = $options['PATH'];
        $query  = $options['QUERY'];

        // Build the url:
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
}