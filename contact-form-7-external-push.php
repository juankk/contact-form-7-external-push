<?php
/*
Plugin Name: Contact Form 7 APIS Integration
Description: Allows to create configuration to push forms to exteranl marketing tools. By default it uses marketo.
Author: Juan Zapata
Author URI: http://juankk.github.io/
Text Domain: contact-form-7-push-apis
Version: 1.0
*/

add_action('plugins_loaded', 'contact_form_7_confirm_email', 10);

function contact_form_7_confirm_email() {
	global $pagenow;
	if ( ! function_exists( 'wpcf7_add_shortcode' ) ) {
		if ( $pagenow != 'plugins.php' ) { return; }
		add_action( 'admin_notices', 'cfconfirm_emailfieldserror' );
		wp_enqueue_script( 'thickbox' );
		function cfconfirm_emailfieldserror() {
			$out = '<div class="error" id="messages"><p>';
			$out .= 'The Contact Form 7 plugin must be installed and activated for Contact form 7 external push.';
			$out .= '</p></div>';
			echo $out;
		}
	} else {
    include_once( 'bootstrap.php' );
  }
}



//$apis_available = get_option( 'contact-form-7-external-push-apis' );
//loading admin configuration
