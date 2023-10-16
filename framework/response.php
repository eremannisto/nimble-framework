<?php declare(strict_types=1);

/**
 * The Response class provides methods for working 
 * with HTTP response codes and headers.
 */
class Response {

    /**
     * Redirects the user to a new page and includes optional query parameters.
     *
     * This method generates a URL based on the provided page name and appends
     * any parameters as a query string. The user's browser will be redirected
     * to the constructed URL using the HTTP Location header and the script will
     * exit immediately.
     *
     * @param string $page
     * The target page to redirect to (without the .page.php extension).
     * 
     * @param array $params
     * An associative array of parameters to pass to the new page, where keys
     * represent parameter names and values are parameter values.
     * 
     * @param int $status
     * The HTTP status code to use for the redirect.
     */
    public static function redirect(string $page, array $params = [], int $status = 302): void {
        $url = $page . (empty($params) ? '' : '?' . http_build_query($params));
        Response::setStatus($status);
        header("Location: {$url}");
        exit;
    }

    /**
     * Returns an error response with the given HTTP status code.
     *
     * @param int|null $code 
     * The HTTP status code of the error response.
     * 
     * @return array 
     * An array containing the error code, title, and description.
     */
    public static function error(?int $code = NULL){

        // Get the HTTP error codes, titles and descriptions
        // and set the default error codes for the fallback error code.
        $code     = $code ?: Response::getStatus();
        $errors   = Errors::get();
        $allowed  = Config::get('application->router->errors');
        $defaults = [404, 500];
        
        
        // In case the given code is not a valid HTTP error code,
        // Try to find a fallback error code based on the given code.
        if (!isset($errors->{$code})) {
            if($code >= 400 && $code <= 499) $code = $defaults[0];
            if($code >= 500 && $code <= 599) $code = $defaults[1];
            Response::setStatus($code);
        }

        // Check which language to use
        $language = Language::current();

        // Return the error data
        return isset($errors->{$code}) && in_array($code, $allowed)
            ? [
                "language"    => $language,
                "code"        => $code,
                "title"       => $errors->{$code}->{$language}->{'title'},
                "description" => $errors->{$code}->{$language}->{"description"}
            ]
            : [
                "language"    => $language,
                "code"        => 404,
                "title"       => $errors->{404}->{$language}->{'title'},
                "description" => $errors->{404}->{$language}->{"description"}
            ];

    }

    /**
     * Returns the HTTP response code.
     *
     * @return int|null 
     * The HTTP response code or null if it has not been set.
     *
     */
    public static function getStatus(): ?int {
        return(http_response_code() ?: NULL);
    }

    /**
     * Sets the HTTP response code for the current request.
     *
     * @param int $code 
     * The HTTP response code to set.
     * 
     * @return void
     * Returns nothing.
     */
    public static function setStatus(mixed $code): void {

        // Check if the code is valid:
        $validate = function (mixed $code): bool {
            return(is_numeric($code) && $code >= 100 && $code <= 599);
        };

        // If the code is invalid, report it and set the code to 500:
        if (!$validate($code)) {
            Report::warning("Invalid HTTP Status Code: $code, using 500 instead."); 
            $code = 500;
        }

        // Set the given code:
        $code = (int)$code;
        http_response_code($code); 
    }
}
