<?php
/**
* Plugin Name: Orbit Batch Process
* Plugin URI:
* Description: Batch processing
* Version: 1.0.0
* Author: Sputznik
*/
?>
<?php

if( !class_exists( 'ORBIT_BATCH_PROCESS' ) ):

	class ORBIT_BATCH_PROCESS{

		var $shortcode;

		private static $instance = null;

		function __construct(){

			$this->shortcode = 'orbit_batch_process';

			/* CREATE SHORTCODE */
			add_shortcode( $this->shortcode, array( $this, 'shortcodeFn' ) );

			/* SAMPLE ACTION HOOK FOR AJAX CALL */
			add_action('orbit_batch_action_default', function(){

				$users = array( 'Samuel', 'Jay', 'Dennis', 4, 5, 6, 7, 8, 9, 10 );

				echo $users[ $_GET['orbit_batch_step'] - 1 ];

				// echo "AJAX ".$_GET['orbit_batch_step']." ".$_GET['orbit_batch_action'];

			} );


			/** ENQUEUE ASSETS TO LOAD THE ASSETS - SCRIPTS AND STYLES */
			add_action('the_posts', array( $this, 'assets') );

			/* AJAX CALLBACK */
			add_action( 'wp_ajax_'.$this->shortcode, array( $this, 'ajax' ) );

		}

		// SINGLETON DESIGN PATTERN - NEEDS TO BE IMPLEMENTED IN EACH CHILD
		public static function getInstance(){
			if( self::$instance == null ){ self::$instance = array(); }

			$class = get_called_class();
			if( !isset( self::$instance[ $class ] ) ){
				// new $class() will work too
				self::$instance[ $class ] = new static();
			}
			return self::$instance[ $class ];
		}

		/* SHORTCODE FUNCTION */
		function shortcodeFn( $atts ){

			/* CREATE ATTS ARRAY FROM DEFAULT PARAMETERS IN THE SHORTCODE */
			$atts = shortcode_atts( array(
				'title'					=> 'Title of the process',
				'desc'					=> 'Description of the process',
				'batches' 			=> '10',
				'btn_text' 			=> 'Process Request',
				'batch_action' 	=> 'default',
				'params'				=> array()
			), $atts, $this->shortcode );

			$url = admin_url( 'admin-ajax.php' ).'?action='.$this->shortcode;
			ob_start();
			include "templates/shortcode.php";
			return ob_get_clean();
		}

		/* AJAX CALLBACK */
		function ajax(){
			if( isset( $_POST['orbit_batch_action'] ) ){
				do_action( 'orbit_batch_action_'.$_POST['orbit_batch_action'] );
			}
			wp_die();
		}

		/* CHECK IF SHORTCODE EXISTS IN THE POSTS OR PAGES */
		function has_shortcode( $posts ){
			$found = false;
			if ( !empty($posts) ){
				foreach ($posts as $post) {
					if ( has_shortcode( $post->post_content, $this->shortcode ) ){
						$found = true;
						break;
					}
				}
			}
			return $found;
		}

		// LOAD ASSETS FOR POSTS THAT HAVE THE SHORTCODE
		function assets( $posts ){
			if( $this->has_shortcode( $posts ) ){
				$this->enqueue();
			}
			return $posts;
		}

		function enqueue(){
			wp_enqueue_style( 'orbit-batch', plugin_dir_url( __FILE__ ).'css/style.css', array(), "1.0.4" );
			wp_enqueue_script( 'orbit-batch-script', plugin_dir_url( __FILE__ ).'js/main.js', array( 'jquery' ), '1.0.1', true );
		}

	}

	ORBIT_BATCH_PROCESS::getInstance();

endif;
