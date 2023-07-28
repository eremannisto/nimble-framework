<?php

// Dependancies:
if (!class_exists('Package')) {
    require_once (__DIR__ . '/package.php');
}

/**
 * Directories class handles all directories related methods,
 * such as getting and setting the media, image, video, and audio directories.
 * 
 * @version 1.0.0
 */
class Directories {

    /**
     * Get media directory.
     * 
     * @return string|null
     * The media directory, or null if the file could not be read or decoded.
     */
    public static function getMediaDirectory(): ?string {
        return Package::get()->directories->media ?? null;
    }

    /** 
     * Set media directory file.
     * 
     * @param string $media
     * The media directory file.
     */
    public static function setMediaDirectory(string $media): void {
        Package::get()->directories->media = $media;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get image directory.
     * 
     * @return string|null
     * The image directory, or null if the file could not be read or decoded.
     */
    public static function getImageDirectory(): ?string {
        return Package::get()->directories->image ?? null;
    }

    /** 
     * Set image directory file.
     * 
     * @param string $image
     * The image directory file.
     */
    public static function setImageDirectory(string $image): void {
        Package::get()->directories->image = $image;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get video directory.
     * 
     * @return string|null
     * The video directory, or null if the file could not be read or decoded.
     */
    public static function getVideoDirectory(): ?string {
        return Package::get()->directories->video ?? null;
    }

    /** 
     * Set video directory file.
     * 
     * @param string $video
     * The video directory file.
     */
    public static function setVideoDirectory(string $video): void {
        Package::get()->directories->video = $video;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get audio directory.
     * 
     * @return string|null
     * The audio directory, or null if the file could not be read or decoded.
     */
    public static function getAudioDirectory(): ?string {
        return Package::get()->directories->audio ?? null;
    }

    /** 
     * Set audio directory file.
     * 
     * @param string $audio
     * The audio directory file.
     */
    public static function setAudioDirectory(string $audio): void {
        Package::get()->directories->audio = $audio;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get favicon directory.
     * 
     * @return string|null
     * The favicon directory, or null if the file could not be read or decoded.
     */
    public static function getFaviconDirectory(): ?string {
        return Package::get()->directories->favicon ?? null;
    }

    /** 
     * Set favicon directory file.
     * 
     * @param string $favicon
     * The favicon directory file.
     */
    public static function setFaviconDirectory(string $favicon): void {
        Package::get()->directories->favicon = $favicon;
        Package::set(Package::get());   
        
        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get pages directory.
     * 
     * @return string|null
     * The pages directory, or null if the file could not be read or decoded.
     */
    public static function getPagesDirectory(): ?string {
        return Package::get()->directories->pages ?? null;
    }

    /** 
     * Set pages directory file.
     * 
     * @param string $pages
     * The pages directory file.
     */
    public static function setPagesDirectory(string $pages): void {
        Package::get()->directories->pages = $pages;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get templates directory.
     * 
     * @return string|null
     * The pages templates, or null if the file could not be read or decoded.
     */
    public static function getTemplatesDirectory(): ?string {
        return Package::get()->directories->templates ?? null;
    }

    /** 
     * Set templates directory file.
     * 
     * @param string $templates
     * The templates directory file.
     */
    public static function setTemplatesDirectory(string $templates): void {
        Package::get()->directories->templates = $templates;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get components directory.
     * 
     * @return string|null
     * The pages components, or null if the file could not be read or decoded.
     */
    public static function getComponentsDirectory(): ?string {
        return Package::get()->directories->components ?? null;
    }

    /** 
     * Set components directory file.
     * 
     * @param string $components
     * The components directory file.
     */
    public static function setComponentsDirectory(string $components): void {
        Package::get()->directories->components = $components;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get styles directory.
     * 
     * @return string|null
     * The pages styles, or null if the file could not be read or decoded.
     */
    public static function getStylesDirectory(): ?string {
        return Package::get()->directories->styles ?? null;
    }

    /** 
     * Set styles directory file.
     * 
     * @param string $styles
     * The styles directory file.
     */
    public static function setStylesDirectory(string $styles): void {
        Package::get()->directories->styles = $styles;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }

    /**
     * Get scripts directory.
     * 
     * @return string|null
     * The pages scripts, or null if the file could not be read or decoded.
     */
    public static function getScriptsDirectory(): ?string {
        return Package::get()->directories->scripts ?? null;
    }

    /** 
     * Set scripts directory file.
     * 
     * @param string $scripts
     * The scripts directory file.
     */
    public static function setScriptsDirectory(string $scripts): void {
        Package::get()->directories->scripts = $scripts;
        Package::set(Package::get());   

        // TODO: Copy the old directory to the new one and delete the old one.
    }
}