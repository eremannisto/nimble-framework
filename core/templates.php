<?php

// Dependencies:
if (!class_exists('Package')) {
    require_once(__DIR__ . '/package.php');
}

if(!class_exists('Controller')) {
    require_once(__DIR__ . '/controller.php');
}

if(!class_exists('Directories')) {
    require_once(__DIR__ . '/directories.php');
}

if(!class_exists('Components')) {
    require_once(__DIR__ . '/components.php');
}

class Templates {

    // Variable to cache templates:
    private static $cache;

    /**
     * Gets all templates from package.json and caches them.
     * If templates are already cached, the cached templates
     * will be returned.
     * 
     * @return object|null
     * The templates object or null if not found
     */
    public static function templates(): ?object {

        // Check if templates are cached:
        if (!isset(Templates::$cache)) {

            // Get templates from package.json and cache them:
            $templates       = Package::get()->templates ?? null;
            Templates::$cache = $templates;
        }

        // Return templates:
        return Templates::$cache;
    }

    /**
     * Get a specific template by it's key name.
     * 
     * @param string $template
     * The template by it's key name
     * 
     * @return object|null
     * The template object or null if not found
     */
    public static function get(string $template): ?object {

        // Get cached templates list:
        $templates = Templates::templates();

        // Check if template exists:
        if (!isset($templates->$template)) {
            Report::error("Template '{$template}' not found");
        }

        // Return template:
        return $templates->$template ?? null;
    }

    /**
     * Set a specific template by it's key name.   
     * 
     * @param string $template
     * The template by it's key name
     * 
     * @param object $object
     * The template object
     */
    public static function set(string $template, object $object): bool {

        // Get cached templates list:
        $templates = Templates::templates();

        // Make sure the template doesn't exists:
        if (isset($templates->$template)) {
            Report::notice("Template '{$template}' already exists");
            return false;
        }

        // Set template:
        $templates->$template = $object;

        // Update the package.json:
        Package::get()->templates = $templates;
        Package::set(Package::get());

        // Update cache:
        Templates::$cache = $templates;

        // Return true:
        return true;
    }

    /**
     * Require a template by its key name. This will
     * require the template file, for example: test.template.php
     * 
     * @param string $template
     * The template name
     * 
     * @return void
     * Returns nothing
     */
    public static function require(string $template): void {

        // Get template file:
        $file = Templates::getFile($template);

        // Get template components list:
        $templates  = Templates::templates();
        $components = $templates->$template->components ?? null;

        // Require template file:
        require_once($file);

        // For each component, require it:
        if (isset($components)) {
            foreach ($components as $component) {
                Components::require($component);
            }
        }
    }

    /**
     * Remove a specific template from package.json.
     * 
     * @param string $template
     * The template by it's key name
     * 
     * @return bool
     * True if the template was removed, false if not
     */
    public static function remove(string $template): bool {

        // Get cached templates list:
        $templates = Templates::templates();

        // Make sure the template exists:
        if (!isset($templates->$template)) {
            Report::error("Template '{$template}' not found");
        }

        // Remove template from package.json:
        unset($templates->$template);

        // Update the package.json:
        Package::get()->templates = $templates;
        Package::set(Package::get());

        // Update cache:
        Templates::$cache = $templates;

        // Return true:
        return true;
    }

    /**
     * Create a new template with the given name. This will create
     * a new directory with the template name and empty template
     * files (PHP, CSS, and JS). At the end the template cache will
     * be updated.
     *
     * @param string $template
     * The name of the new template to create
     * 
     * @return bool
     * True if the template was created, false otherwise
     */
    public static function create(string $template): bool {

        // Get templates from package.json:
        $templates = Templates::templates();

        // Check that the template name is not already in use:
        if (isset($templates->$template)) {
            Report::notice("Template '{$template}' already exists");
            return false;
        }

        // Create template files:
        $root      = sprintf("%s%s", dirname(__DIR__, 2), Controller::getRootFolder());
        $directory = sprintf("%s%s/%s", $root, Directories::getTemplatesDirectory(), $template);
        $file      = sprintf("%s/%s.template.php", $directory, $template);
        $style     = sprintf("%s/%s.template.css", $directory, $template);
        $scripts   = sprintf("%s/%s.template.js",  $directory, $template);

        // Check if the folder exists:
        if (file_exists($directory)) {
            Report::error("Template folder already exists: {$directory}");
            return false;
        } 

        // Check if the template file exists:
        if (file_exists($file)) {
            Report::notice("Template file already exists: {$file}");
            return false;
        } 

        // Check if the template style file exists:
        if (file_exists($style)) {
            Report::notice("Template style file already exists: {$style}");
            return false;
        } 

        // Check if the template script file exists:
        if (file_exists($scripts)) {
            Report::notice("Template script file already exists: {$scripts}");
            return false;
        } 

        // Create the template directory and files (PHP, CSS, and JS):
        mkdir($directory);
        file_put_contents($file,    "");
        file_put_contents($style,   "");
        file_put_contents($scripts, "");

        // Add the new template to the cache:
        $templates->$template = $template;
        Templates::$cache = $templates;

        // Update package.json:
        $package = Package::get();
        $package->templates->$template = (object) [
            'name'   => $template,
            'style'  => [],
            'script' => []
        ];

        // Save package.json:
        Package::set($package);

        // Return true if the template was created:
        Report::success("Created template: {$template}");
        return true;
    }

    /**
     * Upddate a specific template by it's key name. This will
     * update the template name and the template files. At the
     * end the template cache will be updated.
     * 
     * @param string $old
     * The template by it's key name
     * 
     * @param string $new
     * The new template name
     * 
     * @return bool
     * True if the template was updated, false otherwise
     */
    public static function update(string $old, string $new): bool {

        $templates = Templates::templates();

        // Check that the template name is not already in use:
        if (isset($templates->$new)) {
            Report::notice("Template '{$new}' already exists");
            return false;
        }

        // Check that the template name exists:
        if (!isset($templates->$old)) {
            Report::notice("Template '{$old}' does not exist");
            return false;
        }

        // Create the new template object:
        $object = (object) [
            'name'       => $new,
            'components' => $templates->$old->components ? $templates->$old->components : [],
            'styles'     => $templates->$old->styles     ? $templates->$old->styles     : [],
            'scripts'    => $templates->$old->scripts    ? $templates->$old->scripts    : []
        ];

        // Set the new template in package.json:
        if(!Templates::set($new, $object)) {
            Report::notice("Failed to set the new template: {$new}");
            return false;
        }

        // Remove the old template from package.json:
        if(!Templates::remove($old)) {
            Report::notice("Failed to remove the old template: {$old}");
            return false;
        }

        // Rename the template files and directory:
        if(!Templates::rename($old, $new)) {
            Report::notice("Failed to rename template: {$old} -> {$new}");
            return false;
        }

        // Return true if the template was updated:
        Report::success("Updated template: {$old} -> {$new}");
        return true;
    }

    /**
     * Rename a specific template by it's key name. This will
     * update the template name and the template files.
     */
    private static function rename(string $old, string $new): bool {

        // Get templates from package.json:
        $templates = Templates::templates();

        // Update template files:
        $directory = Templates::getDirectory($old);
        $file      = Templates::getFile($old);
        $style     = Templates::getStyle($old);
        $scripts   = Templates::getScript($old);

        // Check if the folder exists:
        if (!file_exists($directory)) {
            Report::error("Template folder not found: {$directory}");
            return false;
        } 

        // Check if the template file exists:
        if (!file_exists($file)) {
            Report::error("Template file not found: {$file}");
            return false;
        } 

        // Check if the template style file exists:
        if (!file_exists($style)) {
            Report::error("Template style file not found: {$style}");
            return false;
        } 

        // Check if the template script file exists:
        if (!file_exists($scripts)) {
            Report::error("Template script file not found: {$scripts}");
            return false;
        } 

        // Rename the template files (PHP, CSS, and JS):
        rename($file,      sprintf("%s/%s.template.php", dirname($file),    $new));
        rename($style,     sprintf("%s/%s.template.css", dirname($style),   $new));
        rename($scripts,   sprintf("%s/%s.template.js",  dirname($scripts), $new));

        // Rename the template directory, this must be done last, otherwise
        // the template files will not be found:
        rename($directory, sprintf("%s/%s", dirname($directory), $new));

        // Rename was successful:
        return true;
    }

    /** 
     * Get template directory from package.json. This
     * will return the directory of the given template starting from
     * the root directory.
     * 
     * @param string $template
     * The template name
     * 
     * @return string
     * The template directory
     */
    private static function getDirectory(string $template): string {
        $root      = sprintf("%s%s", dirname(__DIR__, 2), Controller::getRootFolder());
        $directory = sprintf("%s%s/%s", $root, Directories::gettemplatesDirectory(), $template);

        // Check if the directory exists:
        if (!file_exists($directory)) {
            Report::error("Template directory '{$directory}' not found");
        }

        // Return directory:
        return $directory;
    }

    /**
     * Get template file from its directory.
     * 
     * @param string $template
     * The template name
     * 
     * @return string
     * The template file or null if not found
     */
    private static function getFile(string $template): string {

        // Get template directory:
        $directory  = Templates::getDirectory($template);
        $file       = sprintf("%s/%s.template.php", $directory, $template);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Template file '{$file}' not found");
        }

        // Return file:
        return $file;
    }

    /**
     * Get template style from its directory.
     * 
     * @param string $template
     * The template name
     * 
     * @return string
     * The template style or null if not found
     */
    public static function getStyle(string $template): string {

        // Get template directory:
        $directory  = Templates::getDirectory($template);
        $file       = sprintf("%s/%s.template.css", $directory, $template);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Template file '{$file}' not found");
        }

        // Return file:
        return $file;
    }

    /**
     * Get template script from its directory.
     * 
     * @param string $template
     * The template name
     * 
     * @return string
     * The template script or null if not found
     */
    public static function getScript(string $template): string {

        // Get template directory:
        $directory  = Templates::getDirectory($template);
        $file       = sprintf("%s/%s.template.js", $directory, $template);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Template file '{$file}' not found");
        }

        // Return file:
        return $file;
    }    

    /**
     * Add a template component to a template.
     * 
     * @param string $template
     * The template name
     * 
     * @param string $component
     * The component name
     * 
     * @return bool
     * True if the component was added
     */
    public static function addComponent(string $template, string $component): bool {

        // Get templates from package.json:
        $templates = Templates::templates();

        // Check that the template name exists:
        if (!isset($templates->$template)) {
            Report::notice("Template '{$template}' does not exist");
            return false;
        }

        // Check that the component name exists:
        if (isset($templates->$component)) {
            Report::notice("Component '{$component}' already exists");
            return false;
        }

        // Add the component to the templates component list:
        $templates->$template->components[] = $component;

        // Set the new template in package.json:
        if(!Templates::set($template, $templates)) {
            Report::notice("Failed to set the new template: {$component}");
            return false;
        }

        // Return true if the template was updated:
        Report::success("Added component: {$component}");
        return true;
    }

    /**
     * Remove a template component to a template.
     * 
     * @param string $template
     * The template name
     * 
     * @param string $component
     * The component name
     * 
     * @return bool
     * True if the component was removed
     */
    public static function removeComponent(string $template, string $component): bool {
            
        // Get templates from package.json:
        $templates = Templates::templates();

        // Check that the template name exists:
        if (!isset($templates->$template)) {
            Report::notice("Template '{$template}' does not exist");
            return false;
        }

        // Check that the component name exists:
        if (!isset($templates->$component)) {
            Report::notice("Component '{$component}' does not exist");
            return false;
        }

        // Remove the component from the templates component list:
        $templates->$template->components = array_diff($templates->$template->components, [$component]);

        // Set the new template in package.json:
        if(!Templates::set($template, $templates)) {
            Report::notice("Failed to remove the new component: {$component}");
            return false;
        }

        // Return true if the template was updated:
        Report::success("Removed component: {$component}");
        return true;
    }
}