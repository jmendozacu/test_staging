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
class Flagbit_Mip_Model_Server_Adapter_Csv extends Flagbit_Mip_Model_Server_Adapter_Xml {


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
        $output = null;
        $xmlDom = new DOMdocument;
        $xmlDom->preserveWhiteSpace = false;
        $xmlDom->loadXML(parent::output($resource, $action, $data));

        $dataArray = $this->xmlToData($xmlDom, $resource);
        if(!empty($dataArray['data']) && is_array($dataArray['data'])){
            foreach($dataArray['data'] as $line){
                $output .= $this->_arrayToCsv($line, $this->getSettings('delimiter'), $this->getSettings('enclosure'), $this->getSettings('escape'))."\n";
            }
        }
        //$this->getTrigger()->getController()->getResponse()->setHeader('Content-Type', 'text/csv');

        if ($this->getSettings('charset_out') ) {

            $output = iconv('UTF-8', $this->getSettings('charset_out'), $output);
        }

        return $output;
    }

    protected function _cleanUpHeader($headerArray)
    {
        $col = 0;
        foreach($headerArray as &$value){
            $col = $col + 1;
            $value = preg_replace('/[^a-z0-9\_]/i', '', (string)$value);
            if(is_numeric($value)){
                $value = 'numeric_'.$value;
            }
            if(empty($value)){
                $value = 'col_'.$col;
            }
        }
        return $headerArray;
    }

    protected function _arrayToCsv($input, $delimiter=',', $enclosure='"', $escape='\\')
    {
        foreach ($input as $key => $value){
            if(is_array($value)){
                $value = "[ARRAY]";
            }
            $input[$key]=str_replace($enclosure,$escape.$enclosure,$value);
        }
        return $enclosure.implode($enclosure.$delimiter.$enclosure,$input).$enclosure;
    }

    /**
     * input
     *
     * converts the csv data to xml and calls the parent input method of the xml adapter
     * for processing data.
     * if more than one csv file defined the $csvData attribute will be an array containing the csv strings
     * the parsed xml strings will be attached to each other
     *
     * @param string $resource
     * @param string $action
     * @param string|array $csvData
     *
     * @return array the parsed data
     */
    public function input($resource, $action, $csvData) {

        $csvData = $this->_handleEncoding($csvData);

        if (is_array($csvData)){

            $xmlDom = null;
            $data = array();

            foreach($csvData as $item){
                $data[] = $this->getDataFromCsv($resource, $action, $item);
            }

            $xmlDom = $this->dataToXml($data, $resource);

        }
        else{
            $data = $this->getDataFromCsv($resource, $action, $csvData);
            $xmlDom = $this->dataToXml($data, $resource);
        }

        return parent::input($resource, $action, $xmlDom->saveXML());
    }

    /**
     * @param $csvData
     * @return mixed|string
     */
    protected function _handleEncoding($csvData)
    {
        $returnValue = $csvData;
        if (is_array($csvData)){
            foreach($csvData as &$item){
                $item = $this->_handleEncoding($item);
            }
        }else{
            if ($this->getSettings('charset_in') ) {
                $returnValue = iconv($this->getSettings('charset_in'), 'UTF-8', $csvData);
            }
            else {
                $returnValue = utf8_encode($csvData);
            }
            $returnValue = str_replace(chr(7), ' ', $returnValue);
        }
        return $returnValue;
    }


    /**
     * parsing the csv file
     *
     * @param string $resource resource name
     * @param string $action action anme
     * @param string $csvData csv string
     * @return array csv data array
     */
    public function getDataFromCsv($resource, $action, $csvData) {

        $hasError = false;

        $data = array();
        $keys = array();

        $lineNumber = 1;
        foreach (preg_split("/\r\n|\r|\n/", $csvData) as $line){

            $values = $this->str_getcsv($line, $this->getSettings('delimiter'), $this->getSettings('enclosure'), $this->getSettings('escape'));

            if (!is_array($values)){
                continue;
            }

            if (empty($keys)){

                if ($this->getSettings('header')){
                    $keys = $this->_cleanUpHeader($values);
                    continue;
                }
                else{
                    for ($i = 1; $i <= count($values); $i++){
                        $keys[] = 'col_'.$i;
                    }
                }
            }

            /*if (count($keys) < count($values)){
                $values = array_chunk($values, count($keys));
                $values = $values[0];
            }
            elseif (count($keys) > count($values)){
                $keys = array_chunk($keys, count($values));
                $keys = $keys[0];
            }*/

            if(count($keys) != count($values)){
                continue;
            }

            try {
                $row = array_combine($keys, $values);
            }
            catch(Exception $e){
                Mage::helper('mip/log')->getWriter($this)->error('CSV Parse Error at Line :'.$lineNumber, $e);
                $hasError = true;
                $lineNumber++;
                continue;
            }


            Mage::dispatchEvent('mip_csv_adapter_'.$resource.'_'.$action.'_input_line', array('data' => &$row));

            $data[] = $row;
            $lineNumber ++;
        }

        if ($hasError){
            throw new Flagbit_Mip_Model_Exception('Errors found while parsing the CSV File');
        }

        return $data;
    }

    /**
     * wrapper for the php str_getcsv function
     *
     * cuecks the exitence of the str_getcsv for compatibility with php versions less than 5.3 and
     * creates a file handle for using fregcsv in case of php < 5.3
     *
     * @param string $input csv data
     * @param string $delimiter delimiter string
     * @param string $enclosure enclosure string
     * @param string $escape escape string
     */
    public function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {

        if (function_exists('str_getcsv')) {
            return str_getcsv($input, $delimiter, $enclosure, $escape );
        }

        $fiveMBs = 5 * 1024 * 1024;
        $fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
        fputs($fp, $input);
        rewind($fp);

        $data = fgetcsv($fp, strlen($input), $delimiter, $enclosure); //  $escape only got added in 5.3.0

        fclose($fp);
        return $data;
    }

}