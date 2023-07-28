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
 * such as getting and setting the meta title, descr iption,
 * and OG-data.
 * 
 * @version 1.0.0
 */
class Meta {

    // Variable to cache the meta object
    public static $cache; 


    /**
     * Get meta object. It first tries to get the cached meta object,
     * and if that fails, it tries to get the meta object from the
     * package.json file.
     * 
     * @return object|null
     * The meta object, or null if the file could not be read or decoded.
     */
    private static function meta(): ?object {

        // Check if meta object is cached:
        if (!isset(Meta::$cache)) {

            // Get meta object from package.json and cache it:
            $meta        = Package::get()->meta ?? null;
            Meta::$cache = $meta;
        }

        // Return meta:
        return Meta::$cache;
    }

    /**
     * Get meta title. It first tries to get the title from the
     * current page, and if that fails, it tries to get the default
     * title from the package. When that fails, it returns a fallback
     * title.
     * 
     * @param string $fallback
     * The fallback title.
     * 
     * @return string|null
     * The meta title, or null if the file could not be read or decoded.
     */
    public static function getTitle($fallback = ""): ?string {
        $title          = Pages::getMetaTitle(Pages::this());
        $default        = Meta::meta()->title ?? null;
        return Utility::getNonEmpty($title, $default, $fallback);
    }

    /**
     * Set the default meta title.
     * 
     * @param string $title
     * The default meta title.
     */
    public static function setTitle(string $title): void {
        Meta::meta()->title = $title;
        Package::set(Package::get());
    }

    /**
     * Get meta description. It first tries to get the description from the
     * current page, and if that fails, it tries to get the default
     * description from the package. When that fails, it returns a fallback
     * description.
     * 
     * @param string $fallback
     * The fallback description.
     * 
     * @return string|null
     * The meta description, or null if the file could not be read or decoded.
     */
    public static function getDescription($fallback = ""): ?string {
        $description    = Pages::getMetaDescription(Pages::this());
        $default        = Meta::meta()->description ?? null;
        return Utility::getNonEmpty($description, $default, $fallback);
    }

    /**
     * Set the default meta description.
     * 
     * @param string $description
     * The default meta description.
     */
    public static function setDescription(string $description): void {
        Meta::meta()->description = $description;
        Package::set(Package::get());
    }

    /**
     * Get meta keywords. It first tries to get the keywords from the
     * current page, and if that fails, it tries to get the default
     * keywords from the package. When that fails, it returns a fallback
     * keywords.
     * 
     * @param string $fallback
     * The fallback keywords.
     * 
     * @return string|null
     * The meta keywords, or null if the file could not be read or decoded.
     */
    public static function getKeywords($fallback = ""): ?string {
        $keywords       = Pages::getMetaKeywords(Pages::this());
        $default        = Meta::meta()->keywords ?? null;
        return Utility::getNonEmpty($keywords, $default, $fallback);
    }

    /**
     * Set the default meta keywords.
     * 
     * @param string $keywords
     * The default meta keywords.
     */
    public static function setKeywords(string $keywords): void {
        Meta::meta()->keywords = $keywords;
        Package::set(Package::get());
    }

    /**
     * Get the meta type. It first tries to get the type from the
     * current page, and if that fails, it tries to get the default
     * type from the package. When that fails, it returns a fallback
     * type.
     * 
     * @param string $fallback
     * The fallback type.
     * 
     * @return string|null
     * The meta type, or null if the file could not be read or decoded.
     */
    public static function getType($fallback = "website"): ?string {
        $type           = Pages::getMetaType(Pages::this());
        $default        = Meta::meta()->type ?? null;
        return Utility::getNonEmpty($type, $default, $fallback);
    }

    /**
     * Set the default meta type.
     * 
     * @param string $type
     * The default meta type.
     * 
     * @return bool
     * True if the type was set, false otherwise.
     */
    public static function setType(string $type): bool {
        if (in_array($type, ['website', 'article', 'book', 'profile', 'music.song', 'music.album', 'music.playlist', 'music.radio_station', 'video.movie', 'video.episode', 'video.tv_show', 'video.other', 'article', 'article.book', 'article.profile', 'article.website', 'article.music.song', 'article.music.album', 'article.music.playlist', 'article.music.radio_station', 'article.video.movie', 'article.video.episode', 'article.video.tv_show', 'article.video.other'])) {
            Meta::meta()->type = $type;
            Package::set(Package::get());
            return true;
        }
        return false;
    }

    /**
     * Get language. It first tries to get the language from the
     * current page, and if that fails, it tries to get the default
     * language from the package. When that fails, it returns a fallback
     * language.
     * 
     * @param string $fallback
     * The fallback language.
     * 
     * @return string|null
     * The language, or null if the file could not be read or decoded.
     */
    public static function getLanguage($fallback = ""): ?string {
        $language       = Pages::getMetaLanguage(Pages::this());
        $default        = Meta::meta()->language ?? null;
        return Utility::getNonEmpty($language, $default, $fallback);
    }

    /**
     * Set the default language.
     * 
     * @param string $language
     * The default language.
     */
    public static function setLanguage(string $language): void {
        Meta::meta()->language = $language;
        Package::set(Package::get());
    }
    

    /**
     * Get the meta image. It first tries to get the image from the
     * current page, and if that fails, it tries to get the default
     * image from the package. When that fails, it returns a fallback
     * image.
     * 
     * @param string $fallback
     * The fallback image.
     * 
     * @return string|null
     * The meta image, or null if the file could not be read or decoded.
     */
    public static function getImage($fallback = ""): ?string {
        $image          = Pages::getMetaImage(Pages::this());
        $default        = Meta::meta()->image ?? null;
        return Utility::getNonEmpty($image, $default, $fallback);
    }

    /**
     * Set the default meta image.
     * 
     * @param string $image
     * The default meta image.
     */
    public static function setImage(string $image): void {
        Meta::meta()->image = $image;
        Package::set(Package::get());
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
     * @return bool
     * True if the meta tags were generated, false otherwise.
     */
    public static function generate(string $parameter): bool {

        switch ($parameter) {

            case 'general':
                echo(Meta::generateGeneral());
                return true;

            case 'language':
                echo(Meta::generateLanguage());
                return true;

            case 'open-graph':
                echo(Meta::generateOpenGraph());
                return true;

            default:
                Report::warning("Invalid parameter for Meta::generate() -function.");
                return false;
        }
    }


    /**
     * Generate general meta tags.
     * 
     * @return string
     * The generated meta tags.
     */
    private static function generateGeneral(): string {

        // Generate meta tags:
        $output  = "";

        $output .= sprintf('<meta charset="%s">', "UTF-8");                                                         // Charset
        $output .= sprintf('<meta http-equiv="%s" content="%s">', "X-UA-Compatible", "IE=edge");                    // IE Compatibility
        $output .= sprintf('<meta name="%s" content="%s">', "viewport", "width=device-width, initial-scale=1.0");   // Viewport

        // Title:
        $title = Meta::getTitle();
        if(!empty($title)) {
            $output .= sprintf('<title>%s</title>', $title);            
        }

        // Description:
        $description = Meta::getDescription();
        if(!empty($description)) {
            $output .= sprintf('<meta name="description" content="%s">', $description); 
        }

        // Keywords:
        $keywords = Meta::getKeywords();
        if(!empty($keywords)) {
            $output .= sprintf('<meta name="keywords" content="%s">', $keywords); 
        }

        // Type:
        $type = Meta::getType();
        if(!empty($type)) {
            $output .= sprintf('<meta property="og:type" content="%s">', $type); 
        }

        // Return output:
        return $output;
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

    /**
     * Generate open graph meta tags.
     * 
     * @return string
     * The generated meta tags.
     */
    private static function generateOpenGraph(): string {

        // Generate meta tags:
        $output  = "";

        // Title:
        $title = Meta::getTitle();
        if(!empty($title)) {
            $output .= sprintf('<meta property="og:title" content="%s">', $title); 
        }

        // Description:
        $description = Meta::getDescription();
        if(!empty($description)) {
            $output .= sprintf('<meta property="og:description" content="%s">', $description); 
        }

        // Image:
        $image = Meta::getImage();
        if(!empty($image)) {
            $output .= sprintf('<meta property="og:image" content="%s">', $image); 
        }

        // Type:
        $type = Meta::getType();
        if(!empty($type)) {
            $output .= sprintf('<meta property="og:type" content="%s">', $type); 
        }

        // Return output:
        return $output;
    }

}
