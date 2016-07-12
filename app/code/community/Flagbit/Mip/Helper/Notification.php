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
class Flagbit_Mip_Helper_Notification {

    private $_notification_emails = array();
    const XML_NOTIFICATION_MAILS_PATH = 'mip_core/settings/debug_email';

    public function __construct(){

        $notificationMails = explode(';',Mage::getStoreConfig(self::XML_NOTIFICATION_MAILS_PATH));

        $validator = new Zend_Validate_EmailAddress();
        foreach ((array) $notificationMails as $notificationMail) {

            if(!$validator->isValid(trim($notificationMail))){
                continue;
            }
            $this->setNotificationMail(trim($notificationMail));
        }
    }


    public function setNotificationMail($email) {
        $this->_notification_emails [] = $email;
    }

    public function getNotificationMails() {
        return $this->_notification_emails;
    }


    public function sendNotificationEmails($subject=null, $additionalBody='') {

        $msg = 'Notification:';

        if($subject === null){
            $subject = 'MIP Notification';
        }

        foreach ( $this->_notification_emails as $receiver ) {
            $mail = new Zend_Mail ( );
            $mail->setBodyText ( $msg."\n". strip_tags($additionalBody) );
            $mail->setFrom ( 'notification@flagbit.de', 'Flagbit Notification ('.Mage::app()->getStore()->getName().')' );
            $mail->addTo ( $receiver );
            $mail->setSubject ((isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] . ' - ' :'') . $subject);
            $mail->send ();
        }

    }

}