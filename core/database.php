<?php

// Dependencies:
if (!class_exists('Report')) {
    require_once(__DIR__ . '/report.php'); 
}

if (!class_exists('Environment')) {
    require_once(__DIR__ . '/environment.php'); 
}

/**
 * Database class handles all database related methods, such as reading and writing
 * the database host, name and user. It also handles the database connection.
 * 
 * @version 1.0.0
 */
class Database {

    /**
     * Get database connection.
     *
     * @return PDO
     * The database connection.
     */
    public static function connect(): PDO {

        if(!Database::validate()){
            Report::error("Database validation failed, please check your .env file.");
        }

        try {
            // Configurations:
            $host     = Database::getHost();       
            $name     = Database::getName();
            $user     = Database::getUser();      			 
            $password = Database::getPassword();
            $charset  = Database::getCharset();;
        
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
     * Validate the database connection.
     * 
     * @return bool
     * True if the connection is valid, false otherwise.
     */
    public static function validate(): bool {

        // Check if the database host is set:
        if (Database::getHost() == "") {
            Report::warning("Database host is not set.");
            return false;
        }

        // Check if the database name is set:
        if (Database::getName() == "") {
            Report::warning("Database name is not set.");
            return false;
        }

        // Check if the database user is set:
        if (Database::getUser() == "") {
            Report::warning("Database user is not set.");
            return false;
        }

        // Check if the database password is set:
        if (Database::getPassword() == "") {
            Report::warning("Database password is not set.");
            return false;
        }

        // Check if the database charset is set:
        if (Database::getCharset() == "") {
            Report::warning("Database charset is not set.");
            return false;
        }

        // Return true if all checks passed:
        return true;
    }

    /** 
     * Get database host.
     * 
     * @return string|null
     * Returns the database host, or null if the file could not be read or decoded.
     */
    public static function getHost(): string {
        return Environment::get("DATABASE_HOST") ?? "";
    }

    /**
     * Set database host.
     * 
     * @param string $host
     * The database host.
     */
    public static function setHost(string $host): void {
        Environment::set("DATABASE_HOST", $host);
    }

    /** 
     * Get database name.
     * 
     * @return string|null
     * Returns the database name, or null if the file could not be read or decoded.
     */
    public static function getName(): string {
        return Environment::get("DATABASE_NAME") ?? "";
    }

    /**
     * Set database name.
     * 
     * @param string $name
     * The database name.
     */
    public static function setName(string $name): void {
        Environment::set("DATABASE_NAME", $name);
    }

    /** 
     * Get database user.
     * 
     * @return string|null
     * Returns the database user, or null if the file could not be read or decoded.
     */
    public static function getUser(): string {
        return Environment::get("DATABASE_USER") ?? "";
    }

    /**
     * Set database user.
     * 
     * @param string $user
     * The database user.
     */
    public static function setUser(string $user): void {
        Environment::set("DATABASE_USER", $user);
    }

    /** 
     * Get database password.
     * 
     * @return string|null
     * Returns the database password, or null if the file could not be read or decoded.
     */
    public static function getPassword(): string {
        return Environment::get("DATABASE_PASSWORD") ?? "";
    }

    /**
     * Set database password.
     * 
     * @param string $password
     * The database password.
     */
    public static function setPassword(string $password): void {
        Environment::set("DATABASE_PASSWORD", $password);
    }

    /** 
     * Get database charset.
     * 
     * @return string|null
     * Returns the database charset, or null if the file could not be read or decoded.
     */
    public static function getCharset(): string {
        return Environment::get("DATABASE_CHARSET") ?? "";
    }

    /**
     * Set database charset.
     * 
     * @param string $charset
     * The database charset.
     */
    public static function setCharset(string $charset): void {
        Environment::set("DATABASE_CHARSET", $charset);
    }
}

