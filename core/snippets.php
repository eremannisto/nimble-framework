<?php declare(strict_types=1);
 
/**
 * The Snippets class provides methods to require snippets,
 * which are basically PHP functions that can be called
 * from everywhere in the application.
 * 
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Snippets
 */
class Snippets {

    /**
     * Loads the specified snippets.
     *
     * @param mixed $snippets 
     * The snippets to load. Can be a string or an array of strings.
     * 
     * @return void
     * Returns nothing.
     */
    public static function require(mixed $snippets): void {

        // Get the path to the 'snippets' folder so we can
        // construct the full path to the requested snippet file
        // in the foreach loop
        $folder = Folder::getPath('snippets', Path::root());

        // If snippets is not an array, convert it to an array
        if (!is_array($snippets)) {
            $snippets = [$snippets];
        }

        // Loop through each snippets in the array
        foreach ($snippets as $snippet) {

            // Construct the full path to the requested snippet file
            $file = "$folder/$snippet/$snippet";
    
            // If the requested file does not exist
            if (!file_exists("$snippet.php")) {
                Report::warning("Snippet '$snippet' does not exist");
                continue;
            }

            // Include the requested component file
            require_once "$file.php";
        }
    }
}