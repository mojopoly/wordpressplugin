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
$display_json = true;

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
    global $display_json;
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
    //var_dump($wptreehouse_username);
    require('inc/options-page-wrapper.php');
    
}

class Wptreehouse_Badges_Widgets extends WP_Widget { //copied skeleton from wp widget codex

	function wptreehouse_badges_widgets() {
		// Instantiate the parent object
		parent::__construct( false, 'Official Treehouse Badges Widget' );
	}

	function widget( $args, $instance ) {
        // Widget output
        
        extract( $args ); //we need to include this in our code in order for it to work
        $title = apply_filters( 'widget_title', $instance['title'] );
        $num_badges = $instance['num_badges'];
        $display_tooltip = $instance['display_tooltip'];
        $options = get_option( 'wptreehouse_badges' );
        $wptreehouse_profile = $options['wptreehouse_profile'];

        require ( 'inc/front-end.php' );
	}

	function update( $new_instance, $old_instance ) {
        // Save widget options
        
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']); //strip tags will not display any html if it was inputted in the input box
        $instance['num_badges'] = strip_tags($new_instance['num_badges']); 
        $instance['display_tooltip'] = strip_tags($new_instance['display_tooltip']); 
        return $instance;
	}

	function form( $instance ) {
        // Output admin widget options form
        $title = esc_attr( $instance['title']);
        $num_badges = esc_attr( $instance['num_badges']);
        $display_tooltip = esc_attr( $instance['display_tooltip']);

        $options = get_option( 'wptreehouse_badges' );
        $wptreehouse_profile = $options['wptreehouse_profile'];
        require ( 'inc/widget-fields.php' );
	}
}

function wptreehouse_badges_register_widgets() {
	register_widget( 'Wptreehouse_Badges_Widgets' );
} 

add_action( 'widgets_init', 'wptreehouse_badges_register_widgets' ); //end widget

//begin  shortcode
function wptreehouse_badges_shortcode($atts, $content = null) {
    global $post; //define post object to get info about post in which the shortcode appears

    extract( shortcode_atts( array(
        'num_badges' => '8',
        'tooltip' => 'on'
    ), $atts) );
    if ( $tooltip == 'on' ) $tooltip = 1;
    if ( $tooltip == 'off' ) $tooltip = 0;

    $display_tooltip = $tooltip;
    $options = get_option( 'wptreehouse_badges' );
    $wptreehouse_profile = $options['wptreehouse_profile'];

    //below process is called buffering and needed for shortcodes; it holds the require function below until all above info has been run and then runs the require function below
    ob_start();
    require ( 'inc/front-end.php' );
    $content = ob_get_clean();
    return $content;
}

add_shortcode('wptreehouse_badges', 'wptreehouse_badges_shortcode'); //shortcode end, we used a plugin to show/hide widgets on specific pages

//function to fetch json data of inputted username 
function wptreehouse_badges_get_profile($wptreehouse_username) {
    $json_feed_url = 'http://teamtreehouse.com/' . $wptreehouse_username . '.json';
    $args = array( 'timeout' => 120 ); //we settimeout to give enough time for all data to load
    $json_feed = wp_remote_get($json_feed_url, $args);
    $wptreehouse_profile = json_decode($json_feed['body']); //decode will translate meaningless numbers into understandable values, without adding body, we will get way more than json
    return $wptreehouse_profile; 
}


//function to make the plugin update automatic instead of user pressing update button using server side, which is lenghty/ refresh the profile by Adding AJAX To Plugins on the Front-End
function wptreehouse_badges_refresh_profile() {
    $options = get_option( 'wptreehouse_badges' );
    $last_updated = $options['last_updated'];
    $current_time = time();
    $update_difference = $current_time - $last_updated;

    if ( $update_difference > 86400 ) { //86400 is number of seconds in a day
        $wptreehouse_username = $options['wptreehouse_username'];
        $options['wptreehouse_profile'] = wptreehouse_badges_get_profile($wptreehouse_username);
        $options['last_updated'] = time();

        update_option( 'wptreehouse_badges', $options );
    }
    die(); //this will let ajax call know that function has ended
}
add_action('wp_ajax_wptreehouse_badges_refresh_profile', 'wptreehouse_badges_refresh_profile'); //create a custom hook for the ajax function in order to make it accessable for the front end to make ajax calls directly to the plugin


function wptreehouse_badges_enable_frontend_ajax() { ?>
    <script>
       var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
<?php } 
add_action( 'wp_head', 'wptreehouse_badges_enable_frontend_ajax' );

?>
<?php
function wptreehouse_badges_backend_styles() {
    wp_enqueue_style( 'wptreehouse_badges_backend_css', plugins_url( 'wptreehouse-badges/wptreehouse-badges.css' ) );
}
add_action( 'admin_head' , 'wptreehouse_badges_backend_styles');


function wptreehouse_badges_frontend_scripts_and_styles() {
    wp_enqueue_style( 'wptreehouse_badges_backend_css', plugins_url( 'wptreehouse-badges/wptreehouse-badges.css' ) );
    wp_enqueue_script( 'wptreehouse_badges_frontend_css', plugins_url( 'wptreehouse-badges/wptreehouse-badges.js' ), array('jquery'), '', true );

}
add_action( 'wp_enqueue_scripts' , 'wptreehouse_badges_frontend_scripts_and_styles');



?>