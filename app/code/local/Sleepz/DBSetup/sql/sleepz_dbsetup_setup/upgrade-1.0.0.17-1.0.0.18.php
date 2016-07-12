<?php

#
# perfekt-schlafen: payment icons
#

# set scope
$scopeId = 1; // perfekt schlafen=1, matratzendiscount=4

# set identifier array
$identifierList[] = 'payment-icons';            #1
$identifierList[] = 'product-payment-icons';    #2

# delete blocks, if exist
foreach ($identifierList as $key => $identifier) {
    $cmsBlock = Mage::getModel('cms/block')->load($identifier, 'identifier');
    if ($cmsBlock->getId()) {$cmsBlock->delete();}
}


# set content

#1
$content = <<<EOF
<div class="payment-icons">
    <ul>
        <li class="paypal-ico"><a href="/faq-zahlung#paypal"><span></span></a></li>
        <li class="amazon-ico"><a href="/faq-zahlung#amazon"><span></span></a></li>
        <li class="sofortueberweisung-ico"><a href="/faq-zahlung#sofortueberweisung"><span></span></a></li>
        <li class="visa-ico"><a href="/faq-zahlung#kreditkarte"><span></span></a></li>
        <li class="mastercard-ico"><a href="/faq-zahlung#kreditkarte"><span></span></a></li>
        <li class="barzahlen-ico"><a href="/faq-zahlung#barzahlen"><span></span></a></li>
        <li class="ratepay-ico"><a href="/faq-zahlung#ratepay"><span></span></a></li>
        <li class="rechnung-ico"><a href="/faq-zahlung#rechnung"><span></span></a></li>
        <li class="vorkasse-ico"><a href="/faq-zahlung#vorkasse"><span></span></a></li>
    </ul>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Zahlungsmethoden Icons');
$cmsBlock->setIdentifier('payment-icons');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();

#2
$content = <<<EOF
<div class="block sidebar-cms">
{{block type="cms/block" block_id="payment-icons"}}
<div class="clearfix"></div>
</div>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Payment Icons on product page');
$cmsBlock->setIdentifier('product-payment-icons');
$cmsBlock->setContent($content);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();