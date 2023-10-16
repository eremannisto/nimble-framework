![Simple - PHP Framework](https://github.com/eremannisto/ombra-framework/blob/main/public/assets/images/social.png)

# Simple PHP Framework (B4.0.0)
Simple — a lightweight, `component-based` `server-side rendering (SSR)` framework created in `PHP`. Designed with a *do-it-yourself* spirit for small-scale projects where you want to roll up your sleeves and create something unique, all on your own.

This project is also the culmination of my academic journey, serving as my thesis project.

## Documentation
Documentation can be found here: [Documentation](https://www.notion.so/eremannisto/2a619f43d52945ce9a0efce68d94c671?v=2b2639d2919a470ab88cd114873ec6b1&pvs=4).


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



