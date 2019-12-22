<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; }



trait Issuances {



	function issuances_table_load() {
		require_once IBC_INCLUDES . 'admin/class-admin-table.php';
		require_once IBC_INCLUDES . 'admin/class-admin-table-issuances.php'; // тут находится класс Readers_List_Table...
		// создаем экземпляр и сохраним его дальше выведем
		$GLOBALS[ 'Issuances_List_Table' ] = new Issuances_List_Table();
	}



	function issuances_form_render() {
		ob_start();
		echo '<form method="POST">';
		$GLOBALS[ 'Issuances_List_Table' ]->display();
		echo '</form>';
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}



}