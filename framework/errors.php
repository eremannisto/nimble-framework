<?php declare(strict_types=1);

class Errors {
    
        /**
        * The cached errors object, this is updated when
        * the JSON::get method is called.
        */
        public static mixed $cache = null;
    
        /**
        * The path to the errors.json file from the
        * root directory of the project.
        */
        private static string $file = "/error/errors.json";
    
        /**
        * Retrieves errors data from the specified location.
        *
        * @param string|null $location 
        * The location of the errors.json file. Defaults to an empty string.
        * 
        * @return mixed 
        * The errors data.
        */
        public static function get(?string $location = ""): mixed {
            $folder = Directories::get("pages") . self::$file;
            return JSON::get($location, $folder, get_called_class());
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