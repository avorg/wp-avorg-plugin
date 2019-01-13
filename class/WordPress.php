<?php

namespace Avorg;

if ( !\defined( 'ABSPATH' ) ) exit;

/**
 * @method add_action($string, array $array)
 * @method add_filter($string, array $array)
 * @method get_option($pageIdOptionName)
 * @method get_post_status($postId)
 * @method get_the_ID()
 * @method register_activation_hook($string, array $array)
 * @method status_header($int)
 * @method update_option($pageIdOptionName, $id)
 * @method wp_insert_post(array $array, $true)
 * @method wp_publish_post($postId)
 */
class WordPress {
	public function __call( $function, $arguments ) {
		$result = call_user_func_array( $function, $arguments );
		
		if ( \is_wp_error( $result ) && WP_DEBUG ) {
			die( $result->get_error_message() );
		}
		
		return $result;
	}
}