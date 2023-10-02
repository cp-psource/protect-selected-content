<?php
/*
Plugin Name: Inhalts-Passwortschutz
Plugin URI: https://n3rds.work/piestingtal_source/inhalt-passwortschutz-plugin/
Description: Ermöglicht es Dir, ausgewählte Inhalte innerhalb eines Beitrags oder einer Seite mit einem Passwort zu schützen, während der Rest der Inhalte öffentlich bleibt.
Author: WMS N@W
Version: 1.2.4
Author URI: http://n3rds.work/
Textdomain: psc
*/

/* 
Copyright 2014-2021 WMS N@W (https://n3rds.work)
Author - DerN3rd

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class PartialPostPassword {

	function __construct() {
  
	  // Shortcodes
	  add_shortcode( 'protect', array(&$this, 'shortcode') );
	  
	  // Localize the plugin
		add_action( 'plugins_loaded', array(&$this, 'localization') );
  
		  // Handle cookie
		  add_action( 'wp_ajax_nopriv_psc-set', array(&$this, 'set_password') );
	  add_action( 'wp_ajax_psc-set', array(&$this, 'set_password') );
  
	  // Add Shortcode Button
	  if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) {
		add_filter('mce_external_plugins', array(&$this, 'add_shortcode_button_plugin'));
		add_filter('mce_buttons', array(&$this, 'register_shortcode_button'));
	  }
	}
  
	function localization() {
	  // Load up the localization file if we're using WordPress in a different language
		// Place it in this plugin's "languages" folder and name it "psc-[value in wp-config].mo"
		load_plugin_textdomain('psc', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
  
	function shortcode( $atts, $content = null ) {
	  extract( shortcode_atts( array(
		'password' => false
		), $atts ) );
  
		  // Skip check for no content
	  if ( is_null( $content ) )
		return;
  
		  // If no password set, don't protect
		  if ( !$password )
		  return do_shortcode( $content );
		  
		  // Check cookie for password
		  if ( isset( $_COOKIE['psc-postpass_' . COOKIEHASH] ) && $_COOKIE['psc-postpass_' . COOKIEHASH] == sha1( $password ) ) {
			 return do_shortcode( $content );
		  } else {
			$label = 'pwbox-' . rand();
			  return '<form action="' . admin_url('admin-ajax.php') . '" method="post"><input type="hidden" name="action" value="psc-set" />
			  <p>' . __("Dieser Inhalt ist passwortgeschützt. Um ihn anzuzeigen, gib bitte unten dein Passwort ein:", 'psc') . '</p>
			  <p><label for="' . $label . '">' . __("Passwort:", 'psc') . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr__("Entsperren", 'psc') . '" /></p>
			  </form>
			  ';
		  }
		}
  
	  function register_shortcode_button($buttons) {
		  array_push($buttons, "separator", "protect");
		  return $buttons;
	  }
	  
	  function add_shortcode_button_plugin($plugin_array) {
		  $plugin_array['protect'] = plugins_url('shortcode-button.js', __FILE__);
		  return $plugin_array;
	  }
  
	  function set_password() {
	  
		if ( function_exists( 'stripslashes_deep' ) ) {
			$_POST = stripslashes_deep( $_POST );
		}
	  
		// Set cookie for 10 days
	  setcookie( 'psc-postpass_' . COOKIEHASH, sha1( $_POST['post_password'] ), time() + 864000, COOKIEPATH );
	  
		  // Jump back to post
		  wp_safe_redirect( wp_get_referer() );
		  exit;
	  }
	  
  } //end class
  
function initialize_partial_post_password() {
    $psc = new PartialPostPassword();
}

add_action('init', 'initialize_partial_post_password');


