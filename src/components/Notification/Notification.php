<?php declare(strict_types=1);


class Notification {

    /**
     * The name of the session that will be used to store 
     * the notifications.
     * 
     * @var string
     */
    private static string $notifications = 'Notifications';

    /**
     * The icons that will be used for the notifications.
     * 
     * @var array
     */
    private static array $icons = [
        'success' => 'fa-sharp fa-solid fa-check',
        'error'   => 'fa-sharp fa-solid fa-times',
        'warning' => 'fa-sharp fa-solid fa-exclamation',
        'info'    => 'fa-sharp fa-solid fa-info',
        'mail'    => 'fa-sharp fa-solid fa-envelope'
    ];

    /**
     * Show error codes.
     * 
     * @var bool
     */
    private static bool $showCodes = true;

    /**
     * Get a notification by its index. If no index is provided, the first
     * notification will be returned.
     *
     * @param int $index
     *   The index of the notification to retrieve.
     *
     * @return array|null
     *   The notification at the specified index or null if not found.
     */
    public static function get(int $index = 0): ?array {
        $notifications = $_SESSION[self::$notifications] ?? null;
        if (!isset($notifications)) return null; 
        return isset($notifications[$index]) ? $notifications[$index] : null;
    }
 
    /**
     * Set the notification.
     * 
     * @param string $status
     * The status of the notification. 
     * Can be 'success', 'error', 'warning' or 'info'.
     * 
     * 
     * @return void
     * Returns nothing.
     */
    private static function set(string $status, array $parameters): void {
        $_SESSION[self::$notifications][] = [
            'status'      => $status,
            'size'        => $parameters['size']        ?? 'large',
            'code'        => $parameters['code']        ?? '',
            'title'       => $parameters['title']       ?? '',
            'description' => $parameters['description'] ?? '',
            'icon'        => self::$icons[$status]      ?? '',
        ];
    }

    /**
     * Set the notification to a success, error, 
     * warning, info or mail notification.
     * 
     * @param array $parameters
     * The parameters of the notification.
     * 
     * @return void
     * Returns nothing.
     */
    public static function success ( array $parameters): void { Notification::set('success', $parameters); }
    public static function error   ( array $parameters): void { Notification::set('error',   $parameters); }
    public static function warning ( array $parameters): void { Notification::set('warning', $parameters); }
    public static function info    ( array $parameters): void { Notification::set('info',    $parameters); }
    public static function mail    ( array $parameters): void { Notification::set('mail',    $parameters); }

    /**
     * Remove the notification at the given index.
     * 
     * @param int $index
     * The index of the notification to remove.
     * 
     * @return void
     * Returns nothing.
     */
    private static function remove(int $index): void {
        $notifications = $_SESSION[self::$notifications] ?? null;
        if (isset($notifications[$index])) unset($notifications[$index]);
    }

    /**
     * Clear the notifications from the session
     * 
     * @return void
     * Returns nothing.
     */
    private static function clear(): void {
        $notifications = $_SESSION[self::$notifications] ?? null;
        if (isset($notifications)) unset($notifications); 
    }

    /**
     * Renders the notification.
     * 
     * @return void
     * Returns nothing.
     */
    public static function render():void {
        $notifications = $_SESSION[self::$notifications] ?? null;
        if(!isset($notifications)) return; 
        ob_start(); ?>

        <div data-notifications> <?php
            foreach($notifications as $notification) { 

                // Get the notification properties
                $status      = $notification['status'];
                $title       = $notification['title'];
                $description = $notification['description'];
                $icon        = $notification['icon'];
                $code        = $notification['code'] ?? null;

                // If the notification has a code, append it to the title
                // if the showCodes property is set to true
                if(!empty($code) && self::$showCodes) {
                    $title .= " ({$code})";
                } ?>

                <div data-notification="<?= $status ?>">
                    <div class="content">

                        <div class="icon">
                            <span><i class="<?= $icon ?>"></i></span>
                        </div>

                        <div class="description">
                            <span class="title"      ><?= $title       ?></span>
                            <span class="description"><?= $description ?></span>
                        </div>

                    </div>
                </div>

            <?php } ?> 
        </div> <?php

        // Clear the notifications
        self::clear();
            
        ob_get_contents();
    }

}