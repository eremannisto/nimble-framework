<?php declare(strict_types=1);

/**
 * Meta class handles all meta related methods,
 * such as getting and setting the meta title, description,
 * and OG-data.
 * 
 * @version 4.0.0
 */
class Meta {

    /**
     * Fallback values for the meta data.
     */
    private static array $fallbacks = [];

    /**
     * Get the fallback values for the meta data.
     */
    private static function setFallbacks(): void {
        if(empty(self::$fallbacks)) {

            $url      = URL::get();
            $language = Language::current();
            
            self::$fallbacks = [
                "title"       => "No title found",
                "description" => "No description found",
                "keywords"    => "",
                "type"        => "website",
                "author"      => "",
                "image"       => "",
                "language"    => $language,
                "url"         => $url,
                "canonical"   => $url,
                "robots"      => "index, follow",
                "theme"       => "",
                "manifest"    => ""
            ];
        }
    }

    /**
     * This function retrieves the meta data value for a given parameter.
     * It first checks if the value exists in the page-specific meta data,
     * then in the default meta data, and finally returns a fallback value if none are found.
     *
     * @param string $parameter 
     * The meta data key to retrieve.
     * 
     * @param mixed $page 
     * The page-specific meta data.
     * 
     * @param mixed $default 
     * The default meta data.
     * 
     * @param string $fallback 
     * The fallback value if the meta data key is not found.
     * 
     * @return mixed The meta data value.
     */
    private static function getValue(string $parameter, mixed $page, mixed $default, string $fallback): mixed {
        return $page->{$parameter} ?? $default->{$parameter} ?? $fallback;
    }

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

        // Set fallbacks:
        self::setFallbacks();

        // Get current page:
        $current   = Request::current();
        $page      = Pages::get($current);
        $default   = Config::get("application->meta->default");
        $fallbacks = self::$fallbacks;

        // Validate parameters:
        if(!in_array($parameter, array_keys($fallbacks))){
            Report::warning("Invalid parameter for Meta::get() -function. '$parameter'.");
            return "";
        }

        // Get meta data:
        $value = self::getValue(
            $parameter,                   // Parameter to get
            $page,                        // Page meta
            $default,                     // Default meta
            self::$fallbacks[$parameter]  // Fallback meta
        );    

        // If the parameter is an image, add the version to the path:
        /** @todo This will be updated to the Asset::class */
        if($parameter === "image" && !empty($value)){
            return File::version(Path::images() . '/' . $value);
        }

        return $value;
    }

    /**
     * Get all meta data.
     * 
     * @return array
     * An array of all meta data values.
     */
    public static function getAll(): array {

        // Set fallbacks:
        self::setFallbacks();

        // Get current page:
        $current   = Request::current();
        $page      = Pages::get($current);
        $default   = Config::get("application->meta->default");
        $fallbacks = self::$fallbacks;

        // Get meta data:
        $meta = [];
        foreach($fallbacks as $parameter => $fallback){
            $meta[$parameter] = self::getValue(
                $parameter,                     // Parameter to get
                $page,                          // Page meta
                $default,                       // Default meta
                self::$fallbacks[$parameter]    // Fallback meta
            );
        }

        // If the parameter is an image, add the version to the path:
        /** @todo This will be updated to the Asset::class */
        if(!empty($meta["image"])){
            $meta["image"] = File::version(Path::images() . '/' . $meta["image"]);
        }

        return $meta;
    }
}
