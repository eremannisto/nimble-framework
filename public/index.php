<?php

// Autoload the framework:
if (!class_exists('AutoLoader')) {
    require_once(dirname(__DIR__, 1) . '/core/autoloader.php');
    AutoLoader::load();
}

// First, check if the request is for a client file, these are usually
// done by links in the head or foot of the page, and are used to just
// load a file without loading the entire page.
if (Request::isClientFileFetch()){
    File::getClientFile();
    exit();
}   

// Add all the global head tags here. These will be
// added to the <head> tag of every page.
Head::global([

    // Add third-party libraries here: 
    //(Add the <link> and | or <script> tags)
    "third-party" => [
        '<link rel="preconnect" href="https://fonts.googleapis.com">',
        '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>',
        '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">',
        '<script src="https://kit.fontawesome.com/10c60e7810.js" crossorigin="anonymous"></script>',
    ],

    // Add global scripts here (These will be deferred):
    "scripts" => [
        "assets/styles/global.js" => [
            "conditions" => NULL,
        ],
    ],

    // Add global styles here:
    "styles" => [
        "assets/styles/global.css" => [
            "conditions" => NULL
        ],
    ]

]);

// Load the requested page:
Controller::require();

