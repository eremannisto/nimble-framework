<?php

// class Security {
    
//     /**
//      * Chwck if the user is authenticated.
//      * 
//      * @return bool
//      * True if the user is authenticated, false otherwise.
//      */
//     public static function authenticated(): bool{
//         return isset($_SESSION['admin']['authenticated']) && $_SESSION['admin']['authenticated'] === TRUE;
//     }

//     /**
//      * Authenticate the user.
//      * 
//      * @return void
//      * Returns nothing.
//      */
//     public static function authenticate(): void{
//         Report::notice("User $user signed in, redirecting to dashboard... User ID: " . $_SESSION["admin"]["id"] . ", User Type: " . $_SESSION["admin"]["field"] . ".");
//         $_SESSION['admin']['authenticated'] = TRUE;
//     }

//     /**
//      * Check if the user is authorized.
//      * 
//      * @return bool
//      * True if the user is authorized, false otherwise.
//      */
//     public static function authorized() {
//         return isset($_SESSION['admin']['status']) && $_SESSION['admin']['status'] === 'admin';
//     }

//     /**
//      * Authorize the user.
//      * 
//      * @return void
//      * Returns nothing.
//      */
//     public static function authorize(): void{
//         $_SESSION['admin']['status'] = 'admin';
//     }


//     /**
//      * Sign out the user.
//      */
//     public static function signOut(): void{
//         session_unset();            // Unset all session variables
//         session_destroy();          // Destroy the session
//         session_start();            // Start a new session
//         Notification::success([
//             "title"       => "Olet kirjautunut ulos",
//             "description" => "Kirjauduit ulos onnistuneesti.",
//             "redirect"    => "sign-in" ]); 
//     }

//     /**
//      * Sign-in the user.
//      * 
//      * @param string $email
//      * The user's email address.
//      * 
//      * @param string $password
//      * The user's password.
//      * 
//      * @return bool
//      * True if the user was signed in, false otherwise.
//      */
//     public static function signIn(string $email, string $password, string $token): bool{

//         // If the CRSF token matches, try to sign in:
//         if(!Security::checkToken($token)){
//             Report::notice("CSRF token does not match.");
//             Notification::error([
//                 "title"       => "Kirjautuminen epäonnistui",
//                 "description" => "Tapahtui odottamaton virhe. Yritä hetken kuluttua uudelleen.",
//                 "redirect"    => "sign-in" ]); 
//         }

//         // Either email or password is empty
//         if(!Security::attempts()){
//             Report::notice("Too many attempts.");
//             Notification::error([
//                 "title"       => "Kirjautuminen epäonnistui",
//                 "description" => "Olet ylittänyt kirjautumisyritysten määrän. Yritä hetken kuluttua uudelleen.",
//                 "redirect"    => "sign-in" ]); 
//         }
        
//         // Either email or password is empty
//         if(empty($email) || empty($password)){
//             Report::notice("Either email or password is empty.");
//             Notification::error([
//                 "title"       => "Kirjautuminen epäonnistui",
//                 "description" => "Puutteelliset kirjautumistiedot. Täytä kaikki kentät.",
//                 "redirect"    => "sign-in" ]); 
//         }
        

//         // Email address is not an email address
//         if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
//             Report::notice("Email address is not an email address.");
//             Notification::error([
//                 "title"       => "Kirjautuminen epäonnistui",
//                 "description" => "Sähköpostiosoite tai salasana on väärin.",
//                 "redirect"    => "sign-in" ]); 
//         }

//         // If password is less than 8 characters
//         if(strlen($password) < 8){
//             Report::notice("Password is less than 8 characters.");
//             Notification::error([
//                 "title"       => "Kirjautuminen epäonnistui",
//                 "description" => "Sähköpostiosoite tai salasana on väärin.",
//                 "redirect"    => "sign-in" ]); 
//         }

//         // Check the database connection
//         $connect = Database::connect();
//         if(!Database::status() || !$connect){
//             Report::notice("Database connection failed.");
//             Notification::error([
//                 "title"       => "Kirjautuminen epäonnistui",
//                 "description" => "Sähköpostiosoite tai salasana on väärin.",
//                 "redirect"    => "sign-in" ]);
//         }

//         // Prepare, bind and execute the query
//         $query = $connect->prepare("SELECT * FROM `Admins` WHERE `EmailAddress` = :email");
//         $query->bindParam(":email", $email);
//         $query->execute();

//         // Fetch the result
//         $user = $query->fetch(PDO::FETCH_ASSOC);

//         // Check if the user exists
//         if(!$user){
//             Report::notice("User $user does not exist.");
//             Notification::error([
//                 "title"       => "Kirjautuminen epäonnistui",
//                 "description" => "Sähköpostiosoite tai salasana on väärin.",
//                 "redirect"    => "sign-in" ]);
//         }

//         // Check if the password is correct
//         if(!password_verify($password, $user["Password"])){
//             Report::notice("Incorrect password.");
//             Notification::error([
//                 "title"       => "Kirjautuminen epäonnistui",
//                 "description" => "Sähköpostiosoite tai salasana on väärin.",
//                 "redirect"    => "sign-in" ]);
//         }

//         if($user["status"] === "inactive") {
//             Report::notice("User $user is inactive.");
//             Notification::warning([ 
//                 "title"         => "Kirjautuminen epäonnistui", 
//                 "description"   => "Tilisi on poistettu käytöstä. Ota yhteyttä ylläpitoon.",
//                 "redirect"      => "sign-in" ]);
//         }
    
//         // If the user is not verified, then return FALSE
//         if ($user["verified"] === 0) {
//             Report::notice("User $user is not verified.");
//             Notification::warning([
//                 "title"         => "Kirjautuminen epäonnistui", 
//                 "description"   => "Tilisi ei ole vielä vahvistettu. Vahvista tilisi sähköpostista löytyvästä linkistä.",
//                 "redirect"      => "sign-in" ]);
//         }

//         // Authenticate the user and return true
//         // Set the session user id to the user id of the user,
//         // and set the password attempts to 0
//         $_SESSION["admin"]["id"]         = $user["AdminID"];
//         $_SESSION["admin"]["role"]       = $user["Role"];
//         $_SESSION["admin"]["attempts"]   = 0;
//         Security::authenticate();
//         return true;
//     }

//     /**
//      * This function is used to check if the user has exceeded the
//      * maximum number of login attempts
//      * 
//      * @return bool
//      * Returns TRUE if the user has not exceeded the maximum number
//      * of login attempts, FALSE otherwise
//      */
//     private static function attempts(): bool {

//         // Check if timer is set
//         if (isset($_SESSION["admin"]["timer"])) {

//             // If timer is set, then check if it is greater than the current time
//             if ($_SESSION["admin"]["timer"] > time()) {
//                 return false;
//             } 
            
//             // If timer is set, then check if it is less than the current time, reset
//             // the number of attempts to 0 and reset the timer and return TRUE
//             else if ($_SESSION["admin"]["timer"] < time()) {
//                 $_SESSION["admin"]["attempts"] = 0;

//                 // Remove timer and return TRUE
//                 unset($_SESSION["admin"]["timer"]);
//                 return true;
//             }
//         }

//         // If there are no attempts, then set it to 0 and return TRUE
//         if (!isset($_SESSION["admin"]["attempts"])) {
//             $_SESSION["admin"]["attempts"] = 0;
//             return true;
//         } 

//         // If there are attempts, then check if it is less than 5
//         else if ($_SESSION["admin"]["attempts"] < 5) {
//             $_SESSION["admin"]["attempts"]++;

//             // If attempts have reached 5, then set a session timer for the next login attempt
//             if ($_SESSION["admin"]["attempts"] >= 5) {
//                 $_SESSION["admin"]["timer"] = time() + 300;
//             }

//             return true;
//         } 
        
//         // If there are attempts, then check if it is greater than 5
//         else if ($_SESSION["admin"]["attempts"] >= 5) {
//             return false;
//         }
//     }

//     /**
//      * Get CRSF token.
//      * 
//      * @return string
//      * Returns the CRSF token.
//      */
//     public static function getToken(): string {
//         return $_SESSION["admin"]["token"] ?? "";
//     }

//     /**
//      * Set CRSF token.
//      * 
//      * @return void
//      * Returns nothing.
//      */
//     public static function setToken(): void{
//         $_SESSION["admin"]["token"] = bin2hex(random_bytes(64));
//     }

//     /**
//      *  Generates a CRSF token
//      * 
//      *  @return void
//      *  Returns nothing
//      */
//     public static function generateToken(): void{

//         // If token doesnt exist, generate a new one
//         if(empty(Security::getToken())) { Security::setToken(); }
//         echo(sprintf('<input type="hidden" name="token" value="%s">', Security::getToken()));
//     }

//     /**
//      *  Check if the CRSF token is valid
//      * 
//      *  @return bool
//      *  True if the CRSF token is valid, false otherwise
//      */
//     public static function checkToken($token): bool{

//         // Check if the CRSF token is set
//         if(empty(Security::getToken())){
//             return false;
//         }

//         // Check if the CRSF token is valid
//         if(!hash_equals(Security::getToken(), $token)){
//             return false;
//         }

//         // Return true if the CRSF token is valid
//         return true;
//     }

// }