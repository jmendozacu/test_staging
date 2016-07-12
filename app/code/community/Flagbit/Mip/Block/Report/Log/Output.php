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
class Flagbit_Mip_Block_Report_Log_Output extends Mage_Core_Block_Template {

    public function runHtmlConsole()
    {
?>
<script type="text/javascript">
if (parent && parent.disableInputs) {
    parent.disableInputs(true);
}

if (typeof auto_scroll=='undefined') {
    var auto_scroll = window.setInterval(console_scroll, 1);
}


function console_scroll()
{
    if (typeof top.$!='function') {
        return;
    }

    dh=$("log_output").scrollHeight
    ch=$("log_output").clientHeight
    if(dh>ch){
        moveme=dh-ch
        $("log_output").scrollTop = moveme;
    }
}

var position = 0;
var running = false;

function getUpdates() {
    // skip when the last request ist not answered yet

    if(running) {
        return;
    }

    running = true;
    new Ajax.Request('<?php echo $this->getUrl('adminhtml/report_mip_log/tail', array('_secure' => Mage::app()->getRequest()->isSecure())) ?>',
            {
                    method: 'get',
                    parameters: { position: position },
                    loaderArea: false,
                    onSuccess: function(response) {
                        running = false;
                        var output = $("log_output");
                        var result = response.responseText.evalJSON();

                        logText = result.text.replace(/\n/g, "<br />")

                        if(logText != "<br />") {
                            output.innerHTML += logText;
                        }

                        position = result.position;
                        },
             });
}

getUpdates();

var pe = new PeriodicalExecuter(
    function(pe) {
        getUpdates();
    }
, 2);

if (parent && parent.disableInputs) {
    parent.disableInputs(false);
}
</script>
</body>
</html>
<?php
    }
    }