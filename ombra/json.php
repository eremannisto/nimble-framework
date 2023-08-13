<?php

// Depencencies
if(!class_exists('Report')) require_once(__DIR__ . '/report.php');

class JSON {

    /**
     * Get the value of a nested property in an object using a path string.
     *
     * @param string|null $path 
     * The path to the nested property.
     * 
     * @param string|null $file
     * The path to the JSON file.
     * 
     * @return mixed|null 
     * The value of the nested property or null if not found.
     */
    public static function get(?string $path = null, string $file, string $class): mixed {
        $class::$cache = $class::$cache ?? JSON::read($file);
        return JSON::traverse($class::$cache, $path);
    }

    /**
     * Updates the JSON data at the specified location in the given file.
     *
     * @param string $location 
     * The location of the data to be updated.
     * 
     * @param mixed $data 
     * The updated data.
     * 
     * @param string $file 
     * The path of the JSON file.
     * 
     * @return bool
     * Returns true if the update was successful, false otherwise.
     */
    public static function set(string $path, mixed $data, string $file, string $class): bool {
        
        // Get the JSON data from the file, and
        // return false if the data could not be read.
        $json = JSON::get('', $file, $class);
        if (empty($path) || $json === NULL) {
            Report::error("Updating JSON data failed: Unable to read JSON file");
            return FALSE;
        }

        // Update the JSON data and try to write it to the file.
        // Return false if the data could not be written.
        $updated = JSON::update($json, $data, $path);        
        if ($updated === NULL || !JSON::write($file, $updated)) {
            Report::error("Updating JSON data failed: Unable to write updated JSON data");
            return FALSE;
        }
        
        // Clear the cache and return true if 
        // the data was updated successfully.
        JSON::clear($class);
        Report::success("Successfully updated JSON data");
        return TRUE;
    }
 
    /**
     * Removes a property from a JSON file.
     *
     * @param string $path 
     * The path to the property to remove.
     * 
     * @param string $file
     * The path to the JSON file.
     *
     * @return bool 
     * True if the property was removed successfully, false otherwise.
     */
    public static function remove(string $path, string $file, string $class): bool {

        $json = JSON::get('', $file, $class);
        if ($json === NULL) {
            Report::error("Updating JSON data failed: Unable to read JSON file");
            return FALSE;
        }
    
        // If the path is empty or has a single slash,
        // clear the entire JSON data and create an empty object.
        if ($path === '' || $path === '/') { $json = new stdClass; } 

        // If the path ends with a slash:
        elseif (substr($path, -1) === '/') {

            // Remove the trailing slash from the path, and
            // traverse the JSON data to get the target.
            $path   = substr($path, 0, -1);
            $target = JSON::traverse($json, $path);

            // If the target is an object or array, clear it.
            if (is_object($target) || is_array($target)) {
                foreach ($target as $key => $value) {
                    unset($target->{$key});
                }
            }

            // Otherwise json is a string or number, so change that value
            // to null and update the JSON data.
            else { $json   = JSON::update($json, NULL, $path); }

        }

        // Traverse the json data and remove the property at the path.
        else {
            $keys    = explode('/', $path);
            $current = &$json;
            foreach ($keys as $key) {
                if (is_object($current) && isset($current->$key)) {
                    if (end($keys) === $key) { unset($current->$key); } 
                    else { $current = &$current->$key; }
                } 
                else { break; }
            }
        }

        // Update the JSON data and try to write it to the file.
        // Return false if the data could not be written.
        if (!JSON::write($file, $json)) {
            Report::error("Updating JSON data failed: Unable to write updated JSON data");
            return FALSE;
        }
    
        // Clear the cache and return true if 
        // the data was updated successfully.
        JSON::clear($class);
        Report::success("Successfully updated JSON data");
        return TRUE;
    }

        

    /**
     * Reads and decodes a JSON file.
     *
     * @param string $file  
     * The path to the JSON file.
     * 
     * @return object|null      
     * An object representation of the JSON data, or null if the file 
     * could not be read or decoded.
     */
    private static function read(string $file): ?object {

        // Get the path to the JSON file, and
        // return null if the file does not exist.
        $file = dirname(__DIR__, 1) . $file;
        if (!JSON::exists($file)) 
            return null; 

        // Get the contents of the JSON file,
        // decode the contents and return the decoded data.
        $json    = JSON::getContents($file);
        $decoded = JSON::decode($json);
        return ($decoded === null) ? null : $decoded;
    }

    /**
     * Encodes and writes JSON data to a file.
     *
     * @param string $file 
     * The path to the file to write the JSON data to.

     * @param mixed $data 
     * The data to encode and write to the file.
     * 
     * @return void 
     * Returns nothing.
     */
    private static function write(string $file, mixed $data): bool {

        // Get the path to the JSON file
        $file = dirname(__DIR__, 1) . $file;

        // Encode the data and write it to the file and return true, 
        // otherwise report an error.
        if (!JSON::putContents($file, JSON::encode($data))) {
            Report::error("Failed to write data to JSON file: $file");
            return false;
        }
        return true;
    }

    /**
     * Checks whether a JSON file exists at the specified 
     * location.
     * 
     * @param string $file
     * The path to the JSON file.
     * 
     * @return bool
     * Returns true if the file exists, false otherwise.
     */
    private static function exists(string $file): bool {

        // If the file does not exist, report an error and 
        // return false.
        if (!file_exists($file)) {
            Report::warning("File does not exist: $file");
            return false;
        }
        return true;
    }

    /**
     * Returns the contents of a JSON file as a string.
     *
     * @param string $file 
     * The path to the JSON file.
     * 
     * @return string|null 
     * The contents of the JSON file as a string, or null if the file
     * could not be read.
     */
    private static function getContents(string $file): ?string {

        // Get the contents of the JSON file and return null if the
        // contents could not be read.
        $contents = file_get_contents($file);
        if (!$contents) {
            Report::error("Failed to get contents of JSON file: " . $file);
            return null;
        } 
        return $contents;
    }

    /**
     * Writes the contents of a string to a file.
     *
     * @param string $file 
     * The path to the file to write to.
     * 
     * @param string $contents 
     * The contents to write to the file.
     * 
     * @return bool 
     * Returns true if the contents were successfully written to the file, false otherwise.
     */
    private static function putContents(string $file, string $contents): bool {

        // Write the contents to the file and return false if the
        // contents could not be written.
        if (!file_put_contents($file, $contents)) {
            Report::error("Failed to put contents of JSON file: " . $file);
            return false;
        } 
        return true;
    }

    /**
     * Decodes a JSON string.
     * 
     * @param string $json
     * The JSON string to decode.
     * 
     * @return object|null
     * The decoded JSON string as an object, or null if the string 
     * could not be decoded.
     */
    private static function decode(string $json): ?object {

        // Decode the JSON string and return null if the string
        // could not be decoded.
        $decoded = json_decode($json);
        if (!$decoded) {
            Report::error("Failed to decode JSON string");
            return null;
        } 
        return $decoded;
    }

    /**
     * Encodes a JSON object.
     * 
     * @param object $object
     * The object to encode.
     * 
     * @return string|null
     * The encoded JSON object as a string, or null if the object 
     * could not be encoded.
     */
    private static function encode(mixed $object, int $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE): ?string {

        // Encode the JSON object and return null if the object
        // could not be encoded.
        $encoded = json_encode($object, $options);
        if (!$encoded) {
            Report::error("Failed to encode JSON object");
            return null;
        }
        return $encoded;
    }

    /**
     * Get the value of a nested property in an object using a path string.
     *
     * @param object $data 
     * The object to traverse.
     * 
     * @param string|null $path 
     * The path string to the nested property.
     * 
     * @return mixed|null 
     * The value of the nested property or null if not found.
     */
    private static function traverse(mixed $object, ?string $path = null): mixed {

        // If the path is null or empty, return the original object.
        if ($path === null || $path === "") { return $object; }

        // Split the path string into an array of keys.
        $keys = explode('/', $path);

        // Traverse the object using the keys. If the key 
        // is not found in the object, return null. Otherwise,
        // update the object to the value of the current key.
        foreach ($keys as $key) {
            if (!isset($object->{$key})) { return null; }
            $object = $object->{$key};
        }

        // Return the final value of the nested property.
        return $object;
    }

    /**
     * Updates a JSON object with new data.
     *
     * @param mixed $object  
     * The JSON object to update.
     * 
     * @param mixed $data  
     * The new data to update the JSON object with.
     * 
     * @param string $location  
     * The location of the value to update in the JSON object, in the format 'key1/key2/.../keyN'.
     * 
     * @return bool      
     * True if the object was updated successfully, false otherwise.
     */
    private static function update(mixed &$object, mixed $data, string $location = ''): mixed {
        // Split the location into an array of keys:
        $keys = explode('/', $location);
    
        // Traverse the JSON object to the location of the value 
        // to update and update it with the new data:
        $current = &$object;
        foreach ($keys as $key) {
    
            // If the current value is an array and the key is numeric, 
            // update the value at the specified index:
            if (is_array($current) && is_numeric($key)) { $current = &$current[$key]; } 
            
            // If the current value is an object, update the value of the 
            // specified property. If the property does not exist, create
            // a new stdClass object and assign it to the property:
            elseif (is_object($current)) {
                if (!property_exists($current, $key)) { $current->{$key} = new stdClass(); }
                $current = &$current->{$key};
            } 
    
            // If the current value is not an array or an object, 
            // return false:
            else { return FALSE; }
        }
    
        // Update the value at the specified location with the new data,
        // and return the updated value:
        $current = $data;
        return $object;
    }

    /**
     * Delete a json file.
     * 
     * @param string $file
     * The path to the json file.
     * 
     * @return bool
     * Returns true if the file was deleted successfully, false otherwise.
     */
    private static function delete(string $file): bool {

        // Get the path to the JSON file
        $file = dirname(__DIR__, 1) . $file;

        // Return false if the file does not exist,
        // otherwise delete the file and return true.
        if (!JSON::exists($file) && !unlink($file)) {
            Report::error("Failed to delete JSON file: " . $file);
            return false;
        }
        return true;
    }

    /**
     * Creates a new JSON file at the specified location.
     *
     * @param string $file 
     * The path and filename of the JSON file to create.
     * 
     * @return bool 
     * Returns true if the file was created successfully, false otherwise.
     */
    private static function create(string $file): bool {

        // Get the path to the JSON file
        $file = dirname(__DIR__, 1) . $file;

        // If file exists, or the contents of the file could not be written,
        // report an error and return false.
        if (JSON::exists($file) && !JSON::putContents($file, '{}')) {
            Report::error("Failed to create JSON file: $file");
            return false;
        } 
        return true;
    }


    /**
     * Clears the cache of a JSON file.
     * 
     * @param string $class
     * The class to clear the cache of.
     * 
     * @return bool
     * Returns true if the cache was cleared successfully
     */
    private static function clear(string $class): bool {
        $class::$cache = null;
        return true;
    }

}