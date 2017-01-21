<?php
/*
Plugin Name: Easy Google Places Reviews
Plugin URI: en.wphire.ru
Description: Display your Google business page reviews on your wordpress pages just by adding a single shortcode. 
Version: 0.1
Author: Evgenii rezanov
Author URI: en.wphire.ru
*/

require_once('includes/options_page.php');

// Register Custom Post Type
function google_places_review() {

	$labels = array(
		'name'                  => _x( 'Google Reviews', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Google Review', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Google review', 'text_domain' ),
		'name_admin_bar'        => __( 'Google review', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Google Review', 'text_domain' ),
		'description'           => __( 'Google places review', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 25,
		'menu_icon'             => 'dashicons-googleplus',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'google_review', $args );

}
add_action( 'init', 'google_places_review', 0 );

//cron event
add_action('wp', 'egpr_activation');
function egpr_activation() {
	if( ! wp_next_scheduled( 'egpr_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'egpr_daily_event');
	}
}

// get google data
add_action('egpr_daily_event', 'egpr_get_data');
function egpr_get_data () {
	
   	$egpr_url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=";
	$easy_google_places_review_option_options = get_option( 'easy_google_places_review_option_option_name' ); 
   	$egpr_api = $easy_google_places_review_option_options['google_api_place_id_0'];
   	$egpr_key = $easy_google_places_review_option_options['google_api_key_1']; 


	$egpr_json = $egpr_url . $egpr_api . "&key=" . $egpr_key;

	$egrp_data =  json_decode(file_get_contents($egpr_json));

	for($i=0; $i<count($egrp_data->result->reviews); $i++) {		
 		
 		$egpr_profile_photo_url = $egrp_data->result->reviews[$i]->profile_photo_url; 		
 		$egpr_author_url = $egrp_data->result->reviews[$i]->author_url;
 		$egpr_rating = $egrp_data->result->reviews[$i]->rating;
 		$egpr_author_name = $egrp_data->result->reviews[$i]->author_name;
 		$egpr_date = $egrp_data->result->reviews[$i]->time;
 		$egpr_text = $egrp_data->result->reviews[$i]->text;
 		

		$post = get_page_by_title( $egpr_author_name.'-'.$egpr_date, 'OBJECT', 'google_review' );

		if (!isset($post)) {
 		
	 		$post_data = array(
	  			'post_title'    => $egpr_author_name.'-'.$egpr_date,
	  			'post_content'  => $egpr_text,
	  			'post_status'   => 'publish',
	  			'post_author'   => 1,
	  			'post_type'     => 'google_review'
			);

			$post_id = wp_insert_post( $post_data );

			add_post_meta($post_id, 'egpr_author_url', $egpr_author_url, true);
			add_post_meta($post_id, 'egpr_rating', $egpr_rating, true);
			add_post_meta($post_id, 'egpr_author_name', $egpr_author_name, true);
			add_post_meta($post_id, 'egpr_date', $egpr_date, true);
			add_post_meta($post_id, 'egpr_profile_photo_url', $egpr_profile_photo_url, true);

			//TO DO save avatar to db
			/*		
			if(!empty($egpr_profile_photo_url)){
	            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	        	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	        	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	            // Установим данные файла
	            $file_array = array();
	            $desc = '';
	            $tmp = download_url( $egpr_profile_photo_url );
	            preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $egpr_profile_photo_url, $matches );
	            $file_array['name'] = basename( $matches[0] );
	            $file_array['tmp_name'] = $tmp;
	            // загружаем файл
	            $id = media_handle_sideload( $file_array, $post_id);
	            // если ошибка
				if( is_wp_error( $id ) ) {
					@unlink($file_array['tmp_name']);
					return $id->get_error_messages();
					error_log(print_r($id->get_error_messages()));
				}
	            // удалим временный файл
	            @unlink( $file_array['tmp_name'] );
	            update_post_meta( $post_id, '_thumbnail_id', $id );
	            $image_id = get_post_thumbnail_id($post_id);
	        	$image_url = wp_get_attachment_image_src($image_id, 'full');
	        	$image_url = $image_url[0];
			}
			*/
		}
    }
}			

add_shortcode('egpr_review_shortcode', 'egpr_review_shortcode');
function egpr_review_shortcode() {

	$cacheName = 'easy-google-places-revews';
	$content = get_transient($cacheName);

	if ($content === false) {
	    ob_start();

		$args = array(
		'numberposts' => 5,
		'post_type'   => 'google_review',
		'post_status' => 'publish ',
		);

		$reviews = get_posts( $args );

		if (isset($reviews)) {

			foreach($reviews as $review){ setup_postdata($review);
				$egpr_author_name = get_post_meta($review->ID, 'egpr_author_name', true);
				$egpr_date = get_post_meta($review->ID, 'egpr_date', true);
				$egpr_profile_photo_url = get_post_meta($review->ID, 'egpr_profile_photo_url', true);
				$egpr_author_url = get_post_meta($review->ID, 'egpr_author_url', true);
				$egpr_rating = get_post_meta($review->ID, 'egpr_rating', true);

				$egpr_rating_pic = plugins_url('images', __FILE__).'/'.$egpr_rating.'stars.png';
				if (!isset($egpr_profile_photo_url)) {
	 				$egpr_profile_photo_url = plugins_url('images', __FILE__).'/profil-pic_dummy.png';
	 			}
				// TO DO delete CSS, change style
				?>
				 <style>
				 	.review-rating {
						width:120px;
					}
					.quote-text {
				    	padding-top: 30px;
					}
				</style>
				 <div class="blockquote-list">
					<blockquote class="clearfix small simple">
						<div class="quote-thumbnail">
							<img width="150" height="150" src="<?php echo $egpr_profile_photo_url; ?>" class="attachment-thumbnail size-thumbnail wp-post-image" sizes="(max-width: 150px) 100vw, 150px">
							<strong class="quote-title"><a href="<?php echo $egpr_author_url; ?>"><span class="the-title"><?php echo $egpr_author_name; ?></span></a></strong>
						</div>
						<div class="quote-text">
							<div class="quote-content">
								<img class ="review-rating" src="<?php echo $egpr_rating_pic; ?>">
								<br>
								<span class="review-date"><?php echo date('m/d/Y', $egpr_date); ?></span>
								<br>
								<em><?php echo $review->post_content; ?></em>
							</div>
						</div>
					</blockquote>
				</div>
				<?php
			}
		}
		else {
			echo "Cant find reviews!";
		}
		wp_reset_postdata();
		$content = ob_get_contents();
	    set_transient($cacheName, $content, DAY_IN_SECONDS);
	    ob_end_clean();
	}
	echo $content;
}

?>