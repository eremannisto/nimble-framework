<?php

// Dependencies:
if (!class_exists('Config')) {
    require_once(__DIR__ . '/config.php');
}

if(!class_exists('Controller')) {
    require_once(__DIR__ . '/controller.php');
}

if(!class_exists('Directories')) {
    require_once(__DIR__ . '/directories.php');
}

class Components {

    // Variable to cache components:
    private static $cache;

    /**
     * Gets all components from config.json and caches them.
     * If components are already cached, the cached components
     * will be returned.
     * 
     * @return object|null
     * The components object or null if not found
     */
    public static function components(): ?object {

        // Check if components are cached:
        if (!isset(Components::$cache)) {

            // Get components from config.json and cache them:
            $components       = Config::get()->components ?? null;
            Components::$cache = $components;
        }

        // Return components:
        return Components::$cache;
    }

    /**
     * Get a specific component by it's key name.
     * 
     * @param string $component
     * The component by it's key name
     * 
     * @return object|null
     * The component object or null if not found
     */
    public static function get(string $component): ?object {

        // Get cached components list:
        $components = Components::components();

        // Check if component exists:
        if (!isset($components->$component)) {
            Report::error("Component '{$component}' not found");
        }

        // Return component:
        return $components->$component ?? null;
    }

    /**
     * Set a specific component by it's key name.   
     * 
     * @param string $component
     * The component by it's key name
     * 
     * @param object $value
     * The component object
     */
    public static function set(string $component, object $object): bool {

        // Get cached components list:
        $components = Components::components();

        // Make sure the component doesn't exists:
        if (isset($components->$component)) {
            Report::notice("Component '{$component}' already exists");
            return false;
        }

        // Set component:
        $components->$component = $object;

        // Update the config.json:
        Config::get()->components = $components;
        Config::set(Config::get());

        // Update cache:
        Components::$cache = $components;

        // Return true:
        return true;
    }

    /**
     * Require a component by its key name. This will
     * require the component file, for example: example.component.php
     * 
     * @param string $component
     * The component name
     * 
     * @return void
     * Returns nothing
     */
    public static function require(string $component): void {

        // Get component file:
        $file = Components::getFile($component);

        // Require component file:
        require_once($file);
    }

    /**
     * Remove a specific component from config.json.
     * 
     * @param string $component
     * The component by it's key name
     * 
     * @return bool
     * True if the component was removed, false if not
     */
    public static function remove(string $component): bool {

        // Get cached components list:
        $components = Components::components();

        // Make sure the component exists:
        if (!isset($components->$component)) {
            Report::error("Component '{$component}' not found");
        }

        // Remove component from config.json:
        unset($components->$component);

        // Update the config.json:
        Config::get()->components = $components;
        Config::set(Config::get());

        // Update cache:
        Components::$cache = $components;

        // Return true:
        return true;
    }

    /**
     * Create a new component with the given name. This will create
     * a new directory with the component name and empty component
     * files (PHP, CSS, and JS). At the end the component cache will
     * be updated.
     *
     * @param string $component
     * The name of the new component to create
     * 
     * @return bool
     * True if the component was created, false otherwise
     */
    public static function create(string $component): bool {

        // Get components from config.json:
        $components = Components::components();

        // Check that the component name is not already in use:
        if (isset($components->$component)) {
            Report::notice("Component '{$component}' already exists");
            return false;
        }

        // Create component files:
        $root      = sprintf("%s%s", dirname(__DIR__, 2), Controller::getRootFolder());
        $directory = sprintf("%s%s/%s", $root, Directories::getComponentsDirectory(), $component);
        $file      = sprintf("%s/%s.component.php", $directory, $component);
        $style     = sprintf("%s/%s.component.css", $directory, $component);
        $scripts   = sprintf("%s/%s.component.js",  $directory, $component);

        // Check if the folder exists:
        if (file_exists($directory)) {
            Report::error("Component folder already exists: {$directory}");
            return false;
        } 

        // Check if the component file exists:
        if (file_exists($file)) {
            Report::notice("Component file already exists: {$file}");
            return false;
        } 

        // Check if the component style file exists:
        if (file_exists($style)) {
            Report::notice("Component style file already exists: {$style}");
            return false;
        } 

        // Check if the component script file exists:
        if (file_exists($scripts)) {
            Report::notice("Component script file already exists: {$scripts}");
            return false;
        } 

        // Create the component directory and files (PHP, CSS, and JS):
        mkdir($directory);
        file_put_contents($file,    "");
        file_put_contents($style,   "");
        file_put_contents($scripts, "");

        // Add the new component to the cache:
        $components->$component = $component;
        Components::$cache = $components;

        // Update config.json:
        $config = Config::get();
        $config->components->$component = (object) [
            'name'   => $component,
            'style'  => [],
            'script' => []
        ];

        // Save config.json:
        Config::set($config);

        // Return true if the component was created:
        Report::success("Created component: {$component}");
        return true;
    }

    /**
     * Upddate a specific component by it's key name. This will
     * update the component name and the component files. At the
     * end the component cache will be updated.
     * 
     * @param string $old
     * The component by it's key name
     * 
     * @param string $new
     * The new component name
     * 
     * @return bool
     * True if the component was updated, false otherwise
     */
    public static function update(string $old, string $new): bool {

        $components = Components::components();

        // Check that the component name is not already in use:
        if (isset($components->$new)) {
            Report::notice("Component '{$new}' already exists");
            return false;
        }

        // Check that the component name exists:
        if (!isset($components->$old)) {
            Report::notice("Component '{$old}' does not exist");
            return false;
        }

        // Create the new component object:
        $object = (object) [
            'name'    => $new,
            'styles'  => $components->$old->styles  ? $components->$old->styles  : [],
            'scripts' => $components->$old->scripts ? $components->$old->scripts : [],
        ];

        // Set the new component in config.json:
        if(!Components::set($new, $object)) {
            Report::notice("Failed to set the new component: {$new}");
            return false;
        }

        // Remove the old component from config.json:
        if(!Components::remove($old)) {
            Report::notice("Failed to remove the old component: {$old}");
            return false;
        }

        // Rename the component files and directory:
        if(!Components::rename($old, $new)) {
            Report::notice("Failed to rename component: {$old} -> {$new}");
            return false;
        }

        // Return true if the component was updated:
        Report::success("Updated component: {$old} -> {$new}");
        return true;
    }

    /**
     * Rename a specific component by it's key name. This will
     * update the component name and the component files.
     */
    private static function rename(string $old, string $new): bool {

        // Get components from config.json:
        $components = Components::components();

        // Update component files:
        $directory = Components::getDirectory($old);
        $file      = Components::getFile($old);
        $style     = Components::getStyle($old);
        $scripts   = Components::getScript($old);

        // Check if the folder exists:
        if (!file_exists($directory)) {
            Report::error("Component folder not found: {$directory}");
            return false;
        } 

        // Check if the component file exists:
        if (!file_exists($file)) {
            Report::error("Component file not found: {$file}");
            return false;
        } 

        // Check if the component style file exists:
        if (!file_exists($style)) {
            Report::error("Component style file not found: {$style}");
            return false;
        } 

        // Check if the component script file exists:
        if (!file_exists($scripts)) {
            Report::error("Component script file not found: {$scripts}");
            return false;
        } 

        // Rename the component files (PHP, CSS, and JS):
        rename($file,      sprintf("%s/%s.component.php", dirname($file),    $new));
        rename($style,     sprintf("%s/%s.component.css", dirname($style),   $new));
        rename($scripts,   sprintf("%s/%s.component.js",  dirname($scripts), $new));

        // Rename the component directory, this must be done last, otherwise
        // the component files will not be found:
        rename($directory, sprintf("%s/%s", dirname($directory), $new));

        // Rename was successful:
        return true;
    }

    /** 
     * Get component directory from config.json. This
     * will return the directory of the given component starting from
     * the root directory.
     * 
     * @param string $component
     * The component name
     * 
     * @return string
     * The component directory
     */
    private static function getDirectory(string $component): string {
        $root      = sprintf("%s%s", dirname(__DIR__, 2), Controller::getRootFolder());
        $directory = sprintf("%s%s/%s", $root, Directories::getComponentsDirectory(), $component);

        // Check if the directory exists:
        if (!file_exists($directory)) {
            Report::error("Component directory '{$directory}' not found");
        }

        // Return directory:
        return $directory;
    }

    /**
     * Get component file from its directory.
     * 
     * @param string $component
     * The component name
     * 
     * @return string
     * The component file or null if not found
     */
    private static function getFile(string $component): string {

        // Get component directory:
        $directory  = Components::getDirectory($component);
        $file       = sprintf("%s/%s.component.php", $directory, $component);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Component file '{$file}' not found");
        }

        // Return file:
        return $file;
    }

    /**
     * Get component style from its directory.
     * 
     * @param string $component
     * The component name
     * 
     * @return string
     * The component style or null if not found
     */
    public static function getStyle(string $component): string {

        // Get component directory:
        $directory  = Components::getDirectory($component);
        $file       = sprintf("%s/%s.component.css", $directory, $component);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Component file '{$file}' not found");
        }

        // Return file:
        return $file;
    }

    /**
     * Get component script from its directory.
     * 
     * @param string $component
     * The component name
     * 
     * @return string
     * The component script or null if not found
     */
    public static function getScript(string $component): string {

        // Get component directory:
        $directory  = Components::getDirectory($component);
        $file       = sprintf("%s/%s.component.js", $directory, $component);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Component file '{$file}' not found");
        }

        // Return file:
        return $file;
    }    

    /**
     * Get the components path from config.json.
     * 
     * @return object
     * The components object
     */
    private static function path(): ?object {
        return Config::get()->directories->components;
    }
}
