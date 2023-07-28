<?php

// Dependencies:

if(!class_exists('Environment')) {
    require_once (__DIR__ . '/environment.php');
}

if(!class_exists('Report')) {
    require_once (__DIR__ . '/report.php');
}

/**
 * The mail class handles all mail related methods, and currently handles
 * methods such as getting and setting the mail driver, host, user, and password.
 * It also handles the mail connection and sending mail with PHPMailer.
 * 
 * @version 1.0.0
 */
class Mail {

    /**
     * Get mail driver.
     * 
     * @return string|null
     * The mail driver, or null if the file could not be read or decoded.
     */
    public static function getDriver(): ?string {
        return Environment::get("MAIL_DRIVER") ?? "";
    }

    /**
     * Set mail driver.
     * 
     * @param string $driver
     * The mail driver.
     */
    public static function setDriver(string $driver): void {
        Environment::set($driver);
    }

    /** 
     * Get mail host.
     * 
     * @return string|null
     * The mail host, or null if the file could not be read or decoded.
     */
    public static function getHost(): string {
        return Environment::get("MAIL_HOST") ?? "";
    }

    /**
     * Set mail host.
     * 
     * @param string $host
     * The mail host.
     */
    public static function setHost(string $host): void {
        Environment::set($host);
    }

    /** 
     * Get mail user.
     * 
     * @return string|null
     * The mail user, or null if the file could not be read or decoded.
     */
    public static function getUser(): ?string {
        return Environment::get("MAIL_USER") ?? "";
    }

    /**
     * Set mail user.
     * 
     * @param string $user
     * The mail user.
     */
    public static function setUser(string $user): void {
        Environment::set($user);
    }

    /** 
     * Get mail password.
     * 
     * @return string|null
     * The mail password, or null if the file could not be read or decoded.
     */
    public static function getPassword(): ?string {
        return Environment::get("MAIL_PASSWORD") ?? "";
    }

    /**
     * Set mail password.
     * 
     * @param string $password
     * The mail password.
     */
    public static function setPassword(string $password): void {
        Environment::set($password);
    }

    /** 
     * Get mail port.
     * 
     * @return string|null
     * The mail port, or null if the file could not be read or decoded.
     */
    public static function getPort(): ?string {
        return Environment::get("MAIL_PORT") ?? "";
    }

    /**
     * Set mail port.
     * 
     * @param string $port
     * The mail port.
     */
    public static function setPort(string $port): void {
        Environment::set($port);
    }

    /**
     * Get mail sender name.
     * 
     * @return string|null
     * The mail sender name, or null if the file could not be read or decoded.
     */
    public static function getSenderName(): ?string {
        return Environment::get("MAIL_SENDER_NAME") ?? "";
    }

    /**
     * Set mail sender name.
     * 
     * @param string $name
     * The mail sender name.
     */
    public static function setSenderName(string $name): void {
        Environment::set($name);
    }

    /**
     * Get mail sender address.
     * 
     * @return string|null
     * The mail sender address, or null if the file could not be read or decoded.
     */
    public static function getSenderAddress(): ?string {
        return Environment::get("MAIL_SENDER_ADDRESS") ?? "";
    }

    /**
     * Set mail sender address.
     * 
     * @param string $address
     * The mail sender address.
     */
    public static function setSenderAddress(string $address): void {
        Environment::set($address);
    }

    /**
     * Get the mail reply name.
     * 
     * @return string|null
     * The mail reply name, or null if the file could not be read or decoded.
     */
    public static function getReplyName(): ?string {
        return Environment::get("MAIL_REPLY_NAME") ?? "";
    }

    /**
     * Set mail reply name.
     * 
     * @param string $name
     * The mail reply name.
     */
    public static function setReplyName(string $name): void {
        Environment::set($name);
    }

    /**
     * Get mail reply address.
     * 
     * @return string|null
     * The mail reply address, or null if the file could not be read or decoded.
     */
    public static function getReplyAddress(): ?string {
        return Environment::get("MAIL_REPLY_ADDRESS") ?? "";
    }

    /**
     * Set mail reply address.
     * 
     * @param string $address
     * The mail reply address.
     */
    public static function setReplyAddress(string $address): void {
        Environment::set($address);
    }



    /** 
     * Send mail using PHPMailer.
     * 
     * @param string $recipient
     * The recipient's email address.
     * 
     * @param string $subject
     * The subject of the email.
     * 
     * @param string $message
     * The message of the email.
     * 
     * @return bool
     * True if the email was sent successfully, false otherwise.
     */
    public static function send(string $recipient, string $subject, string $message): bool {

        // Validate mail connection:
        if (!Mail::validate()) {
            return false;
        }

        // Initialize PHPMailer:
        $mail = new PHPMailer(true);

        // Set mail protocol to SMTP:
        $mail->isSMTP();

        // Set mail properties:
        $mail->SMTPAuth   = true;                           // Enable SMTP authentication
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // Enable TLS encryption
        $mail->Host       = Mail::getHost();            // Mail host
        $mail->Username   = Mail::getUser();            // Mail user
        $mail->Password   = Mail::getPassword();        // Mail password
        $mail->Port       = Mail::getPort();            // Mail port

        // Set mail recipient:
        $mail->addAddress($recipient);                      // Add a recipient

        // Set mail sender:
        $mail->setFrom(
            Mail::getSenderAddress(),                   // Sender address
            Mail::getSenderName()                       // Sender name
        );

        // Set mail reply:
        $mail->addReplyTo(
            Mail::getReplyAddress(),                    // Reply address
            Mail::getReplyName()                        // Reply name
        );

        // Content
        $mail->isHTML(true);
        $mail->Subject  = $subject;                         // Subject
        $mail->Body     = $message;                         // HTML version


        // Try to send it:
        try {
            $mail->send();
            return true;
        } 
        
        catch (Exception $error) {
            Report::exception($error);
            return false;
        }
    }

    /**
     * Validate mail connection. This can be used to check if the mail
     * connection is valid before sending mail. This way before setting package
     * to a database, you can check if the mail connection is valid before.
     */
    public static function validate(): bool {

        // Check if the mail driver is set:
        if (Mail::getDriver() !== 'PHPMailer') {
            Report::warning('Mail driver is not set to PHPMailer.');
            return false;
        }

        // Check if the mail host is set:
        if (Mail::getHost() === null) {
            Report::warning('Mail host is not set.');
            return false;
        }

        // Check if the mail user is set:
        if (Mail::getUser() === null) {
            Report::warning('Mail user is not set.');
            return false;
        }

        // Check if the mail password is set:
        if (Mail::getPassword() === null) {
            Report::warning('Mail password is not set.');
            return false;
        }

        // Check if the mail port is set:
        if (Mail::getPort() === null) {
            Report::warning('Mail port is not set.');
            return false;
        }

        // Check if the mail sender name is set:
        if (Mail::getSenderName() === null) {
            Report::warning('Mail sender name is not set.');
            return false;
        }   

        // Check if the mail sender address is set:
        if (Mail::getSenderAddress() === null) {
            Report::warning('Mail sender address is not set.');
            return false;
        }

        // Check if the mail reply name is set:
        if (Mail::getReplyName() === null) {
            Report::warning('Mail reply name is not set.');
            return false;
        }

        // Check if the mail reply address is set:
        if (Mail::getReplyAddress() === null) {
            Report::warning('Mail reply address is not set.');
            return false;
        }

        // Return true if the mail connection is valid:
        return true;
    }

}