<?php
/**
 * Plugin Name: WP Latest Posts Widget
 * Description: Display latest posts with thumbnail
 * Author: Nashita
 * Author URI: https://www.devnash.com
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: www
 * Domain Path: domain/path
 */

function wp_latest_posts_widget() {
	register_widget( 'WP_Latest_Posts_Widget' );
}
add_action( 'widgets_init', 'wp_latest_posts_widget' );

class WP_Latest_Posts_Widget extends WP_Widget {

	function __construct() {
		parent::__construct( 'wp-latest-posts-widget', __('Latest Posts', 'www'), array( 'description' => __( 'Display latest posts with thumbnail', 'www' ), ) );
	}

	public function widget( $args, $instance ) {
		wp_enqueue_style( 'wp-latest-posts-widget-style', plugins_url('style.css', __FILE__) );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$numberposts = $instance['numberposts'];
		echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		$numberposts = ($numberposts) ? $numberposts : 3;
		$q = array(
			'post_type' => 'post',
			'orderby' => 'date',
			'order' => 'DESC',
			'showposts' => $numberposts,
		);
		$query = new WP_Query($q);
		if($query->have_posts()) :
			echo '<div class="latest-posts-widget">';
			while($query->have_posts()) : $query->the_post();
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
				echo '<div class="latest-post">';
				echo '<div class="image"><a href="'.get_permalink().'" style="background-image:url('.$image[0].')"></a></div>';
				echo '<div class="content">';
				echo '<div class="title"><a href="'.get_permalink().'">'.get_the_title().'</a></div>';
				echo '<div class="excerpt">'.get_the_excerpt().'</div>';
				echo '</div>';
				echo '</div>';
			endwhile;
			echo '</div>';
		endif;
		wp_reset_query();

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'New title', 'www' );
		$numberposts = ( isset( $instance[ 'numberposts' ] ) ) ? $instance[ 'numberposts' ] : __( '3', 'www' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numberposts' ); ?>"><?php _e( 'Number of posts:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'numberposts' ); ?>" name="<?php echo $this->get_field_name( 'numberposts' ); ?>" type="text" value="<?php echo esc_attr( $numberposts ); ?>" />
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['numberposts'] = ( ! empty( $new_instance['numberposts'] ) ) ? strip_tags( $new_instance['numberposts'] ) : '';
		return $instance;
	}
}