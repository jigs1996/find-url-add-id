<?php
/**
 * Plugin Name: ID Generator
 * Author : Jignesh Sanghani
 * Description: You can add a new link to the admin, it will go, find the links add the ID.
 * Text Domain: id-generator
 */

function pluginprefix_function_to_run(){
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix.'sb_id_generator';

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name text NOT NULL,
      url text  NOT NULL,
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        dbDelta( $sql );

}

register_activation_hook( __FILE__, 'pluginprefix_function_to_run' );

function enqueue_scripts(){
    wp_enqueue_script( 'id_genrator_script',  plugins_url( 'js/script.js', __FILE__ ),array('jquery'));
}
add_action('admin_enqueue_scripts', 'enqueue_scripts');

/**
 * Plugin require file
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once('includes/Url_Name_List.php');

SP_Plugin::get_instance();

require('includes/Edit_Url_Name.php');

// require('includes/Add_url.php');

require('vendor/autoload.php');
use PHPHtmlParser\Dom;


/**
 * @function ajax_add_url_name
 * @description:   Add new url And Name  
 */
function ajax_add_url_name(){
    
    global $wpdb;

    $wpdb->insert( 
        $wpdb->prefix.'sb_id_generator',
        array(
            'url' => $_POST['url'],
            'name' => $_POST['name'],
            )
    );
    
    wp_redirect(admin_url('/').'admin.php?page=sb-id-generator');
    exit;
    
}
add_action( 'wp_ajax_add_url_name', 'ajax_add_url_name');


/**
 * @function save_edited_url_name
 * @description:   Save edited url And Name  
 */
function save_edited_url_name(){

    global $wpdb;
    
    $result = $wpdb->update( 
                $wpdb->prefix.'sb_id_generator', 
                array( 
                    'name' => $_POST['name'],
                    'url' => $_POST['url']
                ), 
                array( 'ID' => $_POST['id'] 
            ));

    wp_redirect(admin_url('/').'admin.php?page=sb-id-generator');
    exit;
}
add_action( 'wp_ajax_save_edited_url_name', 'save_edited_url_name');



/**
 * @function filter_the_content_in_the_main_loop
 * @description:   Get url from href and add id as a product name  
 */

function filter_the_content_in_the_main_loop( $content ) {

    global $wpdb;

    if ( (is_single() && in_the_loop() && is_main_query()) || is_page() ) {
        $dom = new Dom;
        $dom->load($content);
        $a = $dom->find('a');
        $table_name = $wpdb->prefix.'sb_id_generator';

        foreach($a as $link){
            $name = $wpdb->get_results( "SELECT name FROM $table_name WHERE url='".$link->href."'" );
            // print_r($name[0]->name);die;
            if(!empty($name)){
                $tag = $link->getTag();
                $tag->setAttribute('id', $name[0]->name);
            }
        }
        return $dom;
    }

    return $content;
}

add_filter( 'the_content', 'filter_the_content_in_the_main_loop' );