<?php

/*
 * this update includes all cms blocks of the home page
 *
 * List of all the blocks:
 *
 * #1 home-teaser-slider-html
 * #2 home-teaser-slider-css
 * #3 home-sub-teaser-boxes
 * #4 home-aktionsboxen
 * #5 home-info-teaser-1
 * #6 home-micro-teaser
 * #7 home-categories
 * #8 home-seo-marken
 * #9 home-seo-product-1
 * #10 home-seo-product-2
 * #11 home-seo-brands-carousel
 * #12 home-seo-text
 *
 */


#$configSwitch = new Mage_Core_Model_Config();

/*
 * SET SCOPE
 */
#$scope = 'websites';
$scopeId = 1;



/*
 * IF BLOG EXISTS, DELETE IT...
 */
// #1
$cmsBlock = Mage::getModel('cms/block')->load('home-teaser-slider-html', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #2
$cmsBlock = Mage::getModel('cms/block')->load('home-teaser-slider-css', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #3
$cmsBlock = Mage::getModel('cms/block')->load('home-sub-teaser-boxes', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #4
$cmsBlock = Mage::getModel('cms/block')->load('home-aktionsboxen', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #5
$cmsBlock = Mage::getModel('cms/block')->load('home-info-teaser-1', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #6
$cmsBlock = Mage::getModel('cms/block')->load('home-micro-teaser', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #7
$cmsBlock = Mage::getModel('cms/block')->load('home-categories', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #8
$cmsBlock = Mage::getModel('cms/block')->load('home-seo-marken', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #9
$cmsBlock = Mage::getModel('cms/block')->load('home-seo-product-1', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #10
$cmsBlock = Mage::getModel('cms/block')->load('home-seo-product-2', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #11
$cmsBlock = Mage::getModel('cms/block')->load('home-seo-brands-carousel', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
// #12
$cmsBlock = Mage::getModel('cms/block')->load('home-seo-text', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}



/*
 * SET THE CONTENT...
 */





/*
 * #1 home-teaser-slider-html
 */
$blockcontent = <<<EOF
<!-- matraflex -->
<div class="item">
    <a href="">
        <div></div>
        <img src="{{media url="wysiwyg/sleepz/slider/_with_text_Fruehlingsanfang-2016-Teaser.jpg"}}" />
    </a>
</div>

<!-- matraflex -->
<div class="item">
    <a href="">
        <div></div>
        <img src="{{media url="wysiwyg/sleepz/slider/_with-text_sleep-for-fit-messe-estrel-berlin.jpg"}}" />
    </a>
</div>

<div class="item">
    <a href="">
        <div></div>
        <img src="{{media url="wysiwyg/sleepz/slider/_with-text_ostern.jpg"}}" />
    </a>
</div>

<div class="item">
    <a href="">
        <div></div>
        <img src="{{media url="wysiwyg/sleepz/slider/_with-text_matraflex.jpg"}}" />
    </a>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Slider Html');
$cmsBlock->setIdentifier('home-teaser-slider-html');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #2 home-teaser-slider-css
 */
$blockcontent = <<<EOF
<style type="text/css">

    #slideshow .text-overlay {
		position: absolute;
		z-index: 1;
    }

    /* Schaltjahr 2016 */

    .schaltjahr #item-01 {
        position: absolute; top: 85px; left: 192px;
        font-size: 50pt;
        color: #fff;
        letter-spacing: 7px;
        z-index: 1;
    }

    .schaltjahr span#item-02 {
        position: absolute; top: 55px; left: 190px;
        font-size: 170pt;
        color: #fff;
        letter-spacing: 7px;
        z-index: 1;
    }

    .schaltjahr span#item-03 {
        position: absolute; top: 260px; left: 300px;
        font-size: 50pt;
        color: #fff;
        letter-spacing: 7px;
        z-index: 1;
    }

    .schaltjahr span#item-04 {
        position: absolute; top: 170px; left: 320px;
        font-size: 50pt;
        color: #fff;
        letter-spacing: 7px;
        z-index: 1;
    }

    .schaltjahr span#item-05 {
        position: absolute; top: 250px; left: 360px;
        font-size: 50pt;
        color: #fff;
        letter-spacing: 7px;
        z-index: 1;
    }

    .schaltjahr span#gutscheincode {
        position: absolute; top: 19px; left: 748px;
        font-size: 12pt;
        color: #fff;
        letter-spacing: 0px;
        text-align: center;
        z-index: 1;
    }

    .schaltjahr span#gutscheincode strong {
        font-size: 14pt;
        color: #fff;
        letter-spacing: 0px;
        z-index: 1;
    }

    .schaltjahr-teaser-wrapper {
        width: 100%;
        position: relative;
    }

    /* end of Schaljahr */

    .black-bg-title {
        background-color: #000;
        color: #fff;
        display: inline-block;
        font-size: 17px;
        margin: 30px 0;
        padding: 8px 20px;
        text-transform: uppercase;
    }

    .cta-button {
        position: absolute;
        z-index: 1;
        background-color: #000;
        border: 1px solid #fff;
        padding: 0 7px;
    }

    .multi-teaser-h1 {
        margin-top: 20px;
        text-align: center;
        position: absolute;
        z-index: 10;
        width: 100%;
        font-size: 18px;
        font-weight: 500;
    }

    .slider-text-overlay {
        padding-top: 250px;
    }

    .slider-ratepay-0 {
        font-size: 75px;
        line-height: 1em;
    }

    .slider-ratepay-percent {
        font-size: 60px;
    }

    .slider-ratepay-circle {
        padding-top: 50px;
    }

    .slider-ratepay-text {
        font-size: 14px;
        line-height: 3.8em;
    }

    /* Teaser Schlaraffia */
    .schlaraffia-text-overlay {
        padding: 43px 15px 0;
    }

    .schlaraffia-text-overlay h2 {
        background: transparent;
        color: #000000;
        font-size: 25px;
        letter-spacing: 1.1px;
        line-height: 1.13em;
    }

    .schlaraffia-text-overlay span {
        font-size: 1.16em;
        letter-spacing: 1px;
        font-weight: 800;
    }

    .slider-cta-box {
        position: absolute;
        right: 40px;
        box-sizing: border-box;
        width: 330px;
        height: 94px;
        padding-top: 18px;
        background: none repeat scroll 0 0 #fdc700;
        background: -moz-linear-gradient(top, #fdc700 0%, #FEA000 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fdc700), color-stop(100%, #FEA000)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #fdc700 0%, #FEA000 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #fdc700 0%, #FEA000 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(top, #fdc700 0%, #FEA000 100%); /* IE10+ */
        background: linear-gradient(to bottom, #fdc700 0%, #FEA000 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fdc700', endColorstr='#FEA000', GradientType=0); /* IE6-9 */
        color: #fff;
        border: 2px solid #fff;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        line-height: 1.3;
        font-weight: 700;
        font-size: 20px;
    }

    .logo-overlay {
        background: #013e9b;
        box-shadow: 0 0 3px #888888;
        height: 63px;
        margin-left: 36px;
        padding-top: 0;
        width: 234px;
        padding-top: 0;
    }

    .logo-overlay img {
        height: 40px;
        width: auto;
        margin: 0 auto;
        padding: 10px 0;
    }

    .usp-list {
        padding: 38px 4px 0;
        font-size: 12.2px;
        color: #000;
        letter-spacing: -0.003em;
        line-height: 1.15em;
    }

    .usp-list li {
        margin: 0 7px 9px;
    }

    .usp-list img {
        position: static !important;
        margin-right: 7px;
        height: 14px;
    }

    .usp-sofort {
        position: absolute;
        z-index: 2;
        right: -1%;
        top: 50%;
    }

    .usp-sofort div {
        height: 76px;
        width: 266px;
        font-size: 18px;
        padding-top: 13px;
    }

    .usp-price {
        padding: 20px 110px;
        color: #000;
        font-size: 18px;
    }

    .usp-price .number {
        font-weight: 700;
        font-size: 30px;
    }

    .usp-price .currency {
        font-weight: 700;
        font-size: 26px;
    }


    /* Sale 65 */
    .sale-65-title {
        color: #fff;
        font-size: 52px;
        font-weight: 700;
        margin-top: 10px;
    }

    .sale-65-number-above {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        margin: 26px 0 -30px;
    }

    .padd2 {
        padding: 10px 35px;
    }


    /* estella-50*/
    .estella-50 {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
        width: 315px;
        padding: 20px;
        color: #FFF;
        text-align: center;
    }
    .estella-50 .estella-title, .estella-50 .estella-title2  {
        font-size: 24px;
        margin-left: 0px;
        color: #FFF;
        display: inline-block;
        position: relative;
        line-height: 0.5;
        margin-top: 39px;
    }
    .estella-50 .estella-title2  {
        margin-top: 18px;
    }
    .estella-50 .estella-title:before,
    .estella-50 .estella-title:after {
        content: "";
        position: absolute;
        height: 5px;
        border-bottom: 1px solid white;
        top: 0;
        width: 37px;
    }
    .estella-50 .estella-title:before {
        right: 100%;
        margin-right: 20px;
    }
    .estella-50 .estella-title:after {
        left: 100%;
        margin-left: 20px;
    }
    .estella-50 .estella-number {
        font-size: 76px;
        text-align: center;
        font-weight: 600;
        margin-left: 18px;
        margin-top: 20px;
    }
    .estella-50 .estella-percentage {
        font-size: 57px;
    }
    .estella-50 .estella-subnumber{
        font-size: 38px;
        font-weight: 600;
        margin-top: -10px;
        margin-left: 20px;
        letter-spacing: 3px;
    }
    .estella-50 .estella-subtitle {
        font-size: 18px;
        margin-top: 43px;
        line-height: 20px;
        padding: 3px;
        letter-spacing: 0px;
        margin-left: 0px;
    }
    .estella-50 .estella-text {
        margin-top: 14px;
        font-size: 9px;
        margin-left: 0px;
    }


    /* Teaser HNL Sale*/
    .hnl-sale .slider-text-overlay h2 {
        background: transparent;
        display: inline;
        line-height: 1;
        padding-left: 55px;
        font-size: 22px;
    }

    .hnl-sale .logo-overlay {
        position: absolute;
        background: #ffffff;
        box-shadow: 0 0 3px #888888;
        height: 65px;
        right: 20px;
        padding-top: 0;
        width: 124px;
        padding-top: 0;
        text-align: center;
    }

    .hnl-sale .logo-overlay img {
        height: 50px;
        padding: 5px 0;
    }

    .hnl-sale .slider-circle {
        margin-right: 150px;
    }

    .hnl-sale .slider-ratepay-0 {
        font-size: 40px;
        line-height: 0.7;
        letter-spacing: 0;
    }

    .hnl-sale .slider-ratepay-percent {
        font-size: 30px;
        line-height: 0.7;
    }

    .hnl-sale .slider-ratepay-circle {
        border: 2px solid #fff;
        border-radius: 147px;
        height: 147px;
        width: 147px;
        padding-top: 25px;
        font-size: 22px;
        letter-spacing: 1.5px;
        line-height: 0.9;
    }

    .hnl-sale .slider-ratepay-text {
        font-size: 12px;
        line-height: 1.5;
    }

    .hnl-sale .slider-cta {
        margin-top: 18%;
    }

    .hnl-sale .slider-text-overlay-btn-arrows {
        padding: 13px 38px;
    }

    .hnl-sale .text-overlay {
        padding-top: 30px;
    }


    .slider-text-overlay {
        height: auto !important;
    }


    .relative {
        position: relative;
    }

    .cta-button h3 {
        margin: 0 !important;
    }

</style>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Slider CSS');
$cmsBlock->setIdentifier('home-teaser-slider-css');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * 3 home-sub-teaser-boxes
 */
$blockcontent = <<<EOF
<!-- subteaser -->
<div class="subteaser-wrapper">
    <a href="/#/" class="box">
        <div class="title">Auf welcher Matratze liegen<br>Sie richtig?</div>
        <div class="cta">Zum Matratzenfinder</div>
    </a>
    <a href="/#/" class="box sale">
        <div class="title">Sale</div>
        bis zu <span class="percent">6<span class="null">0</span>%</span> sparen!
    </a>
    <a href="/#/" class="box">
        <div class="title">Persönliche Schlafberatung<br>im Showroom</div>
        <div class="cta">Markenvielfalt zum Anfassen</div>
    </a>
    <div class="clearfix"></div>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Sub Teaser');
$cmsBlock->setIdentifier('home-sub-teaser-boxes');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #4 home-aktionsboxen
 */
$blockcontent = <<<EOF
<!-- aktionsboxen -->
<section class="aktions-boxen">
    <span class="homepage-title">Top Sale Kategorien: <span class="light">Schnäppchen für ausgeschlafene Kunden</span></span>
    <ul>
        <li>
            <div class="icon-percent-container"><span class="icon-percent"></span></div>
            <a href="/sale/moebel/">
                <div class="img-wrapper">
                    <img src="{{media url="wysiwyg/sleepz/homepage/Schlafsofa-und-Couches.jpg"}}" alt="">
                </div>
                <h3>Schlafsofa & Couches</h3>
                <div class="cta">Jetzt sparen</div>
            </a>
        </li>
        <li>
            <div class="icon-percent-container"><span class="icon-percent"></span></div>
            <a href="/sale/kopfkissen/">
                <div class="img-wrapper">
                    <img src="{{media url="wysiwyg/sleepz/homepage/Kissen-und-Decken.jpg"}}" alt="">
                </div>
                <h3>Kissen & Decken</h3>
                <div class="cta">Jetzt sparen</div>
            </a>
        </li>
    </ul>
    <div class="clearfix"></div>
</section>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Aktionsboxen');
$cmsBlock->setIdentifier('home-aktionsboxen');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #5 home-info-teaser-1
 */
$blockcontent = <<<EOF
<!-- infoteaser 1 -->

<style>

    .schlaraffia-teaser {
        position: relative;
        display: block;
        margin-top: 25px;
    }

    .schlaraffia-teaser .cta {
        background: none repeat scroll 0 0 #fdc700;
        background: -moz-linear-gradient(top, #fdc700 0%, #FEA000 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #fdc700), color-stop(100%, #FEA000)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #fdc700 0%, #FEA000 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #fdc700 0%, #FEA000 100%); /* Opera 11.10+ */
        background: -ms-linear-gradient(top, #fdc700 0%, #FEA000 100%); /* IE10+ */
        background: linear-gradient(to bottom, #fdc700 0%, #FEA000 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fdc700', endColorstr='#FEA000', GradientType=0); /* IE6-9 */
        border: 2px solid #fff;
        bottom: 124px;
        box-sizing: border-box;
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.15em;
        line-height: 1.3;
        padding: 10px 30px;
        position: absolute;
        right: 30px;
        text-align: center;
        text-transform: uppercase;
    }

    .schlaraffia-teaser .text-overlay {
        position: absolute;
        padding-left: 30px;
        margin-top: 20px;
    }

    .schlaraffia-teaser .logo {
        background: #013e9b none repeat scroll 0 0;
        box-shadow: 0 0 3px #888888;
        height: auto;
        padding: 8px 0;
        width: 234px;
    }

    .schlaraffia-teaser strong {
        font-size: 25px;
        line-height: 1em;
        margin: 35px 0 30px;
        text-transform: uppercase;
        display: block;
    }

    .schlaraffia-teaser strong span {
        font-size: 1.3em;
    }

    .schlaraffia-teaser .features {
        color: #000;
        font-size: 0.95em;
        list-style: outside none none;
        margin-left: -8px;
        padding: 0;
    }

    .schlaraffia-teaser .features img {
        margin-right: 10px;
    }

    .schlaraffia-teaser .features li {
        padding: 4px 0;
    }

    .schlaraffia-teaser .price {
        color: #000;
        font-size: 16px;
        margin: 30px 100px;
    }

    .schlaraffia-teaser .price .number {
        font-size: 2em;
        font-weight: 700;
    }

    .schlaraffia-teaser .price .currency {
        font-size: 1.6em;
        font-weight: 700;
    }

    .schlaraffia-teaser .teaser-img {
        width: 100%;
        height: auto;
    }

</style>

<section class="info-teaser-1">
    <span class="homepage-title">Exklusive Marken & Produkte: <span class="light">Lassen Sie sich inspirieren</span></span>

    <!-- Schlaraffia images + text overlay-->
    <a href="http://www.perfekt-schlafen.de/matratzen?fq[ps_brand]=Schlaraffia" class="schlaraffia-teaser">
        <div class="cta">Ab sofort<br>hier erhältlich!</div>
        <div class="text-overlay">
            <img src="{{media url="wysiwyg/sleepz/homepage/schlaraffia_logo_x2.png"}}" alt="Sclaraffia" class="logo">
            <strong>Deutsche Schlaf-Tradition<br>
            seit über <span>100</span> Jahren</strong>
            <ul class="features">
                <li><img src="{{media url="wysiwyg/sleepz/homepage/bullet.png"}}">Permanentes „Hygiene-Schutzschild“ für Allergiker</li>
                <li><img src="{{media url="wysiwyg/sleepz/homepage/bullet.png"}}">Druckentlastende Unterstützung des Beckens</li>
                <li><img src="{{media url="wysiwyg/sleepz/homepage/bullet.png"}}">Softes Einsinken der Schultern</li>
                <li><img src="{{media url="wysiwyg/sleepz/homepage/bullet.png"}}">Gesundheits- und umweltverträglich</li>
            </ul>
            <div class="price">
                schon ab <span class="number">229</span> <span class="currency">€</span>
            </div>
        </div>
        <img src="{{media url="wysiwyg/sleepz/homepage/schlaraffia-v2.jpg"}}" alt="Schlaraffia Schlaftradition" class="teaser-img">
    </a>
</section>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Info Teaser 1');
$cmsBlock->setIdentifier('home-info-teaser-1');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #6 home-micro-teaser
 */
$blockcontent = <<<EOF
<!-- microteaser -->
<section class="micro-teaser">
    <a href="/catalogsearch/result/?q=moebel+hasena">Ihre Schlafzimmer-Empfehlung vom Osterhasen: <strong>Designer-Möbel von Hasena</strong></a>
</section>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Micro Teaser');
$cmsBlock->setIdentifier('home-micro-teaser');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #7 home-categories
 */
$blockcontent = <<<EOF
<section class="home-categories">

    <span class="homepage-title">Alles zum Schlafen: <span class="light">Deutschlands größte Auswahl an Schlafartikeln</span></span>

    <div class="homepage-catalog categories">

        <a href="/matratzen/">
            <img src="{{media url="wysiwyg/sleepz/homepage/catalog/kategorie-matratzen-und-topper.jpg"}}" alt="Kategorie Matratzen & Topper">
            <div class="details category">
                <div class="feature">Matratzen & Topper</div>
                <div class="cta">Jetzt Ansehen</div>
            </div>
        </a>

        <a href="/lattenroste/">
            <img src="{{media url="wysiwyg/sleepz/homepage/catalog/kategorie-lattenroste.jpg"}}" alt="Kategorie Lattenroste">
            <div class="details category">
                <div class="feature">Lattenroste</div>
                <div class="cta">Jetzt Ansehen</div>
            </div>
        </a>

        <a href="/bettwaren/">
            <img src="{{media url="wysiwyg/sleepz/homepage/catalog/kategorie-kopfkissen-und-decken.jpg"}}" alt="Kategorie Kopfkissen & Decken">
            <div class="details category">
                <div class="feature">Kopfkissen & Decken</div>
                <div class="cta">Jetzt Ansehen</div>
            </div>
        </a>

        <a href="/bettwaesche/">
            <img src="{{media url="wysiwyg/sleepz/homepage/catalog/kategorie-bettwaesche.jpg"}}" alt="Kategorie Bettwäsche">
            <div class="details category">
                <div class="feature">Bettwäsche</div>
                <div class="cta">Jetzt Ansehen</div>
            </div>
        </a>
    </div>

    <div class="homepage-catalog categories">

        <a href="/betten/">
            <img src="{{media url="wysiwyg/sleepz/homepage/catalog/kategorie-betten-und-boxspringbetten.jpg"}}" alt="Kategorie Betten & Boxspringbetten">
            <div class="details category">
                <div class="feature">Betten & Boxspringbetten</div>
                <div class="cta">Jetzt Ansehen</div>
            </div>
        </a>

        <a href="/moebel/schlafsofas/" class="col-2">
            <img src="{{media url="wysiwyg/sleepz/homepage/catalog/kategorie-schlafsofas-und-couches.jpg"}}" alt="Kategorie Schlafsofas & Couches">
            <div class="details category">
                <div class="feature">Schlafsofas & Couches</div>
                <div class="cta">Jetzt Ansehen</div>
            </div>
        </a>

        <a href="/bettwaren/wohn-kuscheldecken/">
            <img src="{{media url="wysiwyg/sleepz/homepage/catalog/kategorie-wohn-und-kuscheldecken.jpg"}}" alt="Kategorie Wohn- und Kuscheldecken">
            <div class="details category">
                <div class="feature">Wohn- & Kuscheldecken</div>
                <div class="cta">Jetzt Ansehen</div>
            </div>
        </a>
    </div>
</section>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Seo Kategorien');
$cmsBlock->setIdentifier('home-categories');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #8 home-seo-marken
 */
$blockcontent = <<<EOF
<div class="homepage-catalog brands">

    <a href="/ratgeber/marken/dunlopillo/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/estella-logo.png"}}" alt="Estella Logo">
    </a>

    <a href="/ratgeber/marken/tempur/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/tempur-logo.png"}}" alt="Tempur Logo">
    </a>

    <a href="/catalogsearch/result/?q=fey">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/fey-&-co-logo.png"}}" alt="Fey & Co. Logo">
    </a>

    <a href="/ratgeber/marken/dunlopillo/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/dunlopillo-logo.png"}}" alt="Dunlopillo Logo">
    </a>

</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Seo Marken');
$cmsBlock->setIdentifier('home-seo-marken');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #9 home-seo-product-1
 */
$blockcontent = <<<EOF
<div class="homepage-catalog-title">
    <span class="title">Unsere Marken- & Produktempfehlungen</span>
    <i class="triangle-block-shadow"></i>
</div>

<div class="homepage-catalog">

    <a href="/janine-moments-mako-satin-bettwaesche-9018-01-rosa-taupe/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/janine-mako-satin-bettwaesche.jpg"}}" alt="Janine Mako-Satin-Bettwäsche">
        <div class="details">
            <div class="feature">Kuschelige Bettwäsche</div>
            <div class="product-title">Janine Mako-Satin-Bettwäsche</div>
            <div class="price">ab 59,95 €</div>
        </div>
    </a>

    <a href="/irisette-borkum-tonnentaschenfederkern-matratze/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/irisette-borkum-tonnentaschenfederkern-matratze.jpg}}" alt="Borkum Tonnentaschenfederkern - Matratze">
        <div class="details">
            <div class="feature">Der Preisknüller</div>
            <div class="product-title">Irisette Borkum TFK - Matratze</div>
            <div class="price">ab 273,00 €</div>
        </div>
    </a>

    <a href="/pad-sheridan-wohndecke-olive-140x190/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/pad-sheridan-wohndecke-olive.jpg}}" alt="pad Sheridan Wohndecke olive">
        <div class="details">
            <div class="feature">Edle Kuschel- & Wohndecke</div>
            <div class="product-title">pad Sheridan olive</div>
            <div class="price">209,95 €</div>
        </div>
    </a>

    <a href="/dunlopillo-daune-luxus-line-steppbett-mono/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/dunlopillo-daune-luxus-line-steppbett-mono.jpg}}" alt="Dunlopillo Daune Luxus Line Steppbett Mono">
        <div class="details">
            <div class="feature">Bestnote für Schlafkomfort</div>
            <div class="product-title">Daunendecke Dunlopillo Luxus Line</div>
            <div class="price">ab 394,90 €</div>
        </div>
    </a>

</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Seo Product 1');
$cmsBlock->setIdentifier('home-seo-product-1');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #10 home-seo-product-2
 */
$blockcontent = <<<EOF
<div class="homepage-catalog">

    <a href="/dunlopillo-matratze-aqualiter-sensual/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/dunlopillo-aqualite-kaltschaum-matratze.jpg"}}" alt="Dunlopillo AquaLite® Sensual Matratze">
        <div class="details">
            <div class="feature">Kaltschaum-Tipp</div>
            <div class="product-title">Dunlopillo AquaLite® Matratze</div>
            <div class="price">ab 479,90 €</div>
        </div>
    </a>

    <a href="/centa-star-matratzentopper-relax/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/centa-star-matratzentopper-relax.jpg}}" alt="Centa-Star Relax Matratzentopper">
        <div class="details">
            <div class="feature">Unsere Topper-Empfehlung</div>
            <div class="product-title">Centa-Star Relax Matratzentopper</div>
            <div class="price">ab 179,00 €</div>
        </div>
    </a>

    <a href="/billerbeck-daunenkissen-excelsior-106/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/billerbeck-excelsior-daunenkissen.jpg}}" alt="Billerbeck Excelsior 106 Daunenkissen">
        <div class="details">
            <div class="feature">Der Kissen-bestseller</div>
            <div class="product-title">Billerbeck Excelsior 106 Daunenkissen</div>
            <div class="price">ab 74,90 €</div>
        </div>
    </a>

    <a href="/selecta-s6-schaum-matratze/">
        <img src="{{media url="wysiwyg/sleepz/homepage/catalog/selecta-s6-schaum-matratze.jpg}}" alt="Selecta S6 Schaum Matratze">
        <div class="details">
            <div class="feature">Made in Germany</div>
            <div class="product-title">Selecta S6 Schaum-Matratze</div>
            <div class="price">ab 435,00 €</div>
        </div>
    </a>

</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Seo Product 2');
$cmsBlock->setIdentifier('home-seo-product-2');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #11 home-seo-brands-carousel
 */
$blockcontent = <<<EOF
<div id="brands-slider">
    <h6>Weitere Topmarken bei uns erhältlich:</h6>
    <div class="owl-carousel">
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/badenia.png"}}" alt="Badenia" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/bassetti.png"}}" alt="Bassetti" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/bemanova.png"}}" alt="Bemanova" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/betty.png"}}" alt="Betty" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/billerbeck.png"}}" alt="Billerbeck" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/bnp.png"}}" alt="BNP" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/climabalance.png"}}" alt="Climabalance" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/curt-bauer-germany.png"}}" alt="Curt Bauer" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/dunlopillo.png"}}" alt="Dunlopillo" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/esprit-home.png"}}" alt="Esprit" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/essenza.png"}}" alt="Essenza" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/estella.png"}}" alt="Estella" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/feyco.png"}}" alt="Feyco" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/hasena.png"}}" alt="Hasena" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/herding.png"}}" alt="Herding" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/hukla.png"}}" alt="Hukla" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/innobett.png"}}" alt="Innobett" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/innovation.png"}}" alt="Innovation" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/irisette.png"}}" alt="Irisette" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/janine.png"}}" alt="Janine" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/malie.png"}}" alt="Malie" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/marcopolo.png"}}" alt="Marcopolo" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/matraflex.jpg"}}" alt="Matraflex" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/modular.png"}}" alt="Modular" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/pad.jpg"}}" alt="pad" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/roewa.png"}}" alt="Roewa" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/sanders.png"}}" alt="Sanders" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/schlafstil.png"}}" alt="Schlafstil" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/schuon.png"}}" alt="Schuon" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/sealy.png"}}" alt="Sealy" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/Stearns-Foster.jpg"}}" alt="Stearns & Foster" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/technogel.png"}}" alt="Technogel" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/theraline.png"}}" alt="Theraline" /></div>
        <div class="item"><img src="{{media url="wysiwyg/sleepz/brands-slider/Verholt.png"}}" alt="Verholt" /></div>
    </div>
</div>



<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#brands-slider .owl-carousel').owlCarousel({
            loop:true,
            margin:30,
            autoWidth:true,
            items:7,
            slideBy:7,
            autoplay:true,
            dots:false
        });
    });
</script>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Seo Brands Slider');
$cmsBlock->setIdentifier('home-seo-brands-carousel');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


/*
 * #12 home-seo-text
 */
$blockcontent = <<<EOF
<section class="home-info-text">
   <h1>Matratzen, Lattenroste, Decken, Kissen und Bettwäsche <span>ganz einfach online kaufen</span></h1>

    <h2>Einfach entspannt einschlafen und ausgeruht aufwachen</h2>
    <p>Bei <strong>perfekt-schlafen.de</strong>, Deutschlands großem <strong>Online Shop</strong> rund um den erholsamen Schlaf, finden Sie ein umfassendes Angebot an exzellenten <strong>Matratzen</strong>, die optimal auf unterschiedliche Bed&uuml;rfnisse und Schlafgewohnheiten abgestimmt sind. Entdecken Sie von <a href="/bettwaesche/" title="Bettw&auml;sche">Bettw&auml;sche</a> in hochwertigen Qualit&auml;ten und Gr&ouml;ßen über <a href="/matratzen/" title="Matratzen">Matratzen</a> mit individuellen H&auml;rtegraden, <a href="/lattenroste/" title="Lattenroste">Lattenroste</a> in unterschiedlichen Ausf&uuml;hrungen (<a href="/lattenroste/starre-lattenroste/" title="starre Lattenroste">starre Lattenroste</a>, <strong>mechanisch verstellbare Lattenroste</strong> und <strong>motorisch verstellbare Lattenroste</strong>) bis hin zu <strong>Kopfkissen</strong> und <a href="/bettdecken/" title="Bettdecken">Bettdecken</a> mit verschiedenen W&auml;rmeklassen alles, was Sie für einen erholsamen und gesunden Schlaf brauchen. Gegr&uuml;ndet 2003 in Berlin, steht perfekt-schlafen.de schon seit &uuml;ber zehn Jahren und mit über 150.000 Kunden für ein vielseitiges Sortiment, beste Qualit&auml;t und ein umfassendes Serviceangebot.</p>

    <h2>Gut, besser, Trusted Shops geprüft: perfekt-schlafen.de</h2>
    <p>In unserem <strong>Online Shop</strong> finden Sie ausgesuchte <strong>Schlafprodukte</strong> namhafter Hersteller. Entdecken Sie hochwertig verarbeitete Matratzen und <strong>Lattenroste</strong> von Tempur, Dunlopillo, Technogel, Röwa und Grafenfels, kuschelig-anschmiegsame <strong>Bettw&auml;sche</strong> von Bassetti, Esprit, Marc O´Polo, Curt Bauer und Estella, bauschig-weiche <a href="/kopfkissen/" title="Kopfkissen">Kopfkissen</a> und <strong>Bettdecken</strong> von Centa-Star, Schlafstil, Climabalance, InnoBETT – und noch viel mehr. Neben einer großen Auswahl an <strong>Federkernmatratzen</strong>, <a href="/matratzen/kaltschaummatratzen/" title="Kaltschaummatratzen">Kaltschaummatratzen</a>, <strong>Latexmatratzen</strong> und <a href="/matratzen/viscoschaummatratzen/" title="Viscomatratzen">Viscomatratzen</a> finden Sie bei uns perfekt aufeinander abgestimmte Matratzen-Lattenrost-Sets oder Decken-Kissen-Sets.</p>


    <h2>Kostenlose Beratung, persönlich und kompetent: perfekt-schlafen.de</h2>
    <p>Wir beliefern Sie <strong>versand- und retourkostenfrei</strong> an Ihre Wunschadresse in Deutschland und beraten Sie kostenlos, persönlich und kompetent. Auf Matratzen und Lattenrosten können Sie 30 Tage probeschlafen. Telefonisch sind unsere perfekt schlafen Schlafberater montags bis freitags von 10 bis 18 Uhr unter <strong>{{customVar code=ps-telefon}}</strong> oder per E-Mail an <a href="mailto:support@perfekt-schlafen.de">support@perfekt-schlafen.de</a> für Sie erreichbar. Ganz gleich, ob Sie Fragen zum Wärmegrad von Daunendecken haben, sich über die Vorzüge eines verstellbaren Lattenrostes informieren möchten, auf der Suche nach Allergiker-Rundumbezügen sind oder sich nicht zwischen Mako-Satin-Bettäsche und Biber-Bettwäsche entscheiden können. Informationen und viel Wissenswertes finden Sie auch unter <a href="/faq/" title="Häufige Fragen">Häufige Fragen</a> und in unserem  <a onclick="window.open(this.href); return false;" href="http://blog.perfekt-schlafen.de" title="Schlafblog">Schlafblog</a>. Ab sofort können Sie uns zudem gerne in unserem <strong>neuen Ausstellungsraum</strong> in der <strong>Kochstraße 29</strong> in 10969 Berlin montags-samstags von 10:00-18:00 Uhr besuchen.</p>

</section>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Home Seo Text');
$cmsBlock->setIdentifier('home-seo-text');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();