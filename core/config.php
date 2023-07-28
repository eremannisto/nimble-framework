<?php
// Dependancies:
if (!class_exists('Report')) {
    require_once(__DIR__ . '/report.php');
}

/**
 * Config class will be used to read and write to the config.json file.
 * The get and set functions can be also used to write on any other
 * JSON file on the server.
 * 
 * @version 0.0.0
 */
class Config {

    // Cache the config object
    private static $cache   = null;

    /**
     * Reads and decodes the config.json file. If the
     * config.json file is not cached, it will be read and cached, otherwise
     * the cached version will be returned.
     * 
     * @param string $path
     * The path of the JSON object to retrieve, if empty or null, the entire
     * config object will be returned.
     * 
     * @param string $file
     * The path to the config.json file.
     * 
     * @return mixed
     * Returns the value of the JSON object at the specified path.
     */
    public static function get(?string $path = "", string $file = "/config.json"): mixed {

        // Check if the config file is cached, if not, read and cache it.
        if (Config::$cache === null) {
            Config::$cache = Config::read($file);
        }

        // Get the cached config object
        $config = Config::$cache;

        // Check if the path parameter is empty or null, 
        // return the entire config object
        if (empty($path) || is_null($path)) { 
            return $config; 
        }

        // Get the keys of the path to traverse the config object
        $keys = explode('/', $path);

        // Go through each key and search for the value, if the key doesn't exist, return null
        foreach ($keys as $key) {

            // Check if the key exists in the config object
            if (!isset($config->{$key})) {
                Report::exception(new RuntimeException("Key '$key' doesn't exist in the config file"));
            }

            // Get the value of the key
            $config = $config->{$key};
        }

        return $config;
    }

    public static function set(string $path, mixed $data, ?string $file = "/config.json"): bool {

        // Path cannot be empty, otherwise the risk of overwriting the entire 
        // config file is too high
        if (empty($path)) {
            Report::exception(new InvalidArgumentException("Path cannot be empty"));
        }

        // Data must be scalar or null
        if (!is_scalar($data) && !is_null($data)) {
            Report::exception(new InvalidArgumentException("Data must be scalar or null"));
        }
    
        // Retrieve the entire JSON object to update it 
        // and split the path into array of keys
        $config = Config::get('');
        $keys   = explode('/', $path);
    
        // Assigns the reference of the $config variable to the $current variable.
        // This allows changes made to $current to also affect $config.
        $current = &$config;
    
        // Traverse the path and update the value
        foreach ($keys as $key) {

            // If the current object is an array and the key is numeric
            if (is_array($current) && is_numeric($key)) {

                // Update the current object to the value at the numeric key
                $current = &$current[$key];
            } 
            
            // If the current object is an object
            elseif (is_object($current)) {
                
                // If the current object does not have a property with the key
                if (!property_exists($current, $key)) {

                    // Create a new object as the property value
                    $current->{$key} = new stdClass();
                }

                // Update the current object to the value of the property with the key
                $current = &$current->{$key};
            } 
            
            // If the current object is not an array or an object
            else {
                Report::exception(new RuntimeException("Cannot traverse path: '$path'"));
            }
        }

        // Update the value of the current object
        $current = $data;
    
        // Save the updated JSON object back to the file
        if (!Config::write(dirname(__DIR__, 1) . $file, $config)) {
            Report::exception(new RuntimeException("Configuration file could not be updated"));
        }
    
        return true;
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
    private static function read(string $file = "/config.json"): ?object {

        // The location of the JSON file:
        $path = dirname(__DIR__, 1) . $file;

        // Check if the file exists:
        if (!file_exists($path)) {
            $exception = new Exception("Configuration file not found: $path");
            Report::exception($exception);
        }
    
        // Read the JSON file:
        $json = file_get_contents($path);
        if ($json === false) {
            $exception = new Exception("Error reading configuration file: $path");
            Report::exception($exception);
        }
    
        // Decode the JSON data:
        $data = json_decode($json);
        if ($data === null) {
            $exception = new Exception("Error decoding configuration file: $path");
            Report::exception($exception);
        }
    
        // Return the decoded JSON data:
        return $data;
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
    private static function write(string $file, $data): bool {

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
