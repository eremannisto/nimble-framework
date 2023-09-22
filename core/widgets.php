<?php declare(strict_types=1);
 
/**
 * The Widgets class provides methods to require widgets,
 * which are basically PHP functions that can be called
 * from everywhere in the application.
 */
class Widgets {

    /**
     * Loads the specified widgets.
     *
     * @param mixed $widgets 
     * The widgets to load. Can be a string or an array of strings.
     * 
     * @return void
     * Returns nothing.
     */
    public static function require(mixed $widgets): void {

        // Get the path to the 'widgets' folder so we can
        // construct the full path to the requested snippet file
        // in the foreach loop
        $folder = Folder::getPath('widgets', Path::root());

        // If widgets is not an array, convert it to an array
        if (!is_array($widgets)) {
            $widgets = [$widgets];
        }

        // Loop through each widgets in the array
        foreach ($widgets as $widget) {

            // Construct the full path to the requested snippet file
            $file = "$folder/$widget/$widget";
    
            // If the requested file does not exist
            if (!file_exists("$widget.php")) {
                Report::warning("Widget '$widget' does not exist");
                continue;
            }

            // Include the requested component file
            require_once "$file.php";
        }
    }
}