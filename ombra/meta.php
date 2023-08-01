<?php

// Dependancies:
if(!class_exists('Config')) require_once(__DIR__ . '/config.php');
if(!class_exists('Page'))    require_once(__DIR__ . '/pages.php');


/**
 * Meta class handles all meta related methods,
 * such as getting and setting the meta title, description,
 * and OG-data.
 * 
 * @version 1.0.0
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
        $currentPage = Request::page();

        // Get meta data:
        switch ($parameter) {

            // Get the title:
            case 'title':
                return Config::get("pages/$currentPage/meta/title")         // Try to use the page-specific title.
                ?:     Config::get("meta/title")                            // Try to use the default title.
                ?:     "No title found";                                    // Return a fallback title.

            // Get the description:
            case 'description':
                return Config::get("pages/$currentPage/meta/description")   // Try to use the page-specific description.
                ?:     Config::get("meta/description")                      // Try to use the default description.
                ?:     "No description found";                              // Return a fallback description.

            // Get the keywords:
            case 'keywords':
                return Config::get("pages/$currentPage/meta/keywords")      // Try to use the page-specific keywords.
                ?:     Config::get("meta/keywords")                         // Try to use the default keywords.
                ?:     "";                                                  // Return fallback keywords.

            // Get the type:
            case 'type':
                return Config::get("pages/$currentPage/meta/type")          // Try to use the page-specific type.
                ?:     Config::get("meta/type")                             // Try to use the default type.
                ?:     "website";                                           // Return fallback type.

            // Get the language:
            case 'language':
                return Config::get("pages/$currentPage/meta/language")      // Try to use the page-specific language.
                ?:     Config::get("meta/language")                         // Try to use the default language.
                ?:     "en";                                                // Return fallback language.

            // Get the image:
            case 'image':
                return Config::get("pages/$currentPage/meta/image")         // Try to use the page-specific image.
                ?:     Config::get("meta/image")                            // Try to use the default image.
                ?:     "";                                                  // Return fallback image.

            default:
                Report::warning("Invalid parameter for Meta::get() -function.");
                return null;
        }
    }

}
