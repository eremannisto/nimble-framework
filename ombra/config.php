<?php
// Dependancies:
if (!class_exists('Report'))  require_once(__DIR__ . '/report.php');

/**
 * This class provides methods to read and update a JSON configuration file.
 *
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Config
 */
class Config {

    /**
     * The cached config object.
     */
    private static $cache = null;

    /**
     * Reads and decodes the config.json file. If the
     * config.json file is not cached, it will be read and cached, otherwise
     * the cached version will be returned.
     * 
     * @param string $request
     * The path of the JSON object to retrieve, if empty or null, the entire
     * config object will be returned.
     * 
     * @param string $file
     * The path to the config.json file.
     * 
     * @return mixed
     * Returns the value of the JSON object at the specified path.
     */
    public static function get(?string $request = "", string $file = "/config.json"): mixed {

        // Check if the config file is cached, if not, read and cache it.
        if (Config::$cache === null) {
            Config::$cache = Config::read($file);
        }

        // Get the cached config object
        $config = Config::$cache;

        // Check if the request parameter is empty or null, 
        // return the entire config object
        if (empty($request) || is_null($request)) { 
            return $config; 
        }

        // Get the keys of the request to traverse the config object
        $keys = explode('/', $request);

        // Go through each key and search for the value, if the key doesn't exist, return null
        foreach ($keys as $key) {

            // Check if the key exists in the config object
            if (!isset($config->{$key})) {
                return null;
            }

            // Get the value of the key
            $config = $config->{$key};
        }

        return $config;
    }

    /**
     * Reads and decodes a JSON file.
     *
     * @param string $file  
     * The path to the JSON file.
     * 
     * @return object|null      
     * An object representation of the JSON data, or null if the file could not be 
     * read or decoded.
     */
    private static function read(string $file): ?object {

        // The location of the JSON file:
        $location = dirname(__DIR__, 1) . $file;

        // Check if the file exists:
        if (!file_exists($location)) {
            $exception = new Exception("Configuration file not found: $location");
            Report::exception($exception);
        }
    
        // Read the JSON file:
        $json = file_get_contents($location);
        if ($json === false) {
            $exception = new Exception("Error reading configuration file: $location");
            Report::exception($exception);
        }
    
        // Decode the JSON data:
        $data = json_decode($json);
        if ($data === null) {
            $exception = new Exception("Error decoding configuration file: $location");
            Report::exception($exception);
        }
    
        // Return the decoded JSON data:
        return $data;
    }


    /**
     * Sets the value of the JSON object at the specified path.
     * 
     * @param string $location
     * The path of the JSON object to update.
     * 
     * @param mixed $data
     * The new data to set.
     * 
     * @param string $file
     * The path to the json file, by default it's the config.json file.
     * 
     * @return bool
     * Returns true if the JSON object was updated successfully, otherwise false.
     */
    public static function set(string $location, mixed $data, ?string $file = "/config.json"): bool {

        // Location cannot be empty, otherwise the risk of overwriting the entire 
        // config file is too high
        if (empty($location)) {
            Report::exception(new InvalidArgumentException("Location cannot be empty"));
            return false;
        }

        // Retrieve the entire JSON object to update it 
        // and split the request into array of keys
        $config = Config::get('');
        $keys   = explode('/', $location);
    
        // Assigns the reference of the $config variable to the $current variable.
        // This allows changes made to $current to also affect $config.
        $current = &$config;
    
        // Traverse the location and update the value
        foreach ($keys as $key) {

            // If the current object is an array and the key is numeric,
            // update the current object to the value at the numeric key
            if (is_array($current) && is_numeric($key)) { $current = &$current[$key]; } 
            
            // If the current object is an object, and the key does not exist as a property,
            // create a new property with the key and set its value to a new empty object.
            // Update the current object to the new property with the key.
            elseif (is_object($current)) {
                if (!property_exists($current, $key)) {
                    $current->{$key} = new stdClass(); 
                }
                $current = &$current->{$key};
            } 
            
            // If the current object is not an array or an object
            else {
                Report::exception(new RuntimeException("Cannot traverse request: '$request'"));
                return false;
            }
        }

        // Update the value of the current object
        $current = $data;
    
        // Save the updated JSON object back to the file
        if (!Config::write(dirname(__DIR__, 1) . $file, $config)) {
            Report::exception(new RuntimeException("Configuration file could not be updated"));
            return false;
        }
    
        // Update was successful
        return true;
    }

    /**
     * Encodes and writes JSON data to a file.
     *
     * @param string $file 
     * The path to the file to write the JSON data to.
     * 
     * @param mixed $data 
     * The data to encode and write to the file.
     * 
     * @return void 
     * Returns nothing.
     */
    private static function write(string $file, mixed $data): bool {

        // Check if the file is writable
        if (!is_writable($file)) {
            Report::exception(new Exception("Error writing JSON file: File '$file' is not writable"));
            return false;
        }

        // Try to encode the data to JSON and catch any errors
        try {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } 
        catch (JsonException $exception) {
            Report::exception($exception);
            return false;
        }

        // Write the JSON data to the file
        $bytes = file_put_contents($file, $json);
        if ($bytes === false) {
            Report::exception(new Exception("Error writing JSON file: Failed to write to file '$file'"));
            return false;
        }

        return true;
    }
}
