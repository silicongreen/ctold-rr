<?php

/*
 * setups the language and translations model based on WordPress taxonomies
 *
 * @since 1.2
 */
class PLL_Model {
	public $cache; // our internal non persistent cache object
	public $options;
	public $post, $term; // translated objects models

	/*
	 * constructor: registers custom taxonomies and setups filters and actions
	 *
	 * @since 1.2
	 *
	 * @param array $options Polylang options
	 */
	public function __construct( &$options ) {
		$this->options = &$options;

		$this->cache = new PLL_Cache();
		$this->post = new PLL_Translated_Post( $this ); // translated post sub model
		$this->term = new PLL_Translated_Term( $this ); // translated term sub model

		// we need to clean languages cache when editing a language and when modifying the permalink structure
		add_action( 'edited_term_taxonomy', array( &$this, 'clean_languages_cache' ), 10, 2 );
		add_action( 'update_option_permalink_structure', array( &$this, 'clean_languages_cache' ) );
		add_action( 'update_option_siteurl', array( &$this, 'clean_languages_cache' ) );
		add_action( 'update_option_home', array( &$this, 'clean_languages_cache' ) );

		// just in case someone would like to display the language description ;- )
		add_filter( 'language_description', '__return_empty_string' );
	}

	/*
	 * returns the list of available languages
	 * caches the list in a db transient ( except flags ), unless PLL_CACHE_LANGUAGES is set to false
	 * caches the list ( with flags ) in the private property $languages
	 *
	 * list of parameters accepted in $args:
	 *
	 * hide_empty => hides languages with no posts if set to true ( defaults to false )
	 * fields     => return only that field if set ( see PLL_Language for a list of fields )
	 *
	 * @since 0.1
	 *
	 * @param array $args
	 * @return array|string|int list of PLL_Language objects or PLL_Language object properties
	 */
	public function get_languages_list( $args = array() ) {
		if ( false === $languages = $this->cache->get( 'languages' ) ) {

			// create the languages from taxonomies
			if ( ( defined( 'PLL_CACHE_LANGUAGES' ) && ! PLL_CACHE_LANGUAGES ) || false === ( $languages = get_transient( 'pll_languages_list' ) ) ) {
				$languages = get_terms( 'language', array( 'hide_empty' => false, 'orderby' => 'term_group' ) );
				$languages = empty( $languages ) || is_wp_error( $languages ) ? array() : $languages;

				$term_languages = get_terms( 'term_language', array( 'hide_empty' => false ) );
				$term_languages = empty( $term_languages ) || is_wp_error( $term_languages ) ?
					array() : array_combine( wp_list_pluck( $term_languages, 'slug' ), $term_languages );

				if ( ! empty( $languages ) && ! empty( $term_languages ) ) {
					// don't use array_map + create_function to instantiate an autoloaded class as it breaks badly in old versions of PHP
					foreach ( $languages as $k => $v ) {
						$languages[ $k ] = new PLL_Language( $v, $term_languages[ 'pll_' . $v->slug ] );
					}

					// we will need the languages list to allow its access in the filter below
					$this->cache->set( 'languages', $languages );

					// filters the list of languages *before* it is stored in the persistent cache
					// /!\ this filter is fired *before* the $polylang object is available
					$languages = apply_filters( 'pll_languages_list', $languages, $this );

					// don't store directly objects as it badly break with some hosts ( GoDaddy ) due to race conditions when using object cache
					// thanks to captin411 for catching this!
					// see https://wordpress.org/support/topic/fatal-error-pll_model_languages_list?replies=8#post-6782255;
					set_transient( 'pll_languages_list', array_map( 'get_object_vars', $languages ) );
				}
				else {
					$languages = array(); // in case something went wrong
				}
			}

			// create the languages directly from arrays stored in transients
			else {
				foreach ( $languages as $k => $v ) {
					$languages[ $k ] = new PLL_Language( $v );
				}
			}

			// custom flags
			if ( ! PLL_ADMIN ) {
				foreach ( $languages as $language ) {
					$language->set_custom_flag();
				}
			}

			// filters the list of languages *after* it is stored in the persistent cache
			// /!\ this filter is fired *before* the $polylang object is available
			$languages = apply_filters( 'pll_after_languages_cache', $languages );
			$this->cache->set( 'languages', $languages );
		}

		$args = wp_parse_args( $args, array( 'hide_empty' => false ) );

		// remove empty languages if requested
		if ( $args['hide_empty'] ) {
			$languages = wp_list_filter( $languages, array( 'count' => 0 ), 'NOT' );
		}

		return empty( $args['fields'] ) ? $languages : wp_list_pluck( $languages, $args['fields'] );
	}

	/*
	 * cleans language cache
	 * can be called directly with no parameter
	 * called by the 'edited_term_taxonomy' filter with 2 parameters when count needs to be updated
	 *
	 * @since 1.2
	 *
	 * @param int $term not used
	 * @param string $taxonomy taxonomy name
	 */
	public function clean_languages_cache( $term = 0, $taxonomy = null ) {
		// depending on WP version, the action is passed an object or a string
		// backward compatibility with WP < 4.2
		if ( ! empty( $taxonomy ) && is_object( $taxonomy ) ) {
			$taxonomy = $taxonomy->name;
		}

		if ( empty( $taxonomy ) || 'language' == $taxonomy ) {
			delete_transient( 'pll_languages_list' );
			$this->cache->clean();
		}
	}

	/*
	 * returns the language by its term_id, tl_term_id, slug or locale
	 *
	 * @since 0.1
	 *
	 * @param int|string term_id, tl_term_id, slug or locale of the queried language
	 * @return object|bool PLL_Language object, false if no language found
	 */
	public function get_language( $value ) {
		if ( is_object( $value ) ) {
			return $value instanceof PLL_Language ? $value : $this->get_language( $value->term_id ); // will force cast to PLL_Language
		}

		if ( false === $return = $this->cache->get( 'language:' . $value ) ) {
			foreach ( $this->get_languages_list() as $lang ) {
				$this->cache->set( 'language:' . $lang->term_id, $lang );
				$this->cache->set( 'language:' . $lang->tl_term_id, $lang );
				$this->cache->set( 'language:' . $lang->slug, $lang );
				$this->cache->set( 'language:' . $lang->locale, $lang );
			}
			$return = $this->cache->get( 'language:' . $value );
		}

		return $return;
	}

	/*
	 * adds terms clauses to get_terms to filter them by languages - used in both frontend and admin
	 *
	 * @since 1.2
	 *
	 * @param array $clauses the list of sql clauses in terms query
	 * @param object $lang PLL_Language object
	 * @return array modifed list of clauses
	 */
	public function terms_clauses( $clauses, $lang ) {
		if ( ! empty( $lang ) ) {
			$clauses['join'] .= $this->term->join_clause();
			$clauses['where'] .= $this->term->where_clause( $lang );
		}
		return $clauses;
	}

	/*
	 * returns post types that need to be translated
	 * the post types list is cached for better better performance
	 * wait for 'after_setup_theme' to apply the cache to allow themes adding the filter in functions.php
	 *
	 * @since 1.2
	 *
	 * @param bool $filter true if we should return only valid registered post types
	 * @return array post type names for which Polylang manages languages and translations
	 */
	public function get_translated_post_types( $filter = true ) {
		if ( false === $post_types = $this->cache->get( 'post_types' ) ) {
			$post_types = array( 'post' => 'post', 'page' => 'page' );

			if ( ! empty( $this->options['media_support'] ) ) {
				$post_types['attachement'] = 'attachment';
			}

			if ( ! empty( $this->options['post_types'] ) && is_array( $this->options['post_types'] ) ) {
				$post_types = array_merge( $post_types,  array_combine( $this->options['post_types'], $this->options['post_types'] ) );
			}

			$post_types = apply_filters( 'pll_get_post_types', $post_types , false );

			if ( did_action( 'after_setup_theme' ) ) {
				$this->cache->set( 'post_types', $post_types );
			}
		}

		return $filter ? array_intersect( $post_types, get_post_types() ) : $post_types;
	}

	/*
	 * returns true if Polylang manages languages and translations for this post type
	 *
	 * @since 1.2
	 *
	 * @param string|array $post_type post type name or array of post type names
	 * @return bool
	 */
	public function is_translated_post_type( $post_type ) {
		$post_types = $this->get_translated_post_types( false );
		return ( is_array( $post_type ) && array_intersect( $post_type, $post_types ) || in_array( $post_type, $post_types ) );
	}

	/*
	 * return taxonomies that need to be translated
	 *
	 * @since 1.2
	 *
	 * @param bool $filter true if we should return only valid registered taxonmies
	 * @return array array of registered taxonomy names for which Polylang manages languages and translations
	 */
	public function get_translated_taxonomies( $filter = true ) {
		if ( false === $taxonomies = $this->cache->get( 'taxonomies' ) ) {
			$taxonomies = array( 'category' => 'category', 'post_tag' => 'post_tag' );

			if ( ! empty( $this->options['taxonomies'] ) && is_array( $this->options['taxonomies'] ) ) {
				$taxonomies = array_merge( $taxonomies, array_combine( $this->options['taxonomies'], $this->options['taxonomies'] ) );
			}

			$taxonomies = apply_filters( 'pll_get_taxonomies', $taxonomies, false );
			if ( did_action( 'after_setup_theme' ) ) {
				$this->cache->set( 'taxonomies', $taxonomies );
			}
		}

		return $filter ? array_intersect( $taxonomies, get_taxonomies() ) : $taxonomies;
	}

	/*
	 * returns true if Polylang manages languages and translations for this taxonomy
	 *
	 * @since 1.2
	 *
	 * @param string|array $tax taxonomy name or array of taxonomy names
	 * @return bool
	 */
	public function is_translated_taxonomy( $tax ) {
		$taxonomies = $this->get_translated_taxonomies( false );
		return ( is_array( $tax ) && array_intersect( $tax, $taxonomies ) || in_array( $tax, $taxonomies ) );
	}

	/*
	 * return taxonomies that need to be filtered ( post_format like )
	 *
	 * @since 1.7
	 *
	 * @param bool $filter true if we should return only valid registered taxonomies
	 * @return array array of registered taxonomy names
	 */
	public function get_filtered_taxonomies( $filter = true ) {
		if ( did_action( 'after_setup_theme' ) ) {
			static $taxonomies = null;
		}

		if ( empty( $taxonomies ) ) {
			$taxonomies = array( 'post_format' => 'post_format' );
			$taxonomies = apply_filters( 'pll_filtered_taxonomies', $taxonomies, false );
		}

		return $filter ? array_intersect( $taxonomies, get_taxonomies() ) : $taxonomies;
	}

	/*
	 * returns true if Polylang filters this taxonomy per language
	 *
	 * @since 1.7
	 *
	 * @param string|array $tax taxonomy name or array of taxonomy names
	 * @return bool
	 */
	public function is_filtered_taxonomy( $tax ) {
		$taxonomies = $this->get_filtered_taxonomies( false );
		return ( is_array( $tax ) && array_intersect( $tax, $taxonomies ) || in_array( $tax, $taxonomies ) );
	}

	/*
	 * returns the query vars of all filtered taxonomies
	 *
	 * @since 1.7
	 *
	 * @return array
	 */
	public function get_filtered_taxonomies_query_vars() {
		$query_vars = array();
		foreach ( $this->get_filtered_taxonomies() as $filtered_tax ) {
			$tax = get_taxonomy( $filtered_tax );
			$query_vars[] = $tax->query_var;
		}
		return $query_vars;
	}

	/*
	 * create a default category for a language
	 *
	 * @since 1.2
	 *
	 * @param object|string|int $lang language
	 */
	public function create_default_category( $lang ) {
		$lang = $this->get_language( $lang );

		// create a new category
		// FIXME this is translated in admin language when we would like it in $lang
		$cat_name = __( 'Uncategorized' );
		$cat_slug = sanitize_title( $cat_name . '-' . $lang->slug );
		$cat = wp_insert_term( $cat_name, 'category', array( 'slug' => $cat_slug ) );

		// check that the category was not previously created ( in case the language was deleted and recreated )
		$cat = isset( $cat->error_data['term_exists'] ) ? $cat->error_data['term_exists'] : $cat['term_id'];

		// set language
		$this->term->set_language( (int) $cat, $lang );

		// this is a translation of the default category
		$default = (int) get_option( 'default_category' );
		$translations = $this->term->get_translations( $default );
		if ( empty( $translations ) ) {
			if ( $lg = $this->term->get_language( $default ) ) {
				$translations[ $lg->slug ] = $default;
			}
			else {
				$translations = array();
			}
		}

		$this->term->save_translations( (int) $cat, $translations );
	}

	/*
	 * it is possible to have several terms with the same name in the same taxonomy ( one per language )
	 * but the native term_exists will return true even if only one exists
	 * so here the function adds the language parameter
	 *
	 * @since 1.4
	 *
	 * @param string $term_name the term name
	 * @param string $taxonomy taxonomy name
	 * @param int $parent parent term id
	 * @param string|object $language the language slug or object
	 * @return null|int the term_id of the found term
	 */
	public function term_exists( $term_name, $taxonomy, $parent, $language ) {
		global $wpdb;

		$term_name = trim( wp_unslash( $term_name ) );

		$select = "SELECT t.term_id FROM $wpdb->terms AS t";
		$join = " INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id";
		$join .= $this->term->join_clause();
		$where = $wpdb->prepare( " WHERE tt.taxonomy = %s AND t.name = %s", $taxonomy, $term_name );
		$where .= $this->term->where_clause( $this->get_language( $language ) );

		if ( $parent > 0 ) {
			$where .= $wpdb->prepare( " AND tt.parent = %d", $parent );
		}

		return $wpdb->get_var( $select . $join . $where );
	}

	/*
	 * gets the number of posts per language in a date, author or post type archive
	 *
	 * @since 1.2
	 *
	 * @param object lang
	 * @param array $q WP_Query arguments ( accepted: post_type, m, year, monthnum, day, author, author_name, post_format )
	 * @return int
	 */
	public function count_posts( $lang, $q = array() ) {
		global $wpdb;

		if ( ! is_array( $q['post_type'] ) ) {
			$q['post_type'] = array( $q['post_type'] );
		}

		foreach ( $q['post_type'] as $key => $type ) {
			if ( ! post_type_exists( $type ) ) {
				unset( $q['post_type'][ $key ] );
			}
		}

		if ( empty( $q['post_type'] ) ) {
			$q['post_type'] = array( 'post' ); // we *need* a post type
		}

		$cache_key = md5( serialize( $q ) );
		$counts = wp_cache_get( $cache_key, 'pll_count_posts' );

		if ( false === $counts ) {
			$select = "SELECT pll_tr.term_taxonomy_id, COUNT( * ) AS num_posts FROM {$wpdb->posts} AS p";
			$join = $this->post->join_clause();
			$where = " WHERE post_status = 'publish'";
			$where .= $wpdb->prepare( " AND p.post_type IN ( '%s' )", join( "', '", $q['post_type'] ) );
			$where .= $this->post->where_clause( $this->get_languages_list() );
			$groupby = " GROUP BY pll_tr.term_taxonomy_id";

			if ( ! empty( $q['m'] ) ) {
				$q['m'] = '' . preg_replace( '|[^0-9]|', '', $q['m'] );
				$where .= $wpdb->prepare( " AND YEAR( p.post_date ) = %d", substr( $q['m'], 0, 4 ) );
				if ( strlen( $q['m'] ) > 5 ) {
					$where .= $wpdb->prepare( " AND MONTH( p.post_date ) = %d", substr( $q['m'], 4, 2 ) );
				}
				if ( strlen( $q['m'] ) > 7 ) {
					$where .= $wpdb->prepare( " AND DAYOFMONTH( p.post_date ) = %d", substr( $q['m'], 6, 2 ) );
				}
			}

			if ( ! empty( $q['year'] ) ) {
				$where .= $wpdb->prepare( " AND YEAR( p.post_date ) = %d", $q['year'] );
			}

			if ( ! empty( $q['monthnum'] ) ) {
				$where .= $wpdb->prepare( " AND MONTH( p.post_date ) = %d", $q['monthnum'] );
			}

			if ( ! empty( $q['day'] ) ) {
				$where .= $wpdb->prepare( " AND DAYOFMONTH( p.post_date ) = %d", $q['day'] );
			}

			if ( ! empty( $q['author_name'] ) ) {
				$author = get_user_by( 'slug',  sanitize_title_for_query( $q['author_name'] ) );
				if ( $author ) {
					$q['author'] = $author->ID;
				}
			}

			if ( ! empty( $q['author'] ) ) {
				$where .= $wpdb->prepare( " AND p.post_author = %d", $q['author'] );
			}

			// filtered taxonomies ( post_format )
			foreach ( $this->get_filtered_taxonomies_query_vars() as $tax_qv ) {
				if ( ! empty( $q[ $tax_qv ] ) ) {
					$join .= " INNER JOIN {$wpdb->term_relationships} AS tr ON tr.object_id = p.ID";
					$join .= " INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id";
					$join .= " INNER JOIN {$wpdb->terms} AS t ON t.term_id = tt.term_id";
					$where .= $wpdb->prepare( " AND t.slug = %s", $q[ $tax_qv ] );
				}
			}

			$res = $wpdb->get_results( $select . $join . $where . $groupby, ARRAY_A );
			foreach ( (array) $res as $row ) {
				$counts[ $row['term_taxonomy_id'] ] = $row['num_posts'];
			}

			wp_cache_set( $cache_key, $counts, 'pll_count_posts' );
		}

		return empty( $counts[ $lang->term_taxonomy_id ] ) ? 0 : $counts[ $lang->term_taxonomy_id ];
	}

	/*
	 * setup the links model based on options
	 *
	 * @since 1.2
	 *
	 * @return object implementing "links_model interface"
	 */
	public function get_links_model() {
		$c = array( 'Directory', 'Directory', 'Subdomain', 'Domain' );
		$class = get_option( 'permalink_structure' ) ? 'PLL_Links_' . $c[ $this->options['force_lang'] ] : 'PLL_Links_Default';
		return new $class( $this );
	}

	/*
	 * some backward compatibility with Polylang < 1.8
	 * allows for example to call $polylang->model->get_post_languages( $post_id ) instead of $polylang->model->post->get_language( $post_id )
	 * this works but should be slower than the direct call, thus an error is triggered in debug mode
	 *
	 * @since 1.8
	 *
	 * @param string $func function name
	 * @param array $args function arguments
	 */
	public function __call( $func, $args ) {
		$f = $func;

		switch ( $func ) {
			case 'get_object_term':
				$o = false === strpos( $args[1], 'term' ) ? 'post' : 'term';
			break;

			case 'save_translations':
			case 'delete_translation':
			case 'get_translations':
			case 'get_translation':
			case 'join_clause':
				$o = ( 'post' == $args[0] || $this->is_translated_post_type( $args[0] ) ) ? 'post' : ( 'term' == $args[0] || $this->is_translated_taxonomy( $args[0] ) ? 'term' : false );
				unset( $args[0] );
			break;

			case 'set_post_language':
			case 'get_post_language':
			case 'set_term_language':
			case 'get_term_language':
			case 'delete_term_language':
			case 'get_post':
			case 'get_term':
				$str = explode( '_', $func );
				$f = empty( $str[2] ) ? $str[0] : $str[0] . '_' . $str[2];
				$o = $str[1];
			break;

			case 'where_clause':
			case 'get_objects_in_language':
				$o = $args[1];
				unset( $args[1] );
			break;
		}

		if ( ! empty( $o ) && is_object( $this->$o ) && method_exists( $this->$o, $f ) ) {
			if ( WP_DEBUG ) {
				$debug = debug_backtrace();
				$i = 1 + empty( $debug[1]['line'] ); // the file and line are in $debug[2] if the function was called using call_user_func

				trigger_error( sprintf(
					'%1$s was called incorrectly in %4$s on line %5$s: the call to $polylang->model->%1$s() has been deprecated in Polylang 1.8, use PLL()->model->%2$s->%3$s() instead.' . "\nError handler",
					$func, $o, $f, $debug[ $i ]['file'], $debug[ $i ]['line']
				) );
			}
			return call_user_func_array( array( $this->$o, $f ), $args );
		}

		$debug = debug_backtrace();
		trigger_error( sprintf( 'Call to undefined function PLL()->model->%1$s() in %2$s on line %3$s' . "\nError handler", $func, $debug[0]['file'], $debug[0]['line'] ), E_USER_ERROR );
	}
}
