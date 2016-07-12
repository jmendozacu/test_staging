<?php

/**
 * @category   Sleepz
 * @package    Sleepz_Adnymics_Helper_Data
 * @extends    Mage_Core_Model_Abstract
 * @author     minfo@sleepz.com
 * @website    http://www.sleepz.com
 *
 * @method Sleepz_Adnymics_Helper_Data::getGenderIdByOptionText(genderText string): int
 */
class Sleepz_Adnymics_Helper_Data extends Mage_Core_Helper_Abstract
{
    /** @const Map the gender of adnymics for m eq men and f eq women or null is unisex */
    const _ADNYMICS_GENDER_DEFAULT_M = 0;
    const _ADNYMICS_GENDER_DEFAULT_F = 1;
    const _ADNYMICS_GENDER_DEFAULT_NON = 2;

    public function getGenderIdByOptionText($genderText)
    {
        switch ($genderText) {
            case 'Herr':
                $retGenderId = array('result' => self::_ADNYMICS_GENDER_DEFAULT_M);
                break;
            case 'Frau':
                $retGenderId = array('result' => self::_ADNYMICS_GENDER_DEFAULT_F);
                break;
            default:
                $retGenderId = array('result' => self::_ADNYMICS_GENDER_DEFAULT_NON);
                break;
        }
        return $retGenderId;
    }
}