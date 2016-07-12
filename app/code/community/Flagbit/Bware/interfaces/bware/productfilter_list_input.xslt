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


	<xsl:template match="DATA_sku">
		<sku><xsl:value-of select="."/></sku>
        <mip_datahash_id><xsl:value-of select="."/></mip_datahash_id>	
    </xsl:template>
    
    <xsl:template match="NODE_Filter">
		<kinder_groese><xsl:value-of select="mip:attributeOptionValue('kinder_groese', string(./NODE_Filter/DATA_groese), null, 0, null, true)"/></kinder_groese>
        <kinder_toene><xsl:value-of select="mip:attributeOptionValue('kinder_toene', string(./NODE_Filter/DATA_toene), null, 0, null, true)"/></kinder_toene>
        <kinder_weite><xsl:value-of select="mip:attributeOptionValue('kinder_weite', string(./NODE_Filter/DATA_weite), null, 0, null, true)"/></kinder_weite>    
        
                
    </xsl:template>
    
  
        
</xsl:stylesheet>