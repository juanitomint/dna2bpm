<?php

//---End Class
class dbframe {

    public $template;

    public function __construct() {
        $args = func_get_args();
        if (count($args)) {
            $this->load($args[0], $args[1]);
        }
    }

    public function get($property) {
        return (property_exists($this, $property)) ? $this->$property : null;
    }

    //-----make a template from an object and assing it to template.
    public function makeTemplate($object) {
        $template = array();
        foreach ($object as $key => $value) {
            $template[$key] = gettype($value);
        }
        $this->template = $template;
        return $template;
    }

    public function load($array, $template = null) {
        //---declare safe types
        $array = (object) $array;
        if ($template) {
            $this->template = $template;
            foreach ($template as $key => $value) {
                //var_dump($array,$key);
                if (property_exists($array, $key)) {
                    settype($this->$key, $value);
                    $this->$key = $array->$key;
                } else {
                    $this->$key = '';
                }

                //---check 4 array type
                if ($value == 'array' and $this->$key == '') {
                    $this->$key = array();
                }
                //---check 4 booleans
                if ($value == 'boolean' and is_string($value)) {
                    $this->$key = ($this->$key == 'true') ? true : false;
                }
            }
        } else {

            foreach ($array as $key => $value)
                $this->$key = $value;
        }
    }

    public function loadPostdata($array) {
        //---declare safe types
        $template = $this->template;
        foreach ($template as $key => $value) {
            $this->$key = (isset($array[$key])) ? $array[$key] : null;
            if ($this->template[$key] == 'array') {
                $this->$key = json_decode($this->$key);
            }
            settype($this->$key, $value);
        }
    }

    public function toShow() {
        foreach ($this->template as $key => $value) {
            //---return empty property to show or the value if exists
            $obj[$key] = (!is_null($this->get($key))) ? $this->$key : '';

            switch ($value) {
                case 'array':
                    $obj[$key] = json_encode($this->$key);
                    break;
                case 'boolean':
                    $obj[$key] = ($this->get($key)) ? $this->$key : false;
                    break;
            }
        }
        return $obj;
    }

    public function toSave() {
        ////---prepare the frame to be saved
        foreach ($this->template as $key => $value) {
            $obj[$key] = $this->$key;
            settype( $obj[$key], $value);
        }
        return $obj;
    }

}

?>