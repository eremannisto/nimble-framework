<?php declare(strict_types=1);

/** 
 * This class handles how directories work in the application.
 * 
 * @version B4.0.0
 */
class Directories {

    /**
     * Map all the directories in the application and store them in an array.
     * This way we can easily access the directories from anywhere in the application, without
     * having to traverse the directory tree every time we need to access a directory.
     * 
     * @var array $directories
     */
    private static array $directories = [];

    /**
     * Returns the path to a directory in the application.
     *
     * @param string $directory 
     * The name of the directory to find.
     * 
     * @return string 
     * The path to the directory, or an empty string if the 
     * directory is not found.
     */
    public static function get(string $directory): string {
        
        // If the directories array is empty, map the directories
        if (empty(Directories::$directories)) {
            Directories::map();
        }

        // Check if the directory exists
        if (!isset(Directories::$directories[$directory])) {
            Report::warning("Directory '$directory' not found.");
            return "";
        }

        // Return the path to the directory
        return Directories::$directories[$directory];
    }

    /**
     * Map all the directories in the application and store them in an array.
     * This way we can easily access the directories from anywhere in the application, without
     * having to traverse the directory tree every time we need to access a directory.
     * 
     * @return void
     * Returns nothing.
     */
    private static function map(): void {

        // Get the directories from the config file
        $directories = Config::get('application->directories');
    
        $map      = [];
        $stack    = [$directories];
        $preStack = [''];
    
        while (!empty($stack)) {
            $sub    = array_pop($stack);
            $prefix = array_pop($preStack);
    
            foreach ($sub as $dir => $sub) {
                // If the directory is not empty, push it onto the stack to process its sub
                if (!empty($sub)) {
                    $stack[]    = $sub;
                    $preStack[] = $prefix . '/' . $dir;
                }
    
                // Store the directory in the directories array
                Directories::$directories[$dir] = $prefix . '/' . $dir;
            }
        }
    }

    /**
     * Check if the requested directory exists.
     * 
     * @param string $directory
     * The directory name to check for.
     * 
     * @return bool
     * True if the directory exists, false otherwise.
     */
    public static function exists(string $directory): bool {
        return is_dir(Path::get($directory));
    }
}