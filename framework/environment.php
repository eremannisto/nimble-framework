<?php declare(strict_types=1);

/**
 * The Environment class provides methods to retrieve secret 
 * information from the environment file.
 */
class Environment {

    /**
     * The path to the environment file.
     */
    private static string $file = '/.env';

    /**
     * The environment variables.
     */
    private static array $vars = [];


    /**
     * Get the value of the specified environment variable.
     * 
     * @param string $key
     * The name of the environment variable to retrieve.
     * 
     * @return string|null
     * The value of the environment variable if it exists, otherwise null.
     */
    public static function get(string $key): ?string {
        if (empty(Environment::$vars)) Environment::load(); 
        return isset(Environment::$vars[$key]) ? Environment::$vars[$key] : null;
    }

    
    /**
     * Sets the value of a given key in the environment variables array.
     *
     * @param string $key 
     * The key to set the value for.
     * 
     * @param string $value 
     * The value to set for the given key.
     * 
     * @return void
     */
    public static function set(string $key, string $value): void {
        Environment::$vars[$key] = $value;
    }


    /**
     * Load the environment variables from the file specified in the $file property.
     * If the file does not exist, a warning is logged and the function returns.
     * The environment variables are stored in the $vars property as an associative array.
     * 
     * @return void
     */
    public static function load(): void {

        $path = Path::root() . Environment::$file;

        // Check if the environment file exists.
        if (!file_exists($path)) {
            Report::warning('Environment file not found: ' . $path);
            return;
        }

        // Load the environment file as an array of lines and
        // initialize an array to store the environment variables.
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $env   = [];

        // Iterate through each line and process the content.
        foreach ($lines as $line) {

            // Ignore lines starting with '#' (comments) or lines without '='.
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            // Split the line into key and value parts and 
            // store the trimmed key and value in the environment array.
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }

        // Store the environment variables.
        Environment::$vars = $env;
    }

    /**
     * Returns the current environment mode.
     * 
     * @return bool
     * Returns true if the environment mode is development, false otherwise.
     */
    public static function mode(): bool{
        return(Config::get('application->development') === true) ? true : false;
    }
}