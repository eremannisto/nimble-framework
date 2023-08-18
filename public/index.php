<?php

// Autoload all the required classes
require_once(dirname(__DIR__, 1) . '/core/autoloader.php'); 
AutoLoader::load();

// Initialize the controller
Controller::init();

// Add global third-party libraries, scripts and styles
Head::global([

    "third-party" => [
        '<link rel="preconnect" href="https://fonts.googleapis.com">',
        '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>',
        '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">',
        '<script src="https://kit.fontawesome.com/10c60e7810.js" crossorigin="anonymous"></script>',
    ],

    "scripts" => [
        "assets/styles/global.js" => [
            "conditions" => NULL,
        ],
    ],

    "styles" => [
        "assets/styles/global.css" => [
            "conditions" => NULL
        ],
    ]
]);

// Require the requested page
Pages::require();

