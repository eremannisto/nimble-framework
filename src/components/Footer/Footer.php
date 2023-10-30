<?php

class Footer {

    public static function render(): void {
        ob_start(); ?>

            <footer>
 
            </footer>

        <?php ob_get_contents();
    }   
}