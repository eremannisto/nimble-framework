<?php declare(strict_types=1);

/**
 * Autoloader class that loads all PHP files in the same directory 
 * as the class file.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  AutoLoader
 */
class AutoLoader {

    /**
     * Autoloads all classes in the core directory.
     * 
     * @return void
     * Returns nothing.
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