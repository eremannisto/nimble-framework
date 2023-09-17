<?php declare(strict_types=1);
 
/**
 * Components class handles all component related methods,
 * such as loading components and their associated stylesheets
 * and scripts.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Components
 */
class Components {

    /**
     * Loads the specified components and their associated 
     * stylesheets and scripts.
     *
     * @param mixed $components 
     * The components to load. Can be a string or an array of strings.
     * 
     * @return void
     * Returns nothing.
     */
    public static function require(mixed $components): void {

        // Get the path to the 'components' folder so we can
        // construct the full path to the requested component file
        // in the foreach loop

        $folder = Folder::getPath('components', Path::root());

        // If components is not an array, convert it to an array
        if (!is_array($components)) {
            $components = [$components];
        }

        // Loop through each component in the array
        foreach ($components as $component) {

            $componentParts = explode('/', $component);
            $componentName  = end($componentParts);

            // Construct the full path to the requested component file
            $file = "$folder/$component/$componentName";
    
            // If the requested file does not exist
            if (!file_exists("$file.php")) {
                Report::warning("Component '$component' does not exist");
                continue;
            }
    
            // Add the component stylesheet to the stylesheet array
            if (file_exists("$file.css")) {
                Link::add([
                    "mode"       => "server",                                // Get the file from the server 
                    "path"       => "components/$component/$componentName.css",  // Automatically will add from src/->
                    "type"       => "text/css",                              // Type is CSS
                    "conditions" => null,                                    // No conditions
                ]);
            }   
    
            // Add the component script to the scripts array
            if (file_exists("$file.js")) {
                Link::add([
                    "mode"       => "server",                                // Get the file from the server 
                    "path"       => "components/$component/$componentName.js",   // Automatically will add from src/->
                    "type"       => "text/javascript",                       // Type is JS
                    "conditions" => null,                                    // No conditions
                ]);
            }
            // Include the requested component file
            require_once "$file.php";
        }
    }



    public static function requireGlobal(): void {
        if (!empty(Controller::$cache['components']) && is_array(Controller::$cache['components'])) {
            foreach (Controller::$cache['components'] as $class => $components) {
                foreach ($components as $component => $condition) {
                    if (Link::filterCondition($condition)) {
                        Components::require(ucfirst($component));
                    }
                }
            }
        }
    }

    public static function renderGlobal(string $class){
        if (!empty(Controller::$cache['components']) && is_array(Controller::$cache['components'])) {
            foreach (Controller::$cache['components'] as $componentClass => $components) {      
                if ($componentClass !== $class) continue;
                foreach ($components as $component => $condition) {
                    if (Link::filterCondition($condition)) {
                        $component::render();
                    }
                }
            }
        }
    }
}