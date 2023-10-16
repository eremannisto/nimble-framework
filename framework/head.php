<?php declare(strict_types=1);

/**
 * Head class handles all head related methods,
 * such as rendering the head tags and their contents.
 */
class Head {

    /** 
     * Cached preloaded object.
     * 
     * @var array|null
     */
    public static ?array $cache = null;

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

        // If layout hasn't been required yet, require it:
        if (!class_exists('Layout')) require_once(Path::src('/layout.php'));


        // Get the default meta data:
        $defaults = [
            'url'         => URL::get(),
            'title'       => Meta::get('title'),
            'description' => Meta::get('description'),
            'keywords'    => Meta::get('keywords'),
            'type'        => Meta::get('type'),
            'language'    => Meta::get('language'),
            'image'       => Meta::get('image'),
            'robots'      => Meta::get('robots'),
            'theme'       => Meta::get('theme'),
            'manifest'    => Meta::get('manifest')
        ];

        // Merge the defaults with the given meta data:
        $meta = array_merge($defaults, $meta ?: []);

        // General meta data, title, description, keywords, 
        // robots, social media meta data, favicon, third-party
        // links and scripts, stylesheets, and scripts:
        $output =  Head::general();
        $output .= Head::title($meta['title']);
        $output .= Head::description($meta['description']);
        $output .= Head::keywords($meta['keywords']);
        $output .= Head::theme($meta['theme']);
        $output .= Head::manifest($meta['manifest']);
        $output .= Head::robots($meta['robots']);
        $output .= Head::social($meta);
        $output .= Head::favicon();
        $output .= Head::stylesheets();
        $output .= Head::scripts();
        $output .= Head::vendors();
        
        // Initiate the document:
        Head::init(
            Head::language($meta['language']), 
            $output
        );

        // Get the output buffer contents:
        ob_get_contents();
    }

    /**
     * Returns the opening HTML tags for the document with the 
     * specified language.
     *
     * @param string|null $language 
     * The language of the document.
     * 
     * @return void 
     * The opening HTML tags for the document.
     */
    private static function init(?string $language, ?string $data): void {

        $current = Request::current();
        $output  = "<!DOCTYPE html>";
        $output .= "<html lang='$language'>";
        $output .= "<head>$data</head>";
        echo $output;
    }

    /**
     * Returns the general meta data for the HTML head section.
     *
     * @return string 
     * he HTML code for the general meta data.
     */
    private static function general(): string {
        $url    = URL::get([
            "QUERY" => false
        ]);
        $output  = '<!-- General meta data: -->';
        $output .= '<meta charset="UTF-8">';
        $output .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        $output .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        $output .= "<meta http-equiv='Content-Security-Policy' content=\"script-src 'self' {$url}\">";

        return $output;
    }

    /**
     * Returns the HTML language attribute with the specified language code.
     *
     * @param string|null $language 
     * The language code to use.
     * 
     * @return string 
     * The HTML language attribute with the specified language code, 
     * or an empty string if no language code is provided.
     */
    private static function language(?string $language): string {
        return !empty($language) ? $language : '';
    }

    /**
     * Returns a formatted HTML title tag with the given title.
     *
     * @param string|null $title 
     * The title to be displayed in the title tag.
     * 
     * @return string 
     * The formatted HTML title tag.
     */
    private static function title(?string $title): string {
        return !empty($title) ? sprintf('<title>%s</title>', $title) : '';
    }
    
    /**
     * Returns a meta tag for the given description string.
     *
     * @param string $description 
     * The description string to be added to the meta tag.
     * 
     * @return string 
     * The meta tag for the given description string.
     */

    private static function description(string $description): string {
        return !empty($description) ? sprintf('<meta name="description" content="%s">', $description) : '';
    }

    /**
     * Returns a string containing a meta tag with the given keywords.
     *
     * @param string $keywords 
     * The keywords to include in the meta tag.
     * 
     * @return string 
     * The meta tag with the given keywords, or an empty string if no 
     * keywords were provided.
     */
    private static function keywords(string $keywords): string {
        return !empty($keywords) ? sprintf('<meta name="keywords" content="%s">', $keywords) : '';
    }

    /**
     * Generates a meta tag for robots with the given content.
     *
     * @param string $robots 
     * The content for the robots meta tag.
     * 
     * @return string 
     * The generated meta tag for robots.
     */
    private static function robots(string $robots): string {
        return !empty($robots) ? sprintf('<meta name="robots" content="%s">', $robots) : '';
    }

    /**
     * Generates the theme color meta tag.
     * 
     * @return string
     * The theme color meta tag.
     */
    private static function theme(string $theme): string {
        return !empty($theme) ? sprintf('<meta name="theme-color" content="%s">', $theme) : '';
    }

    /**
     * Generates the manifest meta tag.
     * 
     * @return string
     * The manifest meta tag.
     */
    private static function manifest(string $manifest): string {
        return !empty($manifest) ? sprintf('<link rel="manifest" href="%s">', $manifest) : '';
    }

    /**
     * Generates social media meta tags based on the provided meta data.
     *
     * @param array|null $meta 
     * An array containing the following keys: title, description, type, 
     * image, and url.
     *
     * @return string 
     * The generated meta tags as a string.
     */
    private static function social(?array $meta): string{
              
        if(!empty($meta['image'])) {
            
            $url = URL::get([
                "PORT"  => false,
                "QUERY" => false,
                "PATH"  => false
            ]);

            $meta['image'] = $url . $meta['image'];
        }

        $meta = [
            // Open Graph meta tags:
            'og:title'            => $meta['title'],
            'og:description'      => $meta['description'],
            'og:type'             => $meta['type'],
            'og:image'            => $meta['image'],
            'og:url'              => $meta['url'],

            // Twitter meta tags:
            'twitter:title'       => $meta['title'],
            'twitter:description' => $meta['description'],
            'twitter:image'       => $meta['image'],
            'twitter:url'         => $meta['url'],
        ];

        $output = '<!-- Social media meta tags: -->';

        // Generate meta tags for each property with non-empty content
        foreach ($meta as $property => $content) {
            if (!empty($content)) {
                $output .= sprintf('<meta property="%s" content="%s">', $property, $content);
            }
        }

        // Add Twitter meta tags if at least one of title, description, or image is non-empty
        if (!empty($meta['title']) || !empty($meta['description']) || !empty($meta['image'])) {
            $output .= '<meta name="twitter:card" content="summary">';
            $output .= '<meta name="twitter:site" content="@twitter">';
            $output .= '<meta name="twitter:creator" content="@twitter">';
        }

        return $output;
    }

    /**
     * Generates the HTML code for the favicon.
     *
     * @return string 
     * The HTML code for the favicon.
     */
    private static function favicon(): string {
        $output = '<!-- Favicons: -->';
        $output .= Favicon::generate();
        return $output;
    }

    /**
     * Generates the HTML code for stylesheets.
     * 
     * @return string
     * The HTML code for stylesheets.
     */
    private static function stylesheets(): string {
        $output = "";
        if(!empty(Assets::$assets["styles"])) {
            $output .= '<!-- Stylesheets: -->';
            $output .= Assets::link("styles");
        }
        return $output;
    }

    /**
     * Generates the HTML code for scripts.
     * 
     * @return string
     * The HTML code for scripts.
     */
    private static function scripts(): string {
        $output = "";
        if(!empty(Assets::$assets["scripts"])) {
            $output .= '<!-- Scripts: -->';
            $output .= Assets::link("scripts");
        }
        return $output;
    }

    /**
     * Generates the HTML code for vendor scripts and links.
     * 
     * @return string
     * The HTML code for vendor items.
     */
    private static function vendors(): string {
        $output = "";
        if(!empty(Assets::$assets["vendors"])) {
            $output .= '<!-- Vendors: -->';
            $output .= Assets::link("vendors");
        }
        return $output;
    }
    
} 

  