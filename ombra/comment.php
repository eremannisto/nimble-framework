<?php

/**
 * Simple comment class, that will output HTML comments.
 */
class Comment {

    /**
     * Output a HTML comment.
     * 
     * @param string $comment
     * The comment to output.
     * 
     * @return string
     * Returns the HTML comment as a string.
     */
    public static function set(string $comment): string {
        return sprintf('<!-- %s -->', $comment);
    }
}