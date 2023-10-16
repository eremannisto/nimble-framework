<?php declare(strict_types=1);
 
/**
 * The snippets class provides methods to require snippets,
 * which are basically PHP functions that can be called
 * from everywhere in the application.
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
        $directory = Path::snippets();

        // If snippets is not an array, convert it to an array
        if (!is_array($snippets)) {
            $snippets = [$snippets];
        }

        // Loop through each snippets in the array
        foreach ($snippets as $snippet) {

            // Construct the full path to the requested snippet file
            $file = "$directory/$snippet";
    
            // If the requested file does not exist
            if (!file_exists("$file.php")) {
                Report::warning("Snippet '$file' does not exist");
                continue;
            }

            // Include the requested component file
            require_once "$file.php";
        }
    }
}