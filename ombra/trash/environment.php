<?php

// /**
//  * ENV class handles all ENV related methods,
//  * such as reading the .env file.
//  * 
//  * @version 1.0.0
//  */
// class Environment {

//     // Cache the .env file contents
//     private static $cache = null;

//     /**
//      * Reads and decodes the .env file. If the .env file is not cached, 
//      * it will be read and cached, otherwise the cached version will be returned.
//      * 
//      * @return array|null
//      * Returns an array representation of the .env file, or null if the file
//      * could not be read or decoded.
//      */
//     public static function get(string $id): ?string{

//         // Check if the .env file is cached, if not, read and cache it
//         if (Environment::$cache === null) {

//             $file  = dirname(__DIR__, 1) . "/.env";

//             // Check if the .env file exists
//             if (file_exists($file)) {
                
//                 // Read it and parse it into an array
//                 $content    = file_get_contents($file);
//                 $lines      = explode("\n", $content);
//                 $environment = array();

//                 // Go through each line, and create a key/value pair
//                 // for each line that is not empty or a comment
//                 foreach ($lines as $line) {

//                     // Remove all the extra whitespaces
//                     $line = trim($line);

//                     // Check if the line is not empty or a comment
//                     if ($line && strpos($line, '#') !== 0) {
//                         $pos       = strpos($line, '=');      // Find the position of the first '='
//                         $key       = substr($line, 0, $pos);  // Get the key name
//                         $value     = substr($line, $pos + 1); // Get the value data
//                         $environment[rtrim($key)] = $value;   // Remove extra whitespace from key and add the key/value pair to the array
//                     }
//                 }
                
//                 // Set the cache
//                 Environment::$cache = $environment;
//             }
//         }

//         // Return the cached .env file with the key
//         return trim(Environment::$cache[$id] ?? null);
//     }

//     /**
//      * If the $key already exists in the cached environment variables,
//      * the Environment::set() method will overwrite its value with the new $value.
//      * 
//      * If the $key doesn't exist in the cached environment variables,
//      * the Environment::set() method will add a new key/value pair to the cached
//      * environment variables with the $key and $value provided.
//      * 
//      * @param string $key
//      * The key to update.
//      * 
//      * @param string $value
//      * The value to update.
//      * 
//      * @return void
//      * Returns nothing.
//      */
//     public static function set($key, $value): void {

//         // Check if the .env file is cached, if not, load it
//         if (Environment::$cache === null) {
//             Environment::get();
//         }
    
//         // Update the cached value
//         Environment::$cache[$key] = $value;
    
//         // Save the changes to the .env file
//         $file  = dirname(__DIR__, 1) . "/.env";
//         $lines = array();

//         foreach (Environment::$cache as $key => $value) {
//             $lines[] = "$key=$value";
//         }

//         $content = implode("\n", $lines);
//         file_put_contents($file, $content);
//     }
// }
