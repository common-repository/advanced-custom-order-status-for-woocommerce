<?php

/**
 * Holds metabox functionality to create it with array.
 *
 * @package    StorePlugin\CustomOrderStatus
 * @subpackage StorePlugin\CustomOrderStatus\Libs\MetaBoxes
 * @author     StorePlugin <contact@storeplugin.net>
 */
namespace StorePlugin\CustomOrderStatus\Abstracts;

/**
 * AbstractMetaBoxes
 *
 * WooCommerce custom order status post type metabox
 */
abstract class AbstractMetaBoxes {

	/**
	 * Register with php-di
	 *
	 * @return void
	 */
	public function register(): void {
		\add_action('add_meta_boxes', [$this, 'registerMetabox']);
		\add_action('save_post', [$this, 'saveOrderStatuses']);
		\add_action('post_edit_form_tag', [$this, 'update_edit_form']);
	}

	/**
	 * Order status slug filed
	 *
	 * @return void
	 */
	public function registerMetabox() {
		\add_meta_box(
			$this->ID(),
			$this->title(),
			[$this, 'executeFields'],
			$this->screen(),
			$this->context(),
			$this->priority(),
		);
	}

	/**
	 * Set Metabox ID
	 *
	 * @return int
	 */
	abstract protected function ID(): string;

	/**
	 * Set Metabox title
	 *
	 * @return string
	 */
	abstract protected function title(): string;

	/**
	 * Set Metabox screen like post type, comment etc
	 *
	 * @return string
	 */
	abstract protected function screen(): string;

	/**
	 * Add Meta Context
	 * Side, Normal, Advanced
	 *
	 * @return string
	 */
	protected function context(): string {
		return 'normal';
	}

	/**
	 * Add Meta Priority like high
	 *
	 * @return string
	 */
	protected function priority(): string {
		return '';
	}

	/**
	 * Add Meta Fields
	 *
	 * @return array
	 */
	abstract protected function addFields($prefix = '');

	/**
	 * Set form type
	 *
	 * @return void
	 */
	function update_edit_form() {
		echo ' enctype="multipart/form-data"';
	}

	/**
	 * Get trimmed and formated slug
	 *
	 * @param mixed $slug
	 * @return string
	 */
	public function trimmedSlug($slug) {
		$trimed         = trim(substr($slug, 0, 17), ' ');
		$formated_slug 	= str_replace(' ', '-', $trimed);
		return strtolower($formated_slug);
	}

	/**
     * Check whether the order status is default
     *
     * @return array
     */
    protected function isDefaultStatus( $post_id ) {
		$value = get_post_meta( $post_id, "_order_status_default", true );
    	return ( $value === '' ? true : false );
    }

	/**
	 * Execute Meta fields
	 *
	 * @return void
	 */
	public function executeFields($post) {
		// Get all fields data
		$metaFields = $this->addFields();

		// Enqueue WooCommerce style file only for this page.
		wp_enqueue_style('wc-admin-app');
		wp_enqueue_style('wc-onboarding');

		// _wp_nonce for secuirity verification
		wp_nonce_field('order_status_actions', '_wpnonce_order_status');

		echo '<table class="form-table wc-custom-order-status-form">';

		if (is_array($metaFields)) {
			foreach ($metaFields as $field) {

				// get current post meta data
				$value 	= get_post_meta( $post->ID, $field['id'], true);

				echo '<tr>';
				if($field['type'] !== 'hidden') echo '<th class="meta-titles"><label for="', esc_attr( $field['id'] ), '">', esc_attr( $field['name'] ), '</label></th>',
				'<td>';
				switch ($field['type']) {
					case 'button':
						echo '<input type="submit" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', esc_attr( $field['name'] ), '" class="components-button is-primary" />';
						break;
					case 'text':
						echo '<input type="text" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', esc_attr( $value ), '" placeholder="', esc_attr( $field['placeholder'] ?? '' ), '"/>';
						echo '<p class="desc">', esc_html( $field['desc'] ), '</p>';
						break;
					case 'slug':
						if( ! $this->isDefaultStatus( $post->ID ) ) {
							echo '<input type="text" disabled value="', esc_attr( $value ), '" />';
							echo '<p class="desc">', esc_html( 'You should never change the default order status slug' ), '</p>';
							break;
						}
						echo '<input type="text" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', esc_attr( $value ), '" placeholder="', esc_attr( $field['placeholder'] ?? '' ), '"/>';
						echo '<p class="desc">', esc_html($field['desc']), '</p>';
						break;
					case 'textarea':
						echo '<textarea name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" placeholder="', esc_attr( $field['placeholder'] ?? '' ), '" cols="60" rows="4">', esc_textarea( $value ), '</textarea>';
						echo '<p class="desc">', esc_html($field['desc'] ), '</p>';
						break;
					case 'wp_editor':
						wp_editor( $value, $field['id'], [
							'wpautop'       => true,
							'media_buttons' => false,
							'textarea_name' => $field['id'],
							'textarea_rows' => get_option('default_post_edit_rows', 10),
							'teeny'         => false
						]);
						break;
					case 'select':
						echo '<select name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '">';
						echo ( esc_attr( $field['nullvalue'] ) == true ) ? '<option selected value="">'. esc_html( 'Select from options' ) .'</option>' : '';
						foreach ( $field['options'] as $key => $option ) {
							echo '<option ', selected( esc_attr( $value ), esc_attr( $key ) ), ' value="', esc_attr( $key ) ,'">', esc_html( $option ), '</option>';
						}
						echo '</select>';
						break;
					case 'multiselect':
						echo '<select class="wc_multiselect" id="', esc_attr( $field['id'] ), '" name="', esc_attr( $field['id'] ), '[]" multiple>';
						foreach( $field['options'] as $key => $option ) {
							$selectedValue = ( is_array( $value ) && in_array( $key, $value ) ) ? $key : '';
							echo '<option ', selected( esc_attr( $selectedValue ), esc_attr( $key ) ),' value="', esc_attr( $key ) ,'">', esc_html( $option ), '</option>';
						}
						echo '</select>';
						break;
					case 'radio':
						foreach ( $field['options'] as $option ) {
							echo '<input type="radio" name="', esc_attr( $field['id'] ), '" value="', esc_attr( $option['value'] ), '"', checked( esc_attr( $value ), esc_attr( $option['value'] ) ), '/>', esc_attr( $option['name'] );
						}
						break;
					case 'checkbox':
						echo '<input type="checkbox" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" ', checked( esc_attr( $value ), 'on' ), '/>';
						break;
					case 'color':
						echo '<input type="color" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', esc_attr( $value ? $value : $field['default'] ), '" />';
						echo '<input type="submit" name="_reset_status_color" id="_reset_status_color" value="', esc_attr('Reset color'), '" class="components-button is-primary" />';
						break;
					case 'icon':
						do_action( 'wc_custom_order_status_icon', $field['id'], $value );
						break;
					case 'hidden':
						if( $this->isDefaultStatus( $post->ID ) ) {
							echo '<input type="hidden" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="', esc_attr( $field['default'] ), '" />';
						} else {
							echo '<input type="hidden" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="yes" />';
						}
						break;
				}
				echo '</td><td>',
				'</td></tr>';
			}
		}

		echo '</table>';
	}

	/**
	 * Save Order status fields
	 *
	 * @param int $post_id order status post id
	 * @return void
	 */
	public function saveOrderStatuses($post_id) {

		// Validate order status data
		if (!isset($_POST['_wpnonce_order_status'])) return;
		if (!wp_verify_nonce($_POST['_wpnonce_order_status'], 'order_status_actions')) return;

		// Ignore when auto saving mode enabled
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		// Check user capability
		if (!current_user_can('edit_post', $post_id)) return;

		// Get all fields data
		$metaFields  = $this->addFields();
		$slugAsTitle = isset( $_POST['_title_status']) ? sanitize_title( $_POST['_title_status'] ) : '';
		$colorStatus = isset( $_POST['_color_status']) ? sanitize_hex_color( $_POST['_color_status'] ) : '#50575E';

		//Save Metabox data
		if (is_array($metaFields)) {
			foreach ($metaFields as $field) {
				$fieldType = $field['type'];

				switch( $fieldType ) {
					case 'wp_editor':
						if ( isset( $_POST[$field['id']] ) && ! empty( $_POST[$field['id']] ) ) {
							update_post_meta( $post_id, $field['id'], wp_kses_post( $_POST[$field['id']] ) );
						} else {
							update_post_meta( $post_id, $field['id'], '' );
						}
						break;
					case 'multiselect':
						if ( isset( $_POST[$field['id']] ) && ! empty( $_POST[$field['id']] ) ) {
							update_post_meta( $post_id, $field['id'], array_map( 'sanitize_text_field', $_POST[$field['id']] ) );
						} else {
							update_post_meta( $post_id, $field['id'], '' );
						}
						break;
					case 'textarea':
						if ( isset( $_POST[$field['id']] ) && ! empty( $_POST[$field['id']] ) ) {
							update_post_meta( $post_id, $field['id'], sanitize_textarea_field( $_POST[$field['id']] ) );
						} else {
							update_post_meta($post_id, $field['id'], '' );
						}
						break;
					case 'slug':
						if ( ! $this->isDefaultStatus( $post_id ) ) {
							break;
						}
						if ( isset( $_POST[$field['id']] ) && ! empty( $_POST[$field['id']] ) ) {
							update_post_meta( $post_id, $field['id'], $this->trimmedSlug( sanitize_text_field( $_POST[$field['id']] ) ) );
						} else {
							update_post_meta($post_id, $field['id'], $this->trimmedSlug( $slugAsTitle ) );
						}
						break;
					default:
						if ( isset( $_POST[$field['id']] ) && ! empty( $_POST[$field['id']] ) ) {
							update_post_meta( $post_id, $field['id'], sanitize_text_field( $_POST[$field['id']] ) );
						} else {
							update_post_meta($post_id, $field['id'], '' );
						}
						break;
				}
			}
		}

		// Reset color
		if ( isset( $_POST['_reset_status_color'] ) && $colorStatus !== '#50575E' ) {
			update_post_meta( $post_id, '_color_status', sanitize_hex_color( '#50575E' ) );
		}

		// Remove icon
		if( isset( $_POST['_remove_icon_status'] ) && $_POST['_icon_status'] !== 'no-icon' ) {
			update_post_meta( $post_id, '_icon_status', sanitize_text_field( 'no-icon' ) );
		}

	}

}
