<?php

// Dependancies:
if (!class_exists('AutoLoader')) require_once(__DIR__ . '/ombra/autoloader.php');

// Autoload all classes in the core directory:
AutoLoader::load();

// // Preload any global libraries and files to the head:
// Head::global([
//     "third-party" => [
//         '<link rel="preconnect" href="https://fonts.googleapis.com">',
//         '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>',
//         '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">',
//         '<script src="https://kit.fontawesome.com/10c60e7810.js" crossorigin="anonymous"></script>',
//     ],

//     "styles" => [
//         "main.css" => null
//     ]
// ]);

// // Preload any global libraries and files to the foot:
// Foot::global([
//     "scripts" => [
//         "script.js" => null
//     ]
// ]);

// echo(Config::set("application/version", "0.0.1"));
// echo(Config::get("application/version"));
// Config::set("application/name", "Ombra - Simple PHP Framework");
Config::remove("application/version/");

// Pages::require('home');
?>
