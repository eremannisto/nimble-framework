<?php declare(strict_types=1);

/**
 * This class provides methods to read and update the 
 * config.json file.
 */
class Config {

    /**
     * The cached config object, this is updated when
     * the JSON::get method is called.
     */
    public static $cache = null;

    /**
     * The path to the config.json file from the
     * root directory of the project.
     */
    private static $file = "/config.json";

    /**
     * Retrieves configuration data from the specified location.
     *
     * @param string|null $location 
     * The location of the configuration file. Defaults to an empty string.
     * 
     * @return mixed 
     * The configuration data.
     */
    public static function get(?string $location = ""): mixed {
        return JSON::get($location, self::$file, get_called_class());
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
}
