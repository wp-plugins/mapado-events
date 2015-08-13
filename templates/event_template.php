<?php
$template = $vars['template'];
$activity = $vars['activity'];

$template->reset();

$template['url'] = MapadoUtils::getEventUrl($activity->getUuid(), $vars['list_slug']);
$template['image'] = '<img src="' . $vars['thumbs']['700x250'][0] . '" alt="' . $activity->getTitle() . '"/>';
$template['title'] = $activity->getTitle();
$template['description'] = apply_filters('the_content', $activity->getDescription(), true);
$template['shortDescription'] = $activity->getShortDescription();
$template['formattedSchedule'] = $activity->getFormattedSchedule();
$template['shortDate'] = $activity->getShortDate();
$price = $activity->getSimplePrice();
$template['price'] = ($price === 0) ? 'Gratuit' : ($price) ? $price . ' €' : false;
$template['widgetActive'] = MapadoPlugin::widgetActive();

$template['address'] = $activity->getAddress()->getFormattedAddress();
$template['place'] = $activity->getFrontPlaceName();
$template['placeUrl'] = MapadoUtils::getPlaceUrl($activity->getLinks());
$template['city'] = null;
if ($activity->getAddress()) {
    $template['city'] = $activity->getAddress()->getCity();
}
$template['mapActive'] = MapadoPlugin::mapActive();
$template['map'] = false;
if ($activity->getAddress()) {
    $template['map'] = function($options) use ($activity) {
        $zoom = (count($options) >= 1)?$options[0]:16;
        $map = <<<EOD
    <script src="http://maps.googleapis.com/maps/api/js"></script>
    <script>
        var mpd_map; //<-- This is now available to both event listeners and the initialize() function
        var mpd_map_lat = {$activity->getAddress()->getLatitude()};
        var mpd_map_lng = {$activity->getAddress()->getLongitude()};
        function initialize() {
            var mapOptions = {
                center: new google.maps.LatLng(mpd_map_lat, mpd_map_lng),
                zoom: $zoom,
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
        google.maps.event.addDomListener(window, "resize", function () {
            var center = mpd_map.getCenter();
            google.maps.event.trigger(mpd_map, "resize");
            mpd_map.setCenter(center);
        });

    </script>
    <div id="mapado-map-canvas"></div>
EOD;
        return $map;
    };
}
echo $template->output();
?>