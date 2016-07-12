<?php

class Flagbit_Mip_Tools_CliXslInterpreter {

    protected $_args = array();
    
    public function __construct($args)
    {    
        $this->_args = $this->_parseCliArgs($args);
    }
    
    public function run()
    {
        $data = file_get_contents($this->_args['xml']);
        $xsltFile = file_get_contents($this->_args['xslt']);
        preg_match_all('', $xsltFile, $matches);

        $xsltFile = preg_replace('/{{config\b(?>\s+(?:module="([^"]*)"()|group="([^"]*)"()|field="([^"]*)"()|default="([^"]*)"())|[^\s}}]+|\s+)*\2\4}}/siU', '\7', $xsltFile);

        # XML-Daten laden 
        $xmlDom = new DOMdocument; 
        $xmlDom->preserveWhiteSpace = false; 
        $xmlDom->loadXML($data);
        
        if(!empty($xsltFile)){
                    
            $xslDom = new DOMdocument; 
            $xslDom->loadXML($xsltFile); 
            
            $xsl = new XsltProcessor; // XSLT Prozessor Objekt erzeugen 
            $xsl->importStylesheet($xslDom); // Stylesheet laden     

            $xml = $xsl->transformToXML($xmlDom);

            $xmlDom = new DOMdocument; 
            $xmlDom->loadXML($xml);
        }
     
        $fp = fopen($this->_args['output'], "w+");
        fwrite($fp, $xmlDom->saveXML());
        fclose($fp);        

    }
    
    protected function _parseCliArgs($argv)
    {
        array_shift($argv); $o = array();
        foreach ($argv as $a){
            if (substr($a,0,2) == '--'){ $eq = strpos($a,'=');
                if ($eq !== false){ $o[substr($a,2,$eq-2)] = substr($a,$eq+1); }
                else { $k = substr($a,2); if (!isset($o[$k])){ $o[$k] = true; } } }
            else if (substr($a,0,1) == '-'){
                if (substr($a,2,1) == '='){ $o[substr($a,1,1)] = substr($a,3); }
                else { foreach (str_split(substr($a,1)) as $k){ if (!isset($o[$k])){ $o[$k] = true; } } } }
            else { $o[] = $a; } }
        return $o;
    }

}

$cliXlsInterpreter = new Flagbit_Mip_Tools_CliXslInterpreter($_SERVER['argv']);
$cliXlsInterpreter->run();