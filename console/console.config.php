<?php
/**
 * Copy this file to console.config.php and place it (with your settings inside) near console.php
 */

/**
 * Digest HTTP Authentication
 *
 * To enable, add user: "name" => "password".
 */
$users = array(
    'admin' => '1234',
);

$realm = 'Console';

/**
 * Console style.
 * Available styles: white, green, grey, far, ubuntu
 */
$theme = 'default';

/**
 * List of commands filters. You can use * for any symbol. And $1 as replacement.
 * Usually you only need '*' => '$1' command. If you need some mapping add more.
 * Example: 'move * *' => 'mv $1 $2'
 */
$commands = array(
    'git*' => '/usr/bin/local/git $1',
    'composer*' => '/usr/local/bin/composer $1',
    'symfony*' => './app/console $1',
    '*' => '$1', // Allow any command. Must be at the end of the list.
);

/**
 * array of allowed commands. Default: empty array (all are allowed)
 * You can use * for any symbol.
 * Example: "branch*" will allow both "branch" and "branch -v" commands
 */
$allow = array();

/**
 * array of denied commands. Default: empty array (none is denied)
 * You can use * for any symbol.
 */
$deny = array(
    "rm*",
);

