<?php

class Flagbit_Mip_Block_System_Config_Form_Field_Universal_Select extends Mage_Core_Block_Html_Select {


    public function setInputName($value)
    {
        return $this->setName($value);
    }

}