<?php
/*
Plugin Name: Shadow Screen Options
Plugin URI: http://uncommoncontent.com/wordpress/plugins/shadow-screen-options
Description: Create a shadow system of blog-specific screen layout options in a multisite environment. 
Version: 0.1
Author: Jennifer M. Dodd
Author URI: http://bajada.net
*/ 

/*
    Copyright 2011 Jennifer M. Dodd (email: jmdodd@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


function ucc_sso_update_user_metadata( $meta_type = null, $user_id, $meta_key, $meta_value, $prev_value = '' ) {
	global $blog_id;
 
	$prefix = 'ucc_sso_' . $blog_id . '_';
	if ( preg_match( '#^(closedpostboxes|metaboxhidden|meta-box-order|screen_layout)_(post|page|link|nav-menus|dashboard)$#', $meta_key ) ) {
		$meta_key = $prefix . $meta_key;
		$result = update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' );
		return $result;
	} else {
		return null;
	}
}
add_filter( 'update_user_metadata', 'ucc_sso_update_user_metadata', 10, 5 );
 
 
function ucc_sso_get_user_metadata( $meta_type = null, $user_id, $meta_key, $single ) {
	global $blog_id;
 
	$prefix = 'ucc_sso_' . $blog_id . '_';

	if ( preg_match( '#^(closedpostboxes|metaboxhidden|meta-box-order|screen_layout)_(post|page|link|nav-menus|dashboard)$#', $meta_key ) ) {
		$meta_key = $prefix . $meta_key;
		$result = get_user_meta( $user_id, $meta_key, $single );
		return $result;
	} else {
		return null;
	}
}
add_filter( 'get_user_metadata', 'ucc_sso_get_user_metadata', 10, 4 );

 
function ucc_sso_get_user_option( $result, $option, $user ) {
	global $blog_id;
 
	$prefix = 'ucc_sso_' . $blog_id . '_';
	$option = $prefix . $option;
	$result = get_user_option( $option, $user->ID );
 
	return $result;
}


function ucc_sso_init() {
	$types = array( 'post', 'page', 'link', 'nav-menus', 'dashboard' );

	foreach ( $types as $type ) {
		add_filter( 'get_user_option_closedpostboxes_' . $type, 'ucc_sso_get_user_option', 10, 3 );
		add_filter( 'get_user_option_metaboxhidden_' . $type, 'ucc_sso_get_user_option', 10, 3 );
		add_filter( 'get_user_option_meta-box-order_' . $type, 'ucc_sso_get_user_option', 10, 3 );
		add_filter( 'get_user_option_screen_layout_' .  $type, 'ucc_sso_get_user_option', 10, 3 );
	}
}
add_filter( 'init', 'ucc_sso_init' ); 	

?>
