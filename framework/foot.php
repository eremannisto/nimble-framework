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

        // If layout hasn't been required yet, require it:
        if (!class_exists('Layout')) require_once(Path::src('/layout.php'));
 

        Layout::render();

        ?></html>
            
        <?php ob_get_contents();
    }


}