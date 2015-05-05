<?php
/**
 * Mapado Plugin
 * Events list TPL
 */
?>

<!-- List -->
<div id="mapado-plugin" class="mpd-card-list">
	
	<?php foreach ( $vars['events'] as $activity ) : ?>
		<div class="mpd-car-list__item mpd-card">
			<?php if ( !empty($activity->getImageUrlList()['200x250'][0]) ) : ?>
				<img class="mpd-card__thumb"
					 src="<?php echo $activity->getImageUrlList()['200x250'][0] ?>"
					 alt="<?php echo $activity->getTitle() ?>"
					/>
			<?php endif; ?>

			<div class="mpd-card__body">
				<?php if ( $activity->getTitle() ) : ?>
					<h3 class="mpd-card__title">
						<a href="<?php echo MapadoUtils::getEventUrl( $activity->getUuid(), $vars['list_slug'] ) ?>">
							<?php echo $activity->getTitle() ?>
						</a>
					</h3>
				<?php endif; ?>
	
				<?php if ( $activity->getShortDate() ) : ?>
					<p class="mpd-card__date">
						<?php echo $activity->getShortDate() ?>
					</p>
				<?php endif; ?>
	
				<?php if ( $activity->getFrontPlaceName() ) : ?>
					<p class="mpd-card__address">
						<a href="<?php echo $activity->getLinks()['mapado_place_url']['href'] ?>" target="_blank">
							<?php echo $activity->getFrontPlaceName() ?>
						</a>
					</p>
				<?php endif; ?>
	
				<?php if ( $activity->getShortDescription() ) : ?>
					<p class="mpd-card__description">
						<?php echo $activity->getShortDescription() ?>
						<a href="<?php echo MapadoUtils::getEventUrl( $activity->getUuid(), $vars['list_slug'] ) ?>"
						   class="mpd-card__read-more-link"
						>→ Lire la suite</a>
					</p>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>

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

