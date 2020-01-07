<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; };



class Admin {


	use Departments;
	use Publications;
	use Issuances;
	use Readers;
	use Genres;
	use Copies;
	use Controls;
	use PublishingHouses;
	use Authors;



	protected static $instance;



	protected $db;



	public static function init() {
		is_null( self::$instance ) AND self::$instance = new self;
		return self::$instance;
	}
	


	function __construct( $db ) {
		$this->db = $db;
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		add_action( 'admin_init', array( $this, 'admin_tables' ) );
		add_action( 'current_screen', array( $this, 'operations' ) );
		if ( is_network_admin() ) {
			add_action( 'network_admin_menu', array( $this, 'add_mu_pages' ) );
		}
	}



	public function operations() {
		if ( ! isset( $_REQUEST[ 'page' ] ) || ! isset( $_REQUEST[ 'action' ] ) ) return;
		switch ( $_REQUEST[ 'page' ] ) {
			case 'publishing_houses':
				if ( 'delete' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'id' ] ) && ! empty( $_REQUEST[ 'id' ] ) ) {
					$result = $this->delete_publishing_house( $_REQUEST[ 'id' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : __( 'Издательство удалено', IBC_TEXTDOMAIN );
					wp_redirect( $this->get_publishing_house_link( array( 'action' => 'table', 'notice' => $notice ) ) );
					exit;
				} elseif ( 'add' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'name' ] ) && ! empty( trim( $_REQUEST[ 'name' ] ) ) ) {
					$result = $this->add_publishing_house( $_REQUEST[ 'name' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : sprintf(
						__( 'Новое издательство "%1$s" добавлено. <a href="%2$s">Редактировать "%1$s"</a>', IBC_TEXTDOMAIN ),
						$_REQUEST[ 'name' ],
						$this->get_publishing_house_link( array( 'action' => 'edit', 'id' => $result ) )
					);
					wp_redirect( $this->get_publishing_house_link( array( 'action' => 'add', 'notice' => $notice ) ) );
					exit;
				} elseif ( 'edit' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'name' ] ) && isset( $_REQUEST[ 'id' ] ) && ! empty( $_REQUEST[ 'id' ] ) ) {
					$result = $this->update_publishing_house( $_REQUEST[ 'name' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : __( 'Издательство обновлено.', IBC_TEXTDOMAIN );
					wp_redirect( $this->get_publishing_house_link( array( 'action' => 'edit', 'notice' => $notice, 'id' => $result ) ) );
					exit;
				}
				break;
			case 'authors':
				if ( 'delete' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'id' ] ) && ! empty( $_REQUEST[ 'id' ] ) ) {
					$result = $this->delete_author( $_REQUEST[ 'id' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : __( 'Автор удалён', IBC_TEXTDOMAIN );
					wp_redirect( $this->get_author_link( array( 'action' => 'table', 'notice' => $notice ) ) );
					exit;
				} elseif ( 'add' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'first_name' ] ) && isset( $_REQUEST[ 'last_name' ] ) && isset( $_REQUEST[ 'middle_name' ] ) ) {
					$result = $this->add_author( $_REQUEST[ 'first_name' ], $_REQUEST[ 'last_name' ], $_REQUEST[ 'middle_name' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : sprintf(
						__( 'Автор %1$s %2$s добавлен добавлено. <a href="%3$s">Редактировать</a>', IBC_TEXTDOMAIN ),
						$_REQUEST[ 'fist_name' ],
						$_REQUEST[ 'last_name' ],
						$this->get_author_link( array( 'action' => 'edit', 'id' => $result ) )
					);
					wp_redirect( $this->get_author_link( array( 'action' => 'add', 'notice' => $notice ) ) );
					exit;
				} elseif (
					'edit' == $_REQUEST[ 'action' ]
					&& isset( $_REQUEST[ 'id' ] )
					&& ! empty( $_REQUEST[ 'id' ] )
					&& isset( $_REQUEST[ 'first_name' ] )
					&& isset( $_REQUEST[ 'last_name' ] )
					&& isset( $_REQUEST[ 'middle_name' ]
				) ) {
					$result = $this->update_author( $_REQUEST[ 'id' ], $_REQUEST[ 'first_name' ], $_REQUEST[ 'last_name' ], $_REQUEST[ 'middle_name' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : __( 'Данные автора обновлено.', IBC_TEXTDOMAIN );
					wp_redirect( $this->get_author_link( array( 'action' => 'edit', 'notice' => $notice, 'id' => $result ) ) );
					exit;
				}
				break;
			case 'genres':
				if ( 'delete' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'id' ] ) && ! empty( $_REQUEST[ 'id' ] ) ) {
					$result = $this->delete_genre( $_REQUEST[ 'id' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : __( 'Жанр удалён', IBC_TEXTDOMAIN );
					wp_redirect( $this->get_page_link( 'genres', array( 'action' => 'table', 'notice' => $notice ) ) );
					exit;
				} elseif ( 'add' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'name' ] ) && ! empty( $_REQUEST[ 'name' ] ) && isset( $_REQUEST[ 'parent' ] ) ) {
					$result = $this->add_genre( $_REQUEST[ 'name' ], $_REQUEST[ 'parent' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : sprintf(
						__( 'Жанр %1$s успешно добавлено. <a href="%2$s">Редактировать</a>', IBC_TEXTDOMAIN ),
						$_REQUEST[ 'name' ],
						$this->get_genre_link( array( 'action' => 'edit', 'id' => $result ) )
					);
					wp_redirect( $this->get_page_link( 'genres', array( 'action' => 'add', 'notice' => $notice ) ) );
					exit;
				} elseif (
					'edit' == $_REQUEST[ 'action' ]
					&& isset( $_REQUEST[ 'id' ] )
					&& ! empty( $_REQUEST[ 'id' ] )
					&& isset( $_REQUEST[ 'name' ] )
					&& ! empty( $_REQUEST[ 'name' ] )
					&& isset( $_REQUEST[ 'parent' ] )
				) {
					$result = $this->update_genre( $_REQUEST[ 'id' ], $_REQUEST[ 'name' ], $_REQUEST[ 'parent' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : __( 'Жанр обновлён.', IBC_TEXTDOMAIN );
					wp_redirect( $this->get_page_link( 'genres', array( 'action' => 'edit', 'notice' => $notice, 'id' => $result ) ) );
					exit;
				}
				break;
			case 'publications':
				if ( 'delete' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'id' ] ) && ! empty( $_REQUEST[ 'id' ] ) ) {
					$result = $this->delete_publication( $_REQUEST[ 'id' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : __( 'Публикация удалёна', IBC_TEXTDOMAIN );
					wp_redirect( $this->get_page_link( 'publications', array( 'action' => 'table', 'notice' => $notice ) ) );
					exit;
				} elseif ( 'add' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'query' ] ) ) {
					$result = $this->add_publication( $_REQUEST[ 'query' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : sprintf(
						__( 'Публикация успешно добавлена. <a href="%1$s">Редактировать</a>', IBC_TEXTDOMAIN ),
						$this->get_page_link( array( 'action' => 'edit', 'id' => $result ) )
					);
					wp_redirect( $this->get_page_link( 'publications', array( 'action' => 'add', 'notice' => $notice ) ) );
					exit;
				} elseif ( 'edit' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'id' ] ) && ! empty( $_REQUEST[ 'id' ] ) && isset( $_REQUEST[ 'query' ] ) ) {
					$result = $this->update_publication( $_REQUEST[ 'id' ], $_REQUEST[ 'query' ] );
					$notice = ( is_wp_error( $result ) ) ? $result->get_error_message() : __( 'Публикация обновлёна.', IBC_TEXTDOMAIN );
					wp_redirect( $this->get_page_link( array( 'action' => 'edit', 'notice' => $notice, 'id' => $result ) ) );
					exit;
				}
				break;
		}
	}



	public function add_mu_pages() {
		$publications_hook = add_menu_page( __( 'Публикации', IBC_TEXTDOMAIN ), __( 'Публикации', IBC_TEXTDOMAIN ), 'manage_options', 'publications', array( $this, 'render_mu_pages' ), 'dashicons-book-alt', '6' );
		$genres_hook = add_submenu_page( 'publications', __( 'Жанры', IBC_TEXTDOMAIN ), __( 'Жанры', IBC_TEXTDOMAIN ), 'manage_options', 'genres', array( $this, 'render_mu_pages' ) );
		$authors_hook = add_submenu_page( 'publications', __( 'Авторы', IBC_TEXTDOMAIN ), __( 'Авторы', IBC_TEXTDOMAIN ), 'manage_options', 'authors', array( $this, 'render_mu_pages' ) );
		$publishing_houses_hook = add_submenu_page( 'publications', __( 'Издательства', IBC_TEXTDOMAIN ), __( 'Издательства', IBC_TEXTDOMAIN ), 'manage_options', 'publishing_houses', array( $this, 'render_mu_pages' ) );
		add_action( "load-$publications_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-publications.php';
			$GLOBALS[ 'Publications_List_Table' ] = new Publications_List_Table( $this->db );
		} );
		add_action( "load-$genres_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-genres.php';
			$GLOBALS[ 'Genres_List_Table' ] = new Genres_List_Table( $this->db );
		} );
		add_action( "load-$authors_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-authors.php';
			$GLOBALS[ 'Authors_List_Table' ] = new Authors_List_Table( $this->db );
		} );
		add_action( "load-$publishing_houses_hook", function () {
			require_once IBC_INCLUDES . 'admin/class-admin-table-publishing-houses.php';
			$GLOBALS[ 'Publishing_Houses_List_Table' ] = new Publishing_Houses_List_Table( $this->db );
		} );
	}



	public function render_mu_pages() {
		$screen = get_current_screen();
		wp_enqueue_style( 'flexboxgrid' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'select2' );
		wp_add_inline_script( 'select2', "jQuery( '.select2' ).select2();", 'after' );
		if ( preg_match( '/toplevel_page_publications-network$/', $screen->id ) ) {
			include IBC_VIEWS . 'tables/publications.php';
		} elseif ( preg_match( '/genres-network$/', $screen->id ) ) {
			include IBC_VIEWS . 'tables/genres.php';
		} elseif ( preg_match( '/authors-network$/', $screen->id ) ) {
			include IBC_VIEWS . 'tables/authors.php';
		} elseif ( preg_match( '/publishing_houses-network$/', $screen->id ) ) {
			include IBC_VIEWS . 'tables/publishing-houses.php';
		}
	}



	public function admin_tables() {
		require_once IBC_INCLUDES . 'admin/class-admin-table.php';
	}


	public function add_admin_pages() {
		// $readers_hook = add_menu_page( __( 'Читатели', IBC_TEXTDOMAIN ), __( 'Читатели', IBC_TEXTDOMAIN ), 'manage_options', 'readers', array( $this, 'render_page' ), 'dashicons-groups', 34 );
		// $departments_hook = add_submenu_page( 'readers', __( 'Подразделенния', IBC_TEXTDOMAIN ), __( 'Подразделенния', IBC_TEXTDOMAIN ), 'manage_options', 'departments', array( $this, 'render_page' ) );
		// $issuances_hook = add_menu_page( __( 'Выдачи', IBC_TEXTDOMAIN ), __( 'Выдачи', IBC_TEXTDOMAIN ), 'manage_options', 'issuances', array( $this, 'render_page' ), 'dashicons-download', 36 );
		// $copies_hook = add_menu_page( __( 'Фонды', IBC_TEXTDOMAIN ), __( 'Фонды', IBC_TEXTDOMAIN ), 'manage_options', 'copies', array( $this, 'render_page' ), 'dashicons-welcome-add-page', 35 );
		// $add_issuances_hook = add_submenu_page( 'issuances', __( 'Оформление выдачи', IBC_TEXTDOMAIN ), __( 'Оформление выдачи', IBC_TEXTDOMAIN ), 'manage_options', 'add_issuances', array( $this, 'render_page' ) );
		// add_action( "load-$copies_hook", array( $this, 'copies_table_load' ) );
		// add_action( "load-$readers_hook", array( $this, 'readers_table_load' ) );
		// add_action( "load-$issuances_hook", array( $this, 'issuances_table_load' ) );
		// add_action( "load-$departments_hook", function () {
		// 	require_once IBC_INCLUDES . 'admin/class-admin-table-departments.php';
		// 	$GLOBALS[ 'Departments_List_Table' ] = new Departments_List_Table();
		// } );
		// add_action( "load-$readers_hook", function () {
		// 	require_once IBC_INCLUDES . 'admin/class-admin-table-readers.php';
		// 	$GLOBALS[ 'Readers_List_Table' ] = new Readers_List_Table();
		// } );
		// add_action( "load-$issuances_hook", function () {
		// 	require_once IBC_INCLUDES . 'admin/class-admin-table-issuances.php';
		// 	$GLOBALS[ 'Issuances_List_Table' ] = new Issuances_List_Table();
		// } );
	}




	public function get_page_link( $page, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'action'      => 'table',
			'nonce'       => '',
			'id'          => '',
			'notice'      => '',
		) );
		if ( in_array( $args[ 'action' ], array( 'table', 'add', 'edit', 'delete' ) ) ) {
			if ( empty( $args[ 'nonce' ] ) ) {
				$args[ 'nonce' ] = wp_create_nonce( $page );
			}
		} else {
			$args[ 'action' ] = 'table';
		}
		return add_query_arg(
			array(
				'page'    => $page,
				'action'  => $args[ 'action' ],
				'nonce'   => $args[ 'nonce' ],
				'id'      => $args[ 'id' ],
				'notice'  => urlencode( $args[ 'notice' ] ),
			),
			( in_array( $page, array(
				'authors',
				'publications',
				'publishing_houses',
				'genres',
			) ) ) ? network_admin_url( 'admin.php?' ) : admin_url( 'admin.php?' )
		);
	}



	public function the_page_link( $page, $args = array() ) {
		echo $this->get_page_link( $page, $args );
	}




	public function render_page() {
		$screen = get_current_screen();
		wp_enqueue_style( 'flexboxgrid' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'select2' );
		wp_add_inline_script( 'select2', "jQuery( '.select2' ).select2();", 'after' );
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