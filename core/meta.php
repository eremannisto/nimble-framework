<?php

// Dependancies:
if (!class_exists('Package')) {
    require_once(__DIR__ . '/package.php');
}

if(!class_exists('Utility')) {
    require_once(__DIR__ . '/pages.php');
}

if(!class_exists('Page')) {
    require_once(__DIR__ . '/pages.php');
}

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
        $currentPage = Request::url();  // ! Doesnt exist yet.

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


    /**
     * Generate meta items, such as general, language,
     * open graphs and favicons.
     * 
     * @param string|enum $parameter
     * The parameter to generate the meta tags for.
     * - general:    Generate general meta tags.
     * - language:   Generate language meta tags.
     * - open-graph: Generate open graph meta tags.
     * - favicons:   Generate favicons.
     * 
     * @return string
     * The generated meta tags.
     */
    public static function generate(string $parameter): string {

        $output  = "";

        switch ($parameter) {

            case 'general':
                 
                $output .= sprintf('<meta charset="%s">', "UTF-8");                                                      
                $output .= sprintf('<meta http-equiv="%s" content="%s">', "X-UA-Compatible", "IE=edge");                 
                $output .= sprintf('<meta name="%s" content="%s">', "viewport", "width=device-width, initial-scale=1.0");

                // Title:
                $title = Meta::get("title");
                if(!empty($title)) {
                    $output .= sprintf('<title>%s</title>', $title);            
                }

                // Description:
                $description = Meta::get("description");
                if(!empty($description)) {
                    $output .= sprintf('<meta name="description" content="%s">', $description); 
                }

                // Type:
                $type = Meta::get("type");
                if(!empty($type)) {
                    $output .= sprintf('<meta property="og:type" content="%s">', $type); 
                }

                // Return output:
                return $output;


            case 'language':
                echo(Meta::generateLanguage());
                return true;

            case 'open-graph':

                // Generate meta tags:
                $title = Meta::get("title");
                if(!empty($title)) {
                    $output .= sprintf('<meta property="og:title" content="%s">', $title); 
                }

                // Description:
                $description = Meta::get("description");
                if(!empty($description)) {
                    $output .= sprintf('<meta property="og:description" content="%s">', $description); 
                }

                // Type:
                $type = Meta::getType();
                if(!empty($type)) {
                    $output .= sprintf('<meta property="og:type" content="%s">', $type); 
                }

                // Image:
                $image = Meta::getImage();
                if(!empty($image)) {
                    $output .= sprintf('<meta property="og:image" content="%s">', $image); 
                }

                // Return output:
                return $output;

            default:
                Report::warning("Invalid parameter for Meta::generate() -function.");
                return false;
        }
    }



    /**
     * Generate language meta tags.
     * 
     * @return string
     * The generated meta tags.
     */
    private static function generateLanguage(): string {

        // Generate meta tags:
        $output  = "";

        // Language:
        $language = Meta::getLanguage();
        if(isset($language)) {
            $output .= $language;
        }

        // Return output:
        return $output;
    }


}
