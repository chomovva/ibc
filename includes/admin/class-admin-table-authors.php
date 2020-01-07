<?php


namespace ibc;


class Authors_List_Table extends WP_List_Table {


	use Authors;


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


	function prepare_items() {
		$per_page = get_user_meta( get_current_user_id(), get_current_screen()->get_option( 'per_page', 'option' ), true ) ?: 20;
		$this->set_pagination_args( array(
			'total_items' => 3,
			'per_page'    => $per_page,
		) );
		$cur_page = (int) $this->get_pagenum(); // желательно после set_pagination_args()
		$this->items = __return_empty_array();
		$authors = $this->get_authors();
		if ( ! empty( $authors ) ) {
			foreach ( $authors as $author ) {
				$this->items[] = ( object ) array(
					'id'           => $author->id,
					'first_name'   => $author->first_name,
					'last_name'    => $author->last_name,
					'middle_name'  => $author->middle_name,
					'publications' => '-'
 				);
			}
		}
	}

	static function _list_table_css() {
		?>
			<style>
				table.logs .column-first_name { width: 25%; }
				table.logs .column-last_name { width: 25%; }
				table.logs .column-middle_name { width: 25%; }
				table.logs .column-publications { width: 25%; }
			</style>
		<?php
	}

	// колонки таблицы
	function get_columns(){
		return array(
			'cb'           => '<input type="checkbox" />',
			'last_name'    => __( 'Фамилия', IBC_TEXTDOMAIN ),
			'first_name'   => __( 'Имя', IBC_TEXTDOMAIN ),
			'middle_name'  => __( 'Отчество', IBC_TEXTDOMAIN ),
			'publications' => __( 'Количество публикаций', IBC_TEXTDOMAIN ),
		);
	}


	protected function get_bulk_actions() {
		return array(
			'delete' => __( 'Удалить', IBC_TEXTDOMAIN ),
		);
	}




	function column_default( $item, $colname ) {
		if ( 'last_name' === $colname ) {
			$actions = array();
			$actions[ 'delete' ] = sprintf(
				'<a href="%1$s">%2$s</a>',
				$this->get_author_link( array( 'action' => 'delete', 'id' => $item->id ) ),
				__( 'Удалить', IBC_TEXTDOMAIN )
			);
			$actions[ 'edit' ] = sprintf(
				'<a href="%1$s">%2$s</a>',
				$this->get_author_link( array( 'action' => 'edit', 'id' => $item->id ) ),
				__( 'Изменить', IBC_TEXTDOMAIN )
			);
			return esc_html( $item->last_name ) . $this->row_actions( $actions );
		} else {
			return isset( $item->$colname ) ? $item->$colname : print_r( $item, 1 );
		}
	}



	private function bulk_action_handler() {
		if ( empty( $_POST[ 'licids' ] ) || empty( $_POST[ '_wpnonce'] ) ) return;
		if ( ! $action = $this->current_action() ) return;
		if ( ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'bulk-' . $this->_args[ 'plural' ] ) ) wp_die( 'nonce error' );
		switch ( $action ) {
			case 'delete':
				if ( is_array( $_POST[ 'licids' ] ) ) {
					foreach ( $_POST[ 'licids' ] as $id ) {
						$this->delete_author( $id );
					}
				} else {
					$this->delete_author( $_POST[ 'licids' ] );
				}
				break;
		}
	}
	

}