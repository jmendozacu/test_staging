<?php

#
# perfekt-schlafen: update/add footer cms/blocks
#

# set scope
$scopeId = 1; // perfekt schlafen=1, matratzendiscount=4

# set identifier array
$identifierList[] = 'ps_footer_perfektschlafen';    #1
$identifierList[] = 'ps_footer_kundenservice';      #2
$identifierList[] = 'ps_footer_produkte';           #3
$identifierList[] = 'ps_footer_zahlungsmethoden';   #4
$identifierList[] = 'ps_footer_bottom';             #5
$identifierList[] = 'ps_footer2_social_media';      #6
$identifierList[] = 'ps_footer2_app';               #7
$identifierList[] = 'ps_footer2_bekannt_aus';       #8

# delete blocks, if exist
foreach ($identifierList as $key => $identifier) {
    $cmsBlock = Mage::getModel('cms/block')->load($identifier, 'identifier');
    if ($cmsBlock->getId()) {$cmsBlock->delete();}
}


# set content

#1
$content = <<<EOF
<div class="block">
    <div class="block-title"><strong><span>PERFEKT SCHLAFEN</span></strong></div>

    <div class="block-content">
    <ul class="links">
        <li><a href="{{config path="web/unsecure/base_url"}}aboutus/" title="Über Uns">Über Uns</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}gutscheine/" title="(Geschenk-) Gutscheine">(Geschenk-) Gutscheine</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}jobs/" title="Jobs & Karriere">Jobs & Karriere</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}partner/" title="Partnerprogramm">Partnerprogramm</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}ratgeber/" title="Ratgeber">Ratgeber</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}ratgeber/magazin/" title="Magazin">Magazin</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}presse/" title="Presse">Presse</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}agb/" title="Unsere AGB">Unsere AGB</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}datenschutz/" title="Datenschutz">Datenschutz</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}impressum/" title="Impressum">Impressum</a></li>
        <li><a href="{{config path="web/unsecure/base_url"}}widerruf/" title="Widerruf">Widerruf</a></li>
    </ul>
    </div>

</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer column 1 - Perfekt Schlafen');
$cmsBlock->setIdentifier('ps_footer_perfektschlafen');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


#2
$content = <<<EOF
<div class="block">
    <div class="block-title"><strong><span>KUNDENSERVICE</span></strong></div>
    <div class="block-content">
        <ul class="kunden-info">
            <li><a href="{{config path="web/unsecure/base_url"}}faq/">Häufige Fragen</li>
            <li><a href="{{config path="web/unsecure/base_url"}}kontakt/">Kontakt</a></li>
            <li><a href="{{config path="web/unsecure/base_url"}}faq-retour/">Rückgabe</a></li>
            <li><a href="{{config path="web/unsecure/base_url"}}faq-lieferung/">Liefergebiet & Versand</a></li>
            <li><a href="{{config path="web/unsecure/base_url"}}faq-zahlung/">Zahlung</a></li>
            <li><a href="{{config path="web/unsecure/base_url"}}matratzen-sonderanfertigung-sondergroessen/">Sonderanfertigungen & -größen</a></li>
            <li><a href="{{config path="web/unsecure/base_url"}}faq-deals/">Groupon Deals</a></li>
            <li><a href="{{config path="web/unsecure/base_url"}}showroom/">Showroom in Berlin</a></li>
        </ul>
    </div>
    <div class="block-number">
        <span class="phone-icon"></span>
        <span class="phone-number">030 / 200 97 00 33</span><br />
        <span class="phone-text">Fachberatung vom Experten-Team</span>
    </div>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer column 2 - Kundenservice');
$cmsBlock->setIdentifier('ps_footer_kundenservice');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


#3
$content = <<<EOF
<div class="block">
    <div class="block-title"><strong><span>PRODUKTE</span></strong></div>
    <div class="block-content">
        <ul class="kunden-info">
            <li><a href="#">Matratze 80x200</a></li>
            <li><a href="#">Matratze 90x200</a></li>
            <li><a href="#">Matratze 100x200</a></li>
            <li><a href="#">Matratze 120x200</a></li>
            <li><a href="#">Matratze 140x200</a></li>
            <li><a href="#">Matratze 160x200</a></li>
            <li><a href="#">Matratze 180x200</a></li>
            <li><a href="#">Matratze 200x200</a></li>
            <li><a href="#">Bett 120x200</a></li>
            <li><a href="#">Bett 140x200</a></li>
            <li><a href="#">Bett 160x200</a></li>
            <li><a href="#">Bett 180x200</a></li>
            <li><a href="#">Bett 200x200</a></li>

        </ul>
    </div>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer column 3 - Produkte');
$cmsBlock->setIdentifier('ps_footer_produkte');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


#4
$content = <<<EOF
<div class="block">
    <div class="payment-block">
        <div class="block-title"><strong><span>ZAHLUNGSMETHODEN</span></strong></div>
        {{block type="cms/block" block_id="payment-icons"}}
    </div>

    <div class="foot-bewertung">
        <div class="block-title"><strong><span>GEPRÜFTE LEISTUNG & PORTALE</span></strong></div>

        <a href="https://www.trustedshops.de/bewertung/info_XA39C17E0597670D259289CAA00EB1610.html" target="_blank">
            <img alt="Perfekt-Schlafen Bewertungen bei Trusted Shops" src="https://www.trustedshops.com/bewertung/widget/widgets/XA39C17E0597670D259289CAA00EB1610.gif" title="Perfekt-Schlafen Bewertungen bei Trusted Shops">
        </a>

        <a href="http://www.erfahrungen.com/mit/perfekt-schlafen-de/" target="_blank">
            <img alt="Perfekt-Schlafen Bewertungen bei Erfahrungen.com" src="https://www.erfahrungen.com/images/widgets/7190.png" width="100"  title="Perfekt-Schlafen Bewertungen bei Erfahrungen.com" class="trust-erfahrung">
        </a>
    </div>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Footer column 4 - Zahlungsmethoden');
$cmsBlock->setIdentifier('ps_footer_zahlungsmethoden');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


#5
$content = <<<EOF
<sup>1</sup> Nur gültig für Matratzen und Lattenroste, nicht gültig für Sondergrößen & -anfertigungen<br />
<sup>2</sup> Gültig bei einem Einkauf im Wert von mindestens 200 Euro bis maximal 2200 Euro.<br />
Eigentümer und Betreiber der Webseite ist sleepz GmbH, Deutschland<br />
Alle Preise inkl. gesetzlicher Mehrwertsteuer, zzgl. Versand.<br />
Die durchgestrichenen Preise entsprechen dem bisherigen Preis bei perfekt-schlafen.<br />
© 2003-2016 perfekt-schlafen.de. Alle Rechte vorbehalten.
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('PS - Footer bottom');
$cmsBlock->setIdentifier('ps_footer_bottom');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


#6
$content = <<<EOF
<div class="title-footer2">SOCIAL MEDIA:</div>
<ul class="social-links">
    <li class="soc-fb"><a href="https://www.facebook.com/perfektschlafen" target="_blank"><span></span></a></li>
    <li class="soc-pn"><a href="https://de.pinterest.com/perfektschlafen/" target="_blank"><span></span></a></li>
    <li id="fb-like-btn">
        <div id="fb-privacy-popup"></div>
        <div class="dummy" onclick="showFacebookPrivacyPopup()">
            <div class="fb-logo"></div>Gefällt mir
        </div>
    </li>
</ul>

EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('PS - Footer2 column 3 - social media');
$cmsBlock->setIdentifier('ps_footer2_social_media');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


#7
$content = <<<EOF
<div class="title-footer2">PERFEKT SCHLAFEN APP:</div>
<ul>
 <li class="app-apple"><a href="#"><span></span></a></li>
 <li class="app-android"><a href="#"><span></span></a></li>
</ul>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('PS - Footer2 column2 - apps');
$cmsBlock->setIdentifier('ps_footer2_app');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


#8
$content = <<<EOF
<div class="title-footer2">BEKANNT AUS:</div>
<ul>
 <li class="bek-rtl"><span></span></li>
 <li class="bek-handel"><span></span></li>
 <li class="bek-jolle"><span></span></li>
 <li class="bek-bild"><span></span></li>
 <li class="bek-fursie"><span></span></li>
</ul>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('PS - Footer2 column 1 - bekannt aus');
$cmsBlock->setIdentifier('ps_footer2_bekannt_aus');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();