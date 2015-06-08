<div class="mpd-card-list__item">
    <div class="mpd-card">
        <?php if ( !empty($vars['activity']->getImageUrlList()[$vars['card_thumb_design']['dimensions']][0]) ) : ?>
            <img class="mpd-card__thumb
                        mpd-card__thumb--<?= $vars['card_thumb_design']['position'] ?>
                        mpd-card__thumb--<?= $vars['card_thumb_design']['orientation'] ?>
                        mpd-card__thumb--<?= $vars['card_thumb_design']['size'] ?>"
                 src="<?php echo $vars['activity']->getImageUrlList()[$vars['card_thumb_design']['dimensions']][0] ?>"
                 alt="<?php echo $vars['activity']->getTitle() ?>"
                />
        <?php endif; ?>
    
        <div class="mpd-card__body">
            <?php if ( $vars['activity']->getTitle() ) : ?>
                <h3 class="mpd-card__title">
                    <a href="<?php echo MapadoUtils::getEventUrl( $vars['activity']->getUuid(), $vars['list_slug'] ) ?>">
                        <?php echo $vars['activity']->getTitle() ?>
                    </a>
                </h3>
            <?php endif; ?>
    
            <?php if ( $vars['activity']->getShortDate() ) : ?>
                <p class="mpd-card__date">
                    <?php echo $vars['activity']->getShortDate() ?>
                </p>
            <?php endif; ?>
    
            <?php if ( $vars['activity']->getFrontPlaceName() ) : ?>
                <p class="mpd-card__address">
                    <a href="<?php echo MapadoUtils::getPlaceUrl( $vars['activity']->getLinks() ) ?>" target="_blank">
                        <?php echo $vars['activity']->getFrontPlaceName() ?>
                    </a>
                </p>
            <?php endif; ?>
    
            <?php if ( $vars['activity']->getShortDescription() ) : ?>
                <p class="mpd-card__description">
                    <?php echo $vars['activity']->getShortDescription() ?>
                    <a href="<?php echo MapadoUtils::getEventUrl( $vars['activity']->getUuid(), $vars['list_slug'] ) ?>"
                       class="mpd-card__read-more-link"
                        >→ Lire la suite</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>