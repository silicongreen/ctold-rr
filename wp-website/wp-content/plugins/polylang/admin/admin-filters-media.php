<?php

/*
 * manages filters and actions related to media on admin side
 * capability to edit / create media is checked before loading this class
 *
 * @since 1.2
 */
class PLL_Admin_Filters_Media extends PLL_Admin_Filters_Post_Base {
	/*
	 * constructor: setups filters and actions
	 *
	 * @since 1.2
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang );

		// adds the language field and translations tables in the 'Edit Media' panel
		add_filter( 'attachment_fields_to_edit', array( &$this, 'attachment_fields_to_edit' ), 10, 2 );

		// adds actions related to languages when creating, saving or deleting media
		add_action( 'add_attachment', array( &$this, 'set_default_language' ) );
		add_filter( 'attachment_fields_to_save', array( &$this, 'save_media' ), 10, 2 );
		add_filter( 'wp_delete_file', array( &$this, 'wp_delete_file' ) );

		// creates a media translation
		if ( isset( $_GET['action'], $_GET['new_lang'], $_GET['from_media'] ) && 'translate_media' == $_GET['action']) {
			add_action( 'admin_init', array( &$this, 'translate_media' ) );
		}
	}

	/*
	 * adds the language field and translations tables in the 'Edit Media' panel
	 * needs WP 3.5+
	 *
	 * @since 0.9
	 *
	 * @param array $fields list of form fields
	 * @param object $post
	 * @return array modified list of form fields
	 */
	public function attachment_fields_to_edit( $fields, $post ) {
		if ( 'post.php' == $GLOBALS['pagenow'] ) {
			return $fields; // don't add anything on edit media panel for WP 3.5+ since we have the metabox
		}

		$post_id = $post->ID;
		$lang = $this->model->post->get_language( $post_id );

		$dropdown = new PLL_Walker_Dropdown();
		$fields['language'] = array(
			'label' => __( 'Language', 'polylang' ),
			'input' => 'html',
			'html'  => $dropdown->walk( $this->model->get_languages_list(), array(
				'name'     => sprintf( 'attachments[%d][language]', $post_id ),
				'class'    => 'media_lang_choice',
				'selected' => $lang ? $lang->slug : '',
			) ),
		);

		return $fields;
	}

	/*
	 * creates a media translation
	 *
	 * @since 1.8
	 *
	 * @param int $post_id
	 * @param string|object $lang
	 * @return int id of the translated media
	 */
	public function create_media_translation( $post_id, $lang ) {
		$post = get_post( $post_id );
		$lang = $this->model->get_language( $lang ); // make sure we get a valid language slug

		// create a new attachment ( translate attachment parent if exists )
		$post->ID = null; // will force the creation
		$post->post_parent = ( $post->post_parent && $tr_parent = $this->model->post->get_translation( $post->post_parent, $lang->slug ) ) ? $tr_parent : 0;
		$post->tax_input = array( 'language' => array( $lang->slug ) ); // assigns the language
		$tr_id = wp_insert_attachment( $post );

		// copy metadata, attached file and alternative text
		foreach ( array( '_wp_attachment_metadata', '_wp_attached_file', '_wp_attachment_image_alt' ) as $key ) {
			if ( $meta = get_post_meta( $post_id, $key , true ) ) {
				add_post_meta( $tr_id, $key, $meta );
			}
		}

		$this->model->post->set_language( $tr_id, $lang );

		$translations = $this->model->post->get_translations( $post_id );
		if ( ! $translations && $src_lang = $this->model->post->get_language( $post_id ) ) {
			$translations[ $src_lang->slug ] = $post_id;
		}

		$translations[ $lang->slug ] = $tr_id;
		$this->model->post->save_translations( $tr_id, $translations );

		do_action( 'pll_translate_media', $post_id, $tr_id, $lang->slug );
		return $tr_id;
	}

	/*
	 * creates a media translation
	 *
	 * @since 0.9
	 */
	public function translate_media() {
		//security check
		check_admin_referer( 'translate_media' );
		$post_id = (int) $_GET['from_media'];

		// bails if the translations already exists
		// see https://wordpress.org/support/topic/edit-translation-in-media-attachments?#post-7322303
		if ( $this->model->post->get_translation( $post_id, $_GET['new_lang'] ) ) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		$tr_id = $this->create_media_translation( $post_id, $_GET['new_lang'] );
		wp_safe_redirect( admin_url( sprintf( 'post.php?post=%d&action=edit', $tr_id ) ) ); // WP 3.5+
		exit;
	}

	/*
	 * called when a media is saved
	 * saves language and translations
	 *
	 * @since 0.9
	 *
	 * @param array $post
	 * @param array $attachment
	 * @return array unmodified $post
	 */
	public function save_media( $post, $attachment ) {
		// language is filled in attachment by the function applying the filter 'attachment_fields_to_save'
		// all security checks have been done by functions applying this filter
		if ( ! empty( $attachment['language'] ) ) {
			$this->model->post->set_language( $post['ID'], $attachment['language'] );
		}

		if ( isset( $_POST['media_tr_lang'] ) ) {
			$this->save_translations( $post['ID'], $_POST['media_tr_lang'] );
		}

		return $post;
	}

	/*
	 * prevents WP deleting files when there are still media using them
	 * thanks to Bruno "Aesqe" Babic and its plugin file gallery in which I took all the ideas for this function
	 *
	 * @since 0.9
	 *
	 * @param string $file
	 * @return string unmodified $file
	 */
	public function wp_delete_file( $file ) {
		global $wpdb;

		$uploadpath = wp_upload_dir();

		$ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT post_id FROM $wpdb->postmeta
			WHERE meta_key = '_wp_attached_file' AND meta_value = '%s'",
			substr_replace( $file, '', 0, strlen( trailingslashit( $uploadpath['basedir'] ) ) )
		) );

		if ( ! empty( $ids ) ) {
			// regenerate intermediate sizes if it's an image ( since we could not prevent WP deleting them before )
			wp_update_attachment_metadata( $ids[0], wp_generate_attachment_metadata( $ids[0], $file ) );
			return ''; // prevent deleting the main file
		}

		return $file;
	}
}
