<?php declare(strict_types=1);


/**
 * This class handles how different assets get added and linked
 * to the application. This includes CSS, JS, and other assets.
 */
class Assets {

    /**
     * Here we store all the CSS, JS, and other assets that
     * need to be loaded into the application.
     * 
     * @todo Add support for other media files, such as
     * images, videos, and audio files.
     */
    public static array $assets = [
        "styles"  => [],
        "scripts" => [],
        "vendors"  => []
    ];

    /**
     * Here we store the different types of assets that
     * can be added to the application.
     */
    public static array $types = [
        "style"  => "text/css",
        "script" => "text/javascript"
    ];

    /**
     * Cache time in seconds. Default is 1 year, which is
     * 31536000 seconds.
     */
    private static int $cacheTime = 31536000;


    /**
     * === === === === STEP 1 === === === === 
     * Add and build the assets and store them in the 
     * assets array.
     */

    /**
     * Sets the global cache data for the Head class.
     *
     * @param array $data 
     * The cache data to be set.
     * 
     * @return void
     * Returns nothing.
     */
    public static function global(array $assets): void {
        // If asset is styles or scripts, add them to the Assets array.
        if (isset($assets['styles'])) {
            foreach ($assets['styles'] as $path => $conditions) {

                if(empty($conditions) || !is_array($conditions)) $conditions = [];

                Assets::add([
                    'mode'       => 'client',
                    'type'       => 'style',
                    'path'       => $path,
                    'conditions' => $conditions
                ]);
            }
            unset($assets['styles']);
        }

        // If asset is styles or scripts, add them to the Assets array.
        if(isset($assets['scripts'])) {
            foreach ($assets['scripts'] as $path => $conditions) {
                if(empty($conditions) || !is_array($conditions)) $conditions = [];

                Assets::add([
                    'mode'       => 'client',
                    'type'       => 'script',
                    'path'       => $path,
                    'conditions' => $conditions
                ]);
            }
            unset($assets['scripts']);
        }

        // If asset is vendor, add them to the Assets array.
        if(isset($assets['vendors'])){
            foreach ($assets['vendors'] as $asset) {
                Assets::$assets['vendors'][] = [
                    "link" => $asset
                ];
            }
        }
    }

    /**
     * Add an asset to the application.
     * 
     * @param string $type
     * The type of the asset to add, options are:
     * style, script, image, video, audio, and font.
     * 
     * @param string $path
     * The path to the asset to add.
     * 
     * @param array $attributes
     * An array of attributes to add to the asset.
     * 
     * @return void
     */
    public static function add(array $asset): void {    

        // Validate the asset and make sure it's not empty
        $asset = self::validate($asset);
        if(empty($asset)) return; 

        $section = $asset['section'];
        $mode    = $asset['mode'];
        $link    = $asset['link'];

        // Add the asset to the assets array
        self::$assets[$section][$mode][] = [
            "link" => $link
        ];
    }

    /**
     * Validate an asset.
     * 
     * @param array $asset
     * The asset to validate.
     * 
     * @return array
     * The validated asset.
     */
    public static function validate(array $asset): array {
         
        // First we make sure that all the required asset
        // values are set. If not, we return an empty array.
        if (!isset($asset['mode']) || !isset($asset['type']) || !isset($asset['path'])) {
            Report::warning("Asset is missing required values.");
            return [];
        }

        // Make sure the conditions are set and are an array
        if(!isset($asset['conditions']))    $asset['conditions'] = [];
        if(!is_array($asset['conditions'])) $asset['conditions'] = [$asset['conditions']];

        // Make sure the conditions are valid
        foreach ($asset['conditions'] as $condition) {
            if(!self::conditions($condition)) return [];
        }

        // Assign the asset values to variables
        $mode = $asset['mode']; // Mode of the asset: client, server
        $type = $asset['type']; // Type of the asset: style, script, etc.
        $path = $asset['path']; // Path to the asset: /components/...

        // Check if the asset mode is valid
        if ($mode !== 'client' && $mode !== 'server') {
            Report::warning("Asset mode must be 'client' or 'server'.");
            return [];
        }

        // Check if the asset type is valid
        if (!isset(self::$types[$type])) {
            Report::warning("Asset type '$type' is invalid.");
            return [];
        }

        // Assign the asset section based on the asset type
        // and assign the asset mime type based on the asset type:
        $section = "{$type}s";
        $mime    = self::$types[$type];

        // Check if the asset path is valid
        if (empty($path)) {
            Report::warning("Asset path can't be empty.");
            return [];
        }

        // Check if the asset path contains '..' or './'
        if (strpos($path, '..') !== false || strpos($path, './') !== false) {
            Report::warning("Asset path can't contain '..' or './'.");
            return [];
        }

        // Check if the asset path starts with a slash
        if (strpos($path, '/') !== 0) {
            Report::warning("Asset path must start with a slash.");
            return [];
        }

        // If the asset mode is 'client', check if the asset exists
        // in the public folder. If the asset mode is 'server', check
        // if the asset exists in the src folder.
        if ($mode === 'client') {
            $conditions = $asset['conditions'] ?? [];
            $fullPath   = Path::public($path);
            if (!file_exists($fullPath)) {
                Report::warning("Asset path '$path' does not exist.");
                return [];
            }
        } 
        else if ($mode === 'server') {
            $conditions = [];
            $fullPath   = Path::src($path);
            $path       = "/fetch{$path}";
            if (!file_exists($fullPath)) {
                Report::warning("Asset path '$path' does not exist.");
                return [];
            }
        }

        // Get the version number of the asset
        $version = File::modtime($fullPath);

        // Build the asset and return it
        $link = self::build([
            "type"    => $mime,
            "path"    => $path,
            "version" => $version
        ]);

        return [
            "section"    => $section,
            "link"       => $link,
            "mode"       => $mode
        ];
    }

    /**
     * Determines whether the asset should be included on the current
     * page based on the specified condition.
     *
     * @param string|null $condition 
     * The condition to check against. If null, the asset 
     * will be included on every page.
     * 
     * @return bool 
     * Returns true if the asset should be included on 
     * the current page, false otherwise.
     */
    private static function conditions(string $condition = null): bool {

        // If no condition is specified, return true (include on every page)
        if ($condition === null) return true;

        // If the condition starts with an exclamation point, call itself
        // again with the exclamation point removed (exclude on specific pages)
        if (strpos($condition, "!") === 0) return !self::conditions(substr($condition, 1)); 

        // Return true if the current page matches the include condition; otherwise, 
        // return false (include on specific pages)
        $current = Request::current();
        $index   = Config::get("application->router->index");
        return $current === $condition || $current === $index;
    }

    /**
     * Build the assets link and return it.
     * 
     * @param array $asset
     * The asset to build.
     * 
     * @return string
     * The built asset.
     */
    private static function build(array $asset): string {
        
        if (!isset($asset['type']) || !isset($asset['path']) || !isset($asset['version'])) {
            Report::warning("Could not build asset link. Asset is missing required values.");
            return "";
        }

        $type    = $asset['type'];
        $path    = $asset['path'];
        $version = $asset['version'];

        switch($type) {
            case 'text/css':
                return "<link rel='stylesheet' href='{$path}?v={$version}'>";

            case 'text/javascript':
                return "<script defer src='{$path}?v={$version}'></script>";

            default:
                Report::warning("Invalid type '$type' for file '$path'.");
                return '';
        }
    }

    /**
     * Goes trough the assets array and returns the assets
     * that match the specified type.
     * 
     * @param string $type
     * The type of the assets to link. Valid values are:
     * styles, scripts.
     * 
     * @return string
     * The generated links or an empty string if no assets
     * of the specified type were found.
     */
    public static function link(string $type): string {
        
        // Type can be either 'styles' or 'scripts'
        if ($type !== 'styles' && $type !== 'scripts' && $type !== 'vendors') {
            Report::warning("Invalid type '$type' for asset link.");
            return "";
        }

        // If the assets array is empty, return
        if (empty(self::$assets[$type])) return "";

        // Generate the links
        $output = "";
        foreach (self::$assets[$type]['server'] as $asset) { $output .= $asset['link']; }
        foreach (self::$assets[$type]['client'] as $asset) { $output .= $asset['link']; }

        // Return the generated links
        return $output;
    }

    /**
     * Fetches a file from the specified path and sends it 
     * to the client with appropriate headers.
     *
     * @param string $path 
     * The path to the file to fetch, starting with either 
     * '/components' or '/pages'.
     * 
     * @param string $page 
     * The name of the page requesting the file.
     * 
     * @return void
     * Returns nothing.
     */
    public static function fetch(string $page): void {
        
        // Remove the 'fetch' part from the path, leaving only 
        // the path to the component or page.
        $path = Path::strip('fetch', $page);

        // Ensure the path is not empty
        if (empty($path)) {
            Report::error("Fetch: Path can't be empty");
            return;
        }
    
        // Check for security: Path can't contain '..' or './'
        if (strpos($path, '..') !== false || strpos($path, './') !== false) {
            Report::error("Fetch: Path can't contain '..' or './'");
            return;
        }
    
        // Path must start with either '/components' or '/pages'
        if (strpos($path, '/components') !== 0 && strpos($path, '/pages') !== 0) {
            Report::error("Fetch: Path has to start with either '/components' or '/pages'");
            return;
        }
    
        // Determine the file type and set the appropriate MIME type
        if     (substr($path, -3) === '.js')  $type = 'text/javascript';
        elseif (substr($path, -4) === '.css') $type = 'text/css';

        else {
            Report::error("Fetch: File must end with '.js' or '.css'");
            return;
        }
    
        // Construct the full path to the file (root + src + path)
        $filePath = Path::src($path);
    
        // Check if the file exists
        if (!File::exists($filePath)) {
            Report::error("Fetch: File does not exist");
            return;
        }
    
        // If all checks pass, the path is valid, and we can fetch the file.
        // Set cache control headers, echo the file, and send appropriate content type header.
        $cacheTime    = self::$cacheTime;
        $gmDate       = gmdate('D, d M Y H:i:s \G\M\T', time() + $cacheTime);
        $fileContents = file_get_contents($filePath);

        header("Content-Type: $type");
        header("Cache-Control: max-age={$cacheTime}, public, immutable");
        header("Expires: $gmDate");

        echo $fileContents;
    }

}