<?php
/*

Plugin Name: Westernbid payments
Description: Get Paypal payments through Western Bid
Version: 0.1.0
Author: Oleg Petruk
Author URI: https://oleg-petruk.github.io
License: GPLv2 or later
Text Domain: westernbid
Domain Path: /lang

*/

//If no Wordpress, exit.
if (!defined('ABSPATH')) { exit; }

define('PLUGIN_VERSION', '0.1.0');


include_once('src/PAYMENT_Core.php');

add_action('init', 'westernbid_payments_init', 1);
register_activation_hook(__FILE__, 'westernbid_payments_activate');
register_deactivation_hook(__FILE__, 'westernbid_payments_deactivate');

function westernbid_payments_activate()
{
    PAYMENT_Core::install();
}

function westernbid_payments_deactivate()
{
    PAYMENT_Core::uninstall();
}

function westernbid_payments_init()
{
    new PAYMENT_Core();
}




