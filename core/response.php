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
        $url = '/?page=' . urlencode($page) . (empty($params) ? '' : '&' . http_build_query($params));
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
        $errors   = Response::$errors;
        $defaults = [404, 500];
        
        
        // In case the given code is not a valid HTTP error code,
        // Try to find a fallback error code based on the given code.
        if (!isset($errors[$code])) {
            if($code >= 400 && $code <= 499) $code = $defaults[0];
            if($code >= 500 && $code <= 599) $code = $defaults[1];
            Response::setStatus($code);
        }
        // Return the current error otherwise use a fallback error
        return isset($errors[$code]) && $errors[$code]["visible"] === TRUE
        ? array(
            "code"        => $code,
            "title"       => $errors[$code]["title"],
            "description" => $errors[$code]["description"]
        )
        : array(
            "code"        => "404",
            "title"       => "Page Not Found",
            "description" => "The page you are looking for could not be found."
        );
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


    /**
     * List of client side and server side errors.
     * 
     * @var array $errors
     */
    private static array $errors = [

        400 => [
            "title"       => "Bad Request",
            "description" => "The request could not be understood due to invalid syntax.",
            "visible"     => TRUE
        ],
        
        401 => [
            "title"       => "Unauthorized",
            "description" => "You need to authenticate yourself to access the requested content.",
            "visible"     => TRUE
        ],
        
        402 => [
            "title"       => "Payment Required",
            "description" => "This code is reserved for future use.",
            "visible"     => FALSE
        ],
        
        403 => [
            "title"       => "Forbidden",
            "description" => "You are not allowed to access the requested resource.",
            "visible"     => TRUE
        ],
        
        404 => [
            "title"       => "Page Not Found",
            "description" => "The page you are looking for could not be found.",
            "visible"     => TRUE
        ],
        
        405 => [
            "title"       => "Method Not Allowed",
            "description" => "The method specified in the request is not allowed for this resource.",
            "visible"     => TRUE
        ],
        
        406 => [
            "title"       => "Not Acceptable",
            "description" => "The resource cannot generate content that matches the request's accept headers.",
            "visible"     => TRUE
        ],
        
        407 => [
            "title"       => "Proxy Authentication Required",
            "description" => "You must authenticate with the proxy server before accessing this resource.",
            "visible"     => FALSE
        ],
        
        408 => [
            "title"       => "Request Timeout",
            "description" => "The server did not receive a timely request from the client.",
            "visible"     => FALSE
        ],
        
        409 => [
            "title"       => "Conflict",
            "description" => "The request could not be completed due to a conflict with the current state of the resource.",
            "visible"     => FALSE
        ],
        
        410 => [
            "title"       => "Gone",
            "description" => "The requested resource is no longer available, and there is no forwarding address.",
            "visible"     => FALSE
        ],
        
        411 => [
            "title"       => "Length Required",
            "description" => "The server requires a Content-Length header for this request.",
            "visible"     => FALSE
        ],
        
        412 => [
            "title"       => "Precondition Failed",
            "description" => "One or more conditions in the request headers were not met.",
            "visible"     => FALSE
        ],
        
        413 => [
            "title"       => "Request Entity Too Large",
            "description" => "The request entity is too large for the server to process.",
            "visible"     => FALSE
        ],
        
        414 => [
            "title"       => "Request-URI Too Long",
            "description" => "The request URI is too long for the server to interpret.",
            "visible"     => FALSE
        ],
        
        415 => [
            "title"       => "Unsupported Media Type",
            "description" => "The entity format in the request is not supported by the server.",
            "visible"     => FALSE
        ],
        
        416 => [
            "title"       => "Requested Range Not Satisfiable",
            "description" => "The requested byte range cannot be supplied by the server.",
            "visible"     => FALSE
        ],
        
        417 => [
            "title"       => "Expectation Failed",
            "description" => "The server cannot meet the expectations set in the request's Expect header.",
            "visible"     => FALSE
        ],
        
        418 => [
            "title"       => "I'm a teapot",
            "description" => "I was asked to brew coffee, but I'm just a teapot.",
            "visible"     => TRUE
        ],
        
        421 => [
            "title"       => "Misdirected Request",
            "description" => "The request was directed at a server that cannot respond.",
            "visible"     => FALSE
        ],
        
        422 => [
            "title"       => "Unprocessable Entity",
            "description" => "The request was well-formed but has semantic errors preventing its processing.",
            "visible"     => FALSE
        ],
        
        423 => [
            "title"       => "Locked",
            "description" => "The requested resource is locked and unavailable for access.",
            "visible"     => FALSE
        ],
        
        424 => [
            "title"       => "Failed Dependency",
            "description" => "A previous request's failure prevents the server from fulfilling this request.",
            "visible"     => FALSE
        ],
        
        425 => [
            "title"       => "Unordered Collection",
            "description" => "The server cannot process a request that is part of an unrelated set.",
            "visible"     => FALSE
        ],
        
        426 => [
            "title"       => "Upgrade Required",
            "description" => "The server requires a protocol upgrade for this request.",
            "visible"     => FALSE
        ],
        
        428 => [
            "title"       => "Precondition Required",
            "description" => "The server requires a precondition to fulfill the request.",
            "visible"     => FALSE
        ],
        
        429 => [
            "title"       => "Too Many Requests",
            "description" => "You have sent too many requests within a short time span.",
            "visible"     => FALSE
        ],
        
        431 => [
            "title"       => "Request Header Fields Too Large",
            "description" => "The server cannot process the request due to large header fields.",
            "visible"     => FALSE
        ],
        
        451 => [
            "title"       => "Unavailable For Legal Reasons",
            "description" => "Access to the resource is denied due to legal restrictions.",
            "visible"     => FALSE
        ],
        
        500 => [
            "title"       => "Internal Server Error",
            "description" => "The server encountered an unexpected issue preventing it from fulfilling the request.",
            "visible"     => TRUE
        ],
        
        501 => [
            "title"       => "Not Implemented",
            "description" => "The server does not support the functionality needed to complete the request.",
            "visible"     => TRUE
        ],
        
        502 => [
            "title"       => "Bad Gateway",
            "description" => "The server received an invalid response from an upstream server while acting as a gateway or proxy.",
            "visible"     => TRUE
        ],
        
        503 => [
            "title"       => "Service Unavailable",
            "description" => "The server is currently unable to handle the request due to temporary overloading or maintenance.",
            "visible"     => TRUE
        ],
        
        504 => [
            "title"       => "Gateway Timeout",
            "description" => "The server did not receive a timely response from an upstream server while acting as a gateway or proxy.",
            "visible"     => TRUE
        ],
        
        505 => [
            "title"       => "HTTP Version Not Supported",
            "description" => "The server does not support the HTTP protocol version used in the request.",
            "visible"     => FALSE
        ],
        
        506 => [
            "title"       => "Variant Also Negotiates",
            "description" => "Transparent content negotiation for the request resulted in a circular reference.",
            "visible"     => FALSE
        ],
        
        507 => [
            "title"       => "Insufficient Storage",
            "description" => "The server cannot store the representation needed to complete the request.",
            "visible"     => FALSE
        ],
        
        508 => [
            "title"       => "Loop Detected",
            "description" => "The server detected an infinite loop while processing the request.",
            "visible"     => FALSE
        ],
        
        510 => [
            "title"       => "Not Extended",
            "description" => "The server requires further extensions to fulfill the request.",
            "visible"     => FALSE
        ],
        
        511 => [
            "title"       => "Network Authentication Required",
            "description" => "You need to authenticate to access the network.",
            "visible"     => FALSE
        ],
        
        520 => [
            "title"       => "Unknown Error",
            "description" => "The server encountered an unexpected error, listing various triggers.",
            "visible"     => FALSE
        ]
   ];
     

}
