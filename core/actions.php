<?php declare(strict_types=1);
 
/**
 * The Actions class provides methods to require actions,
 * which are basically PHP functions that can be called
 * from everywhere in the application.
 * 
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Actions
 */
class Actions {

    /**
     * Loads the specified actions.
     *
     * @param mixed $actions 
     * The actions to load. Can be a string or an array of strings.
     * 
     * @return void
     * Returns nothing.
     */
    public static function require(mixed $actions): void {

        // Get the path to the 'actions' folder so we can
        // construct the full path to the requested action file
        // in the foreach loop
        $folder = Folder::getPath('actions', Path::root());

        // If actions is not an array, convert it to an array
        if (!is_array($actions)) {
            $actions = [$actions];
        }

        // Loop through each actions in the array
        foreach ($actions as $action) {

            // Construct the full path to the requested action file
            $file = "$folder/$action/$action";
    
            // If the requested file does not exist
            if (!file_exists("$action.php")) {
                Report::warning("Action '$action' does not exist");
                continue;
            }

            // Include the requested component file
            require_once "$file.php";
        }
    }
}