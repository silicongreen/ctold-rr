<?php

/*
 * manages links filters on frontend
 *
 * @since 1.8
 */
class PLL_Frontend_Filters_Links extends PLL_Filters_Links {
	public $curlang;
	public $cache; // our internal non persistent cache object

	/*
	 * constructor
	 * adds filters once the language is defined
	 * low priority on links filters to come after any other modification
	 *
	 * @since 1.8
	 *
	 * @param object $polylang
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang );

		$this->curlang = &$polylang->curlang;
		$this->cache = new PLL_Cache();

		// rewrites author and date links to filter them by language
		foreach ( array( 'feed_link', 'author_link', 'search_link', 'year_link', 'month_link', 'day_link' ) as $filter ) {
			add_filter( $filter, array( &$this, 'archive_link' ), 20 );
		}

		// rewrites post types archives links to filter them by language
		add_filter( 'post_type_archive_link', array( &$this, 'post_type_archive_link' ), 20, 2 );

		// meta in the html head section
		add_action( 'wp_head', array( &$this, 'wp_head' ) );

		// modifies the home url
		if ( ! defined( 'PLL_FILTER_HOME_URL' ) || PLL_FILTER_HOME_URL ) {
			add_filter( 'home_url', array( &$this, 'home_url' ), 10, 2 );
		}

		if ( $this->options['force_lang'] > 1 ) {
			// rewrites next and previous post links when not automatically done by WordPress
			add_filter( 'get_pagenum_link', array( &$this, 'archive_link' ), 20 );

			// rewrites ajax url
			add_filter( 'admin_url', array( &$this, 'admin_url' ), 10, 2 );
		}

		// redirects to canonical url before WordPress redirect_canonical
		// but after Nextgen Gallery which hacks $_SERVER['REQUEST_URI'] !!! and restores it in 'template_redirect' with priority 1
		add_action( 'template_redirect', array( &$this, 'check_canonical_url' ), 4 );
	}

	/*
	 * modifies the author and date links to add the language parameter ( as well as feed link )
	 *
	 * @since 0.4
	 *
	 * @param string $link
	 * @return string modified link
	 */
	public function archive_link( $link ) {
		return $this->links_model->add_language_to_link( $link, $this->curlang );
	}

	/*
	 * modifies the post type archive links to add the language parameter
	 * only if the post type is translated
	 *
	 * @since 1.7.6
	 *
	 * @param string $link
	 * @param string $post_type
	 * @return string modified link
	 */
	public function post_type_archive_link( $link, $post_type ) {
		return $this->model->is_translated_post_type( $post_type ) ? $this->links_model->add_language_to_link( $link, $this->curlang ) : $link;
	}

	/*
	 * modifies post & page links
	 * caches the result
	 *
	 * @since 0.7
	 *
	 * @param string $link post link
	 * @param object $post post object
	 * @return string modified post link
	 */
	public function post_link( $link, $post ) {
		$cache_key = 'post:' . $post->ID;
		if ( false === $_link = $this->cache->get( $cache_key ) ) {
			$_link = parent::post_link( $link, $post );
			$this->cache->set( $cache_key, $_link );
		}
		return $_link;
	}

	/*
	 * modifies page links
	 * caches the result
	 *
	 * @since 1.7
	 *
	 * @param string $link post link
	 * @param int $post_id post ID
	 * @return string modified post link
	 */
	public function _get_page_link( $link, $post_id ) {
		$cache_key = 'post:' . $post_id;
		if ( false === $_link = $this->cache->get( $cache_key ) ) {
			$_link = parent::_get_page_link( $link, $post_id );
			$this->cache->set( $cache_key, $_link );
		}
		return $_link;
	}

	/*
	 * modifies attachment links
	 * caches the result
	 *
	 * @since 1.6.2
	 *
	 * @param string $link attachment link
	 * @param int $post_id attachment link
	 * @return string modified attachment link
	 */
	public function attachment_link( $link, $post_id ) {
		$cache_key = 'post:' . $post_id;
		if ( false === $_link = $this->cache->get( $cache_key ) ) {
			$_link = parent::attachment_link( $link, $post_id );
			$this->cache->set( $cache_key, $_link );
		}
		return $_link;
	}

	/*
	 * modifies custom posts links
	 * caches the result
	 *
	 * @since 1.6
	 *
	 * @param string $link post link
	 * @param object $post post object
	 * @return string modified post link
	 */
	public function post_type_link( $link, $post ) {
		$cache_key = 'post:' . $post->ID;
		if ( false === $_link = $this->cache->get( $cache_key ) ) {
			$_link = parent::post_type_link( $link, $post );
			$this->cache->set( $cache_key, $_link );
		}
		return $_link;
	}

	/*
	 * modifies filtered taxonomies ( post format like ) and translated taxonomies links
	 * caches the result
	 *
	 * @since 0.7
	 *
	 * @param string $link
	 * @param object $term term object
	 * @param string $tax taxonomy name
	 * @return string modified link
	 */
	public function term_link( $link, $term, $tax ) {
		$cache_key = 'term:' . $term->term_id;
		if ( false === $_link = $this->cache->get( $cache_key ) ) {
			if ( in_array( $tax, $this->model->get_filtered_taxonomies() ) ) {
				$_link = $this->links_model->add_language_to_link( $link, $this->curlang );
				$_link = apply_filters( 'pll_term_link', $_link, $this->curlang, $term );
			}

			else {
				$_link = parent::term_link( $link, $term, $tax );
			}
			$this->cache->set( $cache_key, $_link );
		}
		return $_link;
	}

	/*
	 * outputs references to translated pages ( if exists ) in the html head section
	 *
	 * @since 0.1
	 */
	public function wp_head() {
		// Google recommends to include self link https://support.google.com/webmasters/answer/189077?hl=en
		foreach ( $this->model->get_languages_list() as $language ) {
			if ( $url = $this->links->get_translation_url( $language ) ) {
				$urls[ $language->get_locale( 'display' ) ] = $url;
			}
		}

		// ouptputs the section only if there are translations ( $urls always contains self link )
		// don't output anything on paged archives: see https://wordpress.org/support/topic/hreflang-on-page2
		if ( ! empty( $urls ) && count( $urls ) > 1 && ! is_paged() ) {
			foreach ( $urls as $lang => $url ) {
				printf( '<link rel="alternate" href="%s" hreflang="%s" />'."\n", esc_url( $url ), esc_attr( $lang ) );
			}

			// adds the site root url when the default language code is not hidden
			// see https://wordpress.org/support/topic/implementation-of-hreflangx-default
			if ( is_front_page() && ! $this->options['hide_default'] && $this->options['force_lang'] < 3 ) {
				printf( '<link rel="alternate" href="%s" hreflang="x-default" />'."\n", esc_url( home_url( '/' ) ) );
			}
		}
	}

	/*
	 * filters the home url to get the right language
	 *
	 * @since 0.4
	 *
	 * @param string $url
	 * @param string $path
	 * @return string
	 */
	public function home_url( $url, $path ) {
		if ( ! ( did_action( 'template_redirect' ) || did_action( 'login_init' ) ) || rtrim( $url,'/' ) != $this->links_model->home ) {
			return $url;
		}

		static $white_list, $black_list; // avoid evaluating this at each function call

		// we *want* to filter the home url in these cases
		if ( empty( $white_list ) ) {
			// on Windows get_theme_root() mixes / and \
			// we want only \ for the comparison with debug_backtrace
			$theme_root = get_theme_root();
			$theme_root = ( false === strpos( $theme_root, '\\' ) ) ? $theme_root : str_replace( '/', '\\', $theme_root );

			$white_list = apply_filters( 'pll_home_url_white_list',  array(
				array( 'file' => $theme_root ),
				array( 'function' => 'wp_nav_menu' ),
				array( 'function' => 'login_footer' ),
			) );
		}

		// we don't want to filter the home url in these cases
		if ( empty( $black_list ) ) {
			$black_list = apply_filters( 'pll_home_url_black_list',  array(
				array( 'file' => 'searchform.php' ), // since WP 3.6 searchform.php is passed through get_search_form
				array( 'function' => 'get_search_form' ),
			) );
		}

		$traces = version_compare( PHP_VERSION, '5.2.5', '>=' ) ? debug_backtrace( false ) : debug_backtrace();
		unset( $traces[0], $traces[1], $traces[2] ); // we don't need the last 3 calls (this function + apply_filters)

		foreach ( $traces as $trace ) {
			// black list first
			foreach ( $black_list as $v ) {
				if ( ( isset( $trace['file'], $v['file'] ) && false !== strpos( $trace['file'], $v['file'] ) ) || ( isset( $trace['function'], $v['function'] ) && $trace['function'] == $v['function'] ) ) {
					return $url;
				}
			}

			foreach ( $white_list as $v ) {
				if ( ( isset( $trace['function'], $v['function'] ) && $trace['function'] == $v['function'] ) ||
					( isset( $trace['file'], $v['file'] ) && false !== strpos( $trace['file'], $v['file'] ) && in_array( $trace['function'], array( 'home_url', 'get_home_url', 'bloginfo', 'get_bloginfo' ) ) ) ) {
					$ok = true;
				}
			}
		}

		return empty( $ok ) ? $url : ( empty( $path ) ? rtrim( $this->links->get_home_url( $this->curlang ), '/' ) : $this->links->get_home_url( $this->curlang ) );
	}

	/*
	 * rewrites ajax url when using domains or subdomains
	 *
	 * @since 1.5
	 *
	 * @param string $url admin url with path evaluated by WordPress
	 * @param string $path admin path
	 * @return string
	 */
	public function admin_url( $url, $path ) {
		return 'admin-ajax.php' === $path ? $this->links_model->switch_language_in_link( $url, $this->curlang ) : $url;
	}

	/*
	 * if the language code is not in agreement with the language of the content
	 * redirects incoming links to the proper URL to avoid duplicate content
	 *
	 * @since 0.9.6
	 *
	 * @param string $requested_url optional
	 * @param bool $do_redirect optional, whether to perform the redirection or not
	 * @return string if redirect is not performed
	 */
	public function check_canonical_url( $requested_url = '', $do_redirect = true ) {
		global $wp_query, $post, $is_IIS;

		// don't redirect in same cases as WP
		if ( is_trackback() || is_search() || is_admin() || is_preview() || is_robots() || ( $is_IIS && ! iis7_supports_permalinks() ) ) {
			return;
		}

		// don't redirect mysite.com/?attachment_id= to mysite.com/en/?attachment_id=
		if ( 1 == $this->options['force_lang'] && is_attachment() && isset( $_GET['attachment_id'] ) ) {
			return;
		}

		// if the default language code is not hidden and the static front page url contains the page name
		// the customizer lands here and the code below would redirect to the list of posts
		if ( isset( $_POST['wp_customize'], $_POST['customized'] ) ) {
			return;
		}

		if ( empty( $requested_url ) ) {
			$requested_url  = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		if ( is_single() || is_page() ) {
			if ( isset( $post->ID ) && $this->model->is_translated_post_type( $post->post_type ) ) {
				$language = $this->model->post->get_language( (int) $post->ID );
			}
		}

		elseif ( is_category() || is_tag() || is_tax() ) {
			$obj = $wp_query->get_queried_object();
			if ( $this->model->is_translated_taxonomy( $obj->taxonomy ) ) {
				$language = $this->model->term->get_language( (int) $obj->term_id );
			}
		}

		elseif ( $wp_query->is_posts_page ) {
			$obj = $wp_query->get_queried_object();
			$language = $this->model->post->get_language( (int) $obj->ID );
		}

		if ( empty( $language ) ) {
			$language = $this->curlang;
			$redirect_url = $requested_url;
		}
		else {
			// first get the canonical url evaluated by WP
			$redirect_url = ( ! $redirect_url = redirect_canonical( $requested_url, false ) ) ? $requested_url : $redirect_url;

			// then get the right language code in url
			$redirect_url = $this->options['force_lang'] ?
				$this->links_model->switch_language_in_link( $redirect_url, $language ) :
				$this->links_model->remove_language_from_link( $redirect_url ); // works only for default permalinks
		}

		// allow plugins to change the redirection or even cancel it by setting $redirect_url to false
		$redirect_url = apply_filters( 'pll_check_canonical_url', $redirect_url, $language );

		// the language is not correctly set so let's redirect to the correct url for this object
		if ( $do_redirect && $redirect_url && $requested_url != $redirect_url ) {
			wp_redirect( $redirect_url, 301 );
			exit;
		}

		return $redirect_url;
	}
}
