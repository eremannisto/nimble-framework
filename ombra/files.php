<?php

// Dependancies:
if(!class_exists('Request')) require_once(__DIR__ . '/controller.php');

class Files {

    public static function get(string $parameter, ?string $path): mixed{

        switch ($parameter) {

            // Return the file modtime unix timestamp:
            case 'modtime':
                $path = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], $path]);
                return file_exists($path) ? filemtime($path) : null;
            
            // Return the file size:
            case 'size':
                $path = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], $path]);
                return file_exists($path) ? filesize($path) : null;

            // Return the file mime type:
            case 'mime':
                $path = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], $path]);
                return file_exists($path) ? mime_content_type($path) : null;

            // Return the file contents:
            case 'contents':
                $path = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], $path]);
                return file_exists($path) ? file_get_contents($path) : null;

            // Return the file href with a version number:
            case 'version':
                $modified = Files::get("modtime", $path);
                return $modified !== null ? sprintf("%s?version=%d", $path, $modified) : null;
 
            default:
                Report::warning("The parameter '$parameter' is not valid.");
                return null;
        }

    }

    public static function set(string $parameter, ?string $path, mixed $value): bool{

        switch ($parameter) {

            // Set the file modtime:
            case 'modtime':
                $path = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], $path]);
                return file_exists($path) ? touch($path, $value) : null;

            // Set the file contents:
            case 'contents':
                $path = join(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], $path]);
                return file_exists($path) ? file_put_contents($path, $value) : null;

            default:
                Report::warning("The parameter '$parameter' is not valid.");
                return null;
        }

    }

    /**
     * Method to check if the set file should be included on the current page.
     * 
     * @param string $condition
     * The condition to check.
     * 
     * @return bool
     * True if the file should be included, false otherwise.
     */
    private static function includeFilter(string $condition = null): bool {

        // If no condition is specified, return true (include on every page)
        if ($condition === null) {
            return true;
        }

        // If the condition starts with an exclamation point, return the
        // negation of the result of calling itself again with the exclamation point
        // removed (exclude on specific pages)
        if (strpos($condition, "!") === 0) {
            return !includeFilter(substr($condition, 1));
        }

        // Return true if the current page matches the include condition; otherwise, 
        // return false (include on specific pages)
        return Request::page() === $condition;
    }

/**
     * Method to generate a file reference based on the specified file name, type,
     * and conditions.
     * 
     * @param string $file
     * The file name.
     * 
     * @param string $type
     * The file type.
     * 
     * @param string|null $conditions
     * The conditions under which the file should be included.
     * 
     * @return string
     * The HTML code for the file reference.
     */
    private static function link(string $file, string $type, ?string $conditions = null): mixed {
        
        // Get file modification time as the version number:
        $version = Files::get("version", $file);

        // Convert the conditions to an array if necessary:
        $conditions = is_array($conditions) ? $conditions : [$conditions];

        // Iterate over the conditions:
        foreach ($conditions as $condition) {

            // If conditions are not met, skip this iteration:
            if (!Files::includeFilter($condition)) {
                continue;
            }

            // If the version is null, skip this iteration:
            if($version === null) {
                continue;
            }
            
            // Initialize the output variable:
            $output = "";

            // Generate the HTML code based on the type:
            switch (true) {

                // Generate a CSS link:
                case $type === 'css' || $type === 'text/css':
                    $output .= sprintf('<link rel="stylesheet" type="text/css" href="%s" media="all">', $version);
                    break;

                // Generate a JavaScript script:
                case $type === 'js' || $type === 'text/javascript':
                    $output .= sprintf('<script type="text/javascript" src="%s"></script>', $version);
                    break;

                // Generate a JavaScript module:
                case $type === 'module' || $type === 'js-module':
                    $output .= sprintf('<script type="module" src="%s"></script>', $version);
                    break;

                // Invalid type: do nothing
                default:
                    Report::warning("Invalid type '$type' for file '$file'.");
                    break;
            }

            // Return the output:
            return $output;
        }

        // Return null if the file was not included:
        return null;
    }

}



    // /**
    //  * This method reference CSS files. It retrieve stylesheets for the 
    //  * current page, template, and components, combines them with provided
    //  * stylesheets and generate necessary HTML code for each stylesheet.
    //  * 
    //  * @param array $stylesheets
    //  * The stylesheets to include.
    //  * 
    //  * @return bool
    //  * True if the stylesheets were included successfully, false otherwise.
    //  */
    // public static function styles(array $parameter = []): bool {
        
    //     // Current page and the pages object:
    //     $page  = Pages::this();
    //     $pages = Pages::pages();

    //     // Initialize the stylesheets array:
    //     $stylesheets = [];

    //     // Page:
    //     // Get all the current pages stylesheets:
    //     $style = $pages->$page->styles;
    //     $stylesheets = array_merge($stylesheets, $style);

    //     // Add the page styling to the page stylesheets
    //     $style = (array)str_replace($_SERVER['DOCUMENT_ROOT'], '', Pages::getStyle($page));
    //     $stylesheets = array_merge($stylesheets, $style);


    //     // Templates:
    //     // Get all the template stylesheets for each template
    //     // used by the current page:
    //     $templates  = $pages->$page->templates;
    //     foreach ($templates as $template) {

    //         // For each template, get the template stylesheets,
    //         // and add them to the page stylesheets:
    //         $style = (array)str_replace($_SERVER['DOCUMENT_ROOT'], '', Templates::getStyle($template));
    //         $stylesheets = array_merge($stylesheets, $style);

    //         // Add also each templates own stylesheets object:
    //         $style = Templates::templates()->$template->styles;
    //         $stylesheets = array_merge($stylesheets, $style);

    //         // For each component that the template uses, get the component styles,
    //         // and add them to the page styles: (Remove the SERVER_ROOT from the path)
    //         $components = Templates::templates()->$template->components;
    //         foreach ($components as $component) {
    //             $style = (array)str_replace($_SERVER['DOCUMENT_ROOT'], '', Components::getStyle($component));
    //             $stylesheets = array_merge($stylesheets, $style);

    //             // Add also each components own style object:
    //             $style = Components::components()->$component->styles;
    //             $stylesheets = array_merge($stylesheets, $style);
    //         }
    //     }

    //     // Components:
    //     // Get all the component stylesheets for each component
    //     // used by the current page:
    //     $components = $pages->$page->components;
    //     foreach ($components as $component) {

    //         // For each component, get the component stylesheets,
    //         // and add them to the page stylesheets: (Remove the SERVER_ROOT from the path)
    //         $style = (array)str_replace($_SERVER['DOCUMENT_ROOT'], '', Components::getStyle($component));
    //         $stylesheets = array_merge($stylesheets, $style);

    //         // Add also each components own stylesheets object:
    //         $style = Components::components()->$component->styles;
    //         $stylesheets = array_merge($stylesheets, $style);
    //     }

    //     // Build the array with keys being stylesheets file paths and values set to null,
    //     // then merge the parameter stylesheets with the page stylesheets:
    //     $stylesheets = array_fill_keys($stylesheets, null);
    //     $stylesheets = array_merge($stylesheets, $parameter);

    //     // Initialize the output string:
    //     $output = "";

    //     // Iterate over the stylesheets:
    //     foreach ($stylesheets as $filename => $conditions) {

    //         // Generate the HTML code for the stylesheet:
    //         $output .= Files::reference($filename, 'css', $conditions);
    //     }

    //     // Return the generated HTML code:
    //     echo $output;
    //     return true;
    // }

    //     /**
    //  * This method reference CSS files. It retrieve scripts for the 
    //  * current page, template, and components, combines them with provided
    //  * scripts and generate necessary HTML code for each script.
    //  * 
    //  * @param array $scripts
    //  * The scripts to include.
    //  * 
    //  * @return bool
    //  * True if the scripts were included successfully, false otherwise.
    //  */
    // public static function scripts(array $parameter = []): bool {
        
    //     // Current page and the pages object:
    //     $page  = Pages::this();
    //     $pages = Pages::pages();

    //     // Initialize the scripts array:
    //     $scripts = [];

    //     // Page:
    //     // Get all the current pages scripts:
    //     $script = $pages->$page->scripts;
    //     $scripts = array_merge($scripts, $script);

    //     // Add the page styling to the page scripts
    //     $script = (array)str_replace($_SERVER['DOCUMENT_ROOT'], '', Pages::getScript($page));
    //     $scripts = array_merge($scripts, $script);


    //     // Templates:
    //     // Get all the template scripts for each template
    //     // used by the current page:
    //     $templates  = $pages->$page->templates;
    //     foreach ($templates as $template) {

    //         // For each template, get the template scripts,
    //         // and add them to the page scripts:
    //         $script = (array)str_replace($_SERVER['DOCUMENT_ROOT'], '', Templates::getScript($template));
    //         $scripts = array_merge($scripts, $script);

    //         // Add also each templates own script object:
    //         $script = Templates::templates()->$template->scripts;
    //         $scripts = array_merge($scripts, $script);

    //         // For each component that the template uses, get the component scripts,
    //         // and add them to the page scripts: (Remove the SERVER_ROOT from the path)
    //         $components = Templates::templates()->$template->components;
    //         foreach ($components as $component) {
    //             $script = (array)str_replace($_SERVER['DOCUMENT_ROOT'], '', Components::getScript($component));
    //             $scripts = array_merge($scripts, $script);

    //             // Add also each components own script object:
    //             $script = Components::components()->$component->scripts;
    //             $scripts = array_merge($scripts, $script);
    //         }
    //     }

    //     // Components:
    //     // Get all the component scripts for each component
    //     // used by the current page:
    //     $components = $pages->$page->components;
    //     foreach ($components as $component) {

    //         // For each component, get the component scripts,
    //         // and add them to the page scripts: (Remove the SERVER_ROOT from the path)
    //         $script = (array)str_replace($_SERVER['DOCUMENT_ROOT'], '', Components::getScript($component));
    //         $scripts = array_merge($scripts, $script);

    //         // Add also each components own script object:
    //         $script = Components::components()->$component->scripts;
    //         $scripts = array_merge($scripts, $script);
    //     }


    //     // Build the array with keys being scripts file paths and values set to null,
    //     // then merge the parameter scripts with the page scripts:
    //     $scripts = array_fill_keys($scripts, null);
    //     $scripts = array_merge($scripts, $parameter);

    //     // Reverse the order of the scripts:
    //     $scripts = array_reverse($scripts);

    //     // Initialize the output string:
    //     $output = "";

    //     // Iterate over the scripts:
    //     foreach ($scripts as $filename => $conditions) {

    //         // Generate the HTML code for the script:
    //         $output .= Files::reference($filename, 'js', $conditions);
    //     }

    //     // Return the generated HTML code:
    //     echo $output;
    //     return true;
    // }


    // /**
    //  * Open a requested file.
    //  * 
    //  * @param string $file
    //  * The file to open.
    //  * 
    //  * @param string $folder
    //  * The folder where the file is located.
    //  * 
    //  * @return bool
    //  * True if the file was opened successfully, false otherwise.
    //  */
    // public static function open(string $file, string $folder = ''): bool {

    //     // Path to the file, this currently only picks
    //     // files from the uploads folder:
    //     $path = dirname(__DIR__, 2) . "/uploads/$folder/$file";
    //     Report::notice("Opening the file '$file' from the '$folder' folder.");

    //     // If the file does not exist, return false:
    //     if (!file_exists($path)) {
    //         Report::warning("The file '$file' does not exist in the '$folder' folder.");
    //         return false;
    //     }

    //     // Get the file extension, this is used to set the mime type:
    //     $extension = pathinfo($path, PATHINFO_EXTENSION);

    //     // Get the mime type:
    //     $mime = mime_content_type($path);

    //     // Set the headers:
    //     header("Content-Type: $mime");
    //     header("Content-Disposition: inline; filename=$file");
    //     header("Content-Length: " . filesize($path));

    //     // Read the file:
    //     readfile($path);

    //     // Return true:
    //     return true;
    // }

