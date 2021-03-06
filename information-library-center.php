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


	protected $db;


	public static function init() {
		is_null( self::$instance ) AND self::$instance = new self;
		return self::$instance;
	}



	public static function on_activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) return;
		$plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
		$sites = get_sites();
		if ( is_array( $sites ) ) {
			foreach ( $sites as $site ) {
				$this->add_role( $site );
			}
		}
	}




	public static function on_deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) ) return;
		$plugin = isset( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );
		$sites = get_sites();
		if ( is_array( $sites ) ) {
			foreach ( $sites as $site ) {
				$this->remove_roles( $site );
			}
		}
	}



	public static function on_uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) return;
		check_admin_referer( 'bulk-plugins' );
		// Важно: проверим тот ли это файл, который
		// был зарегистрирован во время удаления плагина.
		if ( __FILE__ != WP_UNINSTALL_PLUGIN ) return;
		// Расcкомментируйте эту строку, чтобы увидеть функцию в действии
		// exit( var_dump( $_GET ) );
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



	protected function connect_db() {
		$db_options = get_network_option( NULL, 'ibc_db', false );
		if ( $db_options ) {
			$db = new \wpdb(
				$db_options[ 'user' ],
				$db_options[ 'password' ],
				$db_options[ 'name' ],
				$db_options[ 'host' ]
			);
			if ( empty( $db->error ) ) {
				$result = &$db;
			} else {
				$result = new WP_Error( 'ibc_db', $db->error );
			}
		} else {
			$result = new WP_Error( 'ibc_db', __( 'Заполните настройки БД', IBC_TEXTDOMAIN ) );
		}
		return $result;
	}




	public function __construct() {
		add_action( 'network_admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'plugin_settings' ) );
		$db = $this->connect_db();
		if ( ! is_wp_error( $db ) ) {
			require_once IBC_INCLUDES . 'traits/trait-publishing-houses.php';
			require_once IBC_INCLUDES . 'traits/trait-publications.php';
			require_once IBC_INCLUDES . 'traits/trait-departments.php';
			require_once IBC_INCLUDES . 'traits/trait-issuances.php';
			require_once IBC_INCLUDES . 'traits/trait-controls.php';
			require_once IBC_INCLUDES . 'traits/trait-readers.php';
			require_once IBC_INCLUDES . 'traits/trait-authors.php';
			require_once IBC_INCLUDES . 'traits/trait-genres.php';
			require_once IBC_INCLUDES . 'traits/trait-copies.php';
			add_action( 'wp_insert_site', array( $this, 'create_tables' ), 10, 1 );
			add_action( 'wp_insert_site', array( $this, 'add_roles' ), 10, 1 );
			add_action( 'plugins_loaded', array( $this, 'textdomain' ) );
			if ( is_admin() ) {
				if ( wp_doing_ajax() ) {
					require_once IBC_INCLUDES . 'ajax/class-ajax.php';
					new Ajax( $db );
				} else {
					require_once IBC_INCLUDES . 'admin/class-admin.php';
					add_action( 'admin_enqueue_scripts', array( $this, 'register_enqueue' ), 10, 0 );
					new Admin( $db );
				}
			} else {
				add_action( 'wp_enqueue_scripts', array( $this, 'register_enqueue' ), 10, 0 );
			}
		}
	}



	public function add_plugin_page() {
		add_menu_page(
			__( 'ИБЦ', IBC_TEXTDOMAIN ),
			__( 'ИБЦ', IBC_TEXTDOMAIN ),
			'manage_options',
			IBC_SLUG,
			array( $this, 'options_page_html' ),
			IBC_ASSETS . 'images/library.svg',
			null
		);
	}



	public function options_page_html(){
		?>
			<div class="wrap">
				<h2><?php echo get_admin_page_title() ?></h2>
				<form method="POST" action="edit.php?action=<?php echo IBC_SLUG; ?>">
					<?php
						wp_nonce_field( IBC_SLUG ); // NETWORK - settings_fields() не подходит для мультисайта...
						do_settings_sections( IBC_SLUG ); // секции с настройками (опциями). У нас она всего одна 'section_id'
						submit_button( __( 'Создать таблицы и сохранить', IBC_TEXTDOMAIN ) );
					?>
				</form>
			</div>
		<?php
	}



	public function plugin_settings() {
		// NETWORK - ловим обновления опций через хук 'network_admin_edit_(action)'
		if ( is_multisite() ) {
			add_action( 'network_admin_edit_' . IBC_SLUG, array( $this, 'options_update' ) );
		}
		register_setting( IBC_SLUG, IBC_SLUG, array( $this, 'sanitize_callback' ) );
		add_settings_section( 'ibc_db', __( 'Настройки базы данных', IBC_TEXTDOMAIN ), '', IBC_SLUG );
		$opt_name = 'user';
		add_settings_field( $opt_name, __( 'Пользоваель', IBC_TEXTDOMAIN ), array( $this, 'fill_field' ), IBC_SLUG, 'ibc_db', $opt_name );
		$opt_name = 'password';
		add_settings_field( $opt_name, __( 'Пароль', IBC_TEXTDOMAIN ), array( $this, 'fill_field' ), IBC_SLUG, 'ibc_db', $opt_name );
		$opt_name = 'name';
		add_settings_field( $opt_name, __( 'Имя БД', IBC_TEXTDOMAIN ), array( $this, 'fill_field' ), IBC_SLUG, 'ibc_db', $opt_name );
		$opt_name = 'host';
		add_settings_field( $opt_name, __( 'Хост БД', IBC_TEXTDOMAIN ), array( $this, 'fill_field' ), IBC_SLUG, 'ibc_db', $opt_name );
	}




	## Заполняем опцию 1
	function fill_field( $opt_name ){
		$opts      = get_site_option( 'ibc_db' ); // NETWORK - не get_option()
		$name_attr = "ibc_db[$opt_name]";
		$val       = isset( $opts[ $opt_name ] ) ? $opts[ $opt_name ] : null;
		echo '<input type="text" name="'. $name_attr .'" value="'. esc_attr( $val ) .'" />';
	}




	## Очистка сохраняемых данных
	function sanitize_callback( $options ){
		foreach( $options as $name => & $value ) {
			$value = sanitize_text_field( $value );
		}
		return $options;
	}



	## NETWORK - обновляем опции в БД
	function options_update() {
		check_admin_referer( IBC_SLUG );
		$options = wp_parse_args( wp_unslash( $_POST[ 'ibc_db' ] ), array(
			'user'     => '',
			'password' => '',
			'name'     => '',
			'host'     => '',
		) );
		$connect = __return_false();
		error_reporting( 0 );
		$db = @\mysqli_connect( $options[ 'host' ], $options[ 'user' ], $options[ 'password' ], $options[ 'name' ] );
		if ( $db ) {
			update_site_option( 'ibc_db', $options );
			\mysqli_multi_query( $db, file_get_contents( __DIR__ . '/db/structure.sql' ) );
			mysqli_close( $db );
		}
		wp_redirect( network_admin_url( sprintf(
			'settings.php?page=%1$s&updated=true',
			IBC_SLUG,
			( $connect ) ? 'true' : 'false'
		) ) );
		exit;
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