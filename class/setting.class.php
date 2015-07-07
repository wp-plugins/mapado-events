<?php
/**
 * Class MapadoSetting
 */
Class MapadoSetting extends ArrayObject {
    
    private $index;
    
    private $perpage_options = array( 3, 5, 10, 20, 30, 40 );
    private $perpage_default = 10;
    
    private $card_thumb_position_options = array( 'left' => 'à gauche', 'right' => 'à droite', 'top' => 'en bandeau' );
    private $card_thumb_position_default = 'right';
    
    private $card_thumb_orientation_options = array( 'portrait' => 'portrait', 'landscape' => 'paysage', 'square' => 'carré' );
    
    private $card_thumb_size_options = array( 'l' => 'grand', 'm' => 'moyen', 's' => 'petit' );
    
    private $card_column_max_options = array( '4' => 'jusqu\'à 4', '3' => 'jusqu\'à 3', '2' => 'jusqu\'à 2', '1' => 'toujours 1' );
    private $card_column_max_default = '2';

    private $card_template_default = 
'[%title]
    <h3 class="mpd-card__title">
        <a href="[[url]]">
            [[title]]
        </a>
    </h3>
[title%]

[%date]
    <p class="mpd-card__date">
        [[date]]
    </p>
[date%]

[%place]
    <p class="mpd-card__address">
        <a href="[[placeUrl]]" target="_blank">
            [[place]]
        </a>
        [%city]
            <span class="mpd-card__city">
                - [[city]]
            </span>
        [city%]
    </p>
[place%]

[%description]
    <p class="mpd-card__description">
        [[description]]
        <a href="[[url]]" class="mpd-card__read-more-link">→ Lire la suite</a>
    </p>
[description%]';


    /**
     * Initialization
     */
    public function __construct($index) {
        $this->index = $index;
        $settings = get_option( $index );
        if (is_array($settings)) {
            $this->exchangeArray($settings);
        }
    }


    /**
     * Wordpress methods
     */
    public function update() {
        return update_option( $this->index, $this->getArrayCopy() );
    }

    
    /**
     * Administration methods
     */
    public function getDefinition($name) {
        return array('options' => $this->getOptions($name), 'value' => $this->getValue($name));
    }
    
    public function getOptions($name) {
        $optionsAttribute = $name.'_options';
        if (isset($this->$optionsAttribute)) {
            return $this->$optionsAttribute;
        }
        return array();
    }
    
    public function getValue($name) {
        if (isset($this[$name])) {
            return $this[$name];
        }
        $defaultAttribute = $name.'_default';
        if (isset($this->$defaultAttribute)) {
            return $this->$defaultAttribute;
        }
        $options = $this->getOptions($name);
        return reset($options);
    }

}