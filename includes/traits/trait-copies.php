<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; }



trait Copies {



	function copies_table_load() {
		require_once IBC_INCLUDES . 'admin/class-admin-table.php';
		require_once IBC_INCLUDES . 'admin/class-admin-table-copies.php'; // тут находится класс Books_List_Table...
		// создаем экземпляр и сохраним его дальше выведем
		$GLOBALS[ 'Copies_List_Table' ] = new Copies_List_Table();
	}


	function copies_table_render() {
		ob_start();
		echo '<form method="POST">';
		$GLOBALS[ 'Copies_List_Table' ]->display();
		echo '</form>';
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}


}