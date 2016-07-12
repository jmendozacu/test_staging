<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://mip.flagbit.com/magento-data"  version="1.0">


	<xsl:template match="/">
		<data array="true">
			<xsl:apply-templates select="/OLProducts/Product"/>
	</data>
	</xsl:template>
	
	<xsl:template match="Product">	
		<product>
			<xsl:apply-templates select="*"/>
		</product>
	</xsl:template>


	<xsl:template match="DATA_Artikelnummer">
		<sku> 
			<xsl:value-of select="."/>
		</sku>	
		<status>2</status>
	</xsl:template>
	
</xsl:stylesheet>