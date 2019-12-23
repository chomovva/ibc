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
		self::create_main_tables();
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
			'authors' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				first_name varchar(55) DEFAULT "" NOT NULL,
				last_name varchar(55) DEFAULT "" NOT NULL,
				middle_name varchar(55) DEFAULT "" NOT NULL,
				PRIMARY KEY (id) ) %2$s;',
			'publishing_houses' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				name varchar(200) NOT NULL,
				PRIMARY KEY (id) ) %2$s;',
			'publications' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				title varchar(200) NOT NULL,
				publishing_house bigint(20),
				annotation varchar(1000) NOT NULL,
				isbn varchar(15) NOT NULL,
				year bigint(4) NOT NULL,
				publishing_houses bigint(20),
				PRIMARY KEY (id) ) %2$s;',
			'genres' => 'CREATE TABLE %1$s (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				name varchar(200) NOT NULL,
				parent bigint(20) DEFAULT 0 NOT NULL,
				PRIMARY KEY (id) ) %2$s;',
			'relationships' => 'CREATE TABLE %1$s (
				object_id bigint(20) NOT NULL,
				property_id bigint(20) NOT NULL ) %2$s;',
		);
		foreach ( $sql_formats as $key => $format ) {
			$table_name = sprintf(
				'%1$s%2$s_%3$s',
				$wpdb->get_blog_prefix(),
				IBC_SLUG,
				$key
			);
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'") != $table_name ) {
				$charset = $wpdb->get_charset_collate();
				$sql = sprintf( $format, $table_name, $charset );
				dbDelta( $sql );
			}
		}
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
				status varchar(10) NOT NULL,
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
				status varchar(10) NOT NULL,
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




}



