<?php

// Require any components
Components::require([
    'Notification'
]);

// Add the head content, here we can override any
// of the head content, such as the title, description, etc.
Head::render();

// Add the page content:
class Content {
    public static function render(): void { 
        Notification::render();
    }
};

// Add the foot content, here we can override any
Foot::render();
