<?php

class Mapado_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct('mapado_widget', 'Mapado', array('description' => 'Le widget Mapado'));
    }

    public function widget($args, $instance)
    {
        global $mapado;

        /* in MapadoPublicAuth Class */
        $mapado->eventWidget();
    }
}