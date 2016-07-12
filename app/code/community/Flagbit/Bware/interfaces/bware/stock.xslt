<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://mip.flagbit.com/magento-data" xmlns:dyn="http://exslt.org/dynamic"  version="1.0">


	<xsl:template match="/">
		<data array="true">
			<xsl:apply-templates select="/OLProducts/Product"/>
		</data>
	</xsl:template>
	
	<xsl:template match="Product">
		<xsl:apply-templates select="NODE_Variant[DATA_AuspraegungID != '0']" mode="products"/>		
		<product>
			<xsl:apply-templates select="DATA_Artikelnummer"/>
		</product>
	</xsl:template>

	<xsl:template match="NODE_Variant[DATA_AuspraegungID != '0']" mode="products">
		<xsl:if test="./DATA_Produktnummer != ''">
		<product>
			<sku><xsl:value-of select="./DATA_Produktnummer"/></sku>
			<product_id><xsl:value-of select="./DATA_Produktnummer"/></product_id>
			<stock_data>
				<qty><xsl:value-of select="translate(./DATA_LagerbestandVerfuegbar, ',.' , '.,' )"/></qty>
				<min_qty><xsl:value-of select="translate(./DATA_Reservebestand, ',.' , '.,' )"/></min_qty>
				<is_in_stock>1</is_in_stock>
			</stock_data>
			</product>
		</xsl:if>
	</xsl:template>

	<xsl:template match="DATA_Artikelnummer">
		<xsl:if test=". != ''">
		<sku>
			<xsl:value-of select="."/>
		</sku>

		<stock_data>
			<!--<qty><xsl:value-of select="translate(../NODE_Variant/DATA_Lagerbestand, ',.' , '.,' )"/></qty>-->
			<qty><xsl:value-of select="translate(../NODE_Variant/DATA_LagerbestandVerfuegbar, ',.' , '.,' )"/></qty>
			<min_qty><xsl:value-of select="translate(../NODE_Variant/DATA_DispoBestand, ',.' , '.,' )"/></min_qty>
			<!--min_qty><xsl:value-of select="translate(../NODE_Variant/DATA_Reservebestand, ',.' , '.,' )"/></min_qty-->
			<is_in_stock>1</is_in_stock>
		</stock_data>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
