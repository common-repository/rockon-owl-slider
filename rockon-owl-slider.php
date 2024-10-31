<?php
/*
Plugin Name: Rockon Owl Slider
Plugin URI: https://wordpress.org/plugins/rockon-owl-slider/
Description: Rockon Owl Slider is a simple, responsive WordPress slider plugin. It is based on OWL Carousel New version 2, touch enabled jQuery plugin. Slider works as a custom post type with featured images, excerpt as a slide description and each slide can have a link with an option to open a link in a new tab. Available with shortcode [ROCKON_OWL]
Version: 2.0
Author: Vikas Sharma
Author URI: https://profiles.wordpress.org/devikas301
License: GPLv2 or later
*/

define('ROS_PATH', plugin_dir_path(__FILE__));
define('ROS_LINK', plugin_dir_url(__FILE__));
define('ROS_PLUGIN_NAME', plugin_basename(__FILE__));

//	Slider Post Thumbnail
	if ( function_exists( 'add_theme_support' ) ) { 
	  add_theme_support( 'post-thumbnails' );
	}
	
//  Add ROS Owl Sider Styles
	function ros_register_styles() {
	 wp_register_style('rostyle1', ROS_LINK. 'css/ros-style.css');
     wp_register_style('rostyle2', ROS_LINK. 'owlcarousel/assets/owl.carousel.css');
     wp_register_style('rostyle3', ROS_LINK. 'owlcarousel/assets/owl.theme.css');
     wp_enqueue_style('rostyle1');
     wp_enqueue_style('rostyle2');
	 wp_enqueue_style('rostyle3');
	}
	add_action('wp_print_styles', 'ros_register_styles');

//  Add ROS Owl Slider Script
	function ros_register_script() {
     wp_register_script('roscript1', ROS_LINK. 'owlcarousel/owl.carousel.min.js');
	  wp_register_script('roscript2', ROS_LINK.'owlcarousel/owl.script.js');
     wp_enqueue_script('roscript1');
     wp_enqueue_script('roscript2');
	}
	add_action('wp_print_scripts', 'ros_register_script');
	
//  ROS Slider Post Type
	function ros_register_slides_posttype() {
		$ros_labels = array(
			'name' 				=> _x( 'Rockon Slides', 'post type general name','ros' ),
			'singular_name'		=> _x( 'Rockon Slide', 'post type singular name','ros' ),
			'add_new' 			=> __( 'Add New Slide','ros' ),
			'add_new_item' 		=> __( 'Add New Slide','ros' ),
			'edit_item' 		=> __( 'Edit Slide','ros' ),
			'new_item' 			=> __( 'New Slide','ros' ),
			'view_item' 		=> __( 'View','ros'),
			'search_items' 		=> __( 'Search Slides','ros' ),
			'not_found' 		=> __( 'Slide','ros' ),
			'not_found_in_trash'=> __( 'Slide','ros' ),
			'parent_item_colon' => __( 'Slide','ros' ),
			'menu_name'			=> __( 'Rockon Owl Slider','ros' )
		);
		$ros_taxonomies = array();
		$supports = array('title','excerpt','thumbnail');
		$ros_post_type_args = array(
			'labels' 			=> $ros_labels,
			'singular_label' 	=> __('Rockon Slide','ros'),
			'public' 			=> true,
			'show_ui' 			=> true,
			'publicly_queryable'=> true,
			'can_export'        => true,
			'query_var'			=> true,
			'capability_type' 	=> 'post',
			'has_archive' 		=> false,
			'hierarchical' 		=> true,
			'rewrite' 			=> array('slug' => 'rockon-slides', 'with_front' => false ),
            'supports'          => array( 'title', 'excerpt', 'thumbnail' ),
			'menu_position' 	=> 29,
			'menu_icon' 		=> ROS_LINK . '/images/slider-icon.png',
			'taxonomies'		=> $ros_taxonomies
		 );
		 register_post_type('rockon_slides',$ros_post_type_args);
	}
	add_action('init', 'ros_register_slides_posttype');
	
//  ROS Slider Meta Box
	$ros_slidelink_metabox = array( 
		'id' => 'slidelink',
		'title' => 'Slide Link',
		'page' => array('rockon_slides'),
		'context' => 'normal',
		'priority' => 'default',
		'fields' => array(
                		array(
						'name' 			=> 'URL',
						'desc' 			=> '',
						'id' 			=> 'ros_slideurl',
						'class' 		=> 'ros_slideurl',
						'type' 			=> 'text',
						'rich_editor' 	=> 0,
						'std'          	=> '',
						'max' 			=> 0
						),
						array(
						'name' 			=> 'Open slide link in new tab:',
						'desc' 			=> '',
						'id' 			=> 'ros_slidetarget',
						'class' 		=> 'ros_slidetarget',
						'type' 			=> 'checkbox'
						),
					)
	);	
	
	// ROS add meta box			
	add_action('admin_menu', 'ros_add_slidelink_meta_box');
	function ros_add_slidelink_meta_box() {
		global $ros_slidelink_metabox;		
		foreach($ros_slidelink_metabox['page'] as $page) {
			add_meta_box($ros_slidelink_metabox['id'], $ros_slidelink_metabox['title'], 'ros_show_slidelink_box', $page, 'normal', 'default', $ros_slidelink_metabox);
		}
	}
	
	// ROS show meta boxes
	function ros_show_slidelink_box()	{
		global $post, $ros_slidelink_metabox, $wp_version;
		//global $mykraft_prefix;
		
	//  nonce for verification
		echo '<input type="hidden" name="ros_slidelink_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
		echo '<table class="form-table">';
		foreach ($ros_slidelink_metabox['fields'] as $field) {
			// get current post meta data
			$meta = get_post_meta($post->ID, $field['id'], true);
			echo '<tr>',
					'<th style="width:20%"><label for="', $field['id'], '">', stripslashes($field['name']), '</label></th>',
					'<td class="mykraft_field_type_' . str_replace(' ', '_', $field['type']) . '">';
			switch ($field['type']) {
				case 'text':
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" /><br/>', '', stripslashes($field['desc']);
					break;
				case 'checkbox':
					echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
					break;
			}
			echo    '<td>',
				'</tr>';
		}
		echo '</table>';
	}	
	
    // ROS url target
	function ros_targetlink() {
	$meta = get_post_meta( get_the_ID(), 'ros_slidetarget', true );
    if ($meta == '') {
        echo '_self';
    } else {
        echo '_blank';
      }
	}

	// attachment
	if ( 'post_type' == 'slider' && post_status == 'publish' ) {
    $attachments = get_posts(array(
        'post_type'      => 'attachment',
        'posts_per_page' => -1,
        'post_parent'    => $post->ID,
        'exclude'        => get_post_thumbnail_id()
    ));
        if ($attachments) {
            foreach ($attachments as $attachment) {
             $thumbimg = wp_get_attachment_link( $attachment->ID, 'thumbnail-size', true );
             echo $thumbimg;
            }
        }
    }

// Save data from ROS meta box
	add_action('save_post', 'ros_slidelink_save');
	function ros_slidelink_save($post_id) {
		global $post, $ros_slidelink_metabox;
		
		 foreach ($ros_slidelink_metabox['fields'] as $field) {
			
			$old = get_post_meta($post_id, $field['id'], true);
			$new = $_POST[$field['id']];
			if ($new && $new != $old) {
				if($field['type'] == 'date') {
					$new = mykraft_format_date($new);
					update_post_meta($post_id, $field['id'], $new);
				} else {
					if(is_string($new)) {
						$new = $new;
					} 
					update_post_meta($post_id, $field['id'], $new);
				}
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}
		 }
		
		// verify nonce
		if ( !isset( $_POST['ros_slidelink_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['ros_slidelin_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
		// check permissions
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}	
	}

//  Construct ROS Slider
	function ros_slider() {
?>
		<div id="owl-container" class="owl-carousel owl-theme ros-section">
		    <?php
				$args = array('post_type' => 'rockon_slides', 'posts_per_page' => -1);
				$loop = new WP_Query($args);
				$rs   = 1;
			 while ($loop->have_posts()) : $loop->the_post();
					
				   $slide_imgurl = '';
				  if ( has_post_thumbnail() ) { 
	                   $slide_imgurl = get_the_post_thumbnail_url(); 
                  }	
			?>
			
		   <div class="item owl-background" id="ros-item<?php echo $rs;?>" style="background: url(<?php echo $slide_imgurl;?>) no-repeat top center/cover;">

			<?php if ( get_post_meta( get_the_id(), 'ros_slideurl', true) != '' ) { ?>
			
			  <a href="<?php echo esc_url( get_post_meta( get_the_id(), 'ros_slideurl', true ) ); ?>" target="<?php echo ros_targetlink(); ?>">
				<div class="container">
					<div class="slider_text">
						<h2><?php the_title(); ?></h2>
							<?php if ( has_excerpt() ) { ?>
						       <?php the_excerpt(); ?>
   			   		        <?php } ?>
					</div>
				</div>
   			   </a>			
			   
			<?php } else { ?>
			
               <div class="container">
				 <div class="slider_text">
					<h2><?php the_title(); ?></h2>
						<?php if ( has_excerpt() ) { ?>
					       <?php the_excerpt(); ?>
		   			    <?php } ?>
				 </div>
			    </div>
			<?php } ?>
						
		   </div><!-- .item -->
		   
		<?php $rs++; endwhile; ?>	
					
        </div><!--#owl-container--> 
		
<?php
	}
	
	add_action( 'wp_enqueue', 'ros_slider' );
    add_shortcode('ROCKON_OWL', 'ros_slider');


//  Slider Width for WP Customizer
	function ros_width_size() { 
?>
		<style type="text/css">
			.owl-background {
			max-width: <?php if (get_theme_mod( 'slider_width' )) : echo get_theme_mod( 'slider_width');  endif; ?>px;
			}
    	</style>
<?php 
    }
    add_action('wp_head', 'ros_width_size');		
	
//  WP Customizer Menu Slider Options
    new ros_theme_customizer();
	class ros_theme_customizer {
		
        public function __construct() {
            add_action( 'customize_register', array(&$this, 'ros_customize_manager_slider' ));
		}
		
        public function ros_customize_manager_slider( $wp_manager ) {
            $this->ros_theme_section( $wp_manager );
		}		
		
		public function ros_theme_section( $wp_manager ) {
            $wp_manager->add_section( 'customiser_ros_theme_section', array(
                'title'          => 'Rockon Slider Customizer',
                'priority'       => 172,
			) );        	 		
								
			// Slider Width
			$wp_manager->add_setting( 'slider_width', array(
                'default'        => '',
			));
			
			$wp_manager->add_control( 'slider_width', array(
                'label'   => 'Slider Maximum Width - numeric value (e.g. 1400), without px',
				'section' => 'customiser_ros_theme_section',
				'type'    => 'text',
				'priority' => 5
			) );		
        }
    }
?>