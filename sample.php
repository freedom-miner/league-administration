<?php

/**
 * Create a metabox with multiple fields.
 * Replace `_namespace` with some namespace for your project to avoid conflicts with other items
 */

	//
	// Create Metabox
	//

	/**
	 * Create the metabox
	 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
	 */
	function _namespace_create_metabox() {
		// Can only be used on a single post type (ie. page or post or a custom post type).
		// Must be repeated for each post type you want the metabox to appear on.
		add_meta_box(
			'_namespace_metabox', // Metabox ID
			'Some Metabox', // Title to display
			'_namespace_render_metabox', // Function to call that contains the metabox content
			'post', // Post type to display metabox on
			'normal', // Where to put it (normal = main colum, side = sidebar, etc.)
			'default' // Priority relative to other metaboxes
		);
	}
	add_action( 'add_meta_boxes', '_namespace_create_metabox' );



	/**
	 * Create the metabox default values
	 * This allows us to save multiple values in an array, reducing the size of our database.
	 * Setting defaults helps avoid "array key doesn't exit" issues.
	 * @todo
	 */
	function _namespace_metabox_defaults() {
		return array(
			'item_1' => 'Some value',
			'item_2' => 'on',
			'item_3' => 5,
		);
	}



	/**
	 * Render the metabox markup
	 * This is the function called in `_namespace_create_metabox()`
	 */
	function _namespace_render_metabox() {

		// Variables
		global $post; // Get the current post data
		$saved = get_post_meta( $post->ID, '_namespace', true ); // Get the saved values
		$defaults = _namespace_metabox_defaults(); // Get the default values
		$details = wp_parse_args( $saved, $defaults ); // Merge the two in case any fields don't exist in the saved data

		?>

			<fieldset>

				<?php
					// A simple text input
				?>
				<div>
					<label for="_namespace_custom_metabox_item_1">
						<?php
							// This runs the text through a translation and echoes it (for internationalization)
							_e( 'Item 1', '_namespace' );
						?>
					</label>
					<?php
						// It's important that the `name` is an array. This let's us
						// easily loop through all fields later when we go to save
						// our submitted data.
						//
						// The `esc_attr()` function here escapes the data for
						// HTML attribute use to avoid unexpected issues
					?>
					<input
						type="text"
						name="_namespace_custom_metabox[item_1]"
						id="_namespace_custom_metabox_item_1"
						value="<?php echo esc_attr( $details['item_1'] ); ?>"
					>
				</div>


				<?php
					// A simple text input
				?>
				<div>
					<label>
						<?php _e( 'Item 2', '_namespace' ); ?>
						<input
							type="checkbox"
							name="_namespace_custom_metabox[item_2]"
							value="1"
							<?php
								// `checked()` compares two values, and if they match
								// echoes `checked="checked"`, checking the checkbox
								checked( $details['item_2'], 'on' );
							?>
						>
					</label>
				</div>


				<?php
					// A numeric input
				?>
				<div>
					<label for="_namespace_custom_metabox_item_3">
						<?php _e( 'Item 3', '_namespace' ); ?>
					</label>
					<input type="number" name="_namespace_custom_metabox[item_3]" id="_namespace_custom_metabox_item_3" value="<?php echo esc_attr( $details['item_3'] ); ?>">
				</div>

			</fieldset>

		<?php

		// Security field
		// This validates that submission came from the
		// actual dashboard and not the front end or
		// a remote server.
		wp_nonce_field( '_namespace_form_metabox_nonce', '_namespace_form_metabox_process' );

	}



	//
	// Save our data
	//

	/**
	 * Save the metabox
	 * @param  Number $post_id The post ID
	 * @param  Array  $post    The post data
	 */
	function _namespace_save_metabox( $post_id, $post ) {

		// Verify that our security field exists. If not, bail.
		if ( !isset( $_POST['_namespace_form_metabox_process'] ) ) return;

		// Verify data came from edit/dashboard screen
		if ( !wp_verify_nonce( $_POST['_namespace_form_metabox_process'], '_namespace_form_metabox_nonce' ) ) {
			return $post->ID;
		}

		// Verify user has permission to edit post
		if ( !current_user_can( 'edit_post', $post->ID )) {
			return $post->ID;
		}

		// Check that our custom fields are being passed along
		// This is the `name` value array. We can grab all
		// of the fields and their values at once.
		if ( !isset( $_POST['_namespace_custom_metabox'] ) ) {
			return $post->ID;
		}

		/**
		 * Sanitize all data
		 * This keeps malicious code out of our database.
		 */

		// Set up an empty array
		$sanitized = array();

		// Loop through each of our fields
		foreach ( $_POST['_namespace_custom_metabox'] as $key => $detail ) {
			// Sanitize the data and push it to our new array
			// `wp_filter_post_kses` strips our dangerous server values
			// and allows through anything you can include a post.
			$sanitized[$key] = wp_filter_post_kses( $detail );
		}

		// Save our submissions to the database
		update_post_meta( $post->ID, '_namespace', $sanitized );

	}
	add_action( 'save_post', '_namespace_save_metabox', 1, 2 );



	//
	// Save a copy to our revision history
	// This is optional, and potentially undesireable for certain data types.
	// Restoring a a post to an old version will also update the metabox.

	/**
	 * Save events data to revisions
	 * @param  Number $post_id The post ID
	 */
	function _namespace_save_revisions( $post_id ) {

		// Check if it's a revision
		$parent_id = wp_is_post_revision( $post_id );

		// If is revision
		if ( $parent_id ) {

			// Get the saved data
			$parent = get_post( $parent_id );
			$details = get_post_meta( $parent->ID, '_namespace', true );

			// If data exists and is an array, add to revision
			if ( !empty( $details ) && is_array( $details ) ) {
				// Get the defaults
				$defaults = _namespace_metabox_defaults();

				// For each default item
				foreach ( $defaults as $key => $value ) {
					// If there's a saved value for the field, save it to the version history
					if ( array_key_exists( $key, $details ) ) {
						add_metadata( 'post', $post_id, '_namespace_' . $key, $details[$key] );
					}
				}
			}

		}

	}
	add_action( 'save_post', '_namespace_save_revisions' );



	/**
	 * Restore events data with post revisions
	 * @param  Number $post_id     The post ID
	 * @param  Number $revision_id The revision ID
	 */
	function _namespace_restore_revisions( $post_id, $revision_id ) {

		// Variables
		$post = get_post( $post_id ); // The post
		$revision = get_post( $revision_id ); // The revision
		$defaults = _namespace_metabox_defaults(); // The default values
		$details = array(); // An empty array for our new metadata values

		// Update content
		// For each field
		foreach ( $defaults as $key => $value ) {

			// Get the revision history version
			$detail_revision = get_metadata( 'post', $revision->ID, '_namespace_' . $key, true );

			// If a historic version exists, add it to our new data
			if ( isset( $detail_revision ) ) {
				$details[$key] = $detail_revision;
			}
		}

		// Replace our saved data with the old version
		update_post_meta( $post_id, '_namespace', $details );

	}
	add_action( 'wp_restore_post_revision', '_namespace_restore_revisions', 10, 2 );



	/**
	 * Get the data to display on the revisions page
	 * @param  Array $fields The fields
	 * @return Array The fields
	 */
	function _namespace_get_revisions_fields( $fields ) {

		// Get our default values
		$defaults = _namespace_metabox_defaults();

		// For each field, use the key as the title
		foreach ( $defaults as $key => $value ) {
			$fields['_namespace_' . $key] = ucfirst( $key );
		}

		return $fields;

	}
	add_filter( '_wp_post_revision_fields', '_namespace_get_revisions_fields' );



	/**
	 * Display the data on the revisions page
	 * @param  String|Array $value The field value
	 * @param  Array        $field The field
	 */
	function _namespace_display_revisions_fields( $value, $field ) {
		global $revision;
		return get_metadata( 'post', $revision->ID, $field, true );
	}
	add_filter( '_wp_post_revision_field_my_meta', '_namespace_display_revisions_fields', 10, 2 );