<?php

/*
 *	Plugin Name: Official Treehouse Badges Plugin
 *	Plugin URI: http://wptreehouse.com/wptreehouse-badges-plugin/
 *	Description: Provides both widgets and shortcodes to help you display your Treehouse profile badges on your website.  The official Treehouse badges plugin.
 *	Version: 1.0
 *	Author: Zac Gordon
 *	Author URI: http://wp.zacgordon.com
 *	License: GPL2
 *
*/


/*
 *	Assign global variables
 *
*/
$plugin_url = WP_PLUIGIN_URL . '/wptreehouse-badges' ;
$options = array();

/*
 *	Add a link to our plugin in the admin menu
 *	under 'Settings > Treehouse Badges'
 *
*/

function wptreehouse_badges_menu() {
    /*
	 * 	Use the add_options_page function
	 * 	add_options_page( $page_title, $menu_title, $capability, $menu-slug, $function ) 
	 *
	*/
    add_options_page(
        'Official Treehouse Badges Plugin',
        'Treehouse Badges',
        'manage_options',
        'wptreehouse-badges',
        'wptreehouse_badges_options_page'
    );
}
add_action( 'admin_menu', 'wptreehouse_badges_menu');


function wptreehouse_badges_options_page() {

    if( !current_user_can( 'manage_options' ) ) {

		wp_die( 'You do not have sufficient permission to access this page.' );

	}
    global $plugin_url; //we make this var global to make it available to reference in other files in same directory
    global $options;
    if(isset($_POST['wptreehouse_form_submitted'])) {
        $hidden_field = esc_html ( $_POST['wptreehouse_form_submitted'] );
        if ( $hidden_field == 'Y' ) {
            $wptreehouse_username = esc_html( $_POST['wptreehouse_username'] );
            $wptreehouse_profile = wptreehouse_badges_get_profile($wptreehouse_username);

            $options['wptreehouse_username']       = $wptreehouse_username; //C of CRUD
            $options['wptreehouse_profile']        = $wptreehouse_profile;
            $options['last_updated']               = time();

            update_option( 'wptreehouse_badges', $options );//U of CRUD
        }
    }
    $options = get_option( 'wptreehouse_badges' ); //R of CRUD
    if ($options != '') {
        $wptreehouse_username = $options['wptreehouse_username']; //this would keep the user input after logging in
        $wptreehouse_profile = $options['wptreehouse_profile'];
    }
    var_dump($wptreehouse_profile);
    require('inc/options-page-wrapper.php');
    
}

function wptreehouse_badges_get_profile($wptreehouse_username) {
    $json_feed_url = 'http://teamtreehouse.com/' . $wptreehouse_username . '.json';
    $args = array( 'timeout' => 120 ); //we settimeout to give enough time for all data to load
    $json_feed = wp_remote_get($json_feed_url, $args);
    $wptreeouse_profile = json_decode($json_feed['body']); //decode will translate meaningless numbers into understandable values, without adding body, we will get way more than json
    return $wptreehouse_profile; 
}

function wptreehouse_badges_styles() {
    wp_enqueue_style( 'wptreehouse_badges_style', plugins_url( 'wptreehouse-badges/wptreehouse-badges.css' ) );
}
add_action( 'admin_head' , 'wptreehouse_badges_styles')

?>