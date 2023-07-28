<?php

// Dependencies:
if(!class_exists('Package'))     require_once('system/methods/package.php');
if(!class_exists('Directories')) require_once('system/methods/directories.php');
if(!class_exists('Controller'))  require_once('system/methods/controller.php');
if(!class_exists('Files'))       require_once('system/methods/files.php');


/**
 * This class handles how the favicon is displayed,
 * and also how the favicon is generated.
 * 
 * @version 1.0.0
 */
class Favicon {

    // Cache variable to store the favicon.
    private static $cache;

    /**
     * Retrieves favicons from cache or package.json file if 
     * the cache is empty. If the cache is empty, it will be
     * populated with the favicon object.
     * 
     * @return object|null
     * Returns the favicon object if it exists.
     */
    public static function favicons(): ?object {

        // Check if pages are cached:
        if (!isset(Favicon::$cache)) {

            // Get pages from package.json and cache them:
            $favicons       = Package::get()->favicon ?? null;
            Favicon::$cache = $favicons;
        }

        // Return pages:
        return Favicon::$cache;
    }

    /**
     * This method generates the favicon.
     * 
     * @return bool
     * True if the favicon was generated successfully,
     * otherwise false.
     */
    public static function generate(): bool {

        // Favicon directory:
        $root      = sprintf("%s%s", dirname(__DIR__, 2), Controller::getRootFolder());
        $directory = Directories::getFaviconDirectory();
        $favicons  = Favicon::favicons();


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