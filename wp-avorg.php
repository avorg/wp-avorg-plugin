<?php
/*
Plugin Name: AudioVerse
Description: AudioVerse plugin
Author: Nathan Arthur
Version: 1.0
Author URI: http://NathanArthur.com/
*/

namespace Avorg;

if ( !\defined( 'ABSPATH' ) ) exit;

include_once( dirname(__FILE__) .  "/vendor/autoload.php" );

$factory = new Factory();
$plugin  = $factory->makePlugin();
$adminPanel = $factory->makeAdminPanel();

\register_activation_hook( __FILE__, array( $plugin, "activate" ) );
\register_deactivation_hook( __FILE__, "plugin_deactivate" );
\add_action( "admin_menu", array( $adminPanel, "register" ) );

function plugin_deactivate() {}