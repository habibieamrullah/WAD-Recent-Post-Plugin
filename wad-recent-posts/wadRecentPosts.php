<?php
/*
Plugin Name: WAD Recent Posts
Plugin URI: https://webappdev.my.id/
Description: Recent posts list widget.
Version: 1.0.0
Author: WebAppDev
Author URI: https://webappdev.my.id/
License: GPL2
*/


// The widget class
class WAD_Recent_Posts extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'wad_recent_posts',
			__( 'WAD Recent Posts', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {	
		// Set widget defaults
		$defaults = array(
			'title' => '',
			'maxpost' => '2',
			'catid' => '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<div style="margin-bottom: 20px;">
			<div>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'text_domain' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</div>
			
			<?php // Max Post ?>
			<br>
			<div>
				<label for="<?php echo esc_attr( $this->get_field_id( 'maxpost' ) ); ?>"><?php _e( 'Maximum Post Count', 'text_domain' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'maxpost' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'maxpost' ) ); ?>" type="text" value="<?php echo esc_attr( $maxpost ); ?>" />
			</div>

			<?php 
			// Dropdown 
			?>
			<br>
			<div>
				<label for="<?php echo $this->get_field_id( 'catid' ); ?>"><?php _e( 'Select a Category', 'text_domain' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'catid' ); ?>" id="<?php echo $this->get_field_id( 'catid' ); ?>" class="widefat">
				
				<?php
				
				$categories = get_categories( array(
					'orderby' => 'name',
					'order'   => 'ASC'
				) );
				 
				foreach( $categories as $category ) {
					if(get_cat_ID($category->name) == esc_attr( $catid )){
						?>
						<option value="<?php echo get_cat_ID($category->name) ?>" selected><?php echo $category->name ?></option>
						<?php
					}else{
						?>
						<option value="<?php echo get_cat_ID($category->name) ?>"><?php echo $category->name ?></option>
						<?php
					}
				}
				?>
				</select>
			</div>
		</div>
		<?php
	}

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['maxpost']    = isset( $new_instance['maxpost'] ) ? wp_strip_all_tags( $new_instance['maxpost'] ) : '';
		$instance['catid']   = isset( $new_instance['catid'] ) ? wp_strip_all_tags( $new_instance['catid'] ) : '';
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );

		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$maxpost    = isset( $instance['maxpost'] ) ? apply_filters( 'widget_title', $instance['maxpost'] ) : '';
		$catid   = isset( $instance['catid'] ) ? apply_filters( 'widget_title', $instance['catid'] ) : '';
		

		// WordPress core before_widget hook (always include )
		echo $before_widget;

		// Display the widget

		// Display widget title if defined
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		?>
		
		<h1>Selected Cat: <?php echo $catid ?></h1>
		
		<ul>
			<?php
			$currentpost = 0;
			while (have_posts()) { 
				the_post(); 
				if($currentpost < $maxpost){
					?>
					<li>
						<?php
						
						echo "Wakwaaaak";
						
						//echo get_the_title() . " link: " . get_permalink() . " cat id " . get_cat_name(the_category_ID()) . " category link: " . get_category_link(the_category_ID()) . " post exerpt: " . get_the_excerpt() 
						?>
					</li>
					<?php 
				}
				$currentpost++;
			} // while() 
			?>
		</ul>
		<?php

		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}

}

// Register the widget
function register_wad_recent_posts() {
	register_widget( 'WAD_Recent_Posts' );
}

add_action( 'widgets_init', 'register_wad_recent_posts' );