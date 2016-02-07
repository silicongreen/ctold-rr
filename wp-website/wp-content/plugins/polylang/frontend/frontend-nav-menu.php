<?php

/*
 * manages custom menus translations as well as the language switcher menu item on frontend
 *
 * @since 1.2
 */
class PLL_Frontend_Nav_Menu extends PLL_Nav_Menu {
	public $curlang;

	/*
	 * constructor
	 *
	 * @since 1.2
	 */
	public function __construct( &$polylang ) {
		parent::__construct( $polylang );

		$this->curlang = &$polylang->curlang;

		// split the language switcher menu item in several language menu items
		add_filter( 'wp_get_nav_menu_items', array( &$this, 'wp_get_nav_menu_items' ), 20 ); // after the customizer menus
		add_filter( 'wp_nav_menu_objects', array( &$this, 'wp_nav_menu_objects' ) );
		add_filter( 'nav_menu_link_attributes', array( &$this, 'nav_menu_link_attributes' ), 10, 3 );

		// filters menus by language
		add_filter( 'theme_mod_nav_menu_locations', array( $this, 'nav_menu_locations' ), 20 );
		add_filter( 'wp_nav_menu_args', array( &$this, 'wp_nav_menu_args' ) );

		// the customizer
		if ( isset( $_POST['wp_customize'], $_POST['customized'] ) ) {
			add_filter( 'wp_nav_menu_args', array( &$this, 'filter_args_before_customizer' ) );
			add_filter( 'wp_nav_menu_args', array( &$this, 'filter_args_after_customizer' ), 2000 );
		}
	}

	/*
	 * Sort menu items by menu order
	 *
	 * @since 1.7.9
	 *
	 * @param object $a The first object to compare
	 * @param object $b The second object to compare
	 * @return int -1 or 1 if $a is considered to be respectively less than or greater than $b.
	 */
	protected function usort_menu_items( $a, $b ) {
		return ( $a->menu_order < $b->menu_order ) ? -1 : 1;
	}

	/*
	 * splits the one item of backend in several items on frontend
	 * take care to menu_order as it is used later in wp_nav_menu
	 *
	 * @since 1.1.1
	 *
	 * @param array $items menu items
	 * @return array modified items
	 */
	public function wp_get_nav_menu_items( $items ) {
		if ( doing_action( 'customize_register' ) ) { // needed since WP 4.3, doing_action available since WP 3.9
			return $items;
		}

		// the customizer menus does not sort the items and we need them to be sorted before splitting the language switcher
		usort( $items, array( $this, 'usort_menu_items' ) );

		$new_items = array();
		$offset = 0;

		foreach ( $items as $key => $item ) {
			if ( $options = get_post_meta( $item->ID, '_pll_menu_item', true ) ) {
				$i = 0;

				$switcher = new PLL_Switcher;
				$args = array_merge( array( 'raw' => 1 ), $options );
				$the_languages = $switcher->the_languages( PLL()->links, $args );

				foreach ( $the_languages as $lang ) {
					$lang_item = clone $item;
					$lang_item->ID = $lang_item->ID . '-' . $lang['slug']; // a unique ID
					$lang_item->title = $options['show_flags'] && $options['show_names'] ? $lang['flag'].'&nbsp;'.esc_html( $lang['name'] ) : ( $options['show_flags'] ? $lang['flag'] : esc_html( $lang['name'] ) );
					$lang_item->url = $lang['url'];
					$lang_item->lang = $lang['locale']; // save this for use in nav_menu_link_attributes
					$lang_item->classes = $lang['classes'];
					$lang_item->menu_order += $offset + $i++;
					$new_items[] = $lang_item;
				}
				$offset += $i - 1;
			}
			else {
				$item->menu_order += $offset;
				$new_items[] = $item;
			}
		}

		return $new_items;
	}

	/*
	 * returns the ancestors of a menu item
	 *
	 * @since 1.1.1
	 *
	 * @param object $item
	 * @return array ancestors ids
	 */
	public function get_ancestors( $item ) {
		$ids = array();
		$_anc_id = (int) $item->db_id;
		while ( ( $_anc_id = get_post_meta( $_anc_id, '_menu_item_menu_item_parent', true ) ) && ! in_array( $_anc_id, $ids ) ) {
			$ids[] = $_anc_id;
		}
		return $ids;
	}

	/*
	 * removes current-menu and current-menu-ancestor classes to lang switcher when not on the home page
	 *
	 * @since 1.1.1
	 *
	 * @param array $items
	 * @return array modified menu items
	 */
	public function wp_nav_menu_objects( $items ) {
		$r_ids = $k_ids = array();

		foreach ( $items as $item ) {
			if ( ! empty( $item->classes ) && is_array( $item->classes ) ) {
				if ( in_array( 'current-lang', $item->classes ) ) {
					$item->classes = array_diff( $item->classes, array( 'current-menu-item' ) );
					$r_ids = array_merge( $r_ids, $this->get_ancestors( $item ) ); // remove the classes for these ancestors
				}
				elseif ( in_array( 'current-menu-item', $item->classes ) ) {
					$k_ids = array_merge( $k_ids, $this->get_ancestors( $item ) ); // keep the classes for these ancestors
				}
			}
		}

		$r_ids = array_diff( $r_ids, $k_ids );

		foreach ( $items as $item ) {
			if ( ! empty( $item->db_id ) && in_array( $item->db_id, $r_ids ) ) {
				$item->classes = array_diff( $item->classes, array( 'current-menu-ancestor', 'current-menu-parent', 'current_page_parent', 'current_page_ancestor' ) );
			}
		}

		return $items;
	}

	/*
	 * adds hreflang attribute for the language switcher menu items
	 * available since WP3.6
	 *
	 * @since 1.1
	 *
	 * @param array $atts
	 * @return array modified $atts
	 */
	public function nav_menu_link_attributes( $atts, $item, $args ) {
		if ( isset( $item->lang ) ) {
			$atts['hreflang'] = esc_attr( $item->lang );
		}
		return $atts;
	}

	/*
	 * fills the theme nav menus locations with the right menu in the right language
	 * needs to wait for the language to be defined
	 *
	 * @since 1.2
	 *
	 * @param array|bool list of nav menus locations, false if menu locations have not been filled yet
	 * @return array|bool modified list of nav menus locations
	 */
	public function nav_menu_locations( $menus ) {
		if ( is_array( $menus ) && ! empty( $this->curlang ) ) {
			// first get multilingual menu locations from DB
			$theme = get_option( 'stylesheet' );

			foreach ( $menus as $loc => $menu ) {
				$menus[ $loc ] = empty( $this->options['nav_menus'][ $theme ][ $loc ][ $this->curlang->slug ] ) ? 0 : $this->options['nav_menus'][ $theme ][ $loc ][ $this->curlang->slug ];
			}

			// support for theme customizer
			// let's look for multilingual menu locations directly in $_POST as there are not in customizer object
			if ( isset( $_POST['wp_customize'], $_POST['customized'] ) ) {
				$customized = json_decode( wp_unslash( $_POST['customized'] ) );

				if ( is_object( $customized ) ) {
					foreach ( $customized as $key => $c ) {
						if ( false !== strpos( $key, 'nav_menu_locations[' ) ) {
							$loc = substr( trim( $key, ']' ), 19 );
							$infos = $this->explode_location( $loc );
							if ( $infos['lang'] == $this->curlang->slug ) {
								$menus[ $infos['location'] ] = $c;
							}
							elseif ( $this->curlang->slug == $this->options['default_lang'] ) {
								$menus[ $loc ] = $c;
							}
						}
					}
				}
			}
		}
		return $menus;
	}

	/*
	 * attempt to translate the nav menu when it is hardcoded or when no location is defined in wp_nav_menu
	 *
	 * @since 1.7.10
	 *
	 * @param array $args
	 * @return array modified $args
	 */
	public function wp_nav_menu_args( $args ) {
		$theme = get_option( 'stylesheet' );

		if ( empty( $this->curlang ) || empty( $this->options['nav_menus'][ $theme ] ) ) {
			return $args;
		}

		// Get the nav menu based on the requested menu
		$menu = wp_get_nav_menu_object( $args['menu'] );

		// attempt to find a translation of this menu
		// this obviously does not work if the nav menu has no associated theme location
		if ( $menu ) {
			foreach ( $this->options['nav_menus'][ $theme ] as $menus ) {
				if ( in_array( $menu->term_id, $menus ) && ! empty( $menus[ $this->curlang->slug ] ) ) {
					$args['menu'] = $menus[ $this->curlang->slug ];
					return $args;
				}
			}
		}

		// get the first menu that has items and and is in the current language if we still can't find a menu
		if ( ! $menu && ! $args['theme_location'] ) {
			$menus = wp_get_nav_menus();
			foreach ( $menus as $menu_maybe ) {
				if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
					foreach ( $this->options['nav_menus'][ $theme ] as $menus ) {
						if ( in_array( $menu_maybe->term_id, $menus ) && ! empty( $menus[ $this->curlang->slug ] ) ) {
							$args['menu'] = $menus[ $this->curlang->slug ];
							return $args;
						}
					}
				}
			}
		}

		return $args;
	}

	/*
	 * filters the nav menu location before the customizer so that it matches the temporary location in the customizer
	 *
	 * @since 1.8
	 *
	 * @param array $args wp_nav_menu $args
	 * @return array modified $args
	 */
	public function filter_args_before_customizer( $args ) {
		if ( ! empty( $this->curlang ) ) {
			$args['theme_location'] = $this->combine_location( $args['theme_location'], $this->curlang );
		}
		return $args;
	}

	/*
	 * filters the nav menu location after the customizer to get back the true nav menu location for the theme
	 *
	 * @since 1.8
	 *
	 * @param array $args wp_nav_menu $args
	 * @return array modified $args
	 */
	public function filter_args_after_customizer( $args ) {
		$infos = $this->explode_location( $args['theme_location'] );
		$args['theme_location'] = $infos['location'];
		return $args;
	}
}
