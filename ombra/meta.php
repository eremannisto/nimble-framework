<?php

// Dependancies:
if (!class_exists('Config'))      require_once(__DIR__ . '/config.php');
if (!class_exists('Request'))     require_once(__DIR__ . '/request.php');
if (!class_exists('Report'))      require_once(__DIR__ . '/report.php');
if (!class_exists('Pages'))       require_once(__DIR__ . '/pages.php');
if (!class_exists('Files'))       require_once(__DIR__ . '/files.php');
if (!class_exists('Directories')) require_once(__DIR__ . '/directories.php');


/**
 * Meta class handles all meta related methods,
 * such as getting and setting the meta title, description,
 * and OG-data.
 * 
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Meta
 */
class Meta {

    /**
     * Get meta data by parameter.
     * 
     * @param string $parameter
     * The parameter to get the meta data for, options are:
     * title, description, keywords, type, language, and image.
     * 
     * @return string|null
     * The meta data, or null if the file could not be read or decoded.
     */
    public static function get(string $parameter): ?string {

        // Get current page:
        $currentPage     = Request::current();

        // Get meta data:
        switch ($parameter) {

            case 'title':
                return Pages::get("$currentPage/meta/title")       // Try to use the page-specific title.
                    ?: Config::get("application/meta/title")       // Try to use the default title.
                    ?: "No title found";                           // Return a fallback title.

            case 'description':
                return Pages::get("$currentPage/meta/description") // Try to use the page-specific description.
                    ?: Config::get("application/meta/description") // Try to use the default description.
                    ?: "No description found";                     // Return a fallback description.

            case 'keywords':
                return Pages::get("$currentPage/meta/keywords")    // Try to use the page-specific keywords.
                    ?: Config::get("application/meta/keywords")    // Try to use the default keywords.
                    ?: "";                                         // Return fallback keywords.

            case 'type':
                return Pages::get("$currentPage/meta/type")        // Try to use the page-specific type.
                    ?: Config::get("application/meta/type")        // Try to use the default type.
                    ?: "website";                                  // Return fallback type.

            case 'language':
                return Pages::get("$currentPage/meta/language")    // Try to use the page-specific language.
                    ?: Config::get("application/meta/language")    // Try to use the default language.
                    ?: "en";                                       // Return fallback language.

            case 'image':
                $image = Pages::get("$currentPage/meta/image")     // Try to use the page-specific image.
                    ?:   Config::get("application/meta/image")     // Try to use the default image.
                    ?:   "";                                       // Use fallback image.
                return !empty($image) 
                    ? Files::get('version', sprintf("%s%s", Directories::get('images'), $image)) 
                    : $image;

            default:
                Report::warning("Invalid parameter for Meta::get() -function.");
                return null;
        }
    }

}
