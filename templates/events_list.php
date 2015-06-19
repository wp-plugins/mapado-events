<?php
/**
 * Mapado Plugin
 * Events list TPL
 */
?>

<!-- List -->
<div id="mapado-plugin">
	<div id="mapado-plugin" class="chew-row
									chew-row--<?= $vars['card_column_max'] ?>
									chew-row--thumb-<?= $vars['card_thumb_design']['position_type'] ?>
									chew-row--thumb-<?= $vars['card_thumb_design']['position_side'] ?>
									chew-row--thumb-<?= $vars['card_thumb_design']['size'] ?>">
		<?php 
		foreach ( $vars['events'] as $activity ) {
			$vars['activity'] = $activity;
			MapadoUtils::template( 'event_card', $vars);
		}
		$ghostSize = 5;
		if ($vars['card_column_max'] !== 'auto') {
			$ghostSize = $vars['card_column_max'] - 1;
		}
		for ( $i = 0; $i < $ghostSize; $i++ ): ?>
			<div class="chew-cell chew-cell--ghost">
			</div>
		<?php endfor; ?>
	</div>

	<div class="mpd-card-list__footer">
	
		<!-- Pagination -->
		<?php if ( $vars['pagination']['nb_pages'] > 1 ) : ?>
			<div class="mpd-pagination">
				<?php if ( $vars['pagination']['page'] > 1 ) : ?>
					<a href="<?php echo MapadoUtils::getUserListUrl( $vars['list_slug'], $vars['pagination']['page'] - 1 ) ?>"
					   class="mpd-pagination__item"
					><</a>
				<?php endif; ?>
				<?php $current_page = $vars['pagination']['page'] ?>
				<?php $PAGINATION_BOUNDING = 3 ?>
				<?php if (($current_page - $PAGINATION_BOUNDING) < 1) : ?>
					<?php $begin_page = 1 ?>
				<?php else : ?>
					<?php $begin_page = $current_page - $PAGINATION_BOUNDING ?>
					<?php if ($current_page  > $PAGINATION_BOUNDING + 1 ) : ?>
						<!-- Begining of pagination always visible -->
						<?php if ($current_page > $PAGINATION_BOUNDING * 2 + 1) : ?>
							<?php $limit_pagination_begining = $PAGINATION_BOUNDING ?>	
						<?php else : ?>	
							<?php $limit_pagination_begining = $current_page - $PAGINATION_BOUNDING - 1 ?>	
						<?php endif; ?>	
						<?php for ( $p = 1; $p <= $limit_pagination_begining; $p++ ) : ?>
							<?php if (($p != $limit_pagination_begining) ||  ($p < $PAGINATION_BOUNDING) ) : ?>
								<a href="<?php echo MapadoUtils::getUserListUrl( $vars['list_slug'], $p ) ?>"
								   class="mpd-pagination__item"
								><?php echo $p; ?></a>
							<?php else : ?>
								<a href="<?php echo MapadoUtils::getUserListUrl( $vars['list_slug'], $limit_pagination_begining ) ?>"
							   class="mpd-pagination__item"
								><?php echo "..."; ?></a>
							<?php endif; ?>
						<?php endfor; ?>
					<?php endif; ?>	
				<?php endif; ?>
				<?php if (($current_page + $PAGINATION_BOUNDING) > $vars['pagination']['nb_pages']) : ?>
					<?php $end_page = $vars['pagination']['nb_pages'] ?>
				<?php else : ?>
					<?php $end_page = $current_page + $PAGINATION_BOUNDING ?>
				<?php endif; ?>
				<?php for ( $p = $begin_page; $p <= $end_page; $p++ ) : ?>
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

