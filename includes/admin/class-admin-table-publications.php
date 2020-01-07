<?php


namespace ibc;


class Publications_List_Table extends WP_List_Table {



	use Publications;



	protected $db;



	function __construct( $db ) {
		$this->db = $db;
		parent::__construct( array(
			'singular' => 'log',
			'plural'   => 'logs',
			'ajax'     => false,
		) );
		$this->bulk_action_handler();
		add_screen_option( 'per_page', array(
			'label'   => __( 'Показывать на странице', IBC_TEXTDOMAIN ),
			'default' => 20,
			'option'  => 'logs_per_page',
		) );
		$this->prepare_items();
		add_action( 'wp_print_scripts', [ __CLASS__, '_list_table_css' ] );
	}



	function prepare_items(){
		global $wpdb;
		$per_page = get_user_meta( get_current_user_id(), get_current_screen()->get_option( 'per_page', 'option' ), true ) ?: 20;
		$this->set_pagination_args( array(
			'total_items' => 3,
			'per_page'    => $per_page,
		) );
		$cur_page = (int) $this->get_pagenum(); // желательно после set_pagination_args()
		$this->items = __return_empty_array();
	}

	static function _list_table_css() {
		?>
			<style>
				table.logs .column-isbn { width: 10%; }
				table.logs .column-title { width: 25%; }
				table.logs .column-genres { width: 15%; }
				table.logs .column-authors { width: 15%; }
				table.logs .column-publishing_house { width: 15%; }
				table.logs .column-year { width: 5%; }
				table.logs .column-copies { width: 5%; }
			</style>
		<?php
	}



	function get_columns(){
		return array(
			'cb'         => '<input type="checkbox" />',
			'isbn'       => __( 'ISBN', IBC_TEXTDOMAIN ),
			'title'      => __( 'Название', IBC_TEXTDOMAIN ),
			'genres'     => __( 'Жанры', IBC_TEXTDOMAIN ),
			'authors'    => __( 'Авторы', IBC_TEXTDOMAIN ),
			'publishing_house' => __( 'Издательство', IBC_TEXTDOMAIN ),
			'year'       => __( 'Год', IBC_TEXTDOMAIN ),
		);
	}



	protected function get_bulk_actions() {
		return array(
			'delete' => __( 'Удалить', IBC_TEXTDOMAIN ),
		);
	}



	function extra_tablenav( $which ) {
		echo '<div class="alignleft actions">HTML код полей формы (select). Выбо по подраздедению... Внутри тега form...</div>';
	}



	function column_default( $item, $colname ) {
		if( $colname === 'title' ) {
			$actions = array();
			$actions[ 'delete' ] = sprintf(
				'<a href="#" class="reader-action-delete" data-reader="%1$s">%2$s</a>',
				$item->id,
				__( 'Удалить', IBC_TEXTDOMAIN )
			);
			$actions[ 'edit' ] = sprintf(
				'<a href="%1$s">%2$s</a>',
				add_query_arg( array( 'page' => 'readers', 'tab' => 'edit', 'reader_id' => $item->id ), admin_url( 'admin.php?' ) ),
				__( 'Изменить', IBC_TEXTDOMAIN )
			);
			return esc_html( $item->title ) . $this->row_actions( $actions );
		} else {
			return isset( $item->$colname ) ? $item->$colname : print_r( $item, 1 );
		}
	}



	private function bulk_action_handler() {
		if( empty( $_POST[ 'licids' ] ) || empty( $_POST[ '_wpnonce'] ) ) return;
		if ( ! $action = $this->current_action() ) return;
		if( ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'bulk-' . $this->_args[ 'plural' ] ) ) wp_die( 'nonce error' );
		switch ( $action ) {
			case 'delete':
				if ( is_array( $_POST[ 'licids' ] ) ) {
					foreach ( $_POST[ 'licids' ] as $id ) {
						$this->delete_reader( $id );
					}
				} else {
					$this->delete_reader( $_POST[ 'licids' ] );
				}
				break;
		}
	}
	


}