<div class="wrap">

	<?php $tab = ( isset( $_REQUEST[ 'tab' ] ) && ! empty( $_REQUEST[ 'tab' ] ) ) ? $_REQUEST[ 'tab' ] : ''; ?>

	<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>


	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Вторичное меню', IBC_TEXTDOMAIN ); ?>">
		<a href="<?php echo add_query_arg( array( 'page' => 'issuances' ), admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( empty( $tab ) ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'issuances', 'tab' => 'add' ), admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'edit' || $tab == 'add' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить / редактировать', IBC_TEXTDOMAIN ); ?>
		</a>
	</nav>


	<?php if ( $tab == 'edit' || $tab == 'add' ) : ?>

		<form id="issuances-form">
			<div class="row middle-xs">
				<div class="col-xs-12 col-sm-4">
					<label for="reader"><?php _e( 'Читатель', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-8">
					<?php $this->wp_dropdown_readers(); ?>
				</div>
			</div>
			<div class="row middle-xs">
				<div class="col-xs-12 col-sm-4">
					<label for="book"><?php _e( 'Книга', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-4">
					// список книг
				</div>
			</div>
			<div class="row middle-xs">
				<div class="col-xs-12 col-sm-2 col-sm-offset-4">
					<label for="clearance_date"><?php _e( 'Дата выдачи', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-2">
					<input type="date" name="clearance_date" value="">
				</div>
				<div class="col-xs-12 col-sm-2">
					<label for="return_date"><?php _e( 'Дата возврата', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-2">
					<input id="return_date" type="date" name="return_date" value="">
				</div>
			</div>
			<?php submit_button(); ?>
		</form>
	
	<?php elseif ( $tab == 'archive' ) : ?>
		<form method="POST">
			<?php $GLOBALS[ 'Issuances_List_Table' ]->display(); ?>
		</form>
	<?php endif; ?>
			


</div>