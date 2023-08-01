<?php

// Dependancies:
if (!class_exists('AutoLoader')) require_once(__DIR__ . '/core/autoloader.php');

// Autoload all classes in the core directory:
AutoLoader::load();

Head::render();

echo(Config::get('application/name'));

Foot::render();