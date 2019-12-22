<?php



/**
Plugin Name: Информационно-библиотечный центр
Plugin URI: https://chomovva.ru/
Description: Плагин для создания информационно-библиотечного центра школьных библиотек
Author: chomovva
Version: 0.0.1
Author URI: https://chomovva.ru/
License: GPL2
Text Domain: ibc
Domain Path: /languages/
Network: TRUE
*/



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) {	exit; };



define( 'IBC_INCLUDES', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/' );
define( 'IBC_ASSETS', untrailingslashit( plugin_dir_url(__FILE__) ) . '/assets/' );
define( 'IBC_LANGUAGES', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
define( 'IBC_VIEWS', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/views/' );
define( 'IBC_TEXTDOMAIN', 'ibc' );
define( 'IBC_SLUG', 'ibc' );



register_activation_hook( __FILE__, array( 'ibc\Setup', 'on_activation' ) );
register_deactivation_hook( __FILE__, array( 'ibc\Setup', 'on_deactivation' ) );
register_uninstall_hook( __FILE__, array( 'ibc\Setup', 'on_uninstall' ) );



add_action( 'network_plugin_loaded', array( 'ibc\Setup', 'init' ) );







class Setup {



	protected static $instance;



	public static function init() {
		is_null( self::$instance ) AND self::$instance = new self;
		return self::$instance;
	}



	public static function on_activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) return;
		$plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
		$sites = get_sites();
		$this->create_main_tables();
		if ( is_array( $sites ) && ! empty( $sites ) ) {
			foreach ( $sites as $site ) {
				self::add_roles( $site );
				self::create_sites_tables( $site );
			}
		}
	}




	public static function on_deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) ) return;
		$plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );
		$sites = get_sites();
		if ( is_array( $sites ) && ! empty( $sites ) ) {
			foreach ( $sites as $site ) {
				self::remove_roles( $site );
			}
		}
	}



	public static function on_uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) return;
		check_admin_referer( 'bulk-plugins' );
		if ( __FILE__ != WP_UNINSTALL_PLUGIN ) return;
	}


	protected static function add_roles( $site ) {
		switch_to_blog( $site->blog_id );
		add_role( 'librarian', __( 'Библиотекарь', IBC_TEXTDOMAIN ), array(
			'read'  => true,
		) );
		restore_current_blog();
	}


	protected static function remove_roles( $site ) {
		switch_to_blog( $site->blog_id );
		remove_role( 'librarian' );
		restore_current_blog();
	}



	protected static function create_main_tables() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$sql_formats = array(
			'authors' => '',
			'phouses' => '',
			'publications' => '',
			'genres' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				name varchar(200) NOT NULL,
				parent bigint(20) DEFAULT 0 NOT NULL,
				PRIMARY KEY (id) ) %2$s;',
		);
	}



	protected static function create_sites_tables( $site ) {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$sql_formats = array(
			'copies' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				book bigint(20) NOT NULL,
				date_added datetime NOT NULL,
				librarian bigint(20) NOT NULL,
				registration_number varchar(55) DEFAULT "" NOT NULL,
				PRIMARY KEY (id) ) %2$s;',
			'issuances' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				copy bigint(20) NOT NULL,
				reader bigint(20),
				librarian bigint(20),
				clearance_date datetime NOT NULL,
				return_date datetime NOT NULL,
				PRIMARY KEY (id) ) %2$s;',
			'departments' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				name varchar(200) NOT NULL,
				parent bigint(20) DEFAULT 0 NOT NULL,
				PRIMARY KEY (id) ) %2$s;',
			'readers' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				card_id varchar(40) NOT NULL,
				first_name varchar(55) DEFAULT "" NOT NULL,
				last_name varchar(55) DEFAULT "" NOT NULL,
				sex varchar(20) DEFAULT "" NOT NULL,
				date_added datetime NOT NULL,
				librarian bigint(20) NOT NULL,
				department bigint(20),
				PRIMARY KEY (id) ) %2$s;',
		);
		foreach ( $sql_formats as $key => $format ) {
			switch_to_blog( $site->blog_id );
			$table_name = sprintf(
				'%1$s%2$s_%3$s',
				$wpdb->get_blog_prefix( $site->blog_id ),
				IBC_SLUG,
				$key
			);
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'") != $table_name ) {
				$charset = $wpdb->get_charset_collate();
				$sql = sprintf( $format, $table_name, $charset );
				dbDelta( $sql );
			}
			restore_current_blog();
		}
	}



	public function __construct() {
		require_once IBC_INCLUDES . 'traits/trait-publications.php';
		require_once IBC_INCLUDES . 'traits/trait-departments.php';
		require_once IBC_INCLUDES . 'traits/trait-issuances.php';
		require_once IBC_INCLUDES . 'traits/trait-controls.php';
		require_once IBC_INCLUDES . 'traits/trait-readers.php';
		require_once IBC_INCLUDES . 'traits/trait-genres.php';
		require_once IBC_INCLUDES . 'traits/trait-copies.php';
		add_action( 'wp_insert_site', array( $this, 'create_tables' ), 10, 1 );
		add_action( 'wp_insert_site', array( $this, 'add_roles' ), 10, 1 );
		add_action( 'plugins_loaded', array( $this, 'textdomain' ) );
		add_action( 'init', array( $this, 'register_objects' ) );
		if ( is_admin() ) {
			if ( wp_doing_ajax() ) {
				require_once IBC_INCLUDES . 'ajax/class-ajax.php';
				new Ajax();
			} else {
				require_once IBC_INCLUDES . 'admin/class-admin.php';
				add_action( 'admin_enqueue_scripts', array( $this, 'register_enqueue' ), 10, 0 );
				new Admin();
			}
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'register_enqueue' ), 10, 0 );
		}
	}



	public function register_enqueue() {
		wp_register_script( 'jquery.mask', IBC_ASSETS . 'scripts/jquery.mask.js', array( 'jquery' ), '1.14.16', true );
		wp_register_script( 'select2', IBC_ASSETS . 'scripts/select2.full.js', array( 'jquery' ), '0.12.6', true );
		wp_register_style( 'select2', IBC_ASSETS . 'styles/select2.css', array(), '0.12.6', 'all' );
		wp_register_style( 'flexboxgrid', IBC_ASSETS . 'styles/flexboxgrid.css', array(), '6.3.2', 'all' );
	}



	public function textdomain() {
		load_plugin_textdomain( IBC_TEXTDOMAIN, false, IBC_LANGUAGES );
	}



	function register_objects() {
		$publication_args = array(
			'labels'             => array(
				'name'               => __( 'Публикации', IBC_TEXTDOMAIN ),
				'singular_name'      => __( 'Публикация', IBC_TEXTDOMAIN ),
				'add_new'            => __( 'Добавить новую', IBC_TEXTDOMAIN ),
				'add_new_item'       => __( 'Добавить новую публикацию', IBC_TEXTDOMAIN ),
				'edit_item'          => __( 'Редактировать публикацию', IBC_TEXTDOMAIN ),
				'new_item'           => __( 'Новая публикация', IBC_TEXTDOMAIN ),
				'view_item'          => __( 'Посмотреть публикацию', IBC_TEXTDOMAIN ),
				'search_items'       => __( 'Найти публикацию', IBC_TEXTDOMAIN ),
				'not_found'          => __( 'Публикаций не найдено', IBC_TEXTDOMAIN ),
				'not_found_in_trash' => __( 'В корзине публикаций не найдено', IBC_TEXTDOMAIN ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Публикации', IBC_TEXTDOMAIN ),

			  ),
			'public'                 => true,
			'publicly_queryable'     => true,
			'show_ui'                => true,
			'show_in_menu'           => true,
			'query_var'              => true,
			'rewrite'                => true,
			'capability_type'        => 'post',
			'has_archive'            => true,
			'hierarchical'           => false,
			'menu_position'          => null,
			'menu_icon'              => 'dashicons-book-alt',
			'supports'               => array( 'title', 'editor', 'thumbnail' )
		);
		$genre_args = array( 
			'label'                 => '', // определяется параметром $labels->name
			'labels'                => array(
				'name'              => __( 'Жанр', IBC_TEXTDOMAIN ),
				'singular_name'     => __( 'Жанр', IBC_TEXTDOMAIN ),
				'search_items'      => __( 'Искать жанр', IBC_TEXTDOMAIN ),
				'all_items'         => __( 'Все жанры', IBC_TEXTDOMAIN ),
				'view_item '        => __( 'Редактировать жанр', IBC_TEXTDOMAIN ),
				'parent_item'       => __( 'Родительский жанр', IBC_TEXTDOMAIN ),
				'parent_item_colon' => __( 'Родительский жанр:', IBC_TEXTDOMAIN ),
				'edit_item'         => __( 'Редактировать жанр', IBC_TEXTDOMAIN ),
				'update_item'       => __( 'Обновить запись', IBC_TEXTDOMAIN ),
				'add_new_item'      => __( 'Добавить новый жанр', IBC_TEXTDOMAIN ),
				'new_item_name'     => __( 'Новый жанр', IBC_TEXTDOMAIN ),
				'menu_name'         => __( 'Жанр', IBC_TEXTDOMAIN ),
			),
			'description'           => __( 'Записи с уровнем вложения выше второго не учитываются.', IBC_TEXTDOMAIN ), // описание таксономии
			'public'                => true,
			'publicly_queryable'    => null, // равен аргументу public
			'show_in_nav_menus'     => true, // равен аргументу public
			'show_ui'               => true, // равен аргументу public
			'show_in_menu'          => true, // равен аргументу show_ui
			'show_tagcloud'         => true, // равен аргументу show_ui
			'show_in_quick_edit'    => null, // равен аргументу show_ui
			'hierarchical'          => true,
			'rewrite'               => true,
			'capabilities'          => array(),
			'meta_box_cb'           => false,
			'show_admin_column'     => false, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'          => null, // добавить в REST API
			'rest_base'             => null, // $taxonomy
		);
		$autors_args = array( 
			'label'                 => '', // определяется параметром $labels->name
			'labels'                => array(
				'name'              => __( 'Авторы', IBC_TEXTDOMAIN ),
				'singular_name'     => __( 'Автор', IBC_TEXTDOMAIN ),
				'search_items'      => __( 'Искать автора', IBC_TEXTDOMAIN ),
				'all_items'         => __( 'Все авторы', IBC_TEXTDOMAIN ),
				'view_item '        => __( 'Редактировать запись', IBC_TEXTDOMAIN ),
				'parent_item'       => __( 'Родительская запись', IBC_TEXTDOMAIN ),
				'parent_item_colon' => __( 'Родительская запись:', IBC_TEXTDOMAIN ),
				'edit_item'         => __( 'Редактировать автора', IBC_TEXTDOMAIN ),
				'update_item'       => __( 'Обновить запись', IBC_TEXTDOMAIN ),
				'add_new_item'      => __( 'Добавить новыго автора', IBC_TEXTDOMAIN ),
				'new_item_name'     => __( 'Новый автор', IBC_TEXTDOMAIN ),
				'menu_name'         => __( 'Авторы', IBC_TEXTDOMAIN ),
			),
			'description'           => '', // описание таксономии
			'public'                => true,
			'publicly_queryable'    => null, // равен аргументу public
			'show_in_nav_menus'     => true, // равен аргументу public
			'show_ui'               => true, // равен аргументу public
			'show_in_menu'          => true, // равен аргументу show_ui
			'show_tagcloud'         => true, // равен аргументу show_ui
			'show_in_quick_edit'    => null, // равен аргументу show_ui
			'hierarchical'          => false,
			'rewrite'               => true,
			'capabilities'          => array(),
			'meta_box_cb'           => false,
			'show_admin_column'     => false, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'          => null, // добавить в REST API
			'rest_base'             => null, // $taxonomy
		);
		$publishing_houses_args = array( 
			'label'                 => '', // определяется параметром $labels->name
			'labels'                => array(
				'name'              => __( 'Здательства', IBC_TEXTDOMAIN ),
				'singular_name'     => __( 'Издательствл', IBC_TEXTDOMAIN ),
				'search_items'      => __( 'Искать издательство', IBC_TEXTDOMAIN ),
				'all_items'         => __( 'Все издательства', IBC_TEXTDOMAIN ),
				'view_item '        => __( 'Редактировать запись', IBC_TEXTDOMAIN ),
				'parent_item'       => __( 'Родительская запись', IBC_TEXTDOMAIN ),
				'parent_item_colon' => __( 'Родительская запись:', IBC_TEXTDOMAIN ),
				'edit_item'         => __( 'Редактировать запись', IBC_TEXTDOMAIN ),
				'update_item'       => __( 'Обновить запись', IBC_TEXTDOMAIN ),
				'add_new_item'      => __( 'Добавить новое издательство', IBC_TEXTDOMAIN ),
				'new_item_name'     => __( 'Новое издательство', IBC_TEXTDOMAIN ),
				'menu_name'         => __( 'Издательства', IBC_TEXTDOMAIN ),
			),
			'description'           => '', // описание таксономии
			'public'                => true,
			'publicly_queryable'    => null, // равен аргументу public
			'show_in_nav_menus'     => true, // равен аргументу public
			'show_ui'               => true, // равен аргументу public
			'show_in_menu'          => true, // равен аргументу show_ui
			'show_tagcloud'         => true, // равен аргументу show_ui
			'show_in_quick_edit'    => null, // равен аргументу show_ui
			'hierarchical'          => false,
			'rewrite'               => true,
			'capabilities'          => array(),
			'meta_box_cb'           => false,
			'show_admin_column'     => false, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
			'show_in_rest'          => null, // добавить в REST API
			'rest_base'             => null, // $taxonomy
		);
		$book_args = array(
			'public'                 => true,
			'publicly_queryable'     => true,
			'show_ui'                => true,
			'show_in_menu'           => true,
			'query_var'              => true,
		);
		register_post_type( "publication", $publication_args );
		register_taxonomy( "genre", array( "publication" ), $genre_args );
		register_taxonomy( "authors", array( "publication" ), $autors_args );
		register_taxonomy( "publishing_houses", array( "publication" ), $publishing_houses_args );
	}



}



