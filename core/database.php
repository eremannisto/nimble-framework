<?php declare(strict_types=1);


/**
 * Database class handles all database related methods, such as reading and writing
 * the database host, name and user. It also handles the database connection.
 */
class Database {

    /**
     * Get database connection.
     *
     * @return PDO
     * The database connection.
     */
    public static function connect(): PDO {

        // Validate the database configurations:
        $database = Database::validate();
        if(empty($database)) {
            Report::error("Database connection failed: Invalid database configurations.");
        }

        try {

            // Configurations:
            $host     = $database["HOST"];    
            $name     = $database["NAME"];
            $user     = $database["USER"];
            $password = $database["PASSWORD"];
            $charset  = $database["CHARSET"];
        
            // PDO options:
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => FALSE
            ];
        
            // Data source name:
            $dsn = "mysql:host=$host;dbname=$name;charset=$charset";
        
            // PDO connection:
            $connect = new PDO($dsn, $user, $password, $options);
        
            // Set error modes:
            $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Return the connection:
            return $connect;
        } 
        
        // Catch PDO exceptions:
        catch (PDOException $error) {
            Report::exception($error);
        }
    }

    /** 
     * Close the database connection.
     * 
     * @return void
     */
    public static function close(): void {
        Database::connect()->close();
    }

    /** 
     * Check the database connection status
     * 
     * @return bool
     * True if the connection is established, false otherwise.
     */
    public static function status(): bool {

        // Try to connect:
        try {
            Database::connect();
            return true;
        } 
        
        // Catch PDO exceptions:
        catch (PDOException $error) {
            Report::exception($error);
            return false;
        }
    }

    /**
     * Get the database host from the environment file.
     * 
     * @return string
     * The database host.
     */
    public static function host(): string {
        return Environment::get("DATABASE_HOST") ?? "";
    }

    /**
     * Get the database name from the environment file.
     * 
     * @return string
     * The database name.
     */
    public static function name(): string {
        return Environment::get("DATABASE_NAME") ?? "";
    }

    /**
     * Get the database user from the environment file.
     * 
     * @return string
     * The database user.
     */
    public static function user(): string {
        return Environment::get("DATABASE_USER") ?? "";
    }

    /**
     * Get the database password from the environment file.
     * 
     * @return string
     * The database password.
     */
    public static function password(): string {
        return Environment::get("DATABASE_PASSWORD") ?? "";
    }

    /**
     * Get the database charset from the environment file.
     * 
     * @return string
     * The database charset.
     */
    public static function charset(): string {
        return Environment::get("DATABASE_CHARSET") ?? "utf8mb4";
    }

    /**
     * Validate the database configurations and return them 
     * in an associative array.
     * 
     * @return array
     * The database configurations.
     */
    public static function validate(): array {

        // Database configurations:
        $host     = Database::host();
        $name     = Database::name();
        $user     = Database::user();
        $password = Database::password();
        $charset  = Database::charset();

        // Validate the database host:
        if (empty($host)) {
            Report::warning("Database validation failed: Invalid HOST: $host");
            return [];
        }

        // Validate the database name:
        if (empty($name)) {
            Report::warning("Database validation failed: Invalid NAME: $name");
            return [];
        }

        // Validate the database user:
        if (empty($user)) {
            Report::warning("Database validation failed: Invalid USER: $user");
            return [];
        }

        // Validate the database password:
        if (empty($password)) {
            Report::warning("Database validation failed: Invalid PASSWORD: $password");
            return [];
        }

        // Validate the database charset:
        if (empty($charset)) {
            Report::warning("Database validation failed: Invalid CHARSET: $charset");
            return [];
        }

        // Return the database configurations:
        return [
            "HOST"     => $host,
            "NAME"     => $name,
            "USER"     => $user,
            "PASSWORD" => $password,
            "CHARSET"  => $charset
        ];
    }
}

