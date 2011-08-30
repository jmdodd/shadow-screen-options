<?php
/*
Plugin Name: Shadow Screen Options
Plugin URI: http://uncommoncontent.com/wordpress/plugins/shadow-screen-options
Description: Create a shadow system of blog-specific screen layout options in a multisite environment. 
Version: 0.2
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


function ucc_sso_needs_shadow( $meta_key ) {
	$actions = array( 'closedpostboxes', 'metaboxhidden', 'meta-box-order', 'screen-layout' );
	$objects = array( 'post', 'page', 'link', 'nav-menus', 'dashboard' );

	// Fix screen_layout issue.
	$meta_key = str_replace( 'screen_layout', 'screen-layout', $meta_key );
	$arr = explode( '_', $meta_key );
	if ( 	isset( $arr[0] ) && in_array( $arr[0], $actions ) &&
		isset( $arr[1] ) && in_array( $arr[1], $objects ) &&  
		!isset( $arr[2] ) ) {
		return true;
	} else {
		return false;
	}
}


function ucc_sso_update_user_metadata( $meta_type = null, $user_id, $meta_key, $meta_value, $prev_value = '' ) {
	global $blog_id;
 
	$prefix = 'ucc_sso_' . $blog_id . '_';
	if ( ucc_sso_needs_shadow( $meta_key ) ) {
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
	if ( ucc_sso_needs_shadow( $meta_key ) ) { 
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
        $objects = array( 'post', 'page', 'link', 'nav-menus', 'dashboard' );

	foreach ( $objects as $object ) {
		add_filter( 'get_user_option_closedpostboxes_' . $object, 'ucc_sso_get_user_option', 10, 3 );
		add_filter( 'get_user_option_metaboxhidden_' . $object, 'ucc_sso_get_user_option', 10, 3 );
		add_filter( 'get_user_option_meta-box-order_' . $object, 'ucc_sso_get_user_option', 10, 3 );
		add_filter( 'get_user_option_screen_layout_' .  $object, 'ucc_sso_get_user_option', 10, 3 );
	}
}
add_filter( 'init', 'ucc_sso_init' ); 	

?>
