<?php
/*
 * Updates Legend:
 * new : New Feature
 * fix : Bug Fix
 * enhance : Improved feature
 * initial : Initial Version
*/ 
return array(

	array(
		'version' => '1.0.7',
		'date' =>'13.04.2015',
		'changes' => array(
			
			'fix' => array(
				'Fixed issue with Vimeo player',
				'Fixed font-awesome 4.3 icons',
				'Fixed minor demo import bugs',
				'Fixed Visual Composer output on posts page template',
				'Fixed bug with search toggle on active push menu',
				'Fixed warning notice on category pages',
				'Fixed logo height on mobile small screens',
				'Fixed comments paragraphs margin bottom css',
				'Fixed Follow Us text in top bar if custom icon is used',
				'Fixed Facebook share count if not available',
				'Fix push menu overflow on mobile'
			),	
			'enhance' => array(
				'Show full post titles within post templates and archives',
				'Enhance Theme Panel for a faster load'
			),	
			'new' => array(	
				
				'Added <strong>UberMenu Support</strong>',
				'Added <strong>RTL Support</strong>',
				'Added a separate top menu search bar',
				'Added query options within WP News Widget',
				'Added offset option within VC & WP News Widget',
				'Added Random Posts query within VC & WP News Widget',
				'Added Related Posts query within VC & WP News Widget',
				'Added Date filter within VC & WP News Widget (last week, last month, last year and all time)',
				'Added Custom Query / Custom Post Types to VC News Widget',
				'Added option to change views count meta key',
				'Added option to reset views count',
				'Added option to turn off sticky sidebars',
				'Added API Keys Section in the theme panel',
				'Added Google Fonts API Key option to retrieve all fonts for VC custom heading widget',
				'Updated Woocommerce templates'
			)		
		),
	),
	
	array(
		'version' => '1.0.6',
		'date' =>'09.03.2015',
		'changes' => array(
			
			'fix' => array(
				'Make the Demo Import work on IIS servers',
				'Fixed right push menu overflow',
				'Fixed category styling if more than one is shown',
				'Fixed inner content background color in Full Width layout',
				'Fixed sticky sidebar when sticky Top Bar is disabled',
				'Remove https from social share count urls',
				'Center search form when menu height is changed'
			),	
			'enhance' => array(
				'Increased orbit slider font size',
				'Optimized orbit slider loading',
				'Ajaxified SASS compilation process for a faster Theme Panel',
				'Allow multiple widgets within single posts widget zones'
			),	
			'new' => array(	
				'Added category descriptions',
				'Added Yoast Breadcrumbs Support',
				'Added Website Field to comments form for guests',
				'Added autoplay option for single videos',
				'Added more "Show / Hide" options to News Widget',
				'Added option to show / hide page title in sticky top bar',
				'Added option to show / hide Author Box in single posts',
				'Added option to limit the number of categories displayed within the news widget',
				'Updated language files'
			)		
		),
	),
	
	array(
		'version' => '1.0.5',
		'date' =>'24.02.2015',
		'changes' => array(
			
			'fix' => array(
				'Fixed "Cannot modify header information" error in woo commerce filters',
				'Fixed footer layout when comments are activated on visual composer pages',
			),	
			'enhance' => array(
				'Added a clear message if the theme cannot set the required memory limit',
			),			
		),
	),
		
	array(
		'version' => '1.0.4',
		'date' =>'23.02.2015',
		'changes' => array(
			
			'fix' => array(
				'Fixed issue with sticky top bar on close event',
				'Fixed style url on HTTPS',
				'Fixed author links in comments list',
				'Fixed woocommerce reviews css',
				'Fixed featured image width in "original" mode',
				'Fixed WooCommerce scroll to reviews section',
				'Fixed Customizer Error',
				'Fixed issue with expanded mobile menu on login dialog close event.',
				'Fixed pagination on posts template when set as homepage'
			),	
			'enhance' => array(
				'Optimized SASS compilation process',
				'Make news widget animations optional',
				'Update migration system',
				'Tag Cloud Widget: Limit results to 15',
				'Make mobile topbar sticky'
			),
			'new' => array(	
				'Added Schema Micro Data Markup',
				'Added predefined visual composer templates',
				'Added global option to hide breadcrumbs',
				'Added global option to hide title bar',
				'Added color options to push menu',
				'Added option to show / hide mega menu tags & subcategories',
				'Added new Featured Image Type (Behind Title - Fullwidth)',
				'Added color options to Top Bar buttons',
				'Updated Woocommerce CSS for version 2.3.0'

			)			
		),
	),

	array(	
		'version' => '1.0.3',
		'date' =>'04.02.2015',
		'changes' => array(
			
			'fix' => array(
				'Fixed issue with Default Roboto Font, not able to change size and colors',
				'Adjusted top bar buttons margin when changing top bar height',
				'Fixed Theme Panel Issues on Child Theme',
				'Fixed demo importer remote xml fetching by adding fallback functions',
				'Fixed main menu / top menu default settings',
				'Fixed issue with Twitter widget avatars / tweet date not showing'
			),	
			'enhance' => array(
				'Single post sidebar: Set default options to inherit from global settings',
				'Single post featured image: Set default options to inherit from global settings',
				'Completely removed Woocommerce & Buddypress css if not activated'
			),
			'new' => array(	
				'Added Menu Typography',
				'Added color options to the top bar',
				'Added logo option to main menu',
				'Added Ajax post selection within the news widget',
				'Added exclude categories within the news widget',
				'Added option to show / hide search form from mobile menu',
				'Added option to show / hide follow us from top-bar and mobile menu',
				'Added option to enable / disable comments on a visual composer page',
				'Added po / mo language files to child theme',
				'Added "Via" option to twitter social share',
				'Updated Visual Composer',
				'Updated Optimum Speed Plugin to v1.0.1'	
			)		
			
		)
	),

	array(
		'version' => '1.0.2',
		'date' =>'25.01.2015',
		'changes' => array(
			
			'fix' => array(
				'Fixed issue with sidebar on a visual composer page',
				'Fixed page background settings',
				'Fixed Child Theme CSS',
				'Fixed lost password redirection',
				'Fixed Yoast SEO Sitemap error',
				'Set Easy Foundation Shortcodes to use theme assets'
			),	
			'new' => array(
				'Prepackaged Envato Toolkit for Automatic Theme Updates',
				'Prepackaged Ads Manager + Visual Composer Ads widget',
				'Reskined Sidebar',
				'Added boxed version'
			)	
		)
	),
	array(
		'version' => '1.0.1',
		'date' =>'22.01.2015',
		'changes' => array(
			
			'fix' => array(
				'Fixed initial CSS compilation',
				'Fixed revolution slider arrow position',
				'Fixed parallax and video containers padding on mobile'
			),	
			'enhance' => 'Enhanced shop page responsiveness',
			'new' => array(
				'Prepackaged Easy Foundation Shortcodes',
				'Added Grid Width option'
			)	
		)
	),
	array(
		'version' => '1.0.0',
		'date' =>'20.01.2015',
		'changes' => array(
			'initial' => 'Initial Version',
		)
	)
);