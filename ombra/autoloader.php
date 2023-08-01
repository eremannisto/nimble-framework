<?php

/**
 * Autoloads all classes in the core directory, by
 * scanning the core directory and requiring all php files.
 * 
 * @return void
 * Returns nothing.
 */
class AutoLoader {

    /**
     * Autoloads all classes in the core directory.
     */
    public static function load(): void {

        // Get all files in the core directory
        $files = scandir(__DIR__);

        // Go through each file and require it if it's a php file
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require_once(__DIR__ . "/$file");
            }
        }
    }

}