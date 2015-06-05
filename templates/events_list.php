<?php
/**
 * Mapado Plugin
 * Events list TPL
 */
?>

<!-- List -->
<div id="mapado-plugin" class="mpd-card-list">
	
	<?php 
	foreach ( $vars['events'] as $activity ) {
		$vars['activity'] = $activity;
		MapadoUtils::template( 'event_card', $vars);
	}
	?>

	<div class="mpd-card-list__footer">
	
		<!-- Pagination -->
		<?php if ( $vars['pagination']['nb_pages'] > 1 ) : ?>
			<div class="mpd-pagination">
				<?php if ( $vars['pagination']['page'] > 1 ) : ?>
					<a href="<?php echo MapadoUtils::getUserListUrl( $vars['list_slug'], $vars['pagination']['page'] - 1 ) ?>"
					   class="mpd-pagination__item"
					><</a>
				<?php endif; ?>

				<?php for ( $p = 1; $p <= $vars['pagination']['nb_pages']; $p++ ) : ?>
					<?php if ( $p == $vars['pagination']['page'] ) : ?>
						<span class="mpd-pagination__item mpd-pagination__item--current"><?php echo $p; ?></span>
					<?php else : ?>
						<a href="<?php echo MapadoUtils::getUserListUrl( $vars['list_slug'], $p ) ?>"
						   class="mpd-pagination__item"
						><?php echo $p; ?></a>
					<?php endif; ?>
				<?php endfor; ?>

				<?php if ( $vars['pagination']['page'] < $vars['pagination']['nb_pages'] ) : ?>
					<a href="<?php echo MapadoUtils::getUserListUrl( $vars['list_slug'], $vars['pagination']['page'] + 1 ) ?>"
					   class="mpd-pagination__item"
					>></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="mpd-credits">Informations powered by <a href="http://www.mapado.com/" target="_blank">Mapado</a></div>
	</div>
</div>

