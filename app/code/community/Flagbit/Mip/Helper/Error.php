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

require_once 'Zend/Mail.php';

/**
 * @package     Flagbit
 * @subpackage  Flagbit_Mip
 */
class Flagbit_Mip_Helper_Error {

    private $_debug_emails = array();
    const XML_DEBUG_MAILS_PATH = 'mip_core/settings/debug_email';


    public function __construct(){

        $debugMails = explode(';',Mage::getStoreConfig(self::XML_DEBUG_MAILS_PATH));

        $validator = new Zend_Validate_EmailAddress();
        foreach ((array) $debugMails as $debugMail) {

            if(!$validator->isValid(trim($debugMail))){
                continue;
            }
            $this->setDebugMail(trim($debugMail));
        }
    }


    public function setDebugMail($email) {
        $this->_debug_emails [] = $email;
    }

    public function getDebugMails() {
        return $this->_debug_emails;
    }


    public function sendDebugEmails($exception, $subject=null, $additionalBody='') {

        $message = $exception->getMessage ();
        if($exception instanceof Flagbit_Mip_Model_Exception){
            $message.= ' - '.$exception->getCustomMessage();
        }

        $text = '';
        $html = '';
        if($exception instanceof Exception){
            $text = $this->getExceptionCLI ( $exception );
            $html = $this->getExceptionWeb ( $exception );

            if($subject === null){
                $subject = $message;
            }
        }

        foreach ( $this->_debug_emails as $receiver ) {

            $mail = new Zend_Mail ( );
            $mail->setBodyText ( $text. strip_tags($additionalBody) );
            $mail->setBodyHtml ( $html. $additionalBody );
            $mail->setFrom ( 'debug@flagbit.de', 'Flagbit Exception ('.Mage::app()->getStore()->getName().')' );
            $mail->addTo ( $receiver );
            $mail->setSubject ((isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] . ' - ' :'') . $subject);
            $mail->send ();
        }

    }


    /**
     * Displays the given exception
     *
     * @param Exception $exception: The exception object
     * @return void
     * @author Robert Lemke <robert@typo3.org>
     */
    public function handleException(Exception $exception)
    {
        if(!Mage::getIsDeveloperMode()){
            $this->sendDebugEmails($exception);
        }else{

            switch (php_sapi_name ()) {
                case 'cli' :
                    echo $this->getExceptionCLI ( $exception );
                    break;
                default :
                    echo $this->getExceptionWeb ( $exception );
            }
        }
    }

    /**
     * Formats and echoes the exception as XHTML.
     *
     * @param  Exception $exception: The exception object
     * @return void
     * @author Robert Lemke <robert@typo3.org>
     */
    public function getExceptionWeb(Exception $exception) {

        $filePathAndName = $exception->getFile ();

        $exceptionCodeNumber = ($exception->getCode () > 0) ? '#' . $exception->getCode () . ': ' : '';
        $codeSnippet = $this->getCodeSnippet ( $exception->getFile (), $exception->getLine () );
        $backtraceCode = $this->getBacktraceCode ( $exception->getTrace () );
        $message = $exception->getMessage ();
        if($exception instanceof Flagbit_Mip_Model_Exception){
            $message.= '<br/>'.$exception->getCustomMessage();
        }

        $return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
            <head>
                <title>Flagbit Library Exception</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            </head>
            <style>
                .ExceptionProperty {
                    color: #101010;
                }
                pre {
                    margin: 0;
                    font-size: 11px;
                    color: #515151;
                    background-color: #D0D0D0;
                    padding-left: 30px;
                }
            </style>
            <div style="
                    position: absolute;
                    left: 10px;
                    background-color: #B9B9B9;
                    outline: 1px solid #515151;
                    color: #515151;
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    margin: 10px;
                    padding: 0;
                ">
                <div style="width: 100%; background-color: #515151; color: white; padding: 2px; margin: 0 0 6px 0;">Uncaught Flagbit Library Exception</div>
                <div style="width: 100%; padding: 2px; margin: 0 0 6px 0;">
                    <strong style="color: #BE0027;">' . $exceptionCodeNumber . $message . '</strong> <br />
                    <br />
                    <span class="ExceptionProperty">' . get_class ( $exception ) . '</span> thrown in file<br />
                    <span class="ExceptionProperty">' . $filePathAndName . '</span> in line
                    <span class="ExceptionProperty">' . $exception->getLine () . '</span>.<br />
                    <br />
                    ' . $backtraceCode . '
                </div>
        ';


        $return .= '
            </div>
        ';
        return $return;
    }

    /**
     * Formats and echoes the exception for the command line
     *
     * @param Exception $exception: The exception object
     * @return void
     * @author Robert Lemke <robert@typo3.org>
     */
    public function getExceptionCLI(Exception $exception) {
        $filePathAndName = $exception->getFile ();

        $exceptionCodeNumber = ($exception->getCode () > 0) ? '#' . $exception->getCode () . ': ' : '';

        $return = "\nUncaught Flagbit Library " . $exceptionCodeNumber . $exception->getMessage () . "\n";
        $return .= "thrown in file " . $filePathAndName . "\n";
        $return .= "in line " . $exception->getLine () . "\n\n";
        return $return;
    }

    /**
     * Renders some backtrace
     *
     * @param array $trace: The trace
     * @return string Backtrace information
     * @author Robert Lemke <robert@typo3.org>
     */
    protected function getBacktraceCode(array $trace) {
        $backtraceCode = '';
        if (count ( $trace )) {
            foreach ( $trace as $index => $step ) {
                if (isset ( $step ['file'] )) {
                    $stepFileName = $step ['file'];
                } else {
                    $stepFileName = '< unknown >';
                }
                $class = isset ( $step ['class'] ) ? $step ['class'] . '<span style="color:white;">::</span>' : '';

                $arguments = '';
                if (isset ( $step ['args'] ) && is_array ( $step ['args'] )) {
                    foreach ( $step ['args'] as $argument ) {
                        $arguments .= (strlen ( $arguments ) == 0) ? '' : '<span style="color:white;">,</span> ';
                        if (is_object ( $argument )) {
                            $arguments .= '<span style="color:#FF8700;"><em>' . get_class ( $argument ) . '</em></span>';
                        } elseif (is_string ( $argument )) {
                            $preparedArgument = (strlen ( $argument ) < 40) ? $argument : substr ( $argument, 0, 20 ) . '…' . substr ( $argument, - 20 );
                            $preparedArgument = htmlspecialchars ( $preparedArgument );
                            $preparedArgument = str_replace ( "\n", '<span style="color:white;">⏎</span>', $preparedArgument );
                            $arguments .= '"<span style="color:#FF8700;">' . $preparedArgument . '</span>"';
                        } elseif (is_numeric ( $argument )) {
                            $arguments .= '<span style="color:#FF8700;">' . ( string ) $argument . '</span>';
                        } else {
                            $arguments .= '<span style="color:#FF8700;"><em>' . gettype ( $argument ) . '</em></span>';
                        }
                    }
                }

                $backtraceCode .= '<pre style="color:#69A550; background-color: #414141; padding: 4px 2px 4px 2px;">';
                $backtraceCode .= '<span style="color:white;">' . (count ( $trace ) - $index) . '</span> ' . $class . $step ['function'] . '<span style="color:white;">(' . $arguments . ')</span>';
                $backtraceCode .= '</pre>';

                if (isset ( $step ['file'] )) {
                    $backtraceCode .= $this->getCodeSnippet ( $step ['file'], $step ['line'] ) . '<br />';
                }
            }
        }

        return $backtraceCode;
    }

    /**
     * Returns a code snippet from the specified file.
     *
     * @param string $filePathAndName: Absolute path and file name of the PHP file
     * @param integer $lineNumber: Line number defining the center of the code snippet
     * @return string The code snippet
     * @author Robert Lemke <robert@typo3.org>
     */
    protected function getCodeSnippet($filePathAndName, $lineNumber) {
        $codeSnippet = '<br />';
        if (@file_exists ( $filePathAndName )) {
            $phpFile = @file ( $filePathAndName );
            if (is_array ( $phpFile )) {
                $startLine = ($lineNumber > 2) ? ($lineNumber - 2) : 1;
                $endLine = ($lineNumber < (count ( $phpFile ) - 2)) ? ($lineNumber + 3) : count ( $phpFile ) + 1;
                if ($endLine > $startLine) {
                    $codeSnippet = '<br /><span style="font-size:10px;">' . $filePathAndName . ':</span><br /><pre>';
                    for($line = $startLine; $line < $endLine; $line ++) {
                        $codeLine = str_replace ( "\t", ' ', $phpFile [$line - 1] );

                        if ($line == $lineNumber)
                            $codeSnippet .= '</pre><pre style="background-color: #F1F1F1; color: black;">';
                        $codeSnippet .= sprintf ( '%05d', $line ) . ': ' . $codeLine;
                        if ($line == $lineNumber)
                            $codeSnippet .= '</pre><pre>';
                    }
                    $codeSnippet .= '</pre>';
                }
            }
        }
        return $codeSnippet;
    }

}