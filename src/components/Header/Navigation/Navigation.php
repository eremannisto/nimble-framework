<?php

class Navigation {

    public static function render(): void {
        ob_start(); ?>
            <nav id="navigation"></nav>
        <?php ob_get_contents();
    }




}