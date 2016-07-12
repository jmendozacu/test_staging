<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	
	<xsl:template match="OLProducts">
		<data>
			<category_id>2</category_id>
			<parent_id>0</parent_id>
			<name>Root</name>
			<is_active>1</is_active>
			<position>1</position>
			<level>0</level>
			<delete_old_childs><xsl:value-of select="mip:config('delete_old_childs', 'category', 'mip_bware', null)"/></delete_old_childs>	
			<children array="true">
				<xsl:apply-templates select="Product[DATA_ParentID = mip:config('bware_parent_id', 'category', 'mip_bware', null, '0')]" />
			</children>
		</data>
	</xsl:template>
	
	<xsl:template match="Product">
		<child>
			<category_id><xsl:value-of select="DATA_CategoriesID"/></category_id>
			<parent_id><xsl:value-of select="DATA_ParentID"/></parent_id>
			<is_active>
				<xsl:choose>
					<xsl:when test="DATA_Status = '-1'">
						<xsl:text>1</xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text>0</xsl:text>
					</xsl:otherwise>
				</xsl:choose>
			</is_active>
			<name><xsl:value-of select="DATA_CategoriesName" /></name>
            <!--<is_anchor>1</is_anchor>
            <image><xsl:value-of select="DATA_images"/></image>-->
			<position><xsl:value-of select="DATA_SortOrder" /></position>
			<shop_id><xsl:value-of select="DATA_LanguageCode" /></shop_id>
			
			<meta_title><xsl:value-of select="DATA_CategoriesMetaTitle" /></meta_title>
			<meta_keywords><xsl:value-of select="DATA_CategoriesMetaKeywords" /></meta_keywords>
			<meta_description><xsl:value-of select="DATA_CategoriesMetaDescription" /></meta_description>
			
			<description><xsl:value-of select="DATA_CategoriesDescription" /></description>
			<children array="true">
				<xsl:variable name="parentID">
					<xsl:value-of select="DATA_CategoriesID"/>
				</xsl:variable>
				<xsl:apply-templates select="/OLProducts/Product[DATA_ParentID=$parentID]" />
			</children>
		</child>
	</xsl:template>
	
</xsl:stylesheet>
