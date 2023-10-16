<?php

$error = Response::error();

Head::render([
    "title"         => sprintf("Error %s - %s", $error['code'], $error['title']),
    "description"   => $error['description']
]);

class Content { 
    public static function render(): void {

        $error = Response::error(); ?>

            <section id="error" data-observed>
                <div data-fade="bottom" data-delay="200">
                    <span class="error-code"><?=$error['code']; ?></span>
                </div>

                <div data-fade="bottom" data-delay="400">
                    <h1 class="error-title"><?=$error['title']; ?></h1>
                </div>

                <div data-fade="bottom" data-delay="600">
                    <p class="error-description"><?= $error['description']; ?></p>
                </div>

                <div data-fade="bottom" data-delay="800">
                    <a href="/" class="error-button">Return to Homepage</a>
                </div>
            </section>

        <?php
    }
}

Foot::render();
