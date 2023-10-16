![Simple - PHP Framework](https://github.com/eremannisto/ombra-framework/blob/main/public/assets/images/social.png)

# Simple PHP Framework (B4.0.0)
Simple — a lightweight, `component-based` `server-side rendering (SSR)` framework created in `PHP`. Designed with a *do-it-yourself* spirit for small-scale projects where you want to roll up your sleeves and create something unique, all on your own.

This project is also the culmination of my academic journey, serving as my thesis project.

## How to get started
Get the newest version of the repository and edit the `config.json` file. It's initially set up for the `localhost` environment. Open up your `home` page file from `/src/pages/home/home.php` and start programming your home page:

```php
<?php
// Require any components
Components::require([
    'Notification'
]);

// Add the head content, here we can override any
// of the head content, such as the title, description, etc.
Head::render();

// Add the page content:
class Content {
    public static function render(): void { 
        Notification::render();
    }
};

// Add the foot content, here we can override any
Foot::render();
```


## Structure
The framework utilizes a well-organized file structure to enhance the development process. At the core of this structure is the `public` folder, which acts as the main entry point for the project. All pages are accessed through a front-controller (`index.php`), while the `pages`, `components`, and `widgets` are sourced from the `src` folder located outside the root directory.

The `core` folder contains essential framework classes and methods that are required for the framework to function properly. Additionally, within this folder, you'll find the `reports` directory where all the logs are stored, aiding in debugging and maintenance.

Within the `public` folder, an `assets` directory is present. This directory serves as a repository for publicly accessible files, including `images`, `favicons`, `scripts`, and `styles`.

At the project level, there are two important files. The `config.json` file holds more general public data, while the `.env` file contains more sensitive data that is used on the website and more data can be stored in these, more about this later.

Below is a visualisation how a project would look while using this framework:

```
public_html
│   
├── framework                            [Framework files]
│   ├── class-1.php
│   ├── class-2.php
│   ├── ...
│   │   
│   └── reports                     [All logs are stored here]
│       └── {}
│
├── src
│   ├── components                  [All the components are stored here]
│   │   ├── ComponentA              [Component]    
│   │   │   ├── ComponentA.php      [ - component template file]
│   │   │   ├── ComponentA.css      [ - component specific css file]
│   │   │   └── ComponentA.js       [ - component specific js file]
│   │   ├── ComponentB              [Component]        
│   │   │   ├── ComponentB.php      [ - component template file]
│   │   │   ├── ComponentB.css      [ - component specific css file]
│   │   │   ├── ComponentB.js       [ - component specific js file]
│   │   │   │ 
│   │   │   └── ComponentC          [Nested Component]
│   │   │       ├── ComponentC.php  [ - component template file]
│   │   │       ├── ComponentC.css  [ - component specific css file]
│   │   │       └── ComponentC.js   [ - component specific js file]
│   │   └── ...
│   │   
│   ├── pages                       [All the pages are stored here]
│   │   ├── page-1                  [Page]
│   │   │   ├── page-1.php          [ - page template file]
│   │   │   ├── page-1.css          [ - page specific css file]
│   │   │   └── page-1.js           [ - page specific js file]
│   │   ├── page-2                  [Page]
│   │   │   ├── page-2.php          [ - page template file]
│   │   │   ├── page-2.css          [ - page specific css file]
│   │   │   ├── page-2.js           [ - page specific js file]
│   │   │   └── page-3              [Nested Page]
│   │   │       ├── page-3.php      [ - page template file]
│   │   │       ├── page-3.css      [ - page specific css file]
│   │   │       └── page-3.js       [ - page specific js file]
│   │   ├── ...                     
│   │   └── pages.json
│   │
│   └── snippets                    [All the snippets are stored here]
│       ├── Snippet.php             [Snippet file, for example authentication]
│       ├── Snippet.php             [Snippet file, for example sessions]
│       └── ...
│   
├── public                          [Root folder]
│   ├── index.php                   [The front controller]
│   ├── assets                      [All the publicly available assets, such as:]
│   │   ├── favicon                 [ - favicons and its config file]
│   │   ├── images                  [ - images]
│   │   ├── scripts                 [ - javascripts]
│   │   ├── styles                  [ - stylesheets]
│   │   └── ...                     [ - more!]
│   └── .htaccess                   [Handles the front-controller Re-Writing]
│   
├── .htaccess                       [Handles the root folder and error handling]
└── config.json                     [Configurations]
```

## Configurations
Most data in this framework is stored in various `JSON` files, and thus we have a lot of tools to retrieve and manipulate these files. Here is how your `config.json` may look on an empty project:
```json
{
    "application"       : {
        "development"   : false,
        "version"       : "1.0.0",
        "name"          : "Name of the application",
        "description"   : "Description of the application",
        "project"       : "project-folder-name",
        "created"       : "01.08.2023 00:00:00",
        "updated"       : "01.08.2023 00:00:00",

        "languages"     : [
            "en"
        ],

        "router"            : {
            "host"          : "example.com",
            "base"          : "https://example.com",
            "index"         : "home",
            "error"         : "error",
            "controller"    : "index",
            "parameter"     : "page",
            "errors"        : [400, 401, 403, 404, 418, 405, 500, 501, 503]
        },

        "meta"              : {
            "default"           : {
                "language"      : "en",
                "title"         : "This is the default title",
                "description"   : "This is the default description",
                "type"          : "website",
                "image"         : "social.png",
                "robots"        : "index, follow",
                "theme"         : "#181B25",
                "manifest"      : "/manifest.json"
            }
        },

        "directories"       : {
            "framework"         : {},
            "public"            : {
                "assets"            : {
                    "favicons"          : {},
                    "images"            : {},
                    "styles"            : {},
                    "scripts"           : {}
                }
            },
            "src"               : {
                "pages"             : {},
                "components"        : {},
                "snippets"          : {}
            }
        },

        "time"          : {
            "format"        : "d.m.Y H:i:s",
            "zone"          : "Europe/Helsinki",
            "locale"        : "fi_FI",
            "restriction"   : {
                "start"         : null,
                "end"           : null
            }
        }
    }
}
```

### Class: JSON
| Method name | Description | Argument |
| --- | --- | --- |
| get() | Get the value of a nested property in an object using a path string. | $path (string = ""): The path to the nested property. $file (string): The path to the JSON file. $class (string): The name of the class. |
| set() | Updates the JSON data at the specified location in the given file. | $path (string): The location of the data to be updated. 

$data (mixed): The updated data. 

$file (string): The path of the JSON file. 

$class (string): The name of the class. |
| remove() | Removes a property from a JSON file. | $path (string): The path to the property to remove. - $file (string): The path to the JSON file. - $class (string): The name of the class. |
| read() | Reads and decodes a JSON file. | $file (string): The path to the JSON file. |
| write() | Encodes and writes JSON data to a file. | $file (string): The path to the file to write the JSON data to. - $data (mixed): The data to encode and write to the file. |
| exists() | Check whether a JSON file exists at the specified location. | $file (string): The path to the JSON file. |
| delete() | Delete a JSON file. | $file (string): The path to the JSON file. |
| create() | Creates a new JSON file at the specified location. | $file (string): The path and filename of the JSON file to create. |
| decode() | Decodes a JSON string. | $json (string): The JSON string to decode. |
| encode() | Encodes a JSON object. | $object (mixed): The object to encode. 

$options (int, optional): Encoding options. |
