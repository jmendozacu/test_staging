<?php
/**
 * fXSLTProcessorException
 *
 * @category  PHP
 * @package   TheSeer\fXSL
 * @author    Arne Blankerts <arne@blankerts.de>
 * @access    public
 */
class Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception extends Exception {

   const WrongNamespace         = 1;
   const ImportFailed           = 2;
   const NotCallable            = 3;
   const UnkownInstance         = 4;
   const TransformationFailed     = 5;

}
