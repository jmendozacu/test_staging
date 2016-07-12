<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Welance
 * @package   Welance_ExtendCustomer
 * @author    welance GbR <hello@welance.de>
 * @author    Niels Gongoll <ng@welance.de>
 * @copyright 2012 welance GbR
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.welance.de/
 */
/* @var $installer Welance_DBSetup_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('footer_top_marken', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
 <h3 class="text-left mg-tp-20">Top-Marken zum Traumpreis auf Matratzendiscount.de</h3>
            <div class="footer-logos owl-carousel mg-tp-20">
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
                <div class="item"><a href=""><img src="{{skin url='img/innobett.png'}}"/></a></div>
            </a></div>

EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer Top Marken');
$cmsBlock->setIdentifier('footer_top_marken');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
/**
 * array of store ids
 */
$cmsBlock->setStores(array(4));
$cmsBlock->save();

/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('footer_zahlungsmethoden', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
<img class="icon pull-left" src="{{skin url='img/paypal-ico.jpg'}}"/>
                    <img class="icon pull-left" src="{{skin url='img/amazon-ico.jpg'}}"/>
                    <img class="icon pull-left" src="{{skin url='img/sofortueberweisung-ico.jpg'}}"/>
                    <img class="icon pull-left" src="{{skin url='img/visa-ico.jpg'}}"/>
                    <img class="icon pull-left" src="{{skin url='img/mastercard-ico.jpg'}}"/>
                    <img class="icon pull-left" src="{{skin url='img/barzahlen-ico.jpg'}}"/>
                    <img class="icon pull-left" src="{{skin url='img/ratepay-ico.jpg'}}"/>
                    <img class="icon pull-left" src="{{skin url='img/rechnung-ico.jpg'}}"/>
                    <img class="icon pull-left" src="{{skin url='img/vorkasse-ico.jpg'}}"/>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer Zahlungsmethoden');
$cmsBlock->setIdentifier('footer_zahlungsmethoden');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
/**
 * array of store ids
 */
$cmsBlock->setStores(array(4));
$cmsBlock->save();


/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('footer_menu_left', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF

                    <h3>Matratzendiscount</h3>
                    <ul>
                        <li>
                            <a href="/wissenswert/">Matratzenberater</a>
                        </li>
                        <li>
                            <a href="/aboutus/">Über uns</a>
                        </li>
                        <li>
                            <a href="/partner/">Partnerprogramm</a>
                        </li>
                        <li>
                            <a href="/agb/">AGB</a>
                        </li>
                        <li>
                            <a href="/datenschutz/">Datenschutz</a>
                        </li>
                        <li>
                            <a href="/widerruf/">Widerruf</a>
                        </li>
                        <li>
                            <a href="/impressum/">Impressum</a>
                        </li>
                    </ul>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer Menu Left');
$cmsBlock->setIdentifier('footer_menu_left');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
/**
 * array of store ids
 */
$cmsBlock->setStores(array(4));
$cmsBlock->save();

/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('footer_menu_right', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
  <h3>Kundenservice</h3>
                    <ul>
                        <li>
                            <a title="Kontakt" href="/kontakt/">Kontakt</a>
                        </li>
                        <li>
                            <a title="Liefergebiet" href="/liefergebiet/">Liefergebiet</a>
                        </li>
                        <li>
                            <a title="Liefergebiet" href="/versand/">Versand</a>
                        </li>
                        <li>
                            <a title="Zahlung" href="/zahlung/">Zahlung</a>
                        </li>
                        <li>
                            <a title="Showroom" href="/showroom/">Showroom</a>
                        </li>
                    </ul>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer Menu Right ');
$cmsBlock->setIdentifier('footer_menu_right');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
/**
 * array of store ids
 */
$cmsBlock->setStores(array(4));
$cmsBlock->save();


/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('footer_leistungen', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
     <h3>Geprüfte Leistung</h3>
                    <p class="fs-normal">
                        Kostenlose Lieferung schon ab 49 €1
                        Kostenlose Rücklieferung
                        Sichere Zahlung
                        14 Tage Rückgaberecht
                        Käuferschutz
                    </p>

EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer Leistungen');
$cmsBlock->setIdentifier('footer_leistungen');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
/**
 * array of store ids
 */
$cmsBlock->setStores(array(4));
$cmsBlock->save();

/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('footer_icons', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
<img class="icon" src="{{skin url='img/trusted_shops.gif'}}"/><img class="icon" src="{{skin url='img/idealo.png'}}"/>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer Icons');
$cmsBlock->setIdentifier('footer_icons');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
/**
 * array of store ids
 */
$cmsBlock->setStores(array(4));
$cmsBlock->save();

/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('footer_fussnote', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
1Gültig für deutsches Festland.
                    Alle Preise inkl. der gesetzlichen Mehrwertsteuer.
                    Eigentümer und Betreiber der Webseite ist sleepz GmbH, Deutschland
                    Die durchgestrichenen Preise entsprechen dem bisherigen Preis bei Matratzendiscount.de.
                    23,9 ct/Min aus dem dt. Festnetz, Mobilfunkgebühren anbieterabhängig
                    **Gültig bei einem Einkauf im Wert von mindestens 200 Euro bis maximal 2200 Euro.
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer Fußnote');
$cmsBlock->setIdentifier('footer_fussnote');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
/**
 * array of store ids
 */
$cmsBlock->setStores(array(4));
$cmsBlock->save();

/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('header_leistungen', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
<ul>
<li>
<img alt="Versandkostenfrei ab 49,-" src="http://www.matratzendiscount.de/media/wysiwyg/matratzendiscount/kostenloser-versand.png">
<span>Versandkostenfrei ab 49 €
<sup>1</sup></span>
</li>
<li>
<img alt="14 Tage Probeschlafen" src="http://www.matratzendiscount.de/media/wysiwyg/matratzendiscount/probeschlafen.png">
<span>14 Tage Probeschlafen</span>
</li>
<li>
<img alt="Geld-Zurück-Garantie" src="http://www.matratzendiscount.de/media/wysiwyg/matratzendiscount/garantie.png">
<span>Geld-Zurück-Garantie</span>
</li>
<li>
<img alt="0% Finanzierung" src="http://www.matratzendiscount.de/media/wysiwyg/matratzendiscount/0-prozent.png">
<span>Finanzierung</span>
</li>
</ul>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Header Leistungen');
$cmsBlock->setIdentifier('header_leistungen');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
/**
 * array of store ids
 */
$cmsBlock->setStores(array(4));
$cmsBlock->save();

$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 4;

$content = <<<EOF
Melden Sie sich für unseren Newsletter an
EOF;
$configSwitch->saveConfig('porto/settings/footer/footer/newsletter/title', $content, $scope, $scopeId);

$content = <<<EOF
Ihre Daten werden nicht an Dritte weitergegeben. Die Abmeldung vom Newsletter ist jederzeit möglich. Der Gutscheincode ist einmalig gültig und gilt für Artikel ab einem Mindestbestellwert von 50 Euro je Einkauf, ausgenommen sind die Marken "Badenia", "Badenia-Irisette", "Irisette" und "Tempur". Nicht mit weiteren Gutscheinen/Rabatten kombinierbar. Eine Barauszahlung ist nicht möglich.
EOF;
$configSwitch->saveConfig('row/porto/settings/footer/footer_newsletter', $content, $scope, $scopeId);

$path = 'porto_design/footer/';
$configSwitch->saveConfig($path.'custom', '1', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_top_bgcolor', '#ffffff', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_top_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_top_link_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_top_link_hover_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_bgcolor', '#ffffff', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_link_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_link_hover_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_title_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_links_icon_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_2_bgcolor', '#ffffff', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_2_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_2_link_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_2_link_hover_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_middle_2_title_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_bottom_bgcolor', '#ffffff', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_bottom_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_bottom_link_color', '#333333', $scope, $scopeId);
$configSwitch->saveConfig($path.'footer_bottom_link_hover_color', '#333333', $scope, $scopeId);
