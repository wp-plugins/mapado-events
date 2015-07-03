<?php
/**
 * Mapado Plugin
 * Single Event TPL
 */
?>

<div id="mapado-plugin" class="mapado_activity_single">
	<?php if ( !empty($vars['thumbs']['700x250'][0]) ) : ?>
		<div class="mapado_activity_thumb">
			<img src="<?php echo $vars['thumbs']['700x250'][0] ?>" alt="<?php echo $vars['event']->getTitle() ?>" />
		</div>
	<?php endif; ?>

	<?php if ( !MapadoPlugin::widgetActive()) : ?>
		<div class="mapado_activity_infos">
			<?php if ( $vars['event']->getFormattedSchedule() ) : ?>
				<div>
					<label>Dates</label><div><?php echo $vars['event']->getFormattedSchedule() ?></div>
				</div>
			<?php endif; ?>
	
			<?php if ( $vars['event']->getFrontPlaceName() && $vars['event']->getAddress() ) : ?>
				<div>
					<label>Lieu</label>
					<div>
						<a href="<?php echo MapadoUtils::getPlaceUrl( $vars['event']->getLinks() ) ?>" target="_blank">
							<?php echo $vars['event']->getFrontPlaceName() ?>
						</a>
						 - <?php echo $vars['event']->getAddress()->getFormattedAddress() ?>
					</div>
				</div>
			<?php endif; ?>
	
			<?php if ( $vars['event']->getSimplePrice() ) : ?>
				<div>
					<label>Tarif(s)</label>
					<div>
						<?php
						if ( ($price = $vars['event']->getSimplePrice()) == 0 ) 
							echo 'Gratuit';
						else
							echo $price . ' €';
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $vars['event']->getDescription() ) : ?>
		<div class="mapado_activity_desc">
			<?php echo apply_filters( 'the_content', $vars['event']->getDescription(), true ) ?>
		</div>
	<?php endif; ?>
	<?php 
	 $address = $vars['event']->getAddress();
	 if ( $vars['display_map'] == 1 && $address): 
	?>
		<div class="mpd-google-maps">
			<script src="http://maps.googleapis.com/maps/api/js"></script>
			<script>

				var mpd_map; //<-- This is now available to both event listeners and the initialize() function
				var mpd_map_lat = <?php echo $vars['event']->getAddress()->getLatitude() ?>;
				var mpd_map_lng = <?php echo $vars['event']->getAddress()->getLongitude() ?>;
				function initialize() {
				  var mapOptions = {
				   center: new google.maps.LatLng(mpd_map_lat, mpd_map_lng),
				   zoom: 16,
				   mapTypeId: google.maps.MapTypeId.ROADMAP
				  };
				   mpd_map = new google.maps.Map(document.getElementById("mapado-map-canvas"),
				            mapOptions);
				   var marqueur = new google.maps.Marker({
					position: new google.maps.LatLng(mpd_map_lat, mpd_map_lng),
					map: mpd_map
				   });
				}
				google.maps.event.addDomListener(window, 'load', initialize);
				google.maps.event.addDomListener(window, "resize", function() {
				 var center = mpd_map.getCenter();
				 google.maps.event.trigger(mpd_map, "resize");
				 mpd_map.setCenter(center); 
				});

			</script>
			<div id="mapado-map-canvas"></div>
	</div>
	<?php endif; ?>
	
	<div class="mpd-credits">
		Informations powered by
		<a href="<?php echo $vars['event']->getLinks()['mapado_url']['href'] ?>" target="_blank">Mapado</a>
	</div>
</div>