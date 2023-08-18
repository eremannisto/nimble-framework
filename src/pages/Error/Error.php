<?php

$error       = Response::error();
$title       = sprintf("<strong class='error-code'>%s</strong> - %s", $error['code'], $error['title']);
$description = $error['description'];

Head::render([
    "title"         => sprintf("%s - %s", $error['code'], $error['title']),
    "description"   => $description
]); ?>

<div class="container">
    <h1 class="error-title"><?php echo($title); ?></h1>
    <p class="error-description"><?php echo($description);?></p>
    <button class="error-button">Return</button>
</div> <?php

Foot::render();
