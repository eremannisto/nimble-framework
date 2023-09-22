<?php declare(strict_types=1);

/**
 * This class handles the foot area of the page.
 */
class Foot {

    /**
     * Render the foot.
     * 
     * @return void
     * Returns nothing.
     */
    public static function render(): void {

        // Start the output buffer
        ob_start();

        // Require any global components listed in the cache
        Components::renderGlobal(get_called_class()); ?>

        </body>
        </html>
            
        <?php ob_get_contents();
    }


}