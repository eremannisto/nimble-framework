<?php

// Always list all your components on the top of the page:
// Components::require('CookieConsent');

// Server-side rendering:
// Add any check ups here, e.g. if the user is logged in, etc.

// Render the header component, add any overwrites 
// (e.g. title, description, etc.) as an array, if needed.
// You can also add any custom CSS or JS files here.
Head::render();

// Render the cookie consent component:
// CookieConsent::render();

// Config::remove('app/name');

// Add your page content here: ?>
<div class="container">
    <h1>Hello World (Homepage)!</h1>
</div> <?php

// Render the footer component, add any overwrites
// (e.g. custom JS files, etc.) as an array, if needed.
Foot::render();