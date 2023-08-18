<?php declare(strict_types=1);

/**
 * This class handles the foot area of the page.
 * 
 * @version     0.0.1
 * @package     Ombra
 * @subpackage  Foot
 */
class Foot {

    /**
     * Cache any preloaded data.
     */
    private static ?array $cache = null;

    /**
     * Render the foot.
     * 
     * @return void
     * Returns nothing.
     */
    public static function render(): void {
        ?>
        </body>
        </html>
        <?php
    }


    /**
     * This method is used to store any preloaded data that will be used 
     * to render the page. For example, if you want to preload third-party 
     * scripts or stylesheets, you can do so by calling the Foot::global() 
     * method in the controller, and then render the foot area in the view.
     *
     * @param array|null $data 
     * An optional array of data to be cached for later use.
     * 
     * @return void
     * Returns nothing.
     */
    public static function global(?array $data): void {
        Foot::$cache = $data;
    }
}