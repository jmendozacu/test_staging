<?php

$configSwitch = new Mage_Core_Model_Config();
$scope = 'websites'; $scopeId = 4;




/*  New CMS Block */
$block = Mage::getModel('cms/block')->load('porto_custom_menu', 'identifier');
if ($block->getIdentifier()) {
    $block->delete();
}

$pagecontent = <<<EOF
<ul>
	<li class="menu-item menu-item-has-children menu-parent-item fl-right">
		<a href="javascript:;">Features<span class="cat-label cat-label-label2">Hot!</span></a>
		<div class="nav-sublist-dropdown" style="display: none; list-style: none;">
			<div class="container">
				<ul>
					<li class="menu-item menu-item-has-children menu-parent-item" style="list-style: none;">
						<a class="level1" href="javascript:;"><span>Homepage Variations 1</span></a>
						<div class="nav-sublist level1">
							<ul>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo1_en"><span>Home Layout 1</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo2_en"><span>Home Layout 2</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo3_en"><span>Home Layout 3</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo4_en"><span>Home Layout 4</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en"><span>Home Layout 5</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo6_en"><span>Home Layout 6</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo7_en"><span>Home Layout 7</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo8_en"><span>Home Layout 8</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo9_en"><span>Home Layout 9</span></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="menu-item menu-item-has-children menu-parent-item" style="list-style: none;">
						<a class="level1" href="javascript:;"><span>Homepage Variations 2</span></a>
						<div class="nav-sublist level1">
							<ul>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo10_en"><span>Home Layout 10</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo11_en"><span>Home Layout 11</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo12_en"><span>Home Layout 12</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo13_en"><span>Home Layout 13</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo14_en"><span>Home Layout 14</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo15_en"><span>Home Layout 15</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo16_en"><span>Home Layout 16</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo17_en"><span>Home Layout 17</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo4_sa"><span>Home RTL Layout</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo1_fr"><span>Home Full Ajax Layout</span></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="menu-item menu-item-has-children menu-parent-item" style="list-style: none;">
						<a class="level1" href="javascript:;"><span>Shop Variations 1</span></a>
						<div class="nav-sublist level1">
							<ul>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/electronics.html"><span>Fullwidth Banner</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/fashion.html"><span>Boxed Slider Banner</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/fashion/women.html"><span>Boxed Image Banner</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/fashion.html"><span>Left Sidebar</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/electronics/cameras.html"><span>Right Sidebar</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo17_en/fashion.html"><span>Ajax Infinite Scroll</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo6_fr/fashion/men/accessories.html"><span>2 Columns Products</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo6_fr/fashion/men/tees-knits-and-polos.html"><span>3 Columns Products</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo6_fr/fashion/men/shirts.html"><span>4 Columns Products</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo6_fr/fashion/men/pants-denim.html"><span>5 Columns Products</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo6_fr/fashion/kids/casual-shoes.html"><span>6 Columns Products</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo6_fr/fashion/kids/outwear.html"><span>7 Columns Products</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo6_fr/fashion/kids/sneakers.html"><span>8 Columns Products</span></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="menu-item menu-item-has-children menu-parent-item" style="list-style: none;">
						<a class="level1" href="javascript:;"><span>Shop Variations 2</span></a>
						<div class="nav-sublist level1">
							<ul>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/fashion/women-dress-m-rolex.html"><span>Product Page with Sidebar</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/fashion/racer-back-maxi-dress.html"><span>Product Page without Sidebar</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo11_en/electronics/motorola-male-phone-4g-blue-sony.html"><span>Product Page with Inner Zoom</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo11_fr/electronics/motorola-male-phone-4g-blue-sony.html"><span>Product Page with Outer Zoom</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo8_fr/fashion/fashion-dress.html"><span>Product Page with Vertical Gallery</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo11_fr/electronics/motorola-male-phone-4g-blue-sony.html"><span>Product Page with Addtocart Sticky</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo8_fr/fashion/fashion-dress.html"><span>Product Page with Vertical Tabs</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo9_fr/fashion/fashion-dress.html"><span>Product Page with Accordion</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo14_fr/electronics/motorola-male-phone-4g-blue-sony.html"><span>Product Page with Moved Tabs</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/women-dress.html"><span>Configurable Sample Product</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/electronics/games/seiko-bundle.html"><span>Bundle Sample Product</span></a>
								</li>
								<li class="menu-item " style="list-style: none; width: 250px;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en/fashion/jewellery/women-jewellery.html"><span>Grouped Sample Product</span></a>
								</li>
							</ul>
						</div>
					</li>
					<li class="menu-item menu-item-has-children menu-parent-item" style="list-style: none;">
						<a class="level1" href="javascript:;"><span>Headers</span></a>
						<div class="nav-sublist level1">
							<ul>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo1_en"><span>Header Type 1</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo2_fr"><span>Header Type 2</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo4_en"><span>Header Type 3</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_en"><span>Header Type 4</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo5_fr"><span>Header Type 5</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo7_en"><span>Header Type 6</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo7_fr"><span>Header Type 7</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo2_en"><span>Header Type 8</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo11_en"><span>Header Type 9</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo12_en"><span>Header Type 10</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo13_en"><span>Header Type 11</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo16_en"><span>Header Type 12</span></a>
								</li>
								<li class="menu-item " style="list-style: none;">
									<a class="level2" href="http://newsmartwave.net/magento/porto/index.php/demo17_en"><span>Header Type 13</span></a>
								</li>
							</ul>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</li>
    <li class="fl-right">
		<a href="http://themeforest.net/item/porto-ecommerce-ultimate-magento-theme/9725864?ref=SW-THEMES&amp;license=regular&amp;open_purchase_for_item_id=9725864&amp;purchasable=source" target="_blank">Buy Porto!</a>
	</li>
</ul>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Porto - Custom Menu');
$cmsBlock->setIdentifier('porto_custom_menu');
$cmsBlock->setContent($pagecontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();
