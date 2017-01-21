<?php
/**
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */

class EasyGooglePlacesReviewOption {
	private $easy_google_places_review_option_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'easy_google_places_review_option_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'easy_google_places_review_option_page_init' ) );
	}

	public function easy_google_places_review_option_add_plugin_page() {
		add_management_page(
			'Easy Google Places Review Option', // page_title
			'Easy Google Places Review Option', // menu_title
			'manage_options', // capability
			'easy-google-places-review-option', // menu_slug
			array( $this, 'easy_google_places_review_option_create_admin_page' ) // function
		);
	}

	public function easy_google_places_review_option_create_admin_page() {
		$this->easy_google_places_review_option_options = get_option( 'easy_google_places_review_option_option_name' ); ?>

		<div class="wrap">
			<h2>Easy Google Places Review Option</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'easy_google_places_review_option_option_group' );
					do_settings_sections( 'easy-google-places-review-option-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function easy_google_places_review_option_page_init() {
		register_setting(
			'easy_google_places_review_option_option_group', // option_group
			'easy_google_places_review_option_option_name', // option_name
			array( $this, 'easy_google_places_review_option_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'easy_google_places_review_option_setting_section', // id
			'Settings', // title
			array( $this, 'easy_google_places_review_option_section_info' ), // callback
			'easy-google-places-review-option-admin' // page
		);

		add_settings_field(
			'google_api_place_id_0', // id
			'Google API Place Id', // title
			array( $this, 'google_api_place_id_0_callback' ), // callback
			'easy-google-places-review-option-admin', // page
			'easy_google_places_review_option_setting_section' // section
		);

		add_settings_field(
			'google_api_key_1', // id
			'Google API Key', // title
			array( $this, 'google_api_key_1_callback' ), // callback
			'easy-google-places-review-option-admin', // page
			'easy_google_places_review_option_setting_section' // section
		);
	}

	public function easy_google_places_review_option_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['google_api_place_id_0'] ) ) {
			$sanitary_values['google_api_place_id_0'] = sanitize_text_field( $input['google_api_place_id_0'] );
		}

		if ( isset( $input['google_api_key_1'] ) ) {
			$sanitary_values['google_api_key_1'] = sanitize_text_field( $input['google_api_key_1'] );
		}

		return $sanitary_values;
	}

	public function easy_google_places_review_option_section_info() {
		
	}

	public function google_api_place_id_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="easy_google_places_review_option_option_name[google_api_place_id_0]" id="google_api_place_id_0" value="%s">',
			isset( $this->easy_google_places_review_option_options['google_api_place_id_0'] ) ? esc_attr( $this->easy_google_places_review_option_options['google_api_place_id_0']) : ''
		);
	}

	public function google_api_key_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="easy_google_places_review_option_option_name[google_api_key_1]" id="google_api_key_1" value="%s">',
			isset( $this->easy_google_places_review_option_options['google_api_key_1'] ) ? esc_attr( $this->easy_google_places_review_option_options['google_api_key_1']) : ''
		);
	}

}
if ( is_admin() )
	$easy_google_places_review_option = new EasyGooglePlacesReviewOption();

/* 
 * Retrieve this value with:
 * $easy_google_places_review_option_options = get_option( 'easy_google_places_review_option_option_name' ); // Array of All Options
 * $google_api_place_id_0 = $easy_google_places_review_option_options['google_api_place_id_0']; // Google API Place Id
 * $google_api_key_1 = $easy_google_places_review_option_options['google_api_key_1']; // Google API Key
 */

?>