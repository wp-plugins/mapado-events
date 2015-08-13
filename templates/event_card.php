<?php
    $activity = $vars['activity'];
?>
<div class="chew-cell">
    <div class="chew-card mpd-card">
        <?php if (!empty($activity->getImageUrlList()[$vars['card_thumb_design']['dimensions']][0])) : ?>
            <img class="mpd-card__thumb
                        mpd-card__thumb--<?= $vars['card_thumb_design']['position_type'] ?>
                        mpd-card__thumb--<?= $vars['card_thumb_design']['position_side'] ?>
                        mpd-card__thumb--<?= $vars['card_thumb_design']['orientation'] ?>
                        mpd-card__thumb--<?= $vars['card_thumb_design']['size'] ?>"
                 src="<?php echo $activity->getImageUrlList()[$vars['card_thumb_design']['dimensions']][0] ?>"
                 alt="<?php echo $activity->getTitle() ?>"
                />
        <?php endif; ?>
        <div class="mpd-card__body">
            <?php
            MapadoUtils::template('event_template', $vars);
            ?>
        </div>
    </div>
</div>