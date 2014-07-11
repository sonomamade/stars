<?php
/**
 * Plugin Name: Sonoma Stars
 * Plugin URI: http://sonoma.io/plugins/stars
 * Description: Five-star ratings.
 * Version: 0.0.0
 * Author: Sonoma
 * Author URI: http://sonoma.io
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( !class_exists( 'SonomaStars' ) ) {

    register_activation_hook( __FILE__, array( 'SonomaStars', 'activation_hook' ) );

    class SonomaStars {
		public static $slug 	= 'sonoma-stars';
		public static $slug_	= 'sonoma_stars';
	
		public function __construct() {
			add_filter( 'comment_form_defaults', array( $this, 'comment_form_defaults' ) );
			
			add_action( 'comment_post', array( $this, 'add_comment_rating' ), 1 );
			
			// Tag actions
			add_action( 'the_average_rating', array( $this, 'rating_action' ), 1 );
			add_action( 'the_rating_form', array( $this, 'rating_form' ), 1 );
			
			// Stylesheets and Scripts
			wp_enqueue_style( 'font-awesome-css', plugins_url( '/css/font-awesome.css', __FILE__ ) );
			wp_enqueue_style( 'sonoma-stars', plugins_url( '/css/sonoma-stars.css', __FILE__ ) );
			
			wp_enqueue_script( self::$slug_, plugin_dir_url( __FILE__ ) . 'js/sonoma-stars.js', array( 'jquery' ) );
		}
	
		public static function activation_hook() {
			
		}
		
		public static function log( $message = '' ) {
			error_log( $message . "\n", 3, plugin_dir_path( __FILE__ ) . 'error.log' );
		}
		
		public static function rating_action() {
			return self::rating( get_the_ID() );
		}
		
		public static function rating( $post_id = false ) {
			if ( $post_id === false ) {
				$post_id = get_the_ID();
			}
			
			$rating = round( 2 * self::average_rating( $post_id ) ) / 2;
			$count	= self::rating_count( $post_id );
			$html	= "";
			
			self::log( "Rating: " . $rating . "; Post: ", $post_id );
			
			for ( $i = 0; $i < 5; $i++ ) {
				if ( ( $rating - $i ) > 0.5 ) {
					$html .= "<i class=\"fa fa-star\"></i>";
				}
				else if ( ( $rating - $i ) > 0 ) {
					$html .= "<i class=\"fa fa-star-half-full\"></i>";
				}
				else {
					$html .= "<i class=\"fa fa-star-o\"></i>";
				}
			}
			
			$html = "<span class=\"sonoma-stars rating\" title=\"{$rating} of 5\"><span class=\"stars\">{$html}</span> <span class=\"count\">($count)</span> <span class=\"links\"><a href=\"#comments\">Read reviews</a> | <a href=\"#respond\">Write review</a></span></span>";
			
			echo $html;
		}
		
		public static function rating_count( $post_id ) {
			global $wpdb;
				
			if ( false === ( $count = get_transient( 'sonoma_rating_count_' . $post_id ) ) ) {

				$count = $wpdb->get_var( $wpdb->prepare("
					SELECT COUNT(meta_value) FROM $wpdb->commentmeta
					LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
					WHERE meta_key = 'rating'
					AND comment_post_ID = %d
					AND comment_approved = '1'
					AND meta_value > 0
				", $post_id ) );

				set_transient( 'sonoma_rating_count_' . $post_id, $count, YEAR_IN_SECONDS );
			}

			return $count;
		}
		
		public static function average_rating( $post_id ) {
			global $wpdb;
				
			if ( false === ( $average_rating = get_transient( 'sonoma_average_rating_' . $post_id ) ) ) {

				$average_rating = '';
				$count          = self::rating_count( $post_id );

				if ( $count > 0 ) {

					$ratings = $wpdb->get_var( $wpdb->prepare("
						SELECT SUM(meta_value) FROM $wpdb->commentmeta
						LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
						WHERE meta_key = 'rating'
						AND comment_post_ID = %d
						AND comment_approved = '1'
						AND meta_value > 0
					", $post_id ) );

					$average_rating = number_format( $ratings / $count, 2 );

				}

				set_transient( 'sonoma_average_rating_' . $post_id, $average_rating, YEAR_IN_SECONDS );
			}

			return $average_rating;
		}
		
		public function comment_form_defaults( $args ) {
			$args['comment_field'] = '<p class="sonoma comment-form-rating" data-bind="sonoma-stars-input"><label for="rating">' . __( 'Your Rating', 'sonoma-stars' ) .'</label><select name="rating" id="rating">
							<option value="">'  . __( 'Rate&hellip;', 'sonoma-stars' ) . '</option>
							<option value="5">' . __( 'Perfect', 'sonoma-stars' ) . '</option>
							<option value="4">' . __( 'Good', 'sonoma-stars' ) . '</option>
							<option value="3">' . __( 'Average', 'sonoma-stars' ) . '</option>
							<option value="2">' . __( 'Not that bad', 'sonoma-stars' ) . '</option>
							<option value="1">' . __( 'Very Poor', 'sonoma-stars' ) . '</option>
						</select></p>' . $args['comment_field'];
			
			return $args;
		}
		
		public function add_comment_rating( $comment_id ) {
			if ( isset( $_POST['rating'] ) ) {
				$comment = get_comment( $comment_id );

				if ( !$_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 1 ) {
					return;
				}

				add_comment_meta( $comment_id, 'rating', (int) esc_attr( $_POST['rating'] ), true );
				
				if ( !empty( $comment->comment_post_ID ) ) {
					$post_id = $comment->comment_post_ID;
					
					delete_transient( 'sonoma_average_rating_' . absint( $post_id ) );
					delete_transient( 'sonoma_rating_count_' . absint( $post_id ) );
				}
			}
		}
		
		public static function rating_form() {
			echo "Rating Input!";
		}
    }

	new SonomaStars();
}