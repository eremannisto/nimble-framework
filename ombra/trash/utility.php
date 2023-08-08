<?php

/**
 * Utility class contains utility methods.
 * 
 * @version 1.0.0
 */
class Utility {

    /**
     * Find the first valid value in an array or string.
     *
     * @param array|string $value
     * The array or string to search.
     * 
     * @param string|null $fallback 
     * The value to return if no non-empty values are found.
     * 
     * @return string|null 
     * The first non-empty value found, or the fallback value if no non-empty
     * values are found. If no fallback value is provided, returns null.
     */
    public static function getNonEmpty($value, ?string $fallback = null): ?string {
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!empty($item)) {
                    return $item;}
            }
            return $fallback;
        }
        return $value ?? $fallback;
    }
}
