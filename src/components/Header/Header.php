<?php

Components::require([
    'Header/Navigation',
    'Notification'
]);

class Header {

    public static function render(): void {
        ob_start(); ?>

            <header>
                <?php Navigation::render(); ?>                
            </header>
            
            <?php Notification::render(); ?>

        <?php ob_get_contents();
    }
}