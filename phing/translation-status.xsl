<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<!-- =======================================================================
    index.html
    ======================================================================= -->
<xsl:template match="/translations">
<html>
    <head>
		<title>USVN translation status</title>
	</head>
	<body>
		<h1>USVN translation status</h1>
		<table border="1">
			<tr>
				<th>Language</th>
				<th>Fuzzy</th>
				<th>Total</th>
			</tr>
			<xsl:for-each select="translation">
				<xsl:sort select="@fuzzy" order="ascending" data-type="number"/>
				<tr>
					<td><a href="https://trac.usvn.info/browser/trunk/www/locale/{./@lang}/messages.po"><xsl:value-of select="./@lang"/></a></td>
					<td><xsl:value-of select="./@fuzzy"/></td>
					<td><xsl:value-of select="./@strings"/></td>
				</tr>
			</xsl:for-each>
		</table>
	</body>
</html>
</xsl:template>


</xsl:stylesheet>
