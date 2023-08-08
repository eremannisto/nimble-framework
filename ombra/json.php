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
        $class::$cache = $class::$cache ?? JSON::read($file);   // If the cache is null, read the JSON file
        return JSON::traverse($class::$cache, $path);           // Return the value at the specified path
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
    public static function set(string $location, mixed $data, string $file): bool {

        // Location cannot be empty, otherwise the risk of overwriting
        // the entire json file is too high:
        if (empty($location)) {
            Report::exception(new InvalidArgumentException("Object location cannot be empty"));
            return false;
        }
    
        // Read the JSON data from the file:
        $json = JSON::read($file);
        if ($json === null) {
            Report::exception(new RuntimeException("JSON file could not be read"));
            return false;
        }
    
        // Update the value at the specified location:
        $updated = JSON::update($json, $data, $location);
        if ($updated === null) {
            Report::exception(new RuntimeException("Could not update JSON data"));
            return false;
        }

        // Write the updated JSON data back to the file:
        if (!JSON::write(dirname(__DIR__, 1) . $file, $updated)) {
            Report::exception(new RuntimeException("JSON file could not be updated"));
            return false;
        }
        
        // Update was successful
        Report::success("Successfully updated JSON data");
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
    public static function read(string $file): ?object {

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
    public static function write(string $file, mixed $data): bool {

         // Encode the data as JSON:
        try {
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } 
        catch (JsonException $exception) {
            Report::exception($exception);
            return false;
        }

        // Write the JSON data to the file, if an error occurs,
        // log it and return false:
        $bytes = file_put_contents($file, $json);
        if ($bytes === false) {
            Report::exception(new Exception("Error writing JSON file: Failed to write to file '$file'"));
            return false;
        }

        // Return true if the data was written successfully:
        return true;
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
    public static function remove(string $path, string $file): bool {

        // Read the JSON data from the file:
        $data = JSON::read($file);
        if ($data === null) {
            Report::exception(new Exception("Error removing property: Failed to read JSON file '$file'"));
            return false;
        }

        // Traverse the object to the parent of the property to remove.
        $parentPath = dirname($path);
        $parent = JSON::traverse($data, $parentPath);
        if ($parent === null) {
            Report::exception(new Exception("Error removing property: Parent object not found for path '$path'"));
            return false;
        }

        // Get the name of the property to remove.
        $propertyName = basename($path);

        // Remove the property from the parent object.
        if (!isset($parent->{$propertyName})) {
            Report::exception(new Exception("Error removing property: Property '$propertyName' not found for path '$path'"));
            return false;
        }

        // Remove the property from the parent object.
        unset($parent->{$propertyName});


        // Write the modified object back to the file.
        if (!JSON::write(dirname(__DIR__, 1) . $file, $data)) {
            Report::exception(new Exception("Error removing property: Failed to write JSON file '$file'"));
            return false;
        }

        // Return true if the property was removed successfully:
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
            
        // The location of the JSON file from the root directory:
        $location = dirname(__DIR__, 1) . $file;

        // Check if the file exists:
        if (file_exists($location)) {
            Report::exception(new Exception("JSON file already exists: $location"));
            return false;
        }

        // Create the JSON file:
        $created = file_put_contents($location, '{}');
        if ($created === false) {
            Report::exception(new Exception("Error creating JSON file: $location"));
            return false;
        }

        // Return true if the file was created successfully:
        return true;
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
    private static function traverse(object $data, ?string $path = null): mixed {

        // If the path is null or empty, return the original object.
        if ($path === null || $path === "") { return $data; }

        // Split the path string into an array of keys.
        $keys = explode('/', $path);

        // Traverse the object using the keys. If the key 
        // is not found in the object, return null. Otherwise,
        // update the object to the value of the current key.
        foreach ($keys as $key) {
            if (!isset($data->{$key})) { return null; }
            $data = $data->{$key};
        }

        // Return the final value of the nested property.
        return $data;
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
            else { return false; }
        }
    
        // Update the value at the specified location with the new data,
        // and return the updated value:
        $current = $data;
        return $object;
    }

}