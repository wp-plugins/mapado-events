<?php
/**
 * Mapado Plugin
 * Single Event TPL
 */
?>

<div id="mapado-plugin" class="mapado_activity_single">

<?php
    MapadoUtils::template('event_template', $vars);
?>

    <div class="mpd-credits">
        Informations powered by
        <a href="<?php echo $vars['activity']->getLinks()['mapado_url']['href'] ?>" target="_blank">Mapado</a>
    </div>
</div>