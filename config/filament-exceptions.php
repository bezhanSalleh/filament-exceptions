<?php

return [

    'slug' => 'exceptions',

    /** Show or hide in navigation/sidebare */
    'navigation_enabled' => true,

    /** Sort order, if shown. No effect, if navigation_enabled it set to false. */
    'navigation_sort' => -1,

    /** Whether to show a navigation badge. No effect, if navigation_enabled it set to false. */
    'navigation_badge' => true,

    /** Icons to use for navigation (if enabled) and pills */
    'icons' => [
        'navigation' => 'heroicon-o-chip',
        'exception' => 'heroicon-o-chip',
        'headers' => 'heroicon-o-switch-horizontal',
        'cookies' => 'heroicon-o-database',
        'body' => 'heroicon-s-code',
        'queries' => 'heroicon-s-database',
    ],

    'is_globally_searchable' => false,

    /**-------------------------------------------------
    * Change the default active pill
    *
    * Exception => 1 (Default)
    * Headers => 2
    * Cookies => 3
    * Body => 4
    * Queries => 5
    */
    'active_pill' => 1,

];
