<?php
/*
 * Plugin Name: WAD Recent Posts
 * Description: Recent posts list widget.
 * Version: 1.0.4
 * Author: Habibie
 * Author URI: https://webappdev.my.id/
 * Plugin URI: https://creativeshop.ciihuy.com/product/wordpress-recent-post-widget-plugin/
 * License: GPL2
 * Requires at least: 2.9
 * Requires PHP: 5.2
 * Text Domain: wad-recent-posts
 * Domain Path: /languages
 */


// The widget class
class WAD_Recent_Posts extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'wad_recent_posts',
			__( 'WAD Recent Posts', 'wad-recent-posts' ),
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
			'accentcolor' => '#9a0000',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<div style="margin-bottom: 20px;">
			<div>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'wad-recent-posts' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</div>
			
			<?php // Max Post ?>
			<br>
			<div>
				<label for="<?php echo esc_attr( $this->get_field_id( 'maxpost' ) ); ?>"><?php _e( 'Maximum Post Count', 'wad-recent-posts' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'maxpost' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'maxpost' ) ); ?>" type="number" value="<?php echo esc_attr( $maxpost ); ?>" />
			</div>

			<?php 
			// Dropdown 
			?>
			<br>
			<div>
				<label for="<?php echo $this->get_field_id( 'catid' ); ?>"><?php _e( 'Select a Category', 'wad-recent-posts' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'catid' ); ?>" id="<?php echo $this->get_field_id( 'catid' ); ?>" class="widefat">
				
				<option value="" selected><?php _e( 'All Categories', 'wad-recent-posts' ); ?></option>
				
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
			
			<br>
			<?php // Accent Color ?>
			<br>
			<div>
				<label for="<?php echo esc_attr( $this->get_field_id( 'accentcolor' ) ); ?>"><?php _e( 'Accent Color (color hex code)', 'wad-recent-posts' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'accentcolor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'accentcolor' ) ); ?>" type="text" value="<?php echo esc_attr( $accentcolor ); ?>" />
			</div>
		</div>
		<?php
	}

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['maxpost']    = isset( $new_instance['maxpost'] ) ? wp_strip_all_tags( $new_instance['maxpost'] ) : '';
		$instance['accentcolor']    = isset( $new_instance['accentcolor'] ) ? wp_strip_all_tags( $new_instance['accentcolor'] ) : '';
		$instance['catid']   = isset( $new_instance['catid'] ) ? wp_strip_all_tags( $new_instance['catid'] ) : '';
		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );

		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$maxpost    = isset( $instance['maxpost'] ) ? apply_filters( 'widget_title', $instance['maxpost'] ) : '';
		$accentcolor    = isset( $instance['accentcolor'] ) ? apply_filters( 'widget_title', $instance['accentcolor'] ) : '';
		$catid   = isset( $instance['catid'] ) ? apply_filters( 'widget_title', $instance['catid'] ) : '';
		

		// WordPress core before_widget hook (always include )
		echo $before_widget;

		// Display the widget

		// Display widget title if defined
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		
		?>
		<div style="padding-left: 10px; padding-right: 10px; padding-bottom: 10px;">
			<?php
			$args = array(
			  'post_type' => 'post' ,
			  'orderby' => 'date' ,
			  'order' => 'DESC' ,
			  'posts_per_page' => $maxpost,
			  //'cat'         => $catid,
			  //'category__in' => $catid,
			  'paged' => get_query_var('paged'),
			  'post_parent' => $parent
			); 
			
			$tmpcatarray = [$catid];
			
			// add categories param only if 'all categories' was not selected
			if ( ! in_array( 0, $tmpcatarray ) ) {
				$args[ 'category__in' ] = $catid;
			}
			
			$q = new WP_Query($args);
			


			while ($q->have_posts()) { 
				$q->the_post(); 
				
				if(has_category($catid)){
					?>
					<a href="<?php echo get_permalink() ?>">
						<div style="display: table; width: 100%; table-layout: fixed; margin-bottom: 10px;">
							<?php
							if(get_the_post_thumbnail_url() != ""){
								?>
								<div style="display: table-cell; width: 92px; background: url(<?php echo get_the_post_thumbnail_url(); ?>) no-repeat center center; background-size: cover; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover;">
								</div>
								<?php
							}
							?>
							<div style="display: table-cell; text-align: left; padding-left: 10px;">
								<h3 style="font-size: 16px; margin: 0px;"><?php echo wad_rp_shorten_text(get_the_title(), 32) ?></h3>
								<div style="display: inline-block; font-size: 10px; background-color: <?php echo $accentcolor ?>; color: white; padding: 3px; margin; 3px;"><?php echo get_the_date() ?></div>
								<p style="margin: 0px; font-size: 14px;"><?php echo wad_rp_shorten_text(get_the_excerpt(), 64) ?></p>
							</div>
							
						</div>
					</a>
					<?php 
					
				}

			} 
			
			?>
			<div align="center" style="color: <?php echo $accentcolor ?>; padding: 10px;"><a href="<?php echo get_category_link($catid) ?>"><?php _e( 'Archive of ', 'wad-recent-posts'); ?><?php echo $title ?>...</a></div>
		</div>
		<?php
		
		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}

}

// Register the widget
function register_wad_recent_posts() {
	register_widget( 'WAD_Recent_Posts' );
}
// Add Action
add_action( 'widgets_init', 'register_wad_recent_posts' );



// Text domain and its action
function load_wad_textdomain() {
	load_plugin_textdomain( 'wad-recent-posts', false, basename( dirname( __FILE__ ) ) . '/languages/' ); 
}
// Add Action
add_action( 'plugins_loaded', 'load_wad_textdomain' );
/**
 * Load plugin textdomain.
 */
 
 

//Shortcode

function wad_recent_posts_shortcode($atts){
	$default = array(
		'category' => '',
		'max' => 3,
		'color' => '#9a0000',
	);
	$a = shortcode_atts($default, $atts);
	
	$args = array(
	  'post_type' => 'post' ,
	  'orderby' => 'date' ,
	  'order' => 'DESC' ,
	  'posts_per_page' => $a['max'],
	  'paged' => get_query_var('paged'),
	  'post_parent' => $parent
	); 
	
	$tmpcatarray = [get_cat_ID($a['category'])];
	$tmpcatid = 0;
	
	// add categories param only if 'all categories' was not selected
	if ( ! in_array( 0, $tmpcatarray) ) {
		$args[ 'category__in' ] = get_cat_ID($a['category']);
	}
	
	$tempcontent = '<h3>' . __('Recent posts in this category:', 'wad-recent-posts') .'</h3>';
	$accentcolor = $a['color'];
	
	$q = new WP_Query($args);
	
	while ($q->have_posts()) { 
		$q->the_post(); 
		$postthumbnail = "";
		if(get_the_post_thumbnail_url() != ""){
			$postthumbnail = "<div style='display: table-cell; width: 128px; background: url(" . get_the_post_thumbnail_url() . ") no-repeat center center; background-size: cover; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover;'></div>";
		}
		$tempcontent .= "<a href='" .get_permalink(). "'><div style='display: table; width: 100%; table-layout: fixed; margin-bottom: 10px;'></div>" . $postthumbnail . "<div style='display: table-cell; text-align: left; padding-left: 10px;'><h3 style='font-size: 16px; margin: 0px;'>" . get_the_title() . "</h3><div style='display: inline-block; font-size: 10px; padding: 3px; margin; 3px; background-color: ". $accentcolor ."; color: white;'>" . get_the_date() . "</div><p style='margin: 0px; font-size: 14px;'>" . get_the_excerpt() . "</p></div></a>";
	} 
	
	//return 'Hello world! Category slug is: ' . $a['slug'];
	return $tempcontent;
}

add_shortcode('wad_recents' , 'wad_recent_posts_shortcode');




// Other functions...
function wad_rp_shorten_text($text, $max_length = 140, $cut_off = '...', $keep_word = false)
{
    if(strlen($text) <= $max_length) {
        return $text;
    }

    if(strlen($text) > $max_length) {
        if($keep_word) {
            $text = substr($text, 0, $max_length + 1);

            if($last_space = strrpos($text, ' ')) {
                $text = substr($text, 0, $last_space);
                $text = rtrim($text);
                $text .=  $cut_off;
            }
        } else {
            $text = substr($text, 0, $max_length);
            $text = rtrim($text);
            $text .=  $cut_off;
        }
    }

    return $text;
}