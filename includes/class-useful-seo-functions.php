<?php
class Useful_SEO_Functions {
	// Declare properties for storing option values
	private $options;

	// Constructor
	public function __construct() {
		// Register admin menu
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );

		// Register settings
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	// Add options page
	public function add_plugin_page() {
		add_options_page(
			'Useful SEO Functions',
			'Useful SEO Functions',
			'manage_options',
			'useful-seo-functions',
			array( $this, 'admin_page' )
		);
	}

	// Options page callback
	public function admin_page() {
		// Set class property
		$this->options = get_option( 'useful_seo_functions', array('enable_function_1' => 1, 'enable_function_2' => 1, 'enable_function_3' => 1) );

		// Display the page
		?>
		<div class="wrap">
			<h1>Useful SEO Functions by SyntaxSurge</h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'useful_seo_functions_group' );
					do_settings_sections( 'useful-seo-functions' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	// Register and add settings
	public function page_init() {
		register_setting(
			'useful_seo_functions_group', // Option group
			'useful_seo_functions',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'setting_section_id', // Section ID
			'Settings', // Title
			null,
			'useful-seo-functions'
		);

		add_settings_field(
			'enable_function_1', // ID
			'Disable Wordpress API for unauthorized users or public view', // Title
			array( $this, 'enable_function_1_callback' ), // Callback
			'useful-seo-functions', // Page
			'setting_section_id' // Section
		);
		
		$install_rankmath_url = add_query_arg( array(
			'type' => 'term',
			'tab' => 'search',
			's' => 'Rank Math SEO',
		), get_admin_url() . 'plugin-install.php' );

		add_settings_field(
			'enable_function_2', // ID
			"Automatically update the focus keyword with the post slug <a href='{$install_rankmath_url}'>(Required: Rank Math SEO Plugin)</a>", // Title
			array( $this, 'enable_function_2_callback' ), // Callback
			'useful-seo-functions', // Page
			'setting_section_id' // Section
		);

		add_settings_field(
			'enable_function_3', // ID
			"Automatically update the meta description of posts <a href='{$install_rankmath_url}'>(Required:  Rank Math SEO Plugin)</a>", // Title
			array( $this, 'enable_function_3_callback' ), // Callback
			'useful-seo-functions', // Page
			'setting_section_id' // Section
		);
	}

	// Sanitize input
	public function sanitize( $input ) {
		$new_input = array();

		$new_input['enable_function_1'] = isset( $input['enable_function_1'] ) ? 1 : 0;
		$new_input['enable_function_2'] = isset( $input['enable_function_2'] ) ? 1 : 0;
		$new_input['enable_function_3'] = isset( $input['enable_function_3'] ) ? 1 : 0;

		return $new_input;
	}

	// Print the enable functions checkboxes
	public function enable_function_1_callback() {
		printf(
			'<input type="checkbox" id="enable_function_1" name="useful_seo_functions[enable_function_1]" value="1" %s>',
			checked( isset( $this->options['enable_function_1'] ) ? $this->options['enable_function_1'] : 1, 1, false )
		);
	}

	public function enable_function_2_callback() {
		printf(
			'<input type="checkbox" id="enable_function_2" name="useful_seo_functions[enable_function_2]" value="1" %s>',
			checked( isset( $this->options['enable_function_2'] ) ? $this->options['enable_function_2'] : 1, 1, false )
		);
	}

	public function enable_function_3_callback() {
		printf(
			'<input type="checkbox" id="enable_function_3" name="useful_seo_functions[enable_function_3]" value="1" %s>',
			checked( isset( $this->options['enable_function_3'] ) ? $this->options['enable_function_3'] : 1, 1, false )
		);
	}
}


// Instantiate the main plugin class
if ( is_admin() ) {
	$useful_seo_functions = new Useful_SEO_Functions();
}


function useful_seo_functions_init() {
	$options = get_option( 'useful_seo_functions' );

	// Check if the options are enabled
	if ( isset( $options['enable_function_1'] ) && $options['enable_function_1'] ) {
		// Function 1 code

		/**
		 * Function to disallow Wordpress API on unauthorized users or public view.
		 */
		add_filter( 'rest_authentication_errors', function( $result ) {
			// If a previous authentication check was applied,
			// pass that result along without modification.
			if ( true === $result || is_wp_error( $result ) ) {
				return $result;
			}

			// No authentication has been performed yet.
			// Return an error if user is not logged in.
			if ( ! is_user_logged_in() ) {
				return new WP_Error(
					'disabled_for_you_by_syntaxsurge',
					__( 'Protected by SyntaxSurge.com' ),
					array( 'status' => 401 )
				);
			}

			// Our custom authentication check should have no effect
			// on logged-in requests
			return $result;
		});
		
	}

	if ( isset( $options['enable_function_2'] ) && $options['enable_function_2'] ) {
		// Function 2 code

		/**
		 * Function to automatically update the focus keyword with the post slug, if no focus keyword is set
		 */
		function update_focus_keywords($post_id) {
			// Check if the post is a revision
			if (wp_is_post_revision($post_id)) {
				return;
			}

			$post = get_post($post_id);
			$slug = $post->post_name;
			$slug = str_replace('-', ' ', $slug);

			// Check if Rank Math keyword already exists and only update if it doesn't have it
			$rank_math_keyword = get_post_meta($post_id, 'rank_math_focus_keyword', true);
			if (!$rank_math_keyword) {
				update_post_meta($post_id, 'rank_math_focus_keyword', strtolower($slug));
			}	
		}
		add_action('save_post', 'update_focus_keywords');
	}

	if ( isset( $options['enable_function_3'] ) && $options['enable_function_3'] ) {
		// Function 3 code

		/**
		 * Function to automatically update the meta description of posts, if no meta description is set
		 */

		class RankMathUpdateController {
			public function __construct() {
				add_action('save_post', [$this, 'update_rank_math_meta'], 10, 2);
			}

			function update_rank_math_meta($post_id, $post) {
				// If this is an autosave, skip the update
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
					return;
				}

				// Check if the post is a revision
				if (wp_is_post_revision($post_id)) {
					return;
				}

				// Update the Rank Math meta description
				$this->add_to_rank_math_seo($post_id);
			}

			function add_to_rank_math_seo($post_id) {
				// Check if the meta description already exists
				$existing_meta_desc = get_post_meta($post_id, 'rank_math_description', true);

				// If the meta description is not set, update it
				if (empty($existing_meta_desc)) {
					// Get the post content
					$post_content = get_post_field('post_content', $post_id);

					// Generate a meta description from the post content
					$metadesc = wp_trim_words($post_content, 25, '...');

					// Update the Rank Math SEO meta description
					$updated_desc = update_post_meta($post_id, 'rank_math_description', $metadesc);

					return $updated_desc;
				}
			}
		}

		new RankMathUpdateController();
		
	}
}
add_action( 'init', 'useful_seo_functions_init' );

/**
Function to set meta description for a post via API.
Sample usage:
'meta': {'rank_math_description': meta_description}
**/
add_action( 'rest_insert_post', 'update_rank_math_description_via_api', 10, 3 );
function update_rank_math_description_via_api( $post, $request, $creating ) {
    $meta_description = isset($request->get_param('meta')['rank_math_description']) 
        ? $request->get_param('meta')['rank_math_description'] 
        : null;

    if ( ! empty( $meta_description ) ) {
        update_post_meta( $post->ID, 'rank_math_description', sanitize_text_field( $meta_description ) );
    }
}
