<?php declare(strict_types=1);

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
                return Pages::get("{$currentPage}->title")            // Try to use the page-specific title.
                    ?: Config::get("application->meta->title")       // Try to use the default title.
                    ?: "No title found";                           // Return a fallback title.

            case 'description':
                return Pages::get("{$currentPage}->description")      // Try to use the page-specific description.
                    ?: Config::get("application->meta->description") // Try to use the default description.
                    ?: "No description found";                     // Return a fallback description.

            case 'keywords':
                return Pages::get("{$currentPage}->keywords")         // Try to use the page-specific keywords.
                    ?: Config::get("application->meta->keywords")    // Try to use the default keywords.
                    ?: "";                                         // Return fallback keywords.

            case 'type':
                return Pages::get("{$currentPage}->type")             // Try to use the page-specific type.
                    ?: Config::get("application->meta->type")        // Try to use the default type.
                    ?: "website";                                  // Return fallback type.

            case 'language':
                return Pages::get("{$currentPage}->language")         // Try to use the page-specific language.
                    ?: Config::get("application->meta->language")    // Try to use the default language.
                    ?: "en";                                       // Return fallback language.

            case 'image':
                $image = Pages::get("{$currentPage}->image")          // Try to use the page-specific image.
                    ?:   Config::get("application->meta->image")     // Try to use the default image.
                    ?:   "";                                       // Use fallback image.
                return !empty($image) 
                    ? File::version(Folder::getPath("images") . '/' . $image)
                    : $image;

            case 'robots':
                return Pages::get("{$currentPage}->robots")           // Try to use the page-specific robots.
                    ?: Config::get("application->meta->robots")      // Try to use the default robots.
                    ?: "index, follow";                            // Return fallback robots.

            default:
                Report::warning("Invalid parameter for Meta::get() -function.");
                return null;
        }
    }

}
