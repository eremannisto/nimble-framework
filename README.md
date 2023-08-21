![Ombra - Personal PHP Framework](https://github.com/eremannisto/ombra-framework/blob/main/public/assets/images/social.png)
# Ombra - Simple PHP Framework (V1.0.0)
Ombra is my personal, simple PHP framework project to simplify some of my projects while learning how to create a custom framework. I will try my best to keep the code well documented and updated. This is not meant for production, but any suggestions are always welcome.

## Structure
The framework utilizes a well-organized file structure to enhance the development process. At the core of this structure is the `public` folder, which acts as the main entry point for the project. All pages are accessed through a front-controller (`index.php`), while the `pages`, `components`, and `widgets` are sourced from the `src` folder located outside the root directory.

The `core` folder contains essential framework classes and methods that are required for the framework to function properly. Additionally, within this folder, you'll find the `reports` directory where all the logs are stored, aiding in debugging and maintenance.

Within the `public` folder, an `assets` directory is present. This directory serves as a repository for publicly accessible files, including `images`, `favicons`, `scripts`, and `styles`.

At the project level, there are two important files. The `config.json` file holds more general public data, while the `.env` file contains more sensitive data that is used on the website and more data can be stored in these, more about this later.

Below is a visualisation how a project would look while using this framework:

```
projectName
├── core                            [Framework files]
│   ├── FrameworkClass1.php
│   ├── FrameworkClass2.php
│   ├── ...
│   └── reports                     [All logs are stored here]
│       └── {}
│
├── src
│   ├── widgets
│   │   ├── Widget1.php             [Widgets file, for example authentication]
│   │   ├── Widget2.php             [Widgets file, for example sessions]
│   │   └── ...
│   ├── components                  [All the components are stored here]
│   │   ├── Component1                      
│   │   │   ├── Component1.php      [Component template file]
│   │   │   ├── Component1.css      [Component specific css file]
│   │   │   └── Component1.js       [Component specific js file]
│   │   ├── Component2                      
│   │   │   ├── Component2.php      [Component template file]
│   │   │   ├── Component2.css      [Component specific css file]
│   │   │   └── Component2.js       [Component specific js file]
│   │   └── ...
│   └── pages                       [All the pages are stored here]
│       ├── Page1
│       │   ├── Page1.php           [Page template file]
│       │   ├── Page1.css           [Page specific css file]
│       │   └── Page1.js            [Page specific js file]
│       ├── Page2
│       │   ├── Page2.php           [Page template file]
│       │   ├── Page2.css           [Page specific css file]
│       │   └── Page2.js            [Page specific js file]
│       └── pages.json              [Pages whitelist and config file]
│
├── public                          [Root folder]
│   ├── index.php                   [The front controller]
│   ├── assets                      [All the publicly available assets, such as:]
│   │   ├── favicon                     [ - favicons and its config file]
│   │   ├── images                      [ - images]
│   │   ├── scripts                     [ - javascripts]
│   │   ├── styles                      [ - stylesheets]
│   │   └── ...                         [ - more!]
│   │   
│   └── .htaccess                   [Handles the front-controller Re-Writing]
│
├── .htaccess                       [Handles the root folder and error handling]
└── config.json                     [Configurations]
```

## Configurations
All the data about the website will be stored in the config.json. There is an inbuilt functions to make changes to these.

### `Config::get()`
The `Config::get()` method is used to retrieve configuration data from the config.json file.

#### Parameters
`$path` (optional): A string representing the path to the configuration data. The path should be in the format key/subkey/subsubkey, where each key represents a top-level key in the configuration object, and each subkey represents a nested key. If the $path parameter is not specified, the entire configuration object will be returned.
`$file` (optional): A string representing the path to the configuration file. The default value is /config.json, which assumes that the config.json file is located in the root directory of the project.

#### Return Value
The `Config::get()` method returns the configuration data specified by the $path parameter, or the entire configuration object if the $path parameter is not specified.

#### Example Usage
```php
<?php
// Get the entire configuration object
$config = Config::get();

// Get the value of the 'application/name' key
$name = Config::get('application/name');

// Get the value of the 'application/version' key
$version = Config::get('application/version');

// Get the value of the 'time/restriction/start' key
$start = Config::get('time/restriction/start');
?>
```

In this example, the `Config::get()` method is used to retrieve configuration data from the `config.json` file. The first call to `Config::get()` retrieves the entire configuration object. The subsequent calls retrieve the values of specific keys in the configuration object by specifying the path to the key as the `$path` parameter.
