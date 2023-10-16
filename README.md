![Simple - PHP Framework](https://github.com/eremannisto/ombra-framework/blob/main/public/assets/images/social.png)

# Simple PHP Framework (B4.0.0)
Simple — a lightweight, `component-based` `server-side rendering (SSR)` framework created in `PHP`. Designed with a *do-it-yourself* spirit for small-scale projects where you want to roll up your sleeves and create something unique, all on your own.

This project is also the culmination of my academic journey, serving as my thesis project.

## Documentation
Documentation can be found here: [Documentation](https://www.notion.so/eremannisto/2a619f43d52945ce9a0efce68d94c671?v=2b2639d2919a470ab88cd114873ec6b1&pvs=4).

## Structure
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



