<?php

class Components {

    public static function require(string $component): void {

        // Get the path to the 'components' folder and construct the full
        // path to the requested component file
        $folder = Folder::getPath('components', Path::root());
        $file = sprintf("%s/%s/%s.php", $folder, $component, $component);

        // If the requested file does not exist
        if (!file_exists($file)) {
            Report::warning("Component '$component' does not exist");
        }

        // Include the requested component file
        require_once $file;
    }

}