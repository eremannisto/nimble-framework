<?php

// Autoload all the required classes
require_once(dirname(__DIR__, 1) . '/framework/autoloader.php'); 
AutoLoader::load();

// Initialize the controller
Controller::init();

// Add global third-party libraries, scripts and styles
Assets::global([

    // Add global styles
    "styles" => [
        "/assets/styles/normalize.css"          => [],
        "/assets/styles/global.css"             => [],
        "/assets/styles/colors.css"             => [],
        "/assets/styles/transition.css"         => [],

    ],

    // Add global scripts
    "scripts" => [
        "/assets/scripts/global.js"             => []
    ],

    // Add third-party libraries
    "vendors" => [
        // "<link rel='preconnect' href='https://fonts.gstatic.com'>",
        // "<link href='https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap' rel='stylesheet'>",
        // "<script src='https://kit.fontawesome.com/2b9c0a1c2f.js' crossorigin='anonymous'></script>"
    ]
]);

// Require the requested page
Pages::require();

