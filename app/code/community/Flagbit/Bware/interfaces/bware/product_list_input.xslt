<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://mip.flagbit.com/magento-data"  version="1.0">


	<xsl:template match="/">
		<data array="true">
			<xsl:apply-templates select="/BWProducts/Product"/>
	</data>
	</xsl:template>
	
	<xsl:template match="Product">	
		<product>
			<xsl:apply-templates select="*"/>
		</product>
	</xsl:template>
    
    <xsl:template match="DATA_set">
		<set><xsl:value-of select="."/></set>
    </xsl:template>
    
        
    <xsl:template match="DATA_type">
        <type><xsl:value-of select="."/></type>
    </xsl:template>


	<xsl:template match="DATA_sku">
		<sku><xsl:value-of select="."/></sku>
        <mip_datahash_id><xsl:value-of select="."/></mip_datahash_id>	
    </xsl:template>
    
    
   
    <xsl:template match="DATA_weight">
		<weight><xsl:value-of select="."/></weight>
    </xsl:template>
    
     <xsl:template match="DATA_datumvon">
		<news_from_date><xsl:value-of select="."/></news_from_date>
    </xsl:template>
    
    <xsl:template match="DATA_datumbis">
		<news_to_date><xsl:value-of select="."/></news_to_date>
    </xsl:template>
    
    <xsl:template match="DATA_category_ids">
		<category_ids><xsl:value-of select="."/></category_ids>
    </xsl:template>
    
     <xsl:template match="DATA_status">
		<status><xsl:value-of select="."/></status>
    </xsl:template>
    
     <xsl:template match="DATA_visibility">
		<visibility><xsl:value-of select="."/></visibility>
    </xsl:template>
    
    <xsl:template match="DATA_name">
		<name><xsl:value-of select="."/></name>
    </xsl:template>
    
     <xsl:template match="DATA_description">
		<description><xsl:value-of select="."/></description>
    </xsl:template>
    
    <xsl:template match="DATA_images">
        <images array="true">
            <xsl:call-template name="split">
                <xsl:with-param name="pText" select="."/>
                <xsl:with-param name="pItemElementName" select="'image'"/>
            </xsl:call-template>
        </images>
    </xsl:template>
    
    
    <xsl:template match="DATA_short_description">
		<short_description><xsl:value-of select="."/></short_description>
    </xsl:template>
    
    <xsl:template match="DATA_price">
		<price><xsl:value-of select="."/></price>
    </xsl:template>
    
    
    
    <xsl:template match="DATA_tax_class_id">
		<tax_class_id><xsl:value-of select="."/></tax_class_id>
    </xsl:template>
    
    
    
    <xsl:template match="DATA_msrp_enabled">
		<msrp_enabled><xsl:value-of select="."/></msrp_enabled>
    </xsl:template>
    
    <xsl:template match="DATA_msrp_display_actual_price_type">
		<msrp_display_actual_price_type><xsl:value-of select="."/></msrp_display_actual_price_type>
    </xsl:template>
    

    <xsl:template match="NODE_Filter">
        <msrp><xsl:value-of select="mip:attributeOptionValue('msrp', string(./NODE_Filter/DATA_msrp), null, 0, '', true, 1)"/></msrp>
        <ps_tobi><xsl:value-of select="mip:attributeOptionValue('ps_tobi', string(./NODE_Filter/data_tobi), null, 0, null, true)"/></ps_tobi>
        <ps_varchariante><xsl:value-of select="mip:attributeOptionValue('ps_varchariante', string(./NODE_Filter/ps_varchariante), null, 0, null, true)"/></ps_varchariante>
        <ps_anzahl_der_leisten><xsl:value-of select="mip:attributeOptionValue('ps_anzahl_der_leisten', string(./NODE_Filter/ps_anzahl_der_leisten), null, 0, null, true)"/></ps_anzahl_der_leisten>
		<ps_ausfuehrung><xsl:value-of select="mip:attributeOptionValue('ps_ausfuehrung', string(./NODE_Filter/ps_ausfuehrung), null, 0, null, true)"/></ps_ausfuehrung>
		<ps_bautiefe><xsl:value-of select="mip:attributeOptionValue('ps_bautiefe', string(./NODE_Filter/ps_bautiefe), null, 0, null, true)"/></ps_bautiefe>
		<ps_befestigung><xsl:value-of select="mip:attributeOptionValue('ps_befestigung', string(./NODE_Filter/ps_befestigung), null, 0, null, true)"/></ps_befestigung>
		<ps_besonderheit><xsl:value-of select="mip:attributeOptionValue('ps_besonderheit', string(./NODE_Filter/ps_besonderheit), null, 0, null, true)"/></ps_besonderheit>
		<ps_bettwaesche><xsl:value-of select="mip:attributeOptionValue('ps_bettwaesche', string(./NODE_Filter/ps_bettwaesche), null, 0, null, true)"/></ps_bettwaesche>
		<ps_bezug><xsl:value-of select="mip:attributeOptionValue('ps_bezug', string(./NODE_Filter/ps_bezug), null, 0, null, true)"/></ps_bezug>
		<ps_bezug_abnehmbar><xsl:value-of select="mip:attributeOptionValue('ps_bezug_abnehmbar', string(./NODE_Filter/ps_bezug_abnehmbar), null, 0, null, true)"/></ps_bezug_abnehmbar>
		<ps_bezug_waschbar><xsl:value-of select="mip:attributeOptionValue('ps_bezug_waschbar', string(./NODE_Filter/ps_bezug_waschbar), null, 0, null, true)"/></ps_bezug_waschbar>
		<ps_box><xsl:value-of select="mip:attributeOptionValue('ps_box', string(./NODE_Filter/ps_box), null, 0, null, true)"/></ps_box>
		<ps_box-hoehe_ohne_fuesse><xsl:value-of select="mip:attributeOptionValue('ps_box-hoehe_ohne_fuesse', string(./NODE_Filter/ps_box-hoehe_ohne_fuesse), null, 0, null, true)"/></ps_box-hoehe_ohne_fuesse>
		<ps_breite><xsl:value-of select="mip:attributeOptionValue('ps_breite', string(./NODE_Filter/ps_breite), null, 0, null, true)"/></ps_breite>
		<ps_breite_fuss><xsl:value-of select="mip:attributeOptionValue('ps_breite_fuss', string(./NODE_Filter/ps_breite_fuss), null, 0, null, true)"/></ps_breite_fuss>
		<ps_ean><xsl:value-of select="mip:attributeOptionValue('ps_ean', string(./NODE_Filter/ps_ean), null, 0, null, true)"/></ps_ean>
		<ps_einlegetiefe><xsl:value-of select="mip:attributeOptionValue('ps_einlegetiefe', string(./NODE_Filter/ps_einlegetiefe), null, 0, null, true)"/></ps_einlegetiefe>
		<ps_farbe><xsl:value-of select="mip:attributeOptionValue('ps_farbe', string(./NODE_Filter/ps_farbe), null, 0, null, true)"/></ps_farbe>
		<ps_farben><xsl:value-of select="mip:attributeOptionValue('ps_farben', string(./NODE_Filter/ps_farben), null, 0, null, true)"/></ps_farben>
		<ps_fernbedienung><xsl:value-of select="mip:attributeOptionValue('ps_fernbedienung', string(./NODE_Filter/ps_fernbedienung), null, 0, null, true)"/></ps_fernbedienung>
		<ps_festigkeit><xsl:value-of select="mip:attributeOptionValue('ps_festigkeit', string(./NODE_Filter/ps_festigkeit), null, 0, null, true)"/></ps_festigkeit>
		<ps_filter><xsl:value-of select="mip:attributeOptionValue('ps_filter', string(./NODE_Filter/ps_filter), null, 0, null, true)"/></ps_filter>
		<ps_fuellgewicht><xsl:value-of select="mip:attributeOptionValue('ps_fuellgewicht', string(./NODE_Filter/ps_fuellgewicht), null, 0, null, true)"/></ps_fuellgewicht>
		<ps_fuellung><xsl:value-of select="mip:attributeOptionValue('ps_fuellung', string(./NODE_Filter/ps_fuellung), null, 0, null, true)"/></ps_fuellung>
		<ps_fuesse><xsl:value-of select="mip:attributeOptionValue('ps_fuesse', string(./NODE_Filter/ps_fuesse), null, 0, null, true)"/></ps_fuesse>
		<ps_funktionen><xsl:value-of select="mip:attributeOptionValue('ps_funktionen', string(./NODE_Filter/ps_funktionen), null, 0, null, true)"/></ps_funktionen>
		<ps_fussfarbe><xsl:value-of select="mip:attributeOptionValue('ps_fussfarbe', string(./NODE_Filter/ps_fussfarbe), null, 0, null, true)"/></ps_fussfarbe>
		<ps_fussform><xsl:value-of select="mip:attributeOptionValue('ps_fussform', string(./NODE_Filter/ps_fussform), null, 0, null, true)"/></ps_fussform>
		<ps_fusshoehe><xsl:value-of select="mip:attributeOptionValue('ps_fusshoehe', string(./NODE_Filter/ps_fusshoehe), null, 0, null, true)"/></ps_fusshoehe>
		<ps_gesamthoehe><xsl:value-of select="mip:attributeOptionValue('ps_gesamthoehe', string(./NODE_Filter/ps_gesamthoehe), null, 0, null, true)"/></ps_gesamthoehe>
		<ps_gesamthoehe_inkl_kopfteil><xsl:value-of select="mip:attributeOptionValue('ps_gesamthoehe_inkl_kopfteil', string(./NODE_Filter/ps_gesamthoehe_inkl_kopfteil), null, 0, null, true)"/></ps_gesamthoehe_inkl_kopfteil>
		<ps_gewebe><xsl:value-of select="mip:attributeOptionValue('ps_gewebe', string(./NODE_Filter/ps_gewebe), null, 0, null, true)"/></ps_gewebe>
		<ps_gewicht><xsl:value-of select="mip:attributeOptionValue('ps_gewicht', string(./NODE_Filter/ps_gewicht), null, 0, null, true)"/></ps_gewicht>
		<ps_groesse><xsl:value-of select="mip:attributeOptionValue('ps_groesse', string(./NODE_Filter/ps_groesse), null, 0, null, true)"/></ps_groesse>
		<ps_groesse_moebel><xsl:value-of select="mip:attributeOptionValue('ps_groesse_moebel', string(./NODE_Filter/ps_groesse_moebel), null, 0, null, true)"/></ps_groesse_moebel>
		<ps_groesse_ausgeklappt><xsl:value-of select="mip:attributeOptionValue('ps_groesse_ausgeklappt', string(./NODE_Filter/ps_groesse_ausgeklappt), null, 0, null, true)"/></ps_groesse_ausgeklappt>
		<ps_guetesiegel><xsl:value-of select="mip:attributeOptionValue('ps_guetesiegel', string(./NODE_Filter/ps_guetesiegel), null, 0, null, true)"/></ps_guetesiegel>
		<ps_gutscheinwert><xsl:value-of select="mip:attributeOptionValue('ps_gutscheinwert', string(./NODE_Filter/ps_gutscheinwert), null, 0, null, true)"/></ps_gutscheinwert>
		<ps_haertegrad><xsl:value-of select="mip:attributeOptionValue('ps_haertegrad', string(./NODE_Filter/ps_haertegrad), null, 0, null, true)"/></ps_haertegrad>
		<ps_haertegradeinstellung><xsl:value-of select="mip:attributeOptionValue('ps_haertegradeinstellung', string(./NODE_Filter/ps_haertegradeinstellung), null, 0, null, true)"/></ps_haertegradeinstellung>
		<ps_herstellergarantie><xsl:value-of select="mip:attributeOptionValue('ps_herstellergarantie', string(./NODE_Filter/ps_herstellergarantie), null, 0, null, true)"/></ps_herstellergarantie>
		<ps_hoehe><xsl:value-of select="mip:attributeOptionValue('ps_hoehe', string(./NODE_Filter/ps_hoehe), null, 0, null, true)"/></ps_hoehe>
		<ps_hoehe_kern><xsl:value-of select="mip:attributeOptionValue('ps_hoehe_kern', string(./NODE_Filter/ps_hoehe_kern), null, 0, null, true)"/></ps_hoehe_kern>
		<ps_hoehe_lattenroste><xsl:value-of select="mip:attributeOptionValue('ps_hoehe_lattenroste', string(./NODE_Filter/ps_hoehe_lattenroste), null, 0, null, true)"/></ps_hoehe_lattenroste>
		<ps_hoehe_matratze><xsl:value-of select="mip:attributeOptionValue('ps_hoehe_matratze', string(./NODE_Filter/ps_hoehe_matratze), null, 0, null, true)"/></ps_hoehe_matratze>
		<ps_inlett><xsl:value-of select="mip:attributeOptionValue('ps_inlett', string(./NODE_Filter/ps_inlett), null, 0, null, true)"/></ps_inlett>
		<ps_jahreszeit><xsl:value-of select="mip:attributeOptionValue('ps_jahreszeit', string(./NODE_Filter/ps_jahreszeit), null, 0, null, true)"/></ps_jahreszeit>
		<ps_kassetten><xsl:value-of select="mip:attributeOptionValue('ps_kassetten', string(./NODE_Filter/ps_kassetten), null, 0, null, true)"/></ps_kassetten>
		<ps_kindermatratze><xsl:value-of select="mip:attributeOptionValue('ps_kindermatratze', string(./NODE_Filter/ps_kindermatratze), null, 0, null, true)"/></ps_kindermatratze>
		<ps_knopfleiste><xsl:value-of select="mip:attributeOptionValue('ps_knopfleiste', string(./NODE_Filter/ps_knopfleiste), null, 0, null, true)"/></ps_knopfleiste>
		<ps_kopfteil><xsl:value-of select="mip:attributeOptionValue('ps_kopfteil', string(./NODE_Filter/ps_kopfteil), null, 0, null, true)"/></ps_kopfteil>
		<ps_kopfteilhoehe><xsl:value-of select="mip:attributeOptionValue('ps_kopfteilhoehe', string(./NODE_Filter/ps_kopfteilhoehe), null, 0, null, true)"/></ps_kopfteilhoehe>
		<ps_laenge><xsl:value-of select="mip:attributeOptionValue('ps_laenge', string(./NODE_Filter/ps_laenge), null, 0, null, true)"/></ps_laenge>
		<ps_laenge_inkl_kopfteil><xsl:value-of select="mip:attributeOptionValue('ps_laenge_inkl_kopfteil', string(./NODE_Filter/ps_laenge_inkl_kopfteil), null, 0, null, true)"/></ps_laenge_inkl_kopfteil>
		<ps_lieferzeit><xsl:value-of select="mip:attributeOptionValue('ps_lieferzeit', string(./NODE_Filter/ps_lieferzeit), null, 0, null, true)"/></ps_lieferzeit>
		<ps_liegeflaeche><xsl:value-of select="mip:attributeOptionValue('ps_liegeflaeche', string(./NODE_Filter/ps_liegeflaeche), null, 0, null, true)"/></ps_liegeflaeche>
		<ps_liegeflaeche-breite><xsl:value-of select="mip:attributeOptionValue('ps_liegeflaeche-breite', string(./NODE_Filter/ps_liegeflaeche-breite), null, 0, null, true)"/></ps_liegeflaeche-breite>
		<ps_liegeflaeche-laenge><xsl:value-of select="mip:attributeOptionValue('ps_liegeflaeche-laenge', string(./NODE_Filter/ps_liegeflaeche-laenge), null, 0, null, true)"/></ps_liegeflaeche-laenge>
		<ps_liegegefuehl><xsl:value-of select="mip:attributeOptionValue('ps_liegegefuehl', string(./NODE_Filter/ps_liegegefuehl), null, 0, null, true)"/></ps_liegegefuehl>
		<ps_liegezonen><xsl:value-of select="mip:attributeOptionValue('ps_liegezonen', string(./NODE_Filter/ps_liegezonen), null, 0, null, true)"/></ps_liegezonen>
		<ps_lordosenstuetze><xsl:value-of select="mip:attributeOptionValue('ps_lordosenstuetze', string(./NODE_Filter/ps_lordosenstuetze), null, 0, null, true)"/></ps_lordosenstuetze>
		<ps_marke><xsl:value-of select="mip:attributeOptionValue('ps_marke', string(./NODE_Filter/ps_marke), null, 0, null, true)"/></ps_marke>
		<ps_masse><xsl:value-of select="mip:attributeOptionValue('ps_masse', string(./NODE_Filter/ps_masse), null, 0, null, true)"/></ps_masse>
		<ps_material><xsl:value-of select="mip:attributeOptionValue('ps_material', string(./NODE_Filter/ps_material), null, 0, null, true)"/></ps_material>
		<ps_material_bezug><xsl:value-of select="mip:attributeOptionValue('ps_material_bezug', string(./NODE_Filter/ps_material_bezug), null, 0, null, true)"/></ps_material_bezug>
		<ps_material_farbe><xsl:value-of select="mip:attributeOptionValue('ps_material_farbe', string(./NODE_Filter/ps_material_farbe), null, 0, null, true)"/></ps_material_farbe>
		<ps_material_kern><xsl:value-of select="mip:attributeOptionValue('ps_material_kern', string(./NODE_Filter/ps_material_kern), null, 0, null, true)"/></ps_material_kern>
		<ps_material_leisten><xsl:value-of select="mip:attributeOptionValue('ps_material_leisten', string(./NODE_Filter/ps_material_leisten), null, 0, null, true)"/></ps_material_leisten>
		<ps_material_rahmen><xsl:value-of select="mip:attributeOptionValue('ps_material_rahmen', string(./NODE_Filter/ps_material_rahmen), null, 0, null, true)"/></ps_material_rahmen>
		<ps_matratze_1><xsl:value-of select="mip:attributeOptionValue('ps_matratze_1', string(./NODE_Filter/ps_matratze_1), null, 0, null, true)"/></ps_matratze_1>
		<ps_matratze_2><xsl:value-of select="mip:attributeOptionValue('ps_matratze_2', string(./NODE_Filter/ps_matratze_2), null, 0, null, true)"/></ps_matratze_2>
		<ps_matratzeneignung><xsl:value-of select="mip:attributeOptionValue('ps_matratzeneignung', string(./NODE_Filter/ps_matratzeneignung), null, 0, null, true)"/></ps_matratzeneignung>
		<ps_matratzentyp><xsl:value-of select="mip:attributeOptionValue('ps_matratzentyp', string(./NODE_Filter/ps_matratzentyp), null, 0, null, true)"/></ps_matratzentyp>
		<ps_motor><xsl:value-of select="mip:attributeOptionValue('ps_motor', string(./NODE_Filter/ps_motor), null, 0, null, true)"/></ps_motor>
		<ps_muster><xsl:value-of select="mip:attributeOptionValue('ps_muster', string(./NODE_Filter/ps_muster), null, 0, null, true)"/></ps_muster>
		<ps_pflegehinweis><xsl:value-of select="mip:attributeOptionValue('ps_pflegehinweis', string(./NODE_Filter/ps_pflegehinweis), null, 0, null, true)"/></ps_pflegehinweis>
		<ps_pflegehinweis_bezug><xsl:value-of select="mip:attributeOptionValue('ps_pflegehinweis_bezug', string(./NODE_Filter/ps_pflegehinweis_bezug), null, 0, null, true)"/></ps_pflegehinweis_bezug>
		<ps_pflegehinweis_matratze><xsl:value-of select="mip:attributeOptionValue('ps_pflegehinweis_matratze', string(./NODE_Filter/ps_pflegehinweis_matratze), null, 0, null, true)"/></ps_pflegehinweis_matratze>
		<ps_produkt_model><xsl:value-of select="mip:attributeOptionValue('ps_produkt_model', string(./NODE_Filter/ps_produkt_model), null, 0, null, true)"/></ps_produkt_model>
		<ps_produktart><xsl:value-of select="mip:attributeOptionValue('ps_produktart', string(./NODE_Filter/ps_produktart), null, 0, null, true)"/></ps_produktart>
		<ps_qualitaet><xsl:value-of select="mip:attributeOptionValue('ps_qualitaet', string(./NODE_Filter/ps_qualitaet), null, 0, null, true)"/></ps_qualitaet>
		<ps_rahmen_lattenrosteignung><xsl:value-of select="mip:attributeOptionValue('ps_rahmen_lattenrosteignung', string(./NODE_Filter/ps_rahmen_lattenrosteignung), null, 0, null, true)"/></ps_rahmen_lattenrosteignung>
		<ps_rahmenhoehe><xsl:value-of select="mip:attributeOptionValue('ps_rahmenhoehe', string(./NODE_Filter/ps_rahmenhoehe), null, 0, null, true)"/></ps_rahmenhoehe>
		<ps_raumgewicht><xsl:value-of select="mip:attributeOptionValue('ps_raumgewicht', string(./NODE_Filter/ps_raumgewicht), null, 0, null, true)"/></ps_raumgewicht>
		<ps_reissverschluss><xsl:value-of select="mip:attributeOptionValue('ps_reissverschluss', string(./NODE_Filter/ps_reissverschluss), null, 0, null, true)"/></ps_reissverschluss>
		<ps_rundumbezug><xsl:value-of select="mip:attributeOptionValue('ps_rundumbezug', string(./NODE_Filter/ps_rundumbezug), null, 0, null, true)"/></ps_rundumbezug>
		<ps_schubladen><xsl:value-of select="mip:attributeOptionValue('ps_schubladen', string(./NODE_Filter/ps_schubladen), null, 0, null, true)"/></ps_schubladen>
		<ps_schulterkomfortzone><xsl:value-of select="mip:attributeOptionValue('ps_schulterkomfortzone', string(./NODE_Filter/ps_schulterkomfortzone), null, 0, null, true)"/></ps_schulterkomfortzone>
		<ps_schulterzone_lattenrost><xsl:value-of select="mip:attributeOptionValue('ps_schulterzone_lattenrost', string(./NODE_Filter/ps_schulterzone_lattenrost), null, 0, null, true)"/></ps_schulterzone_lattenrost>
		<ps_sitz-liegehoehe><xsl:value-of select="mip:attributeOptionValue('ps_sitz-liegehoehe', string(./NODE_Filter/ps_sitz-liegehoehe), null, 0, null, true)"/></ps_sitz-liegehoehe>
		<ps_steghoehe><xsl:value-of select="mip:attributeOptionValue('ps_steghoehe', string(./NODE_Filter/ps_steghoehe), null, 0, null, true)"/></ps_steghoehe>
		<ps_steppung><xsl:value-of select="mip:attributeOptionValue('ps_steppung', string(./NODE_Filter/ps_steppung), null, 0, null, true)"/></ps_steppung>
		<ps_tkz_matratze><xsl:value-of select="mip:attributeOptionValue('ps_tkz_matratze', string(./NODE_Filter/ps_tkz_matratze), null, 0, null, true)"/></ps_tkz_matratze>
		<ps_textilkennzeichnung><xsl:value-of select="mip:attributeOptionValue('ps_textilkennzeichnung', string(./NODE_Filter/ps_textilkennzeichnung), null, 0, null, true)"/></ps_textilkennzeichnung>
		<ps_textilkennzeichnung_polster><xsl:value-of select="mip:attributeOptionValue('ps_textilkennzeichnung_polster', string(./NODE_Filter/ps_textilkennzeichnung_polster), null, 0, null, true)"/></ps_textilkennzeichnung_polster>
		<ps_textilkennzeichnung_topper><xsl:value-of select="mip:attributeOptionValue('ps_textilkennzeichnung_topper', string(./NODE_Filter/ps_textilkennzeichnung_topper), null, 0, null, true)"/></ps_textilkennzeichnung_topper>
		<ps_tiefe><xsl:value-of select="mip:attributeOptionValue('ps_tiefe', string(./NODE_Filter/ps_tiefe), null, 0, null, true)"/></ps_tiefe>
		<ps_topper><xsl:value-of select="mip:attributeOptionValue('ps_topper', string(./NODE_Filter/ps_topper), null, 0, null, true)"/></ps_topper>
		<ps_variante><xsl:value-of select="mip:attributeOptionValue('ps_variante', string(./NODE_Filter/ps_variante), null, 0, null, true)"/></ps_variante>
		<ps_versandart><xsl:value-of select="mip:attributeOptionValue('ps_versandart', string(./NODE_Filter/ps_versandart), null, 0, null, true)"/></ps_versandart>
		<ps_volumen><xsl:value-of select="mip:attributeOptionValue('ps_volumen', string(./NODE_Filter/ps_volumen), null, 0, null, true)"/></ps_volumen>
		<ps_waermestufe><xsl:value-of select="mip:attributeOptionValue('ps_waermestufe', string(./NODE_Filter/ps_waermestufe), null, 0, null, true)"/></ps_waermestufe>
		<ps_zertifikat><xsl:value-of select="mip:attributeOptionValue('ps_zertifikat', string(./NODE_Filter/ps_zertifikat), null, 0, null, true)"/></ps_zertifikat>
		<ps_zusaetze><xsl:value-of select="mip:attributeOptionValue('ps_zusaetze', string(./NODE_Filter/ps_zusaetze), null, 0, null, true)"/></ps_zusaetze>
		<ps_fuer_schwitzen_geeignet><xsl:value-of select="mip:attributeOptionValue('ps_fuer_schwitzen_geeignet', string(./NODE_Filter/ps_fuer_schwitzen_geeignet), null, 0, null, true)"/></ps_fuer_schwitzen_geeignet>
		<ps_geeignet_fuer_allergiker><xsl:value-of select="mip:attributeOptionValue('ps_geeignet_fuer_allergiker', string(./NODE_Filter/ps_geeignet_fuer_allergiker), null, 0, null, true)"/></ps_geeignet_fuer_allergiker>
		<ps_regulierbare_mittelzone><xsl:value-of select="mip:attributeOptionValue('ps_regulierbare_mittelzone', string(./NODE_Filter/ps_regulierbare_mittelzone), null, 0, null, true)"/></ps_regulierbare_mittelzone>
		<ps_trocknergeeignet><xsl:value-of select="mip:attributeOptionValue('ps_trocknergeeignet', string(./NODE_Filter/ps_trocknergeeignet), null, 0, null, true)"/></ps_trocknergeeignet>
		<ps_verstellbar><xsl:value-of select="mip:attributeOptionValue('ps_verstellbar', string(./NODE_Filter/ps_verstellbar), null, 0, null, true)"/></ps_verstellbar>
		<ps_waschbar><xsl:value-of select="mip:attributeOptionValue('ps_waschbar', string(./NODE_Filter/ps_waschbar), null, 0, null, true)"/></ps_waschbar>
		<ps_waschmaschinenfester_bezug><xsl:value-of select="mip:attributeOptionValue('ps_waschmaschinenfester_bezug', string(./NODE_Filter/ps_waschmaschinenfester_bezug), null, 0, null, true)"/></ps_waschmaschinenfester_bezug>
		<ps_wasserdicht><xsl:value-of select="mip:attributeOptionValue('ps_wasserdicht', string(./NODE_Filter/ps_wasserdicht), null, 0, null, true)"/></ps_wasserdicht>
	
               
    
	<stock_data>
		<qty><xsl:value-of select="../DATA_qty"/></qty>
		<is_in_stock><xsl:value-of select="../DATA_is_in_stock"/></is_in_stock>
    </stock_data>
    
    <website_ids array="true">
    	<website_id><xsl:value-of select="../DATA_website"/></website_id>
    </website_ids>
    
    <store_ids array="true">
    	<store_id><xsl:value-of select="../DATA_store"/></store_id>
    </store_ids>
    
    

            
	<xsl:if test="../DATA_type = 'configurable'">
			<configurable_products_data array="true">
			<xsl:for-each select="../*[starts-with(name(), 'DATA_skuconfig')]">
			<xsl:if test=". != ''">
			<xsl:variable name="currentConfig" select="substring-after(name(), 'DATA_skuconfig')"/> 
				<node>
					<id>
						<xsl:value-of select="."/>
					</id>
					<values array="true">
						<xsl:for-each select="../NODE_Filter/NODE_Filter/*">
							<xsl:if test="starts-with(name() , concat('DATA_code', $currentConfig, '_'))">
								<value>
									<attribute_id>
										<xsl:value-of select="mip:getid('attribute', 'attribute_code', string(.))"/>
									</attribute_id>
									<value_index>
										<xsl:value-of select="mip:attributeOptionValue(string(.), string(following-sibling::*[1]), null, 0, null, true)"/>
								</value_index>
								</value>
							</xsl:if>
						</xsl:for-each>
					</values>
				</node>
				</xsl:if>
				</xsl:for-each>
			</configurable_products_data>
		</xsl:if>
	</xsl:template>

    <xsl:template name="split">
        <xsl:param name="pText" select="."/>
        <xsl:param name="pItemElementName" />
        <xsl:param name="pCount" select="1" />

        <xsl:if test="string-length($pText) > 0">
            <xsl:variable name="vNextItem" select=
                    "substring-before(concat($pText, ','), ',')"/>
            <xsl:variable name="vNextFullPath" select=
                    "concat('/var/mip/bware/images/product_images/', $vNextItem)"/>

            <xsl:if test="$vNextItem">
            <xsl:element name="{$pItemElementName}">
                <xsl:if test="$pCount = 1">
                    <types array="true">
                        <type><xsl:text>thumbnail</xsl:text></type>
                        <type><xsl:text>image</xsl:text></type>
                        <type><xsl:text>small_image</xsl:text></type>
                    </types>
                </xsl:if>
                <full><xsl:value-of select="$vNextItem" /></full>
                <position><xsl:value-of select="$pCount" /></position>
                <file><xsl:value-of select="$vNextFullPath"/></file>
                <cache><xsl:value-of select="mip:filetime($vNextFullPath)"/></cache>
            </xsl:element>
            </xsl:if>
            <xsl:call-template name="split">
                <xsl:with-param name="pText" select="substring-after($pText, ',')"/>
                <xsl:with-param name="pItemElementName" select="$pItemElementName"/>
                <xsl:with-param name="pCount" select="$pCount + 1" />
            </xsl:call-template>
        </xsl:if>
    </xsl:template>
    
    <xsl:template name="parseImages">
        <xsl:param name="str" select="."/>
        <xsl:param name="splitString" select="' '"/>
        
        <xsl:variable name="filename">
            <xsl:choose>
                <xsl:when test="contains($str, $splitString)">
                    <xsl:value-of select="substring-before($str, $splitString)"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$str"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        
        <image>
			<types array="true">
			
				<xsl:choose>
					<xsl:when test="contains($filename, '_1.')">
						<type><xsl:text>thumbnail</xsl:text></type>
						<type><xsl:text>image</xsl:text></type>
						<type><xsl:text>small_image</xsl:text></type>						
					</xsl:when>
					<xsl:when test="contains($filename, '_2.')">

					</xsl:when>						
				</xsl:choose>					
			
			</types>
			<label/>
			<position>
				<xsl:choose>
					<xsl:when test="contains($filename, '_1.')">
						<xsl:text>1</xsl:text>
					</xsl:when>
					<xsl:when test="contains($filename, '_2.')">
						<xsl:text>2</xsl:text>
					</xsl:when>
					<xsl:when test="contains($filename, '_3.')">
						<xsl:text>3</xsl:text>
					</xsl:when>
					<xsl:when test="contains($filename, '_4.')">
						<xsl:text>4</xsl:text>
					</xsl:when>
					<xsl:when test="contains($filename, '_5.')">
						<xsl:text>5</xsl:text>
					</xsl:when>
					<xsl:when test="contains($filename, '_6.')">
						<xsl:text>6</xsl:text>
					</xsl:when>	
					<xsl:when test="contains($filename, '_7.')">
						<xsl:text>7</xsl:text>
					</xsl:when>	
					<xsl:when test="contains($filename, '_8.')">
						<xsl:text>8</xsl:text>
					</xsl:when>						
					<xsl:when test="contains($filename, '_9.')">
						<xsl:text>9</xsl:text>
					</xsl:when>
					
					<xsl:otherwise>
						<xsl:text>image</xsl:text>
					</xsl:otherwise>					
				</xsl:choose>
			</position>	            
            <file>
                <xsl:value-of select="concat('/var/mip/bware/images/product_images/', $filename)"/>
            </file>
            <cache>
                <xsl:value-of select="mip:filetime(concat('/var/mip/bware/images/product_images/', string($filename)))"/>
            </cache>
        </image>
        
        <xsl:if test="contains($str,$splitString)">
            <xsl:call-template name="parseImages">
                <xsl:with-param name="str"
                    select="substring-after($str,$splitString)"/>
                <xsl:with-param name="splitString" select="$splitString"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>
    
    
    
    <xsl:template match="*"> </xsl:template>
    
    
        
        
  
        
</xsl:stylesheet>