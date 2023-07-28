<?php

// Dependancies:
if (!class_exists('Package')) {
    require_once(__DIR__ . '/package.php');
}

/**
 * Time class handles all time related methods,
 * such as getting and setting the time zone, -format,
 * and -locale.
 * 
 * @version 1.0.0
 */
class Time {

    /**
     * Get the time format
     * 
     * @return string|null
     * The time format, or null if the file could not be read or decoded.
     */
    public static function getTimeFormat(): ?string {
        return Package::get()->time->format ?? null;
    }

    /**
     * Set the time format.
     * 
     * @param string $format
     * The time format.
     */
    public static function setTimeFormat(string $format): void {
        Package::get()->time->format = $format;
        Package::set(Package::get());
    }


    /**
     * Get the time zone
     * 
     * @return string|null
     * The time zone, or null if the file could not be read or decoded.
     */
    public static function getTimeZone(): ?string {
        return Package::get()->time->zone ?? null;
    }

    /**
     * Set the time zone.
     * 
     * @param string $zone
     * The time zone.
     */
    public static function setTimeZone(string $zone): void {
        Package::get()->time->zone = $zone;
        Package::set(Package::get());
    }

    /**
     * Get the time locale
     * 
     * @return string|null
     * The time locale, or null if the file could not be read or decoded.
     */
    public static function getTimeLocale(): ?string {
        return Package::get()->time->locale ?? null;
    }

    /**
     * Set the time locale.
     * 
     * @param string $locale
     * The time locale.
     */
    public static function setTimeLocale(string $locale): void {
        Package::get()->time->locale = $locale;
        Package::set(Package::get());
    }

    /**
     * Get restriction start time
     * 
     * @return string|null
     * The restriction start time, or null if the file could not be read or decoded.
     */
    public static function getRestrictionStart(): ?string {
        return Package::get()->time->restriction->start ?? null;
    }

    /**
     * Set restriction start time.
     * 
     * @param string $start
     * The restriction start time.
     */
    public static function setRestrictionStart(string $start): void {
        Package::get()->time->restriction->start = $start;
        Package::set(Package::get());
    }

    /**
     * Get restriction end time
     * 
     * @return string|null
     * The restriction end time, or null if the file could not be read or decoded.
     */
    public static function getRestrictionEnd(): ?string {
        return Package::get()->time->restriction->end ?? null;
    }

    /**
     * Set restriction end time.
     * 
     * @param string $end
     * The restriction end time.
     */
    public static function setRestrictionEnd(string $end): void {
        Package::get()->time->restriction->end = $end;
        Package::set(Package::get());
    }

    /**
     * Time restrictions. This method is used to restrict
     * access to certain pages based on time.
     * 
     * @param string $start
     * The start time.
     * 
     * @param string $end
     * The end time.
     * 
     * @return bool
     * Returns true if the current time is within the
     * start and end time, otherwise false.
     */
    public static function restrict(string $start, string $end): bool {

        // Get restriction start and end time
        $start  = Time::getRestrictionStart();
        $end    = Time::getRestrictionEnd();

        // If both start and end time are null, return true
        if ($start === null && $end === null) {
            return true;
        }

        $start  = strtotime($start);            // Start time in unix timestamp
        $end    = strtotime($end);              // End time in unix timestamp
        $now    = strtotime(date('H:i:s'));     // Current time in unix timestamp

        // If start time is null and end time is not null
        if ($start === null && $end !== null) {
            if ($now > $end) {
                return false;
            }
            return true;
        }

        // If start time is not null and end time is null
        if ($start !== null && $end === null) {
            if ($now < $start) {
                return false;
            }
            return true;
        }

        // If neither start or end time is null,
        // check if the current time is not within the
        // start and end time.
        if ($now < $start || $now > $end) {
            return false;
        }

        // If the current time is within the start and end time,
        // return true.
        return true;
    }

    /**
     * Set default time zone
     * 
     * @return void
     * Returns nothing.
     */
    public static function setDefaultTimeZone(): void {
        date_default_timezone_set(Time::getTimeZone());
    }
}