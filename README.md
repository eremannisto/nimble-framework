![Ombra - Personal PHP Framework](https://github.com/eremannisto/ombra-framework/blob/main/app/public/images/banner.png)

# Ombra - Simple PHP Framework (in progress)
Ombra is my personal, simple PHP framework project to simplify some of my projects while learning how to create a custom framework. I will try my best to keep the code well documented and updated. This is not meant for production, but any suggestions are always welcome.

## Front Controller
(...)

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
