<?php


namespace ibc;


class Authors_List_Table extends WP_List_Table {



	function __construct() {
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
				table.logs .column-first_name { width: 10em; }
				table.logs .column-last_name { width: 10em; }
				table.logs .column-middle_name { width: 10em; }
			</style>
		<?php
	}

	// колонки таблицы
	function get_columns(){
		return array(
			'cb'           => '<input type="checkbox" />',
			'first_name'   => __( 'Имя', IBC_TEXTDOMAIN ),
			'last_name'    => __( 'Фамилия', IBC_TEXTDOMAIN ),
			'middle_name'  => __( 'Отчество', IBC_TEXTDOMAIN ),
		);
	}

	// сортируемые колонки
	// function get_sortable_columns(){
	// 	return array(
	// 		'name' => array( 'name', 'desc' ),
	// 	);
	// }

	protected function get_bulk_actions() {
		return array(
			'delete' => __( 'Удалить', IBC_TEXTDOMAIN ),
		);
	}

	// Элементы управления таблицей. Расположены между групповыми действиями и панагией.
	function extra_tablenav( $which ) {
		echo '<div class="alignleft actions">HTML код полей формы (select). Выбо по подраздедению... Внутри тега form...</div>';
	}


	// вывод каждой ячейки таблицы...
	function column_default( $item, $colname ) {
		if( $colname === 'card_id' ) {
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
			// $actions[ 'info' ] = sprintf(
			// 	'<a href="%1$s" class="reader-action-button" data-reader="%1$s" data-action="info">%2$s</a>',
			// 	$item->id,
			// 	__( 'Подробней', IBC_TEXTDOMAIN )
			// );
			return '<code>' . esc_html( $item->card_id ) . '</code>' . $this->row_actions( $actions );
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