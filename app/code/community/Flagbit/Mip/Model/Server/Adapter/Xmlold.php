<?php
/**
 * This source file is subject to the Magento Integration Platform License
 * that is bundled with this package in the file LICENSE_MIP.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.flagbit.de/license/mip
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to magento@flagbit.de so we can send you a copy immediately.
 *
 * The Magento Integration Platform is a property of Flagbit GmbH & Co. KG.
 * It is NO part or deravative version of Magento and as such NOT published
 * as Open Source. It is NOT allowed to copy, distribute or change the
 * Magento Integration Platform or any of its parts. If you wish to adapt
 * the software to your individual needs, feel free to contact us at
 * http://www.flagbit.de or via e-mail (magento@flagbit.de) or phone
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * Dieser Quelltext unterliegt der Magento Integration Platform License,
 * welche in der Datei LICENSE_MIP.txt innerhalb des MIP Paket hinterlegt ist.
 * Sie ist außerdem über das World Wide Web abrufbar unter der Adresse:
 * http://www.flagbit.de/license/mip
 * Falls Sie keine Kopie der Lizenz erhalten haben und diese auch nicht über
 * das World Wide Web erhalten können, senden Sie uns bitte eine E-Mail an
 * magento@flagbit.de, so dass wir Ihnen eine Kopie zustellen können.
 *
 * Die Magento Integration Platform ist Eigentum der Flagbit GmbH & Co. KG.
 * Sie ist WEDER Bestandteil NOCH eine derivate Version von Magento und als
 * solche nicht als Open Source Softeware veröffentlicht. Es ist NICHT
 * erlaubt, die Software als Ganze oder in Einzelteilen zu kopieren,
 * verbreiten oder ändern. Wenn Sie eine Anpassung der Software an Ihre
 * individuellen Anforderungen wünschen, kontaktieren Sie uns unter
 * http://www.flagbit.de oder via E-Mail (magento@flagbit.de) oder Telefon
 * (+49 (0)800 FLAGBIT (3524248)).
 *
 *
 *
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 * @copyright   2009 by Flagbit GmbH & Co. KG
 * @author      Flagbit Magento Team <magento@flagbit.de>
 */


/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_Model_Server_Adapter_Xmlold
    extends Flagbit_Mip_Model_Server_Adapter_Abstract
    implements Flagbit_Mip_Model_Server_Adapter_Interface
    {

    /**
     * input Data
     *
     * @param string $resource Name of the Resourcemodel
     * @param string $action
     * @param string $data
     * @return array
     */
    public function input($resource, $action, $data) {

        # XML-Daten laden
        $xmlDom = new DOMdocument;
        $xmlDom->preserveWhiteSpace = false;
        $xmlDom->loadXML($data);

        // dispatch Event for Data manipulation
        Mage::dispatchEvent('mip_xml_adapter_'.$resource.'_'.$action.'_input', array('data' => &$xmlDom, 'adapter' => $this));

        $xsltFile = $this->_getXslt($resource, $action, 'input');
        # XSLT Stylesheet als "normales" XML-DOM Dokument laden
        if($xsltFile !== null){

            Mage::helper('mip/log')->getWriter($this)->info('XML Adapter: XSLT Template '.$xsltFile);

            $xslProcessor = $this->getTrigger()->getProcessor();
            $xslProcessor->setRuleFile($xsltFile);
            $xslProcessor->setInput($xmlDom);

            $xml = $xslProcessor->transform();

            $xml = $this->filterData($xml);

            $xmlDom = new DOMdocument;
            $xmlDom->loadXML($xml);
        }

        return $this->xmlToData($xmlDom);
    }

    /**
     * get XSL Template File
     *
     * @param string $resource
     * @param string $action
     * @param string $direction
     * @return string
     */
    protected function _getXslt($resource, $action, $direction){

        $file = null;
        if($this->getSettings('xslt') !== null && file_exists($this->getPath() . $this->getSettings('xslt'))){
            $file = $this->getPath() . $this->getSettings('xslt');
        }elseif(file_exists($this->getPath() . $resource .'_'. $action .'_'.$direction.'.xslt')){
            $file = $this->getPath() . $resource .'_'. $action .'_'.$direction.'.xslt';
        }
        return $file;
    }

    /**
     * output Data
     *
     * @param string $resource
     * @param string $action
     * @param string $data
     * @return string
     */
    public function output($resource, $action, $data)
    {
        # XML-Daten laden
        $xmlDom = $this->dataToXml($data);

        $xsltFile = $this->_getXslt($resource, $action, 'output');

        if($xsltFile !== null){

            $xslProcessor = $this->getTrigger()->getProcessor();
            $xslProcessor->setRuleFile($xsltFile);
            $xslProcessor->setInput($xmlDom);

            return $xslProcessor->transform();

        }else{
            return $xmlDom->saveXML();
        }
    }

    /**
     * transform XML to Array
     *
     * @param DOMNode $parentNode
     * @return array
     */
    protected function xmlToData(DOMNode $parentNode)
    {
        $retVal = array();
            foreach ($parentNode->childNodes as $domNode) {

               if (count($domNode->childNodes)
                   && $domNode->firstChild !== NULL
                   && !$domNode->firstChild instanceof DOMCharacterData) {

                   if(/*$domNode->firstChild->tagName == $domNode->lastChild->tagName
                       && */ $domNode->nodeName != 'node'){

                       $retVal[$domNode->nodeName] = $this->xmlToData($domNode);
                   }else{
                       $retVal[] = $this->xmlToData($domNode);
                   }
               } else {
                   switch (get_class($domNode)){

                       case 'DOMText':
                           $retVal[$domNode->nodeName] = $domNode->wholeText;
                           break;

                       default:
                           if($domNode->tagName == 'node'){
                               if ( !is_numeric($domNode->nodeValue) && empty($domNode->nodeValue)){
                                   $retVal = array();
                               }
                               else{
                                   $retVal[] = $domNode->nodeValue;
                               }

                           }else{
                               $retVal[$domNode->tagName] = $domNode->nodeValue;
                           }
                   }
               }
           }
           return $retVal;
    }

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param DomElement $elem - should only be used recursively
     * @param DOMDocument $xml - should only be used recursively
     * @return object DOMDocument
     */
    protected function dataToXml(array $data, $rootNodeName = 'data', $elem=null, $xml=null)
    {

        if ($xml === null)
        {
            $xml = new DOMDocument("1.0", "UTF-8");
            $xml->formatOutput = true;
            $elem = $xml->createElement( $rootNodeName );
              $xml->appendChild( $elem );
        }

        // loop through the data passed in.
        foreach((array) $data as $key => $value)
        {
            // no numeric keys in our xml please!
            if (is_numeric($key)) {
                $key = 'node';
            }

            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z0-9\_]/i', '', (string)$key);

            // if there is another array found recrusively call this function
            if (is_array($value))
            {
                $subelem = $xml->createElement( $key );
                $elem->appendChild( $subelem);
                // recrusive call.
                $this->DataToXml($value, $rootNodeName, $subelem, $xml);
            }
            else
            {
                $subelem = $xml->createElement( $key );
                $subelem->appendChild(
                    strstr($value, array('<','>'))
                    ? $xml->createCDATASection( $value )
                    : $xml->createTextNode( $value )
                );
                $elem->appendChild( $subelem );
            }
        }
        // pass back as DOMDocument object
        return $xml;
    }

}