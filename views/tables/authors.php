<div class="wrap">

	<?php $tab = ( isset( $_REQUEST[ 'tab' ] ) && ! empty( $_REQUEST[ 'tab' ] ) ) ? $_REQUEST[ 'tab' ] : ''; ?>

	<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>


	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php network_admin_url( 'Вторичное меню', IBC_TEXTDOMAIN ); ?>">
		<a href="<?php echo add_query_arg( array( 'page' => 'authors' ), network_admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( empty( $tab ) ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'authors', 'tab' => 'add' ), network_admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'edit' || $tab == 'add' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить / редактировать', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'authors', 'tab' => 'add' ), network_admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'import' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Импорт', IBC_TEXTDOMAIN ); ?>
		</a>
	</nav>

	<?php if ( $tab == 'edit' || $tab == 'add' ) : ?>
		<?php
			$author_id = __return_empty_string();
			$first_name = __return_empty_string();
			$last_name = __return_empty_string();
			$middle_name = __return_empty_string();
		?>
		<form id="genre-form">
			<input type="hidden" name="author_id" value="<?php echo esc_attr( $author_id ); ?>">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<figure>
						<img src="<?php echo IBC_ASSETS . 'images/human.svg' ?>" style="display: block; width: 100%;">
					</figure>
				</div>
				<div class="col-xs-12 col-sm-6 first-sm">
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="first_name"><?php _e( 'имя', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="first_name" type="text" name="first_name" value="<?php echo esc_attr( $first_name ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="last_name"><?php _e( 'Фамилия', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="last_name" type="text" name="last_name" value="<?php echo esc_attr( $last_name ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="middle_name"><?php _e( 'Отчество', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="middle_name" type="text" name="middle_name" value="<?php echo esc_attr( $middle_name ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12">
							<?php submit_button(); ?>
						</div>
					</div>
				</div>
			</div>
		</form>
	<?php elseif ( $tab == 'import' ) : ?>
	<?php else : ?>
		<form method="POST">
			<?php $GLOBALS[ 'Authors_List_Table' ]->display(); ?>
		</form>
	<?php endif; ?>

</div>