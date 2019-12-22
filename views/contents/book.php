<?php if ( ! defined( 'ABSPATH' ) ) { exit; }; ?>
<table class="ibc-book">
	<tr>
		<td><?php _e( 'Жанр', $this->textdomain ); ?></td>
		<td><?php echo $genre; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'ISBN', $this->textdomain ); ?></td>
		<td><?php echo $isbn; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Год издания', $this->textdomain ); ?></td>
		<td><?php echo $year; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Издательство', $this->textdomain ); ?></td>
		<td><?php echo $publishing_houses; ?></td>
	</tr>
	<tr>
		<td><?php _e( 'Авторы', $this->textdomain ); ?></td>
		<td><?php echo $authors; ?></td>
	</tr>
</table>