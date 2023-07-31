<?php

// Dependencies:
if(!class_exists('Config'))     require_once(__DIR__ . '/config.php');
if(!class_exists('Report'))     require_once(__DIR__ . '/report.php');
if(!class_exists('Directory'))  require_once(__DIR__ . '/directory.php');
if(!class_exists('Controller')) require_once(__DIR__ . '/controller.php');
if(!class_exists('Files'))      require_once(__DIR__ . '/files.php');


/**
 * This class handles how the favicon is displayed,
 * and also how the favicon is generated.
 * 
 * @version 1.0.0
 */
class Favicon {


    // Cache the favicon object
    private static $cache = null;

    /**
     * Get the favicon object, and it's information and cache it.
     * 
     * @param string $favicon
     * The favicon to get the object for.
     * 
     * @param string $file
     * The path to the favicons.json file.
     * 
     * @return mixed
     * Returns the favicon object.
     */
    public static function get(?string $request, string $file = "favicons.json"): mixed {

        // Check if the favicon object is cached, if not, read and cache it. Notice that
        // we search for the whole favicon object, and not just the favicon itself. If the 
        // favicon is cached, we can return parts of it, instead of reading the file again.
        if (Favicon::$cache === null) {
            Favicon::$cache = Config::get("", sprintf("%s/%s", Directory::get("favicon"), $file));
        }

        // Get the cached favicon object
        $favicons = Favicon::$cache;

        // Check if the favicon parameter is empty or null, 
        // return the entire favicon object
        if (empty($request) || is_null($request)) { 
            return $favicons; 
        }

        // Get the keys of the path to traverse the favicon object
        $keys = explode('/', $request);

        // Go through each key and search for the value, if the key doesn't exist, return null
        foreach ($keys as $key) {

            // Check if the key exists in the favicon object
            if (!isset($favicons->{$key})) {
                Report::exception(new RuntimeException("Key '$key' doesn't exist in the favicon file"));
            }

            // Get the value of the key
            $favicons = $favicons->{$key};
        }

        // Return the favicon object
        return $favicons;

    }






    /**
     * This method generates the favicon.
     * 
     * @return bool
     * True if the favicon was generated successfully,
     * otherwise false.
     */
    public static function generate(string $directory = "/public/favicon"): bool {

        // Get favicon object,
        $favicons  = Favicon::$cache;

        // Check if the favicon object is exists:
        if (!isset($favicons)) {
            Report::warning("The favicon object does not exist");
            return false;
        }

        // Check if the favicon directory exists:
        if (!is_dir(sprintf("%s%s", $root, $directory))) {
            Report::warning("The favicon directory does not exist");
            return false;
        }

        // Initialize favicons:
        $output = "";

        // For each favicon, generate the tag, if part of the 
        // favicon is missing / null, ignore that part:
        foreach ($favicons as $favicon) {

            // Favicon properties:
            $version = $favicon->href;
            $href = sprintf('%s/%s', $directory, $favicon->href);
            $rel  = $favicon->rel;
            $type = $favicon->type ?? null;
            $size = $favicon->size ?? null;

            // Get the file modification time and then
            // remove the server root from the favicon's href property
            $href = Files::getVersion($href);
            $href = str_replace($_SERVER['DOCUMENT_ROOT'], '', $href);

            // Generate favicon tag:
            $link  = "";
            $link .= sprintf('href="%s"', $href);
            $link .= sprintf('rel="%s"',  $rel);
            if(isset($type)) $link .= sprintf('type="%s"',  $type);
            if(isset($size)) $link .= sprintf('sizes="%s"', $size);

            // Add favicon tag to output:
            $output .= sprintf('<link %s>', $link);
        }
        
        // Check if the output is empty:
        if(!isset($output)){
            Report::warning("The favicon could not be generated");
            return false;
        }

        // Output the favicons:
        echo $output;
        return true;
    }


}