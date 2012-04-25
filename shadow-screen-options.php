<?php
/*
Plugin Name: Shadow Screen Options
Description: Create a shadow system of blog-specific screen layout options in a multisite environment. 
Version: 0.4
Author: Jennifer M. Dodd
Author URI: http://uncommoncontent.com/
*/ 

/*
	Copyright 2012  Jennifer M. Dodd  <jmdodd@gmail.com>

	This program is free software; you can redistribute it and/or modify
   	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, see <http://www.gnu.org/licenses/>. 
*/


if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! class_exists( 'UCC_Shadow_Screen_Options' ) ) {
class UCC_Shadow_Screen_Options {
	public static $instance;
	public static $prefix;
	public static $actions;
	public static $objects;

	public function __construct() {
		self::$instance = $this;

		global $blog_id;
		$this->prefix = 'ucc_sso_' . $blog_id . '_';
		$this->actions = array( 'closedpostboxes', 'metaboxhidden', 'meta-box-order', 'screen_layout' );
		$this->objects = array( 'post', 'page', 'link', 'nav-menus', 'dashboard' );

		add_filter( 'get_user_metadata', array( $this, 'get_user_metadata' ), 10, 4 );
		add_filter( 'update_user_metadata', array( $this, 'update_user_metadata' ), 10, 5 );

		foreach ( $this->objects as $object ) {
			add_filter( 'get_user_option_closedpostboxes_' . $object, array( $this, 'get_user_option' ), 10, 3 );
			add_filter( 'get_user_option_metaboxhidden_'   . $object, array( $this, 'get_user_option' ), 10, 3 );
			add_filter( 'get_user_option_meta-box-order_'  . $object, array( $this, 'get_user_option' ), 10, 3 );
			add_filter( 'get_user_option_screen_layout_'   . $object, array( $this, 'get_user_option' ), 10, 3 );
		}
	}

	// True if meta_key needs a shadow key generated.
	public function needs_shadow( $meta_key ) {
		foreach ( $this->actions as $action ) {
			if ( strpos( $meta_key, $action ) === 0 ) {
				foreach ( $this->objects as $object ) {
					if ( strpos( $meta_key, $object ) > 0 )
						return true;
				}
			}
		}
		return false;
	}

	// Return shadow meta_key when meta_key is requested.
	public function get_user_metadata( $meta_type = null, $user_id, $meta_key, $single ) {
		if ( $this->needs_shadow( $meta_key ) ) {
			$meta_key = $this->prefix . $meta_key;
			$result = get_user_meta( $user_id, $meta_key, $single );
			if ( $single )
				return array( $result );
			else
				return $result;
		} else {
			return null;
		}
	}

	// Update shadow meta_key when meta_key is sent.
	public function update_user_metadata( $meta_type = null, $user_id, $meta_key, $meta_value, $prev_value = '' ) {
		if ( $this->needs_shadow( $meta_key ) ) {
			$meta_key = $this->prefix . $meta_key;
			$result = update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' );
			return $result;
		} else {
			return null;
		}
	}
 
	// Change meta_key requests into shadow meta_key requests.
	public function get_user_option( $result, $option, $user ) {
		$option = $this->prefix . $option;
		$result = get_user_option( $option, $user->ID );
		return $result;
	}
} }


new UCC_Shadow_Screen_Options;

