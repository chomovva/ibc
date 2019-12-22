<div class="wrap">

	<?php $tab = ( isset( $_REQUEST[ 'tab' ] ) && ! empty( $_REQUEST[ 'tab' ] ) ) ? $_REQUEST[ 'tab' ] : ''; ?>

	<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>


	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php network_admin_url( 'Вторичное меню', IBC_TEXTDOMAIN ); ?>">
		<a href="<?php echo add_query_arg( array( 'page' => 'genres' ), network_admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( empty( $tab ) ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'genres', 'tab' => 'add' ), network_admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'edit' || $tab == 'add' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить / редактировать', IBC_TEXTDOMAIN ); ?>
		</a>
	</nav>

	<?php if ( $tab == 'edit' || $tab == 'add' ) : ?>
		<?php
			$genre_id = __return_empty_string();
			$name = __return_empty_string();
			$parent = __return_empty_string();
		?>
		<form id="genre-form">
			<input type="hidden" name="<?php echo esc_attr( $genre_id ); ?>">
			<div class="row middle-xs" style="margin: .5em 0;">
				<div class="col-xs-12 col-sm-4">
					<label for="name"><?php _e( 'Название', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-4">
					<input style="width: 100%;" id="name" type="text" name="name" value="<?php echo esc_attr( $name ); ?>" required="required">
				</div>
			</div>
			<div class="row middle-xs" style="margin: .5em 0;">
				<div class="col-xs-12 col-sm-4">
					<label for="parent"><?php _e( 'Родительская запись', IBC_TEXTDOMAIN ); ?></label>
				</div>
				<div class="col-xs-12 col-sm-4">
					<select style="width: 100%;" name="parent">
						<option></option>
					</select>
				</div>
			</div>
		</form>
	<?php else : ?>
		<form method="POST">
			<?php $GLOBALS[ 'Genres_List_Table' ]->display(); ?>
		</form>
	<?php endif; ?>

</div>