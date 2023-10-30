<?php 

Head::render();

class Content {

    public static function render(): void { ?>

        <div class="container">
            <img src="/assets/images/banner.png" alt="Simple Framework">
        </div>

    <?php }

};

Foot::render();