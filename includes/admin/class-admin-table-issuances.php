<?php



namespace ibc;



class Issuances_List_Table extends WP_List_Table {


	use Readers;
	use Departments;


	function __construct(){
		parent::__construct( array(
			'singular' => 'log',
			'plural'   => 'logs',
			'ajax'     => false,
		) );
		$this->bulk_action_handler();
		add_screen_option( 'per_page', array(
			'label'   => 'Показывать на странице',
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
		$cur_page = (int) $this->get_pagenum();
		$this->items = __return_empty_array();
	}



	function get_columns(){
		return array(
			'cb'            => '<input type="checkbox" />',
			'id'            => 'ID',
			'name'          => 'Имя',
		);
	}



	function get_sortable_columns(){
		return array(
			'name' => array( 'name', 'desc' ),
		);
	}



	protected function get_bulk_actions() {
		return array(
			'delete' => 'Delete',
		);
	}



	function extra_tablenav( $which ){
		echo '<div class="alignleft actions">HTML код полей формы (select). Внутри тега form...</div>';
	}



	static function _list_table_css(){
		?>
			<style>
				table.logs .column-id{ width:2em; }
				table.logs .column-name{ width:15%; }
			</style>
		<?php
	}



	function column_default( $item, $colname ){
		if ( $colname === 'name' ){
			$actions = array();
			$actions[ 'edit' ] = sprintf( '<a href="%s">%s</a>', '#', __('edit','hb-users') );
			return esc_html( $item->name ) . $this->row_actions( $actions );
		} else {
			return isset( $item->$colname ) ? $item->$colname : print_r( $item, 1 );
		}
	}



	function column_cb( $item ){
		echo '<input type="checkbox" name="licids[]" id="cb-select-'. $item->id .'" value="'. $item->id .'" />';
	}



	private function bulk_action_handler(){
		if( empty($_POST['licids']) || empty($_POST['_wpnonce']) ) return;

		if ( ! $action = $this->current_action() ) return;

		if( ! wp_verify_nonce( $_POST['_wpnonce'], 'bulk-' . $this->_args['plural'] ) )
			wp_die('nonce error');

		// делает что-то...
		die( $action ); // delete
		die( print_r($_POST['licids']) );

	}



}