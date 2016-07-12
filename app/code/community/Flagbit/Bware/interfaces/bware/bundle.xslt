<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

    <xsl:template match="/">
        <data>
            <xsl:apply-templates select="/OLProducts" />
        </data>
    </xsl:template>
    
    <xsl:key name="products-by-stueckliste" match="Product" use="DATA_Stueckliste" />
    
    <xsl:template match="OLProducts">
        <xsl:for-each select="Product[count(. | key('products-by-stueckliste', DATA_Stueckliste)[1]) = 1]">
            <xsl:sort select="DATA_Stueckliste" />
            <node>
                <sku>
                    <xsl:value-of select="DATA_Stueckliste" />
                </sku>
                <website_ids>
                    <node>1</node>
                </website_ids>
                <store_ids>
                    <node>1</node>
                </store_ids>
                <mip_datahash_id>
                    <xsl:value-of select="DATA_Stueckliste" />_Bundle
                </mip_datahash_id>
                <has_options>1</has_options>
                <required_options>0</required_options>
                <required_options>container2</required_options>
                <tax_class_id>2</tax_class_id>
                <stock_data>
                    <use_config_manage_stock>1</use_config_manage_stock>
                    <is_in_stock>1</is_in_stock>
                </stock_data>
                <can_save_custom_options>1</can_save_custom_options>
                <can_save_configurable_attributes></can_save_configurable_attributes>
                <type>bundle</type>
                <sku_type>0</sku_type>
                <price_type>1</price_type>
                <type_has_options>true</type_has_options>
                <can_save_bundle_selections>1</can_save_bundle_selections>
                <affect_bundle_product_selections>1</affect_bundle_product_selections>
                <bundle_options_data>
                    <xsl:for-each select="key('products-by-stueckliste', DATA_Stueckliste)">
                        <xsl:sort select="DATA_Element" />
                        <node>
                            <type>radio</type>
                            <title><xsl:value-of select="DATA_Element" /> Product</title>
                            <default_title><xsl:value-of select="DATA_Element" /> Product</default_title>
                            <required>1</required>
                            <position></position>
                            <delete></delete>
                            <option_id></option_id>
                        </node>
                    </xsl:for-each>
                </bundle_options_data>   
                <bundle_selections_data>
                    <xsl:for-each select="key('products-by-stueckliste', DATA_Stueckliste)">
                        <xsl:sort select="DATA_Element" />
                        <node>
                            <node>
                                <product_id>{{getid type='product' field='sku' value='<xsl:value-of select="DATA_Element" />'}}</product_id>
                                <sku><xsl:value-of select="DATA_Element" /></sku>
                                <selection_qty><xsl:value-of select="translate(DATA_Menge, ',.' , '.,' )" /></selection_qty>
                                <selection_can_change_qty>0</selection_can_change_qty>
                                <selection_price_value>0.00</selection_price_value>
                                <selection_price_type>0</selection_price_type>
                                <delete></delete>
                                <selection_id></selection_id>
                                <option_id></option_id>
                                <position></position>
                            </node>
                        </node>
                    </xsl:for-each>
                </bundle_selections_data>  
            </node>
        </xsl:for-each>
    </xsl:template> 
</xsl:stylesheet>