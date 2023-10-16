<?php

Components::require([
    'Header',
    'Footer',
]);

class Layout {

    /**
     * Render the content.
     * 
     * @return void
     * Render the content.
     */
    private static function renderContent(): void {
        ob_start();
            Content::render();
        ob_get_contents();
    }

    /**
     * Render the layout.
     * 
     * @return void
     * Render the layout with the page content.
     */
    public static function render() {
        ob_start(); ?>

            <body data-page="<?= htmlspecialchars(Request::current()); ?>" >
                <?php Header::render(); ?>

                <div class="view">
                    <main class="content">
                        <?php self::renderContent(); ?>
                    </main>
                </div>

                <?php Footer::render(); ?>
            </body>

        <?php ob_get_contents();
    }

}