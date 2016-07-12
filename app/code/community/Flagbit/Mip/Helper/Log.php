<?php
// add Log4PHP to the include path
$inc_base = Mage::getBaseDir('lib') . DS . 'Log4php';
$add_path = $inc_base . PS . implode(PS, array($inc_base . DS . 'appenders', $inc_base . DS . 'configurators', $inc_base . DS . 'filters', $inc_base . DS . 'helpers', $inc_base . DS . 'pattern', $inc_base . DS . 'layouts', $inc_base . DS . 'renderers'));
set_include_path(get_include_path() . PS . $add_path);

class Flagbit_Mip_Helper_Log extends Logger {

    const RUN_MODE_LOG      = 'log';
    const RUN_MODE_TRACE    = 'debug';
    const RUN_MODE_DISABLED = 'disabled';

    const XML_DEVLOG_PATH   = 'mip_core/settings/devlog';

    /**
     * @var bool
     */
    private static $isInitialized = false;

    /**
     * Prefix
     * @var string
     */
    protected $_prefix = '';

    /**
     * Constructor.
     * @param string $name Name of the logger.
     */
    public function __construct($name = null) {
        if(is_string($name)){
            parent::__construct($this->_rewriteName($name));
        }
    }

    /**
     * get Log Writer
     *
     * @param $name
     * @return Logger
     */
    public function getWriter($name)
    {
        // init logger
        if (!self::$isInitialized) {
            $this->initMageLogger();
        }

        // define logger name
        if (is_object($name)) {
            return parent::getLogger(self::_rewriteName(get_class($name)));
        } else {
            return parent::getLogger(self::_rewriteName($name));
        }
    }

    /**
     * set name prefix
     *
     * @param $prefix
     * @return $this
     */
    public function setNamePrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }

    /**
     * init Logger
     *
     * @param string $file
     */
    public function initMageLogger( $mode = self::RUN_MODE_LOG )
    {
        // is logging enabled?
        if(Mage::getStoreConfigFlag(self::XML_DEVLOG_PATH) !== FALSE && $mode != self::RUN_MODE_DISABLED){

            // load config by run mode
            $config = Mage::getSingleton('mip/log_config', array( 'fileName' => 'mip'.$mode.'.xml') );

            // convert to log4php config
            $configuration = Mage::getSingleton('mip/log_configurator')->convert($config);

            // configure log4php
            self::configure($configuration);

            // fix Logfile Paths
            foreach($this->getRootLogger()->getAllAppenders() as $appender){
                if(is_callable(array($appender, 'setFile')) && is_callable(array($appender, 'getFile'))){
                    $appender->setFile( Mage::getBaseDir() . DS . $appender->getFile());
                    $appender->activateOptions();
                }
            }

            // add echo Output when MIP is running from cli
            if(Mage::helper('mip')->isRunningFromShell()){
                //$this->_addEchoOutput();
            }

        // logging is disabled
        }else{
            self::configure();
            $this->getRootLogger()->removeAllAppenders();
            $this->getRootLogger()->addAppender(new LoggerAppenderNull);
        }

        self::$isInitialized = true;
    }

    /**
     * add Echo Output Appender
     */
    protected function _addEchoOutput()
    {
        $layout = new LoggerLayoutPattern();
        $layout->setConversionPattern("%-6level (%r/%mem) [%logger] %message%newline");
        $layout->activateOptions();

        $echoAppender = null;
        /* @var $appender LoggerAppender */
        foreach($this->getRootLogger()->getAllAppenders() as $appender){
            if($appender instanceof LoggerAppenderEcho){
                $echoAppender = $appender;
                break;
            }
        }

        if($echoAppender === null){
            $echoAppender = new LoggerAppenderEcho('cli');
            $this->getRootLogger()->addAppender($echoAppender);
        }

        $echoAppender->setLayout($layout);
        $echoAppender->setThreshold('info');
        $echoAppender->activateOptions();
    }

    /**
     * set Name
     *
     * @param string $name
     */
    public function setName($name)
    {
        parent::setName($this->_rewriteName($name));
    }

    /**
     * rewrite name
     *
     * @param $name
     * @return string
     */
    private function _rewriteName($name)
    {
        return strtolower(($this->_prefix ? $this->_prefix.'.' : '').str_replace('Flagbit.Mip.Model.', '', str_replace('_', '.', $name)));
    }

}