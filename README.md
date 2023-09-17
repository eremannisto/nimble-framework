![Ombra - Personal PHP Framework](https://github.com/eremannisto/ombra-framework/blob/main/public/assets/images/social.png)
# Simple PHP Framework (B1.0.0)
Simple is a PHP framework project designed to streamline my projects and facilitate the learning process of crafting custom frameworks.

## How to get started
Get the newest version of the repository and edit the `config.json` file. It's initially set up for the `localhost` environment. Don't forget to add a `.env` file in the project folder for everything to work smoothly. For more details about those check out the `Environment` & `Configuration` section. 

Open up your `Home` page file from `/src/pages/Home/Home.php` and start programming your home page:

```php
<?php
// Always list all your components on the top of the page:
Components::require('Notification');

// Server-side rendering:
// Add any check ups here, e.g. if the user is logged in, etc.

// Render the header component, add any overwrites 
// (e.g. title, description, etc.) as an key/value array, if needed.
// You can also add any custom CSS or JS files here.
Head::render([
    "title" => "My title overwrite"
]);

// Add your page content here: ?>
<div class="container">
    <h1>Hello World (Homepage)!</h1>
    <?php Notification::render(); ?>
</div> <?php

// Render the footer component, add any overwrites
// (e.g. custom JS files, etc.) as an array, if needed.
Foot::render();
```

## Structure
The framework utilizes a well-organized file structure to enhance the development process. At the core of this structure is the `public` folder, which acts as the main entry point for the project. All pages are accessed through a front-controller (`index.php`), while the `pages`, `components`, and `widgets` are sourced from the `src` folder located outside the root directory.

The `core` folder contains essential framework classes and methods that are required for the framework to function properly. Additionally, within this folder, you'll find the `reports` directory where all the logs are stored, aiding in debugging and maintenance.

Within the `public` folder, an `assets` directory is present. This directory serves as a repository for publicly accessible files, including `images`, `favicons`, `scripts`, and `styles`.

At the project level, there are two important files. The `config.json` file holds more general public data, while the `.env` file contains more sensitive data that is used on the website and more data can be stored in these, more about this later.

Below is a visualisation how a project would look while using this framework:

```
projectName
│   
├── core                            [Framework files]
│   ├── FrameworkClass1.php
│   ├── FrameworkClass2.php
│   ├── ...
│   │   
│   └── reports                     [All logs are stored here]
│       └── {}
│
├── src
│   │   
│   ├── snippets                    [All the snippets are stored here]
│   │   ├── Snippet.php             [Snippet file, for example authentication]
│   │   ├── Snippet.php             [Snippet file, for example sessions]
│   │   └── ...
│   │   
│   ├── components                  [All the components are stored here]
│   │   │   
│   │   ├── Component1              [Component]    
│   │   │   ├── Component1.php      [ - component template file]
│   │   │   ├── Component1.css      [ - component specific css file]
│   │   │   └── Component1.js       [ - component specific js file]
│   │   │   
│   │   ├── Component2              [Component]        
│   │   │   ├── Component2.php      [ - component template file]
│   │   │   ├── Component2.css      [ - component specific css file]
│   │   │   ├── Component2.js       [ - component specific js file]
│   │   │   │ 
│   │   │   └── Component3          [Nested Component]
│   │   │       ├── Component3.php  [ - component template file]
│   │   │       ├── Component3.css  [ - component specific css file]
│   │   │       └── Component3.js   [ - component specific js file]
│   │   └── ...
│   │   
│   └── pages                       [All the pages are stored here]
│   │   │ 
│   │   ├── page1                   [Page]
│   │   │   ├── page1.php           [ - page template file]
│   │   │   ├── page1.css           [ - page specific css file]
│   │   │   └── page1.js            [ - page specific js file]
│   │   │ 
│   │   ├── page2                   [Page]
│   │   │   ├── page2.php           [ - page template file]
│   │   │   ├── page2.css           [ - page specific css file]
│   │   │   ├── page2.js            [ - page specific js file]
│   │   │   │
│   │   │   └── page3               [Nested Page]
│   │   │       ├── page3.php       [ - page template file]
│   │   │       ├── page3.css       [ - page specific css file]
│   │   │       └── page3.js        [ - page specific js file]
│   │   │ 
│   │   └── pages.json              [Pages whitelist and config file]
│   │   
│   └── widgets
│       ├── widget1.php             [Widgets file, for example authentication]
│       ├── widget2.php             [Widgets file, for example sessions]
│       └── ...
│
├── public                          [Root folder]
│   │   
│   ├── index.php                   [The front controller]
│   │   
│   ├── assets                      [All the publicly available assets, such as:]
│   │   ├── favicon                 [ - favicons and its config file]
│   │   ├── images                  [ - images]
│   │   ├── scripts                 [ - javascripts]
│   │   ├── styles                  [ - stylesheets]
│   │   └── ...                     [ - more!]
│   │   
│   └── .htaccess                   [Handles the front-controller Re-Writing]
│
├── .htaccess                       [Handles the root folder and error handling]
└── config.json                     [Configurations]
```

## Configurations
Most data in this framework is stored in various `JSON` files, and thus we have a lot of tools to retrieve and manipulate these files. Here is how your `config.json` may look on an empty project:
```
{
    "application"       : {                                 // Application data:           

        "PHP"           : "8.0.0",                          // Required PHP version        
        "version"       : "1.0.0",                          // Version number              
        "name"          : "Application name",               // Your application name       
        "description"   : "Application description",        // Your application description
        "project"       : "project-name",                   // Your project folder name    
        "created"       : "01.01.2023 00:00:00",            // Optional: created at        
        "updated"       : "01.01.2023 00:00:00",            // Optional: updated at        

        "router"        : {                                 // Router data:                
            "host"          : "localhost",                  // - host name                 
            "base"          : "https://localhost:8888",     // - base name                 
            "index"         : "home",                       // - index PAGE name           
            "error"         : "error",                      // - error PAGE name           
            "controller"    : "index",                      // - controller name           
            "parameter"     : "page"                        // - page parameter name       
        },

        "meta"          : {                                 // Meta data:                  
            "language"      : "en",                         // - default language          
            "title"         : "The default title",          // - default page title        
            "description"   : "The default description",    // - default page description  
            "type"          : "website",                    // - default page type         
            "image"         : "social.png",                 // - default page image file   
            "robots"        : "index, follow"               // - default robots            
        },

        "folders"       : {                                 // Structural data:            
            "root"          : "/public",                    // - root folder               
            "assets"        : "/public/assets",             // - assets folder             
            "favicon"       : "/public/assets/favicon",     // - favicon folder            
            "images"        : "/public/assets/images",      // - images folder             
            "styles"        : "/public/assets/styles",      // - styles folder             
            "scripts"       : "/public/assets/scripts",     // - scripts folder            
            "framework"     : "/core",                      // - framework folder          
            "src"           : "/src",                       // - src folder                
            "pages"         : "/src/pages",                 // - pages folder              
            "components"    : "/src/components",            // - components folder         
            "snippets"      : "/src/snippets"               // - snippets folder           
        },

        "time"          : {                                 // Time data                   
            "format"        : "d.m.Y H:i:s",                // - time format               
            "zone"          : "Europe/Helsinki",            // - time zone                 
            "locale"        : "fi_FI",                      // - current locale            
            "restriction"   : {                             // - restrictions:  (Not implemented yet)
                "start"         : null,                     // - - start time   (Not implemented yet)
                "end"           : null                      // - - end time     (Not implemented yet)
            }
        }
    }
}
```

### Getting data
Getting data from the `config.json` can be done using the core class `Config`. For example:
```php
// Get the entire configuration object
$config  = Config::get();

// Get the value of the 'application/name' key
$name    = Config::get('application/name');

// Get the value of the 'application/version' key
$version = Config::get('application/version');

// Get the value of the 'application/time/restriction/start' key
$start   = Config::get('appliaction/time/restriction/start');
```

### Setting data
Setting data from the `config.json` can be fone using the core class `Config`. If the object doesn't exist, create it, otherwise overwrite it. For example:
```php
// Set the entire configuration object
Config::set();

// Set the new value of the 'application/name' key
Config::set('application/name', 'My new name');

// Set the new value of the 'application/version' key
Config::set('application/version', '1.0.1');

// Set the new value of the 'application/time/restriction/start' key
Config::set('appliaction/time/restriction/start', "01.01.2024");
```

Continues...
