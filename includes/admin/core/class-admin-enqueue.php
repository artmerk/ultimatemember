<?php
namespace um\admin\core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Admin_Enqueue' ) ) {
    class Admin_Enqueue {
        var $js_url;
        var $css_url;

        function __construct() {
            $this->slug = 'ultimatemember';

            $this->js_url = um_url . 'includes/admin/assets/js/';
            $this->css_url = um_url . 'includes/admin/assets/css/';

            add_action('admin_head', array(&$this, 'admin_head'), 9);

            add_action('admin_enqueue_scripts',  array(&$this, 'admin_enqueue_scripts') );

            add_filter( 'admin_body_class', array( &$this, 'admin_body_class' ), 999 );

            add_filter('enter_title_here', array(&$this, 'enter_title_here') );

            add_action( 'load-user-new.php', array( &$this, 'enqueue_role_wrapper' ) );
            add_action( 'load-user-edit.php', array( &$this, 'enqueue_role_wrapper' ) );
        }


        function enqueue_role_wrapper() {
            add_action( 'admin_enqueue_scripts',  array( &$this, 'load_role_wrapper' ) );
        }


        /**
         * Load js for Add/Edit User form
         */
        function load_role_wrapper() {

            wp_register_script( 'um_admin_role_wrapper', $this->js_url . 'um-admin-role-wrapper.js', '', '', true );
            wp_enqueue_script( 'um_admin_role_wrapper' );

            $localize_data = get_option( 'um_roles' );

            wp_localize_script( 'um_admin_settings', 'um_roles', $localize_data );

        }


        /***
         ***	@enter title placeholder
         ***/
        function enter_title_here( $title ){
            $screen = get_current_screen();
            if ( 'um_directory' == $screen->post_type ){
                $title = 'e.g. Member Directory';
            }
            if ( 'um_role' == $screen->post_type ){
                $title = 'e.g. Community Member';
            }
            if ( 'um_form' == $screen->post_type ){
                $title = 'e.g. New Registration Form';
            }
            return $title;
        }

        /***
         ***	@Runs on admin head
         ***/
        function admin_head(){

            if ( $this->is_plugin_post_type() ){

                ?>

                <style type="text/css">
                    .um-admin.post-type-<?php echo get_post_type(); ?> div#slugdiv,
                    .um-admin.post-type-<?php echo get_post_type(); ?> div#minor-publishing,
                    .um-admin.post-type-<?php echo get_post_type(); ?> div#screen-meta-links
                    {display:none}
                </style>

                <?php
            }

        }


        /***
         ***	@check that we're on a custom post type supported by UM
         ***/
        function is_plugin_post_type(){
            if (isset($_REQUEST['post_type'])){
                $post_type = $_REQUEST['post_type'];
                if ( in_array($post_type, array('um_form','um_role','um_directory'))){
                    return true;
                }
            } else if ( isset($_REQUEST['action'] ) && $_REQUEST['action'] == 'edit') {
                $post_type = get_post_type();
                if ( in_array($post_type, array('um_form','um_role','um_directory'))){
                    return true;
                }
            }
            return false;
        }


        /***
         ***	@Load Form
         ***/
        function load_form() {

            wp_register_style( 'um_admin_form', $this->css_url . 'um-admin-form.css' );
            wp_enqueue_style( 'um_admin_form' );

            wp_register_script( 'um_admin_form', $this->js_url . 'um-admin-form.js', '', '', true );
            wp_enqueue_script( 'um_admin_form' );

        }


        /***
         ***	@Load Form
         ***/
        function load_forms() {

            wp_register_style( 'um_admin_forms', $this->css_url . 'um-admin-forms.css' );
            wp_enqueue_style( 'um_admin_forms' );

            wp_register_script( 'um_admin_forms', $this->js_url . 'um-admin-forms.js', '', '', true );
            wp_enqueue_script( 'um_admin_forms' );

            $localize_data = array(
                'texts' => array(
                    'remove' => __( 'Remove', 'ultimate-member' ),
                    'select' => __( 'Select', 'ultimate-member' )
                )
            );

            wp_localize_script( 'um_admin_forms', 'php_data', $localize_data );

        }


        /***
         ***	@Load dashboard
         ***/
        function load_dashboard() {

            wp_register_style( 'um_admin_dashboard', $this->css_url . 'um-admin-dashboard.css' );
            wp_enqueue_style( 'um_admin_dashboard' );

            wp_register_script( 'um_admin_dashboard', $this->js_url . 'um-admin-dashboard.js', '', '', true );
            wp_enqueue_script( 'um_admin_dashboard' );

        }


        /***
         ***	@Load settings
         ***/
        function load_settings() {

            wp_register_style( 'um_admin_settings', $this->css_url . 'um-admin-settings.css' );
            wp_enqueue_style( 'um_admin_settings' );

            wp_register_script( 'um_admin_settings', $this->js_url . 'um-admin-settings.js', '', '', true );
            wp_enqueue_script( 'um_admin_settings' );

            $localize_data = array(
                'onbeforeunload_text' => __( 'Are sure, maybe some settings not saved', 'ultimate-member' ),
                'texts' => array(
                    'remove' => __( 'Remove', 'ultimate-member' ),
                    'select' => __( 'Select', 'ultimate-member' )
                )
            );

            wp_localize_script( 'um_admin_settings', 'php_data', $localize_data );

        }


        /***
         ***	@Load modal
         ***/
        function load_modal() {

            wp_register_style( 'um_admin_modal', $this->css_url . 'um-admin-modal.css' );
            wp_enqueue_style( 'um_admin_modal' );

            wp_register_script( 'um_admin_modal', $this->js_url . 'um-admin-modal.js', '', '', true );
            wp_enqueue_script( 'um_admin_modal' );

            $localize_data = array(
                'ajax_url' => UM()->get_ajax_route( 'um\admin\core\Admin_Builder', 'dynamic_modal_content' ),
                'dropdown_ajax_url' => UM()->get_ajax_route( 'um\admin\core\Admin_Builder', 'populate_dropdown_options' ),
            );
            wp_localize_script( 'um_admin_modal', 'um_admin_modal_data', $localize_data );

        }


        /***
         ***	@Field Processing
         ***/
        function load_field() {

            wp_register_script( 'um_admin_field', $this->js_url . 'um-admin-field.js', '', '', true );
            wp_enqueue_script( 'um_admin_field' );

            $localize_data = array(
                'ajax_url' => UM()->get_ajax_route( 'um\admin\core\Admin_Builder', 'update_field' ),
                'do_ajax_url' => UM()->get_ajax_route( 'um\core\Fields', 'do_ajax_action' ),
            );
            wp_localize_script( 'um_admin_field', 'um_admin_field_data', $localize_data );

        }


        /***
         ***	@Load Builder
         ***/
        function load_builder() {

            wp_register_script( 'um_admin_builder', $this->js_url . 'um-admin-builder.js', '', '', true );
            wp_enqueue_script( 'um_admin_builder' );

            $localize_data = array(
                'ajax_url' => UM()->get_ajax_route( 'um\admin\core\Admin_Builder', 'update_builder' ),
            );
            wp_localize_script( 'um_admin_builder', 'um_admin_builder_data', $localize_data );

            wp_register_script( 'um_admin_dragdrop', $this->js_url . 'um-admin-dragdrop.js', '', '', true );
            wp_enqueue_script( 'um_admin_dragdrop' );


            $localize_data = array(
                'ajax_url' => UM()->get_ajax_route( 'um\admin\core\Admin_DragDrop', 'update_order' ),
            );
            wp_localize_script( 'um_admin_dragdrop', 'um_admin_dragdrop_data', $localize_data );


            wp_register_style( 'um_admin_builder', $this->css_url . 'um-admin-builder.css' );
            wp_enqueue_style( 'um_admin_builder' );

        }


        /***
         ***	@Load core WP styles/scripts
         ***/
        function load_core_wp() {

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );

            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'jquery-ui-sortable' );

            wp_enqueue_script( 'jquery-ui-tooltip' );

        }


        /***
         ***	@Load Admin Styles
         ***/
        function load_css() {

            wp_register_style( 'um_admin_menu', $this->css_url . 'um-admin-menu.css' );
            wp_enqueue_style( 'um_admin_menu' );

            wp_register_style( 'um_admin_columns', $this->css_url . 'um-admin-columns.css' );
            wp_enqueue_style( 'um_admin_columns' );

            wp_register_style( 'um_admin_misc', $this->css_url . 'um-admin-misc.css' );
            wp_enqueue_style( 'um_admin_misc' );

            if ( get_post_type() != 'shop_order' ) {
                wp_register_style( 'um_admin_select2', $this->css_url . 'um-admin-select2.css' );
                wp_enqueue_style( 'um_admin_select2' );
            }

        }


        /***
         ***	@Load functions js
         ***/
        function load_functions() {

            wp_register_script('um_functions', um_url . 'assets/js/um-functions' . '.js' );
            wp_enqueue_script('um_functions');

        }


        /***
         ***	@Load Fonticons
         ***/
        function load_fonticons() {

            wp_register_style('um_fonticons_ii', um_url . 'assets/css/um-fonticons-ii.css' );
            wp_enqueue_style('um_fonticons_ii');

            wp_register_style('um_fonticons_fa', um_url . 'assets/css/um-fonticons-fa.css' );
            wp_enqueue_style('um_fonticons_fa');

        }


        /***
         ***	@Load global css
         ***/
        function load_global_css() {

            wp_register_style( 'um_admin_global', $this->css_url . 'um-admin-global.css' );
            wp_enqueue_style( 'um_admin_global' );

        }


        /***
         ***	@Load jQuery custom code
         ***/
        function load_custom_scripts() {

            wp_register_script( 'um_admin_scripts', $this->js_url . 'um-admin-scripts.js', '', '', true );
            wp_enqueue_script( 'um_admin_scripts' );

        }


        /***
         ***	@Load AJAX
         ***/
        function load_ajax_js() {

            wp_register_script( 'um_admin_ajax', $this->js_url . 'um-admin-ajax.js', '', '', true );
            wp_enqueue_script( 'um_admin_ajax' );

            $localize_data = array(
                'ajax_url' => UM()->get_ajax_route( 'um\core\Fields', 'do_ajax_action' ),
            );
            wp_localize_script( 'um_admin_ajax', 'um_admin_ajax_data', $localize_data );

        }


        /***
         ***	@Boolean check if we're viewing UM backend
         ***/
        function is_UM_admin() {
            global $current_screen;

            $screen_id = $current_screen->id;
            if ( strstr( $screen_id, 'ultimatemember' ) || strstr( $screen_id, 'um_' ) || strstr( $screen_id, 'user' ) || strstr( $screen_id, 'profile' ) || $screen_id == 'nav-menus' ) return true;

            global $post;
            if ( isset( $post->post_type ) ) return true;

            global $tax;
            if ( isset( $tax->name ) ) return true;

            return false;
        }

        /***
         ***	@Adds class to our admin pages
         ***/
        function admin_body_class($classes){
            if ( $this->is_UM_admin() ) {
                return "$classes um-admin";
            }
            return $classes;
        }

        /***
         ***	@Enqueue scripts and styles
         ***/
        function admin_enqueue_scripts() {
            if ( $this->is_UM_admin() ) {

                /*if ( get_post_type() != 'shop_order' ) {
                    UM()->enqueue()->wp_enqueue_scripts();
                }*/

                $this->load_functions();
                $this->load_global_css();
                $this->load_form();
                $this->load_forms();
                $this->load_modal();
                $this->load_dashboard();
                $this->load_settings();
                $this->load_field();
                $this->load_builder();
                $this->load_css();
                $this->load_core_wp();
                $this->load_ajax_js();
                $this->load_custom_scripts();
                $this->load_fonticons();

                if ( is_rtl() ) {
                    wp_register_style( 'um_admin_rtl', $this->css_url . 'um-admin-rtl.css' );
                    wp_enqueue_style( 'um_admin_rtl' );
                }

            } else {

                $this->load_global_css();

            }

        }

    }
}