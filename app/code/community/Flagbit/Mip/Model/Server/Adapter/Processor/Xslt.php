<?php
/**
 * Copyright (c) 2011 Arne Blankerts <arne@blankerts.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Arne Blankerts nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT  * NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @category  PHP
 * @package   TheSeer\fXSL
 * @author    Arne Blankerts <arne@blankerts.de>
 * @copyright Arne Blankerts <arne@blankerts.de>, All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://github.com/theseer/fxsl
 *
 */

/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_Model_Server_Adapter_Processor_Xslt
    extends XSLTProcessor
    implements Flagbit_Mip_Model_Server_Adapter_Processor_Interface {

    /**
    * Static registry for registered callback objects
    *
    * @var array
    */
    protected static $registry = array();

   /**
    * Flag to signal if initStyleSheet has been called
    *
    * @var boolean
    */
    protected $initDone = false;

   /**
    * Flag to signal if registerPHPFunctions has been called
    *
    * @var boolean
    */
    protected $registered = false;

   /**
    * The given XSL Stylesheet to process
    *
    * @var DOMDocument
    */
    protected $stylesheet;

   /**
    * The spl_object_hash of the current instance
    *
    * @var string
    */
    protected $hash;

    /**
     * input object
     *
     * @var DOMdocument
     */
    protected $_input;

    /**
     * Constructor, allowing to directly inject a Stylesheet for later processing
     *
     * @param string $xsltPath path of the xsl file
     */
    public function __construct($xsltPath = null){
        $this->hash = spl_object_hash($this);
        libxml_use_internal_errors(true);
        if ($xsltPath) {
            $this->setRuleFile($xsltPath);
        }

        // register XSLT Callbacks
        $processors = Mage::getSingleton('mip/config')->getProcessors();
        if(isset($processors['xslt']->settings->callbacks)
            && ($processors['xslt']->settings->callbacks instanceof  Mage_Core_Model_Config_Element)){

            foreach ($processors['xslt']->settings->callbacks->children() as $callback){
                $xsltCallback = Mage::getModel('mip/server_adapter_processor_xslt_callback', array('xmlns' => (string) $callback->xmlns, 'prefix' => (string) $callback->prefix));
                $xsltCallback->setObject(Mage::getModel((string) $callback->model));
                $this->registerCallback($xsltCallback);
            }
        }
    }


    /**
     * transform XML
     *
     * transforms XML File
     */
    public function transform()
    {
       $dom = $this->transformToDoc($this->_input);
       $dom->encoding = 'UTF-8';
       return $dom->saveXML();
   	}


    /**
     * set xsl file
     *
     * @param string $path path of the xsl file
     */
    public function setRuleFile($path)
    {
        $xslDom = new DOMdocument;
        $xslDom->load($path);

        return $this->importStylesheet($xslDom);
    }

    /**
     * @param unknown_type $input
     */
    public function setInput($input)
    {
        $xmlDom = null;
        if ($input instanceof DOMDocument){
            $xmlDom = $input;
        }
        elseif (is_string($input)) {
            $xmlDom = new DOMDocument();
            $xmlDom->preserveWhiteSpace = false;
            $xmlDom->loadXML($input);
        }

        $this->_input = $xmlDom;
        return $this;
    }



   /**
    * Destructor to cleanup registry
    */
   public function __destruct() {
      unset(self::$registry[$this->hash]);
   }

   /**
    * @see Flagbit_Mip_Model_Server_Adapter_Xml_XsltProcessor::importStylesheet()
    *
    * Extended version to throw exception on error
    */
   public function importStylesheet($stylesheet) {
      if ($stylesheet->documentElement->namespaceURI != 'http://www.w3.org/1999/XSL/Transform') {
         throw new Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception(
            "Namespace mismatch: Expected 'http://www.w3.org/1999/XSL/Transform' but '{$stylesheet->documentElement->namespaceURI}' found.",
            Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception::WrongNamespace
         );
      }
      $this->stylesheet = $stylesheet;
   }

   /**
    * @see Flagbit_Mip_Model_Server_Adapter_Xml_XsltProcessor::registerPHPFunctions()
    *
    * Extended version to enforce callability of fXSLProcessor::callbackHook and generally callable methods
    */
   public function registerPHPFunctions($restrict = null) {
      if (is_string($restrict)) {
         $restrict = array($restrict);
      }
      if (is_array($restrict)) {
         foreach ($restrict as $func) {
            if (!is_callable($func)) {
               throw new Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception("'$func' is not a callable method or function", Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception::NotCallable);
            }
         }
         $restrict[] = 'Flagbit_Mip_Model_Server_Adapter_Processor_Xslt::callbackHook';
      }
      $restrict === null ? parent::registerPHPFunctions() : parent::registerPHPFunctions($restrict);
      $this->registered = true;
   }

   /**
    * @see Flagbit_Mip_Model_Server_Adapter_Xml_XsltProcessor::transformToDoc()
    * Extended version to throw exception on error
    */
   public function transformToDoc($node) {
      if(!$this->initDone) {
        $this->initStylesheet();
      }
      libxml_clear_errors();
      $rc = parent::transformToDoc($node);
      if (libxml_get_last_error()) {
         throw new Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception('Error in transformation: '.print_r(libxml_get_last_error(), true), Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception::TransformationFailed);
      }
      return $rc;
   }

   /**
    * @see Flagbit_Mip_Model_Server_Adapter_Xml_XsltProcessor::transformToUri()
       *
    * Extended version to throw exception on erro
    *
    */
   public function transformToUri($doc, $uri) {
      return $this->transformToDoc($doc)->save($uri);
   }

   /**
    * @see Flagbit_Mip_Model_Server_Adapter_Xml_XsltProcessor::transformToXml()
       *
    * Extended version to throw exception on erro
    *
    */
   public function transformToXml($doc) {
      if(!$this->initDone) {
        $this->initStylesheet();
      }
      // Do not remap this to $this->transformToDoc(..)->saveXML()
      // for that will break xsl:output as text, as well as omit xml decl
      libxml_clear_errors();
      $rc = parent::transformToXml($doc);
      if (libxml_get_last_error()) {
         throw new Exception('XSLT Processor: transformToXml - '.libxml_get_last_error()->message);
      }
      return $rc;
   }

   /**
    * Register an Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Callback object instance
    *
    * @param Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Callback $callback The instance of the Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Callback to register
    */
   public function registerCallback(Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Callback $callback) {
      $this->initDone = false;
      if (!$this->registered) {
         $this->registerPHPFunctions();
      }

      if (!isset(self::$registry[$this->hash])) {
         self::$registry[$this->hash] = array();
      }
      self::$registry[$this->hash][$callback->getNamespace()] = $callback;
   }

   /**
    * Static method to be called from within xsl
    *
    * Additional parameters are going to get passed on the to method called
    *
    * @param string $hash       The spl_object_hash of the fXSLProcessor instance the call has been triggered in
    * @param string $namespace  The namespace of the class instance the call is ment for
    * @param string $method     The method to call on the instance specified by namespace
    *
    * @return string|\DomNode
    */
   public static function callbackHook($hash, $namespace, $method) {
      $obj = self::$registry[$hash][$namespace]->getObject();
      $params = array_slice(func_get_args(),3);
      return call_user_func_array(array($obj, $method), $params);
   }

   /**
    * Internal helper to do the template initialisation and injection of registered objects
    */
   protected function initStylesheet() {
      $this->initDone = true;
      libxml_clear_errors();

      if (isset(self::$registry[$this->hash])) {
         foreach(self::$registry[$this->hash] as $cb) {
            $cb->injectCallbackCode($this->stylesheet, $this->hash);
         }
      }
      if (libxml_get_last_error()) {
         throw new Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception('Error registering callbacks', Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception::ImportFailed);
      }
      parent::importStylesheet($this->stylesheet);
      if (libxml_get_last_error()) {
         throw new Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception('Error while importing given stylesheet', Flagbit_Mip_Model_Server_Adapter_Processor_Xslt_Exception::ImportFailed);
      }
   }

}