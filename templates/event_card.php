<?php
    $card_template = $vars['card_template'];
$activity = $vars['activity'];
?>
<div class="chew-cell">
    <div class="chew-card mpd-card">
        <?php if ( !empty($activity->getImageUrlList()[$vars['card_thumb_design']['dimensions']][0]) ) : ?>
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
            $card_template->reset();
            $card_template['url'] = MapadoUtils::getEventUrl( $activity->getUuid(), $vars['list_slug'] );
            $card_template['title'] = $activity->getTitle();
            $card_template['description'] = $activity->getShortDescription();
            $card_template['date'] = $activity->getShortDate();
            $card_template['place'] = $activity->getFrontPlaceName();
            $card_template['placeUrl'] = MapadoUtils::getPlaceUrl( $activity->getLinks() );
            $card_template['city'] = null;
            if ($activity->getAddress()) {
                $card_template['city'] = $activity->getAddress()->getCity();
            }
            echo $card_template->output();
        ?>
        </div>
    </div>
</div>