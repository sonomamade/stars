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
		public static function activation_hook() {
			
		}
		
		public static function rating() {
			echo '<span class="sonoma-stars rating"><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-full"></i></span>';
		}
    }

	new SonomaStars();
}