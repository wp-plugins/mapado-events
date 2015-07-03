<?php

/**
 * Class MapadoMicroTemplate
 */
Class MapadoMicroTemplate extends ArrayObject {

    private $template;


    /**
     * Initialization
     */
    public function __construct($template) {
        $this->template = $template;
    }


    /**
     * Public function
     */
    public function assign($key, $value) {
        $this->offsetSet($key, $value);
    }
    
    public function reset() {
        $this->exchangeArray(array());
    }
    
    public function getTemplate() {
        return $this->template;
    }
    
    public function output() {
        $output = $this->template;
        foreach ($this as $key => $value) {
            $replace = '$1';
            if (empty($value)) {
                $replace = '';
            }
            $output = preg_replace('/\[%'.$key.'](.*)\['.$key.'%]/s', $replace, $output);
        }
        foreach ($this as $key => $value) {
            $output = preg_replace('/\[\['.$key.'\]\]/', $value, $output);
        }

        return $output;
    }

}