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
	}


	public function admin_tables() {
		require_once IBC_INCLUDES . 'admin/class-admin-table.php';
	}


	public function add_admin_pages() {
		$readers_hook = add_menu_page( __( 'Читатели', IBC_TEXTDOMAIN ), __( 'Читатели', IBC_TEXTDOMAIN ), 'manage_options', 'readers', array( $this, 'render_page' ), 'dashicons-groups', 34 );
		$departments_hook = add_submenu_page( 'readers', __( 'Подразделенния', IBC_TEXTDOMAIN ), __( 'Подразделенния', IBC_TEXTDOMAIN ), 'manage_options', 'departments', array( $this, 'render_page' ) );
		$issuances_hook = add_menu_page( __( 'Выдачи', IBC_TEXTDOMAIN ), __( 'Выдачи', IBC_TEXTDOMAIN ), 'manage_options', 'issuances', array( $this, 'render_page' ), 'dashicons-download', 36 );
		// $add_departments_hook = add_submenu_page( 'readers', __( 'Добавить подразделение', IBC_TEXTDOMAIN ), __( 'Добавить подразделение', IBC_TEXTDOMAIN ), 'manage_options', 'add_departments', array( $this, 'render_page' ) );
		// $copies_hook = add_menu_page( __( 'Фонды', IBC_TEXTDOMAIN ), __( 'Фонды', IBC_TEXTDOMAIN ), 'manage_options', 'copies', array( $this, 'render_page' ), 'dashicons-welcome-add-page', 35 );
		// $add_copies_hook = add_submenu_page( 'copies', __( 'Добавить книгу', IBC_TEXTDOMAIN ), __( 'Добавить книгу', IBC_TEXTDOMAIN ), 'manage_options', 'add_copies', array( $this, 'render_page' ) );
		// $add_issuances_hook = add_submenu_page( 'issuances', __( 'Оформление выдачи', IBC_TEXTDOMAIN ), __( 'Оформление выдачи', IBC_TEXTDOMAIN ), 'manage_options', 'add_issuances', array( $this, 'render_page' ) );
		// $add_reader_hook = add_submenu_page( 'readers', __( 'Добавить читателя', IBC_TEXTDOMAIN ), __( 'Добавить читателя', IBC_TEXTDOMAIN ), 'manage_options', 'add_reader', array( $this, 'render_page' ) );
		// add_options_page( __( 'ИБЦ', IBC_TEXTDOMAIN ), __( 'ИБЦ', IBC_TEXTDOMAIN ), 'manage_options', IBC_SLUG, array( $this, 'render_page' ) );
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
		// } elseif ( preg_match( '/page_add_reader$/', $screen->id ) ) {
		// 	$content = $this->readers_form_render();
		// } 
		// 	$content = $this->departments_table_render();
		// } elseif ( preg_match( '/page_add_departments$/', $screen->id ) ) {
		// 	// добавление контента страницы (форма)
		// } elseif ( preg_match( '/issuances$/', $screen->id ) ) {
		// 	$content = $this->issuances_form_render();
		// } elseif ( preg_match( '/add_issuances$/', $screen->id ) ) {
		// 	// добавление контента страницы (форма)
		// }
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