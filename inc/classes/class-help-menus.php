<?php
// namespace PMPro_LPV_UI\inc\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class Help_Menus {

	public static function init() {
		// add_action( 'admin_menu', array( __CLASS__, 'test_admin_help_tab' ) );
		add_action( 'admin_head', array( __CLASS__, 'add_help_screen_to_pmpro' ) );
		add_action( 'admin_head', array( __CLASS__, 'add_context_menu_help' ) );
		add_action( 'admin_head', array( __CLASS__, 'add_help_sidebar' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_parent_dashboard_style' ) );
	}

	public static function print_current_screen_in_header() {
		$current_screen = get_current_screen();
		echo '<h2 class="screen">' . $current_screen->id . ' is the current_screen</h2>';
	}
	public static function enqueue_parent_dashboard_style() {

	}

	public static function pbrx_add_help() {
		// We are in the correct screen because we are taking advantage of the load-* action (below)
		$screen = get_current_screen();
		// $screen->remove_help_tabs();
		$screen->add_help_tab(
			array(
				'id'       => 'pbrx-default',
				'title'    => __( 'Default' ),
				'content'  => 'This is where I would provide tabbed help to the user on how everything in my admin panel works. Formatted HTML works fine in here too',
			)
		);
		// add more help tabs as needed with unique id's
		// Help sidebars are optional
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
			'<p><a href="https://wordpress.org/support/" target="_blank">' . _( 'Support Forums' ) . '</a></p>'
		);
	}

	public static function test_admin_help_tab() {
		$test_help_page = add_options_page( __( 'Test Help Tab Page', 'text_domain' ), __( 'Test Help Tab Page', 'text_domain' ), 'manage_options', 'text_domain', 'test_help_admin_page' );

		add_action( 'load-' . $test_help_page, 'admin_add_help_tab' );
	}

	public static function admin_add_help_tab() {
		global $test_help_page;
		$screen = get_current_screen();

		// Add my_help_tab if current screen is My Admin Page
		$screen->add_help_tab(
			array(
				'id'    => 'test_help_tab',
				'title' => __( 'Test Help Tab' ),
				'content'   => '<p>' . __( 'Use this field to describe to the user what text you want on the help tab.' ) . '</p>',
			)
		);
	}

	public static function test_help_admin_page() {
		echo '<h3>test_help_admin_page</h3>';
	}

	// adds a sidebar to the help context menu
	public static function add_help_sidebar() {

		// get the current screen object
		$current_screen = get_current_screen();

		// show only on listing / single post type screens
		if ( $current_screen->base == 'edit' || $current_screen->base == 'post' ) {
			$current_screen->add_help_tab(
				array(
					'id'        => 'penn_sports_book_sample',
					'title'     => __( 'Book Help Tab' ),
					'content'   => '<p>This is a simple help tab, hi </p>',
				)
			);
			// add the help sidebar (outputs a simple list)
			$current_screen->set_help_sidebar(
				'<ul><li>Here is a list item</li><li>Here is a second item</li><li>Final item</li></ul>'
			);
		}
	}

	public static function add_context_menu_help() {

		// get the current screen object
		$screen = get_current_screen();

		// content for help tab
		$content = '<h3>Inline title</h3>';
		$content .= '<p>Inline info text from our function</p>';

		// register our main help tab
		$screen->add_help_tab(
			array(
				'id'        => 'penn_sports_basic_help_tab',
				'title'     => __( 'Penn Sports Help' ),
				'content'   => $content,
			)
		);

		// register our secondary help tab (with a callback instead of content)
		$screen->add_help_tab(
			array(
				'id'        => 'penn_sports_help_tab_callback',
				'title'     => __( 'Penn Sports Help With Callback' ),
				'callback'  => array( __CLASS__, 'display_help_tab' ),
			)
		);
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
			'<p><a href="https://wordpress.org/support/" target="_blank">' . _( 'Support Forums' ) . '</a></p>'
		);
	}

	// function used to display the second help tab
	public static function display_help_tab() {
		$content = '<h3>Callback title</h3>';
		$content .= '<p>Callback info text from our output function</p>';
		echo $content;
	}
	public static function display_help_tab1() {
		$content = '<p>This is text from our output function</p>';
		echo $content;
	}
	public static function display_help_tab2() {
		$content = '<p>This is text from our output function</p>';
		echo $content;
	}

	public static function add_help_screen_to_pmpro() {
		// get the current screen object
		$current_screen = get_current_screen();

		// show only on book listing page
		if ( 'toplevel_page_pmpro-membershiplevels' === $current_screen->id ) {
			$content = '';
			$content .= '<p>This is a help tab, you can add <strong><em>whatever</em></strong> it is you like here, such as instructions</p>';
			$current_screen->add_help_tab(
				array(
					'id'        => 'penn_sports_book_help_tab',
					'title'     => __( 'PMPro Help Tab' ),
					'content'   => $content,
				)
			);
		}
	}

}
