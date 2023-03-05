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

    /**-------------------------------------------------
    * Here you can define when the exceptions should be pruned
    * The default is 7 days (a week)
    * The format for providing period should follow carbon's format. i.e.
    * 1 day => 'subDay()',
    * 3 days => 'subDays(3)',
    * 7 days => 'subWeek()',
    * 1 month => 'subMonth()',
    * 2 months => 'subMonths(2)',
    *
    */

    'period' => now()->subWeek(),
];
