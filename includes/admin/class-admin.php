<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; };



class Admin {


	use Departments;
	use Publications;
	use Issuances;
	use Readers;
	use Copies;
	use Controls;



	protected static $instance;



	public static function init() {
		is_null( self::$instance ) AND self::$instance = new self;
		return self::$instance;
	}
	


	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		add_action( 'admin_init', array( $this, 'admin_tables' ) );
		if ( is_network_admin() ) {
			add_action( 'network_admin_menu', array( $this, 'add_mu_pages' ) );
		}
	}



	public function add_mu_pages() {
		$publications_hook = add_menu_page( __( 'Публикации', IBC_TEXTDOMAIN ), __( 'Публикации', IBC_TEXTDOMAIN ), 'manage_options', 'publications', array( $this, 'render_mu_pages' ), 'dashicons-book-alt', '6' );
		$genres_hook = add_submenu_page( 'publications', __( 'Жанры', IBC_TEXTDOMAIN ), __( 'Жанры', IBC_TEXTDOMAIN ), 'manage_options', 'genres', array( $this, 'render_mu_pages' ) );
		$authors_hook = add_submenu_page( 'publications', __( 'Авторы', IBC_TEXTDOMAIN ), __( 'Авторы', IBC_TEXTDOMAIN ), 'manage_options', 'authors', array( $this, 'render_mu_pages' ) );
		$publishing_houses_hook = add_submenu_page( 'publications', __( 'Издательства', IBC_TEXTDOMAIN ), __( 'Издательства', IBC_TEXTDOMAIN ), 'manage_options', 'publishing_houses', array( $this, 'render_mu_pages' ) );
		add_action( "load-$publications_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-publications.php';
			$GLOBALS[ 'Publications_List_Table' ] = new Publications_List_Table();
		} );
		add_action( "load-$genres_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-genres.php';
			$GLOBALS[ 'Genres_List_Table' ] = new Genres_List_Table();
		} );
		add_action( "load-$authors_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-authors.php';
			$GLOBALS[ 'Authors_List_Table' ] = new Authors_List_Table();
		} );
		add_action( "load-$publishing_houses_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-publishing-houses.php';
			$GLOBALS[ 'Publishing_Houses_List_Table' ] = new Publishing_Houses_List_Table();
		} );
	}



	public function render_mu_pages() {
		$screen = get_current_screen();
		wp_enqueue_style( 'flexboxgrid' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'select2' );
		wp_add_inline_script( 'select2', "jQuery( '.select2' ).select2();", 'after' );
		if ( preg_match( '/toplevel_page_publications-network$/', $screen->id ) ) {
			$action = 'publications';
			$nonce = wp_create_nonce( 'publications' );
			include IBC_VIEWS . 'tables/publications.php';
		} elseif ( preg_match( '/genres-network$/', $screen->id ) ) {
			$action = 'genres';
			$nonce = wp_create_nonce( 'genres' );
			include IBC_VIEWS . 'tables/genres.php';
		} elseif ( preg_match( '/authors-network$/', $screen->id ) ) {
			$action = 'authors';
			$nonce = wp_create_nonce( 'authors' );
			include IBC_VIEWS . 'tables/authors.php';
		} elseif ( preg_match( '/publishing_houses-network$/', $screen->id ) ) {
			$action = 'publishing_houses';
			$nonce = wp_create_nonce( 'publishing_houses' );
			include IBC_VIEWS . 'tables/publishing-houses.php';
		}
	}



	public function admin_tables() {
		require_once IBC_INCLUDES . 'admin/class-admin-table.php';
	}


	public function add_admin_pages() {
		$readers_hook = add_menu_page( __( 'Читатели', IBC_TEXTDOMAIN ), __( 'Читатели', IBC_TEXTDOMAIN ), 'manage_options', 'readers', array( $this, 'render_page' ), 'dashicons-groups', 34 );
		$departments_hook = add_submenu_page( 'readers', __( 'Подразделенния', IBC_TEXTDOMAIN ), __( 'Подразделенния', IBC_TEXTDOMAIN ), 'manage_options', 'departments', array( $this, 'render_page' ) );
		$issuances_hook = add_menu_page( __( 'Выдачи', IBC_TEXTDOMAIN ), __( 'Выдачи', IBC_TEXTDOMAIN ), 'manage_options', 'issuances', array( $this, 'render_page' ), 'dashicons-download', 36 );
		// $copies_hook = add_menu_page( __( 'Фонды', IBC_TEXTDOMAIN ), __( 'Фонды', IBC_TEXTDOMAIN ), 'manage_options', 'copies', array( $this, 'render_page' ), 'dashicons-welcome-add-page', 35 );
		// $add_issuances_hook = add_submenu_page( 'issuances', __( 'Оформление выдачи', IBC_TEXTDOMAIN ), __( 'Оформление выдачи', IBC_TEXTDOMAIN ), 'manage_options', 'add_issuances', array( $this, 'render_page' ) );
		// add_action( "load-$copies_hook", array( $this, 'copies_table_load' ) );
		// add_action( "load-$readers_hook", array( $this, 'readers_table_load' ) );
		// add_action( "load-$issuances_hook", array( $this, 'issuances_table_load' ) );
		add_action( "load-$departments_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-departments.php';
			$GLOBALS[ 'Departments_List_Table' ] = new Departments_List_Table();
		} );
		add_action( "load-$readers_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-readers.php';
			$GLOBALS[ 'Readers_List_Table' ] = new Readers_List_Table();
		} );
		add_action( "load-$issuances_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-issuances.php';
			$GLOBALS[ 'Issuances_List_Table' ] = new Issuances_List_Table();
		} );
	}




	public function render_page() {
		$screen = get_current_screen();
		wp_enqueue_style( 'flexboxgrid' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'select2' );
		wp_add_inline_script( 'select2', "jQuery( '.select2' ).select2();", 'after' );
		// $page_title = get_admin_page_title();
		// $content = __return_empty_string();
		
		// 	$content = $this->readers_table_render();
		// } elseif ( preg_match( '/toplevel_page_copies$/', $screen->id ) ) {
		// 	$content = $this->copies_table_render();
		// } elseif ( preg_match( '/page_add_copies$/', $screen->id ) ) {
		// 	// добавление книги
		// } elseif ( preg_match( '/settings_page_ibc$/', $screen->id ) ) {
		// 	ob_start();
		// 	settings_fields( IBC_SLUG );
		// 	do_settings_sections( IBC_SLUG );
		// 	submit_button();
		// 	$content = ob_get_contents();
		// 	ob_end_clean();
		if ( preg_match( '/toplevel_page_readers$/', $screen->id ) ) {
			$action = 'readers';
			$nonce = wp_create_nonce( 'readers' );
			include IBC_VIEWS . 'tables/readers.php';
		} elseif ( preg_match( '/page_departments$/', $screen->id ) ) {
			$action = 'departments';
			$nonce = wp_create_nonce( 'departments' );
			include IBC_VIEWS . 'tables/departments.php';
		} elseif ( preg_match( '/issuances$/', $screen->id ) ) {
			$action = 'issuances';
			$nonce = wp_create_nonce( 'issuances' );
			include IBC_VIEWS . 'tables/issuances.php';
		}
	}






}