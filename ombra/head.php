<?php

// Dependancies:
if (!class_exists('Config')) require_once(__DIR__ . '/config.php');
if (!class_exists('Report')) require_once(__DIR__ . '/report.php');
if (!class_exists('URL'))    require_once(__DIR__ . '/url.php');

/**
 * Head class handles all head related methods,
 * such as rendering the head tags and their contents.
 * 
 * @version     1.0.0
 * @package     Ombra
 * @subpackage  Head
 */
class Head {

    /** 
     * Cached preloaded object.
     */
    private static ?array $cache = null;

    /**
     * Render the head tags and their contents. You can also
     * pass a key => value array to the render method to override
     * the default meta data.
     * 
     * @param array|null $meta
     * An array containing the meta data to override the default meta data.
     * 
     * @return void|null
     * The rendered head tags and their contents, or null if the file could not be read or decoded.
     * 
     * @example Head::render();
     * @example Head::render(['title' => 'My title']);
     * 
     */
    public static function render(?array $meta = null): void {

        // Start output buffer:
        ob_start();

        // Get the default meta data:
        $defaults = [
            'url'           => URL::get(),
            'title'         => Meta::get('title'),
            'description'   => Meta::get('description'),
            'keywords'      => Meta::get('keywords'),
            'type'          => Meta::get('type'),
            'language'      => Meta::get('language'),
            'image'         => Meta::get('image')
        ];

        // Merge the defaults with the given meta data:
        $meta = array_merge($defaults, $meta ?: []);

        // New variables for the meta data:
        $url         = $meta['url'];
        $title       = $meta['title'];
        $description = $meta['description'];
        $keywords    = $meta['keywords'];
        $type        = $meta['type'];
        $language    = $meta['language'];
        $image       = $meta['image'];

        // Initialize the output and the head tag:
        $output  = '<!DOCTYPE html>';
        $output .= sprintf('<html lang="%s">', $language);
        $output .= '<head>';

        // General meta data:
        $output .= '<!-- General meta data: -->';
        $output .= '<meta charset="UTF-8">';
        $output .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        $output .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';

        // Title, description and keywords:
        $output .= '<!-- Title, description and keywords: -->';
        $output .= !empty($title)       ? sprintf('<title>%s</title>',                      $title)       : '';
        $output .= !empty($description) ? sprintf('<meta name="description" content="%s">', $description) : '';
        $output .= !empty($keywords)    ? sprintf('<meta name="keywords"    content="%s">', $keywords)    : '';
    
        // Open graph meta data:
        $output .= '<!-- Open graph meta data: -->';
        $output .= !empty($title)       ? sprintf('<meta property="og:title"       content="%s">', $title)       : '';
        $output .= !empty($description) ? sprintf('<meta property="og:description" content="%s">', $description) : '';
        $output .= !empty($type)        ? sprintf('<meta property="og:type"        content="%s">', $type)        : '';
        $output .= !empty($image)       ? sprintf('<meta property="og:image"       content="%s">', $image)       : '';
        $output .= !empty($url)         ? sprintf('<meta property="og:url"         content="%s">', $url)         : '';

        // Twitter meta data:
        $output .= '<!-- Twitter meta data: -->';
        $output .= !empty($title)       ? sprintf('<meta name="twitter:title"       content="%s">', $title)       : '';
        $output .= !empty($description) ? sprintf('<meta name="twitter:description" content="%s">', $description) : '';
        $output .= !empty($image)       ? sprintf('<meta name="twitter:image"       content="%s">', $image)       : '';
        $output .= !empty($url)         ? sprintf('<meta name="twitter:url"         content="%s">', $url)         : '';
        if(!empty($title || $description || $image)) {
            $output .= '<meta name="twitter:card"    content="summary_large_image">';
            $output .= '<meta name="twitter:site"    content="@twitter">';
            $output .= '<meta name="twitter:creator" content="@twitter">';
        }

        // Favicons
        $output .= '<!-- Favicons: -->';
        $output .= Favicon::generate();

        // Third-party links and scripts
        if (!empty(Head::$cache['third-party']) && is_array(Head::$cache['third-party'])) {
            $output .= '<!-- Third-party links and scripts: -->';
            foreach (Head::$cache['third-party'] as $link) {
                $output .= $link;
            }
        }

        // Stylesheets
        $output .= '<!-- Stylesheets: -->';
        // $output .= Files::stylesheets();
    
        // Close the head tag:
        $output .= '</head>';
        $output .= '<body>';

        // Return the output:
        echo($output);

        // Add any custom code between the two lines:
        //--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---// Starts

        // Component::render('Header');

        //--- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- ---// Ends

        // Flush the output buffer:
        ob_flush();
    }

    /**
     * This is used to store any preloaded data that will be used to render the page.
     * For example, if you want to preload third-party scripts
     * or stylesheets, you can do so by calling the Head::preload() method in
     * the controller, and then render the head tags in the view.
     */
    public static function global(?array $data): void {
        Head::$cache = $data;
    }
} 

  