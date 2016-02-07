<?php

/*
 * filters content by language on frontend
 *
 * @since 1.2
 */
class PLL_Frontend_Filters extends PLL_Filters{
	/*
	 * constructor: setups filters and actions
	 *
	 * @since 1.2
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang );

		// filters the WordPress locale
		add_filter( 'locale', array( &$this, 'get_locale' ) );

		// filter sticky posts by current language
		add_filter( 'option_sticky_posts', array( &$this, 'option_sticky_posts' ) );

		// adds cache domain when querying terms
		add_filter( 'get_terms_args', array( &$this, 'get_terms_args' ) );

		// filters categories and post tags by language
		add_filter( 'terms_clauses', array( &$this, 'terms_clauses' ), 10, 3 );

		// rewrites archives, next and previous post links to filter them by language
		foreach ( array( 'getarchives', 'get_previous_post', 'get_next_post' ) as $filter ) {
			foreach ( array( '_join', '_where' ) as $clause ) {
				add_filter( $filter.$clause, array( &$this, 'posts'.$clause ) );
			}
		}

		// filters the widgets according to the current language
		add_filter( 'widget_display_callback', array( &$this, 'widget_display_callback' ), 10, 2 );

		// strings translation ( must be applied before WordPress applies its default formatting filters )
		foreach ( array( 'widget_text', 'widget_title', 'option_blogname', 'option_blogdescription', 'option_date_format', 'option_time_format' ) as $filter ) {
			add_filter( $filter, 'pll__', 1 );
		}

		// translates biography
		add_filter( 'get_user_metadata', array( &$this, 'get_user_metadata' ), 10, 4 );

		// set posts and terms language when created from frontend ( ex with P2 theme )
		add_action( 'save_post', array( &$this, 'save_post' ), 200, 2 );
		add_action( 'create_term', array( &$this, 'save_term' ), 10, 3 );
		add_action( 'edit_term', array( &$this, 'save_term' ), 10, 3 );

		if ( $this->options['media_support'] ) {
			add_action( 'add_attachment', array( &$this, 'set_default_language' ) );
		}

		// support theme customizer
		// FIXME of course does not work if 'transport' is set to 'postMessage'
		if ( isset( $_POST['wp_customize'], $_POST['customized'] ) ) {
			add_filter( 'pre_option_blogname', 'pll__', 20 );
			add_filter( 'pre_option_blogdescription', 'pll__', 20 );
		}
	}

	/*
	 * returns the locale based on current language
	 *
	 * @since 0.1
	 *
	 * @param string $locale
	 * @return string
	 */
	public function get_locale( $locale ) {
		return $this->curlang->locale;
	}

	/*
	 * filters sticky posts by current language
	 *
	 * @since 0.8
	 *
	 * @param array $posts list of sticky posts ids
	 * @return array modified list of sticky posts ids
	 */
	public function option_sticky_posts( $posts ) {
		if ( $this->curlang && ! empty( $posts ) ) {
			update_object_term_cache( $posts, 'post' ); // to avoid queries in foreach
			foreach ( $posts as $key => $post_id ) {
				$lang = $this->model->post->get_language( $post_id );
				if ( empty( $lang ) || $lang->term_id != $this->curlang->term_id ) {
					unset( $posts[ $key ] );
				}
			}
		}
		return $posts;
	}

	/*
	 * adds language dependent cache domain when querying terms
	 * useful as the 'lang' parameter is not included in cache key by WordPress
	 *
	 * @since 1.3
	 *
	 * @param array $args
	 * @return array
	 */
	public function get_terms_args( $args ) {
		$lang = isset( $args['lang'] ) ? $args['lang'] : $this->curlang->slug;
		$key = '_' . ( is_array( $lang ) ? implode( ',', $lang ) : $lang );
		$args['cache_domain'] = empty( $args['cache_domain'] ) ? 'pll' . $key : $args['cache_domain'] . $key;
		return $args;
	}

	/*
	 * filters categories and post tags by language when needed
	 *
	 * @since 0.2
	 *
	 * @param array $clauses sql clauses
	 * @param array $taxonomies
	 * @param array $args get_terms arguments
	 * @return array modified sql clauses
	 */
	public function terms_clauses( $clauses, $taxonomies, $args ) {
		// does nothing except on taxonomies which are filterable
		if ( ! $this->model->is_translated_taxonomy( $taxonomies ) ) {
			return $clauses;
		}

		// adds our clauses to filter by language
		return $this->model->terms_clauses( $clauses, isset( $args['lang'] ) ? $args['lang'] : $this->curlang );
	}

	/*
	 * modifies the sql request for wp_get_archives an get_adjacent_post to filter by the current language
	 *
	 * @since 0.1
	 *
	 * @param string $sql join clause
	 * @return string modified join clause
	 */
	public function posts_join( $sql ) {
		return $sql . $this->model->post->join_clause();
	}

	/*
	 * modifies the sql request for wp_get_archives and get_adjacent_post to filter by the current language
	 *
	 * @since 0.1
	 *
	 * @param string $sql where clause
	 * @return string modified where clause
	 */
	public function posts_where( $sql ) {
		preg_match( "#post_type = '([^']+)'#", $sql, $matches );	// find the queried post type
		return ! empty( $matches[1] ) && $this->model->is_translated_post_type( $matches[1] ) ? $sql . $this->model->post->where_clause( $this->curlang ) : $sql;
	}

	/*
	 * filters the widgets according to the current language
	 * don't display if a language filter is set and this is not the current one
	 *
	 * @since 0.3
	 *
	 * @param array $instance widget settings
	 * @param object $widget WP_Widget object
	 * @return bool|array false if we hide the widget, unmodified $instance otherwise
	 */
	public function widget_display_callback( $instance, $widget ) {
		return ! empty( $instance['pll_lang'] ) && $instance['pll_lang'] != $this->curlang->slug ? false : $instance;
	}

	/*
	 * translates biography
	 *
	 * @since 0.9
	 *
	 * @param null $null
	 * @param int $id user id
	 * @param string $meta_key
	 * @param bool $single Whether to return only the first value of the specified $meta_key
	 * @return null|string
	 */
	public function get_user_metadata( $null, $id, $meta_key, $single ) {
		return 'description' === $meta_key && $this->curlang->slug !== $this->options['default_lang'] ? get_user_meta( $id, 'description_'.$this->curlang->slug, $single ) : $null;
	}

	/*
	 * allows to set a language by default for posts if it has no language yet
	 *
	 * @since 1.5.4
	 *
	 * @param int $post_id
	 */
	public function set_default_language( $post_id ) {
		if ( ! $this->model->post->get_language( $post_id ) ) {
			if ( isset( $_REQUEST['lang'] ) ) {
				$this->model->post->set_language( $post_id, $_REQUEST['lang'] );
			}

			elseif ( ( $parent_id = wp_get_post_parent_id( $post_id ) ) && $parent_lang = $this->model->post->get_language( $parent_id ) ) {
				$this->model->post->set_language( $post_id, $parent_lang );
			}

			else {
				$this->model->post->set_language( $post_id, $this->curlang );
			}
		}
	}

	/*
	 * called when a post ( or page ) is saved, published or updated
	 * does nothing except on post types which are filterable
	 * sets the language but does not allow to modify it
	 *
	 * @since 1.1
	 *
	 * @param int $post_id
	 * @param object $post
	 * @param bool $update whether it is an update or not
	 */
	public function save_post( $post_id, $post ) {
		if ( $this->model->is_translated_post_type( $post->post_type ) ) {
			$this->set_default_language( $post_id );
		}
	}

	/*
	 * called when a category or post tag is created or edited
	 * does nothing except on taxonomies which are filterable
	 * sets the language but does not allow to modify it
	 *
	 * @since 1.1
	 *
	 * @param int $term_id
	 * @param int $tt_id term taxonomy id
	 * @param string $taxonomy
	 */
	public function save_term( $term_id, $tt_id, $taxonomy ) {
		if ( $this->model->is_translated_taxonomy( $taxonomy ) && ! $this->model->term->get_language( $term_id ) ) {
			if ( isset( $_REQUEST['lang'] ) ) {
				$this->model->term->set_language( $term_id, $_REQUEST['lang'] );
			}

			elseif ( ( $term = get_term( $term_id, $taxonomy ) ) && ! empty( $term->parent ) && $parent_lang = $this->model->term->get_language( $term->parent ) ) {
				$this->model->term->set_language( $term_id, $parent_lang );
			}

			else {
				$this->model->term->set_language( $term_id, $this->curlang );
			}
		}
	}
}
