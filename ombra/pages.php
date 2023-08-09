<?php

// Dependencies:
if(!class_exists('JSON'))        require_once(__DIR__ . '/json.php');
if(!class_exists('Report'))      require_once(__DIR__ . '/report.php');

class Pages {

    /**
     * The cached pages object, this is updated when
     * the JSON::get method is called. Works also as
     * a whitelist for pages.
     */
    public static mixed $cache;

    /**
     * The path to the pages.json file from the
     * root directory of the project.
     */
    private static string $file = "/pages.json";

    /**
     * Retrieves favicons data from the specified location.
     *
     * @param string|null $location 
     * The location of the pages.json file. Defaults to an empty string.
     * 
     * @return mixed 
     * The pages data.
     */
    public static function get(?string $location = ""): mixed {
        $directory = sprintf("%s%s", Directories::get("pages"), self::$file);
        return JSON::get($location, $directory, get_called_class());
    }

    /**
     * Sets the given data at the specified location in the json file.
     *
     * @param string $location 
     * The location where the data should be set.
     * 
     * @param mixed $data 
     * The data to be set.
     * 
     * @return bool 
     * Returns true if the data was successfully set, false otherwise.
     */
    public static function set(string $location, mixed $data): bool {
        return JSON::set($location, $data, self::$file, get_called_class());
    }

    /**
     * Deletes the data at the specified location in the json file.
     * 
     * @param string $location
     * The location where the data should be deleted.
     * 
     * @return bool
     * Returns true if the data was successfully deleted, false otherwise.
     */
    public static function remove(string $location): bool {
        return JSON::remove($location, self::$file, get_called_class());
    }

    /**
     * Require a page by its key name. This will
     * require the page file, for example: test.page.php
     * from the pages directory.
     * 
     * @param string $page
     * The page name
     * 
     * @return void
     * Returns nothing
     */
    public static function require(string $page): void {
        $directory = Directories::get("pages", dirname(__DIR__, 1)) . "/$page";
        file_exists($path = "$directory/$page.page.php") 
        ? require_once $path : Report::error("Page '$page' not found");
    }

    /**
     * Components can be loaded in many ways, either by requiring them
     * directly at the top of the page by using the Components::require method,
     * or by adding them to the page components list in the pages.json file.
     * 
     * @param string $page
     * The page name
     * 
     * @return void
     * Returns nothing
     */
    public static function load(string $page): void {

    }
}