<?php
/**
 * Meetup.com Importer for The Events Calendar Delete Import
 * @version 0.1.0
 * @package Meetup.com Importer for The Events Calendar
 */

class TMI_Delete_Import {
	/**
	 * Parent plugin class
	 *
	 * @var   class
	 * @since NEXT
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  NEXT
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function hooks() {
		add_action( 'init', array($this, 'maybe_delete_import'), 100 );
	}

	/**
	 * Delete import if we should
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function maybe_delete_import() {
		$should_delete_import = $this->should_delete_import();
		if ($should_delete_import) {
			$this->handle_deletion();
			// wp_delete_post( $postid, $force_delete );
		}
	}

	/**
	 * Handle the actual deletion
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function handle_deletion() {
		$post_title = sanitize_text_field( $_GET['meetup-import'] );
		if ( $post_title ) {
			$post = get_page_by_title( $post_title, 'OBJECT', 'tec_meetup_import' );
			if ( $post ) {
				wp_delete_post( $post->ID, true );
				add_action( 'admin_notices', array( $this, 'send_success_message' ), 100 );
			}
		}
	}

	/**
	 * Show success message
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function send_success_message() {
	    ?>
	    <div class="notice notice-success is-dismissible">
	        <p><?php _e( 'Successfully deleted Meetup.com group import.', 'tec-meetup' ) ?></p>
	    </div>
	    <?php
	}

	/**
	 * Check if the current action is telling us to delete an import
	 *
	 * @since  NEXT
	 * @return void
	 */
	public function should_delete_import() {

		if ( isset($_POST['tec-meetup-import-group-url']) ) {
			// This might happen if the user adds a new import after deleting one.
			return false;
		}

		if (isset($_GET['action']) && $_GET['action'] === "delete" && isset($_GET['meetup-import'])) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'deletemeetupimport' ) ) {
				return true;
			}
		}

		return false;
	}
}