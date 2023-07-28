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

if(!class_exists('Templates')) {
    require_once(__DIR__ . '/templates.php');
}

if(!class_exists('Components')) {
    require_once(__DIR__ . '/components.php');
}

class Pages {

    // Variable to cache pages:
    private static $cache;

    public static function this(): string {
        $parameter  = Controller::getPageParameter();           // Get parameter name
        $index      = Controller::getIndexName() ?? 'page';     // Get index name, or fallback to 'page'
        return isset($_GET[$parameter]) ? $_GET[$parameter] : $index;
    }

    /**
     * Gets all pages from package.json and caches them.
     * If pages are already cached, the cached pages
     * will be returned.
     * 
     * @return object|null
     * The pages object or null if not found
     */
    public static function pages(): ?object {

        // Check if pages are cached:
        if (!isset(Pages::$cache)) {

            // Get pages from package.json and cache them:
            $pages        = Package::get()->pages ?? null;
            Pages::$cache = $pages;
        }

        // Return pages:
        return Pages::$cache;
    }

    /**
     * Get a specific page by it's key name.
     * 
     * @param string $page
     * The page by it's key name
     * 
     * @return object|null
     * The page object or null if not found
     */
    public static function get(string $page): ?object {

        // Get cached pages list:
        $pages = Pages::pages();

        // Check if page exists:
        if (!isset($pages->$page)) {
            Report::error("Page '{$page}' not found");
        }

        // Return page:
        return $pages->$page ?? null;
    }

    /**
     * Set a specific page by it's key name.   
     * 
     * @param string $page
     * The page by it's key name
     * 
     * @param object $object
     * The page object
     */
    public static function set(string $page, object $object): bool {

        // Get cached pages list:
        $pages = Pages::pages();

        // Make sure the page doesn't exists:
        if (isset($pages->$page)) {
            Report::notice("Page '{$page}' already exists");
            return false;
        }

        // Set page:
        $pages->$page = $object;

        // Update the package.json:
        Package::get()->pages = $pages;
        Package::set(Package::get());

        // Update cache:
        Pages::$cache = $pages;

        // Return true:
        return true;
    }

    /**
     * Require a page by its key name. This will
     * require the page file, for example: test.page.php
     * 
     * @param string $page
     * The page name
     * 
     * @return void
     * Returns nothing
     */
    public static function require(string $page): void {

        // Get page file:
        $file = Pages::getFile($page);

        // Require page file:
        require_once($file);
    }

    /**
     * Load page components and templates.
     * 
     * @param string $page
     * The page name
     * 
     * @return void
     * Returns nothing
     */
    public static function load(string $page): void {
        // Get page templates and components list:
        $pages      = Pages::pages();
        $templates  = $pages->$page->templates  ?? null;
        $components = $pages->$page->components ?? null;

        // For each template, require it:
        if (isset($templates)) {
            foreach ($templates as $template) {
                Templates::require($template);
            }
        }

        // For each component, require it:
        if (isset($components)) {
            foreach ($components as $component) {
                Components::require($component);
            }
        }
    }

    /**
     * Remove a specific page from package.json.
     * 
     * @param string $page
     * The page by it's key name
     * 
     * @return bool
     * True if the page was removed, false if not
     */
    public static function remove(string $page): bool {

        // Get cached pages list:
        $pages = Pages::pages();

        // Make sure the page exists:
        if (!isset($pages->$page)) {
            Report::error("Page '{$page}' not found");
        }

        // Remove page from package.json:
        unset($pages->$page);

        // Update the package.json:
        Package::get()->pages = $pages;
        Package::set(Package::get());

        // Update cache:
        Pages::$cache = $pages;

        // Return true:
        return true;
    }

    /**
     * Create a new page with the given name. This will create
     * a new directory with the page name and empty page
     * files (PHP, CSS, and JS). At the end the page cache will
     * be updated.
     *
     * @param string $page
     * The name of the new page to create
     * 
     * @return bool
     * True if the page was created, false otherwise
     */
    public static function create(string $page): bool {

        // Get pages from package.json:
        $pages = Pages::pages();

        // Check that the page name is not already in use:
        if (isset($pages->$page)) {
            Report::notice("Page '{$page}' already exists");
            return false;
        }

        // Create page files:
        $root      = sprintf("%s%s", dirname(__DIR__, 2), Controller::getRootFolder());
        $directory = sprintf("%s%s/%s", $root, Directories::getpagesDirectory(), $page);
        $file      = sprintf("%s/%s.page.php", $directory, $page);
        $style     = sprintf("%s/%s.page.css", $directory, $page);
        $scripts   = sprintf("%s/%s.page.js",  $directory, $page);

        // Check if the folder exists:
        if (file_exists($directory)) {
            Report::error("Page folder already exists: {$directory}");
            return false;
        } 

        // Check if the page file exists:
        if (file_exists($file)) {
            Report::notice("Page file already exists: {$file}");
            return false;
        } 

        // Check if the page style file exists:
        if (file_exists($style)) {
            Report::notice("Page style file already exists: {$style}");
            return false;
        } 

        // Check if the page script file exists:
        if (file_exists($scripts)) {
            Report::notice("Page script file already exists: {$scripts}");
            return false;
        } 

        // Create the page directory and files (PHP, CSS, and JS):
        mkdir($directory);
        file_put_contents($file,    "");
        file_put_contents($style,   "");
        file_put_contents($scripts, "");

        // Add the new page to the cache:
        $pages->$page = $page;
        Pages::$cache = $pages;

        // Update package.json:
        $package = Package::get();
        $package->pages->$page = (object) [
            'name'   => $page,
            'style'  => [],
            'script' => []
        ];

        // Save package.json:
        Package::set($package);

        // Return true if the page was created:
        Report::success("Created page: {$page}");
        return true;
    }

    /**
     * Check whether a page exists or not.
     * 
     * @param string $page
     * The page name
     * 
     * @return bool
     * True if the page exists, false otherwise
     */
    public static function exists(string $page): bool {
        return isset(Pages::pages()->$page);
    }

    /**
     * Upddate a specific page by it's key name. This will
     * update the page name and the page files. At the
     * end the page cache will be updated.
     * 
     * @param string $old
     * The page by it's key name
     * 
     * @param string $new
     * The new page name
     * 
     * @return bool
     * True if the page was updated, false otherwise
     */
    public static function update(string $old, string $new): bool {

        $pages = Pages::pages();

        // Check that the page name is not already in use:
        if (isset($pages->$new)) {
            Report::notice("Page '{$new}' already exists");
            return false;
        }

        // Check that the page name exists:
        if (!isset($pages->$old)) {
            Report::notice("Page '{$old}' does not exist");
            return false;
        }

        // Create the new page object:
        $object = (object) [
            'name'    => $new,
            'styles'  => $pages->$old->styles  ? $pages->$old->styles  : [],
            'scripts' => $pages->$old->scripts ? $pages->$old->scripts : [],
        ];

        // Set the new page in package.json:
        if(!Pages::set($new, $object)) {
            Report::notice("Failed to set the new page: {$new}");
            return false;
        }

        // Remove the old page from package.json:
        if(!Pages::remove($old)) {
            Report::notice("Failed to remove the old page: {$old}");
            return false;
        }

        // Rename the page files and directory:
        if(!Pages::rename($old, $new)) {
            Report::notice("Failed to rename page: {$old} -> {$new}");
            return false;
        }

        // Return true if the page was updated:
        Report::success("Updated page: {$old} -> {$new}");
        return true;
    }

    /**
     * Rename a specific page by it's key name. This will
     * update the page name and the page files.
     */
    private static function rename(string $old, string $new): bool {

        // Get pages from package.json:
        $pages = Pages::pages();

        // Update page files:
        $directory = Pages::getDirectory($old);
        $file      = Pages::getFile($old);
        $style     = Pages::getStyle($old);
        $scripts   = Pages::getScript($old);

        // Check if the folder exists:
        if (!file_exists($directory)) {
            Report::error("Page folder not found: {$directory}");
            return false;
        } 

        // Check if the page file exists:
        if (!file_exists($file)) {
            Report::error("Page file not found: {$file}");
            return false;
        } 

        // Check if the page style file exists:
        if (!file_exists($style)) {
            Report::error("Page style file not found: {$style}");
            return false;
        } 

        // Check if the page script file exists:
        if (!file_exists($scripts)) {
            Report::error("Page script file not found: {$scripts}");
            return false;
        } 

        // Rename the page files (PHP, CSS, and JS):
        rename($file,      sprintf("%s/%s.page.php", dirname($file),    $new));
        rename($style,     sprintf("%s/%s.page.css", dirname($style),   $new));
        rename($scripts,   sprintf("%s/%s.page.js",  dirname($scripts), $new));

        // Rename the page directory, this must be done last, otherwise
        // the page files will not be found:
        rename($directory, sprintf("%s/%s", dirname($directory), $new));

        // Rename was successful:
        return true;
    }

    /** 
     * Get page directory from package.json. This
     * will return the directory of the given page starting from
     * the root directory.
     * 
     * @param string $page
     * The page name
     * 
     * @return string
     * The page directory
     */
    private static function getDirectory(string $page): string {
        $root      = sprintf("%s%s", dirname(__DIR__, 2), Controller::getRootFolder());
        $directory = sprintf("%s%s/%s", $root, Directories::getPagesDirectory(), $page);

        // Check if the directory exists:
        if (!file_exists($directory)) {
            Report::error("Page directory '{$directory}' not found");
        }

        // Return directory:
        return $directory;
    }

    /**
     * Get page file from its directory.
     * 
     * @param string $page
     * The page name
     * 
     * @return string
     * The page file or null if not found
     */
    private static function getFile(string $page): string {

        // Get page directory:
        $directory  = Pages::getDirectory($page);
        $file       = sprintf("%s/%s.page.php", $directory, $page);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Page file '{$file}' not found");
        }

        // Return file:
        return $file;
    }

    /**
     * Get page style from its directory.
     * 
     * @param string $page
     * The page name
     * 
     * @return string
     * The page style or null if not found
     */
    public static function getStyle(string $page): string {

        // Get page directory:
        $directory  = Pages::getDirectory($page);
        $file       = sprintf("%s/%s.page.css", $directory, $page);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Page file '{$file}' not found");
        }

        // Return file:
        return $file;
    }

    /**
     * Get page script from its directory.
     * 
     * @param string $page
     * The page name
     * 
     * @return string
     * The page script or null if not found
     */
    public static function getScript(string $page): string {

        // Get page directory:
        $directory  = Pages::getDirectory($page);
        $file       = sprintf("%s/%s.page.js", $directory, $page);

        // Check if the file exists:
        if (!file_exists($file)) {
            Report::error("Page file '{$file}' not found");
        }

        // Return file:
        return $file;
    }    

    /**
     * Add a page template to a page.
     * 
     * @param string $page
     * The page name the template is being added to
     * 
     * @param string $template
     * The template that is being added to the page
     * 
     * @return bool
     * True if the template was added
     */
    public static function addTemplate(string $page, string $template): bool {

        // Get pages from package.json:
        $pages      = Pages::pages();
        $templates  = Templates::templates();

        // Check that the page name exists:
        if (!isset($pages->$page)) {
            Report::notice("Page '{$page}' does not exist");
            return false;
        }

        // Check that the template name exists:
        if (!isset($templates->$template)) {
            Report::notice("Template '{$template}' doesn't exists");
            return false;
        }

        // Make sure the template is not already part of the page 
        // template list:
        if (in_array($template, $pages->$page->templates)) {
            Report::notice("Template '{$template}' is already part of the page '{$page}'");
            return false;
        }

        // Add the template to the pages template list:
        $pages->$page->templates[] = $template;

        // Set the new template in package.json:
        if(!Pages::set($page, $pages)) {
            Report::notice("Failed to set the new template: {$template}");
            return false;
        }

        // Return true if the template was updated:
        Report::success("Added template: {$template}");
        return true;
    }

    /**
     * Remove a page template from the page template list.
     * 
     * @param string $page
     * The page name the template is being removed from
     * 
     * @param string $template
     * The template name to be removed
     * 
     * @return bool
     * True if the template was removed successfully,
     * false otherwise
     */
    public static function removeTemplate(string $page, string $template): bool {
            
        // Get page from package.json:
        $pages      = Pages::pages();
        $templates  = Templates::templates();

        // Check that the page exists:
        if (!isset($pages->$page)) {
            Report::notice("Page '{$page}' does not exist");
            return false;
        }

        // Check that the template exists:
        if (!isset($templates->$template)) {
            Report::notice("Template '{$template}' does not exist");
            return false;
        }

        // Remove the template from the page template list:
        $pages->$page->templates = array_diff($pages->$page->templates, [$template]);

        // Set the new updated template list in package.json:
        if(!Pages::set($page, $pages)) {
            Report::notice("Failed to remove the template: {$template} from the page: {$page} settings.");
            return false;
        }

        // Return true if the template was updated:
        Report::success("Removed template: {$template} from the page: {$page} settings.");
        return true;
    }

    /**
     * Add a page component to a page.
     * 
     * @param string $page
     * The page name to add the component to.
     * 
     * @param string $component
     * The component name to add to the page.
     * 
     * @return bool
     * True if the component was added
     */
    public static function addComponent(string $page, string $component): bool {

        // Get pages from package.json:
        $pages       = Pages::pages();
        $components  = Components::component();

        // Check that the page name exists:
        if (!isset($pages->$page)) {
            Report::notice("Page '{$page}' does not exist");
            return false;
        }

        // Check that the component name exists:
        if (!isset($components->$component)) {
            Report::notice("Component '{$component}' doesn't exists");
            return false;
        }

        // Make sure the component is not already part of the page 
        // component list:
        if (in_array($component, $pages->$page->components)) {
            Report::notice("Component '{$component}' is already part of the page '{$page}'");
            return false;
        }

        // Add the component to the pages component list:
        $pages->$page->components[] = $component;

        // Set the new component in package.json:
        if(!Pages::set($page, $pages)) {
            Report::notice("Failed to set the new component: {$component}");
            return false;
        }

        // Return true if the component was updated:
        Report::success("Added component: {$component}");
        return true;
    }

    /**
     * Remove a page component from the page component list.
     * 
     * @param string $page
     * The page name the component is being removed from
     * 
     * @param string $component
     * The component name to be removed
     * 
     * @return bool
     * True if the component was removed successfully,
     * false otherwise
     */
    public static function removeComponent(string $page, string $component): bool {
            
        // Get page from package.json:
        $pages       = Pages::pages();
        $components  = Components::components();

        // Check that the page exists:
        if (!isset($pages->$page)) {
            Report::notice("Page '{$page}' does not exist");
            return false;
        }

        // Check that the component exists:
        if (!isset($components->$template)) {
            Report::notice("Component '{$component}' does not exist");
            return false;
        }

        // Remove the component from the page component list:
        $pages->$page->components = array_diff($pages->$page->components, [$component]);

        // Set the new updated component list in package.json:
        if(!Pages::set($page, $pages)) {
            Report::notice("Failed to remove the component: {$component} from the page: {$page} settings.");
            return false;
        }

        // Return true if the component was updated:
        Report::success("Removed component: {$component} from the page: {$page} settings.");
        return true;
    }

    /**
     * Get page meta title.
     * 
     * @param string $page
     * The page name to get the meta title for.
     * 
     * @return string
     * The title of the page.
     */
    public static function getMetaTitle(string $page): string {
        $pages = Pages::pages();
        return $pages->$page->meta->title ?? "";
    }

    /**
     * Set page meta title.
     * 
     * @param string $page
     * The page name to set the meta title for.
     * 
     * @param string $title
     * The title to set for the page.
     * 
     * @return bool
     * True if the title was set successfully,
     */
    public static function setMetaTitle(string $page, string $title): bool {
        $pages = Pages::pages();
        $pages->$page->meta->title = $title;
        return Pages::set($page, $pages);
    }

    /**
     * Get page meta description.
     * 
     * @param string $page
     * The page name to get the meta description for.
     * 
     * @return string
     * The description of the page.
     * 
     * @return string
     * The description of the page.
     */
    public static function getMetaDescription(string $page): string {
        $pages = Pages::pages();
        return $pages->$page->meta->description ?? "";
    }

    /**
     * Set page meta description.
     * 
     * @param string $page
     * The page name to set the meta description for.
     * 
     * @param string $description
     * The description to set for the page.
     * 
     * @return bool
     * True if the description was set successfully, false otherwise.
     */
    public static function setMetaDescription(string $page, string $description): bool {
        $pages = Pages::pages();
        $pages->$page->meta->description = $description;
        return Pages::set($page, $pages);
    }

    /**
     * Get page meta keywords.
     * 
     * @param string $page
     * The page name to get the meta keywords for.
     * 
     * @return string
     * The keywords of the page.
     */
    public static function getMetaKeywords(string $page): string {
        $pages = Pages::pages();
        return $pages->$page->meta->keywords ?? "";
    }

    /**
     * Set page meta keywords.
     * 
     * @param string $page
     * The page name to set the meta keywords for.
     * 
     * @param string $keywords
     * The keywords to set for the page.
     * 
     * @return bool
     * True if the keywords were set successfully, false otherwise.
     */
    public static function setMetaKeywords(string $page, string $keywords): bool {
        $pages = Pages::pages();
        $pages->$page->meta->keywords = $keywords;
        return Pages::set($page, $pages);
    }

    /**
     * Get page meta type.
     * 
     * @param string $page
     * The page name to get the meta type for.
     * 
     * @return string
     * The type of the page.
     */
    public static function getMetaType(string $page): string {
        $pages = Pages::pages();
        return $pages->$page->meta->type ?? "";
    }

    /**
     * Set page meta type.
     * 
     * @param string $page
     * The page name to set the meta type for.
     * 
     * @param string $type
     * The type to set for the page.
     * 
     * @return bool
     * True if the type was set successfully, false otherwise.
     */
    public static function setMetaType(string $page, string $type): bool {
        $pages = Pages::pages();
        $pages->$page->meta->type = $type;
        return Pages::set($page, $pages);
    }

    /**
     * Get page language.
     * 
     * @param string $page
     * The page name to get the language for.
     * 
     * @return string
     * The language of the page.
     */
    public static function getMetaLanguage(string $page): string {
        $pages = Pages::pages();
        return $pages->$page->meta->language ?? "";
    }
    
    /**
     * Set page language.
     * 
     * @param string $page
     * The page name to set the language for.
     * 
     * @param string $language
     * The language to set for the page.
     * 
     * @return bool
     * True if the language was set successfully, false otherwise.
     */
    public static function setMetaLanguage(string $page, string $language): bool {
        $pages = Pages::pages();
        $pages->$page->language = $language;
        return Pages::set($page, $pages);
    }

    /**
     * Get page meta image.
     * 
     * @param string $page
     * The page name to get the meta image for.
     * 
     * @return string
     * The image path of the meta image.
     */
    public static function getMetaImage(string $page): string {
        $pages = Pages::pages();
        return $pages->$page->meta->image ?? "";
    }

    /**
     * Set page meta image.
     * 
     * @param string $page
     * The page name to set the meta image for.
     * 
     * @param string $image
     * The image path to set as the meta image.
     * 
     * @return bool
     * True if the meta image was set successfully,
     */
    public static function setMetaImage(string $page, string $image): bool {
        $pages = Pages::pages();
        $pages->$page->meta->image = $image;
        return Pages::set($page, $pages);
    }



    // TODO:
    // - Add page restrictions
    // - Add page permissions
    // - Add page creation date
    // - Add page last modified date
    // - Add page public boolean
    // Setters and getters for styles and scripts
}
