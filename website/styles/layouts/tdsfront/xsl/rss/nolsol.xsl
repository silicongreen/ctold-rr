<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html" /> 
	
	 <xsl:variable name="title" select="/rss/channel/title"/>		
	<xsl:template match="/">
	
		<html>
			<head>
				<title>
					<xsl:value-of select="$title"/> XML Feed</title>
			<link rel="stylesheet" href="/shared/bsp/xsl/rss/css/nolsol_xsl.css" type="text/css"/>
			
			</head>	
		<xsl:apply-templates select="rss/channel"/>		
		</html>
	</xsl:template>
	
		<xsl:template match="channel">
		<body>		
		<xsl:if test="contains($title, 'BBC News') or contains($title, 'BBC Election 2005')">
		<a href="{link}"><img src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/news_logo.gif" width="163" height="34" alt="News logo" border="0" vspace="10" /></a><br />
      		</xsl:if>
      		<xsl:if test="contains($title, 'BBC Sport')">
		 <a href="{link}"><img src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/sport_logo.gif" width="197" height="34" alt="Sport logo" border="0" vspace="10" /></a><br />
      		</xsl:if>
			
		 	<div class="topbox">
			<div class="padtopbox">
			<h2>What is this page?</h2>
			<p>This is an RSS feed from the <xsl:value-of select="image/title" /> website. RSS feeds allow you to stay up to date with the latest news and features you want from  <xsl:value-of select="image/title" />.</p>
			<p>To subscribe to it, you will need a News Reader or other similar device. If you would like to use this feed to display  <xsl:value-of select="image/title" /> content on your site, 
			<xsl:if test="contains($title, 'BBC News') or contains($title, 'BBC Election 2005')">			<a href="http://news.bbc.co.uk/1/hi/help/3223484.stm#mysite">please go here.</a></xsl:if>	
			<xsl:if test="contains($title, 'BBC Sport')"><a href="http://news.bbc.co.uk/sport1/hi/help/rss/3397215.stm#mysite">please go here.</a></xsl:if>
			</p>
			<p>
			
			<xsl:if test="contains($title, 'BBC News') or contains($title, 'BBC Election 2005')"><a href="http://news.bbc.co.uk/1/hi/help/3223484.stm#whatisrss"><img height="15" width="21" vspace="3" border="0" alt="question mark" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/question.gif" align="left" /><strong>Help</strong>, I don't know what a news reader is and still don't know what this is about.</a><br clear="all" /></xsl:if>	
			<xsl:if test="contains($title, 'BBC Sport')"><a href="http://news.bbc.co.uk/sport1/hi/help/rss/3397215.stm#whatisrss"><img height="15" width="21" vspace="3" border="0" alt="question mark" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/question.gif" align="left" /><strong>Help</strong>, I don't know what a news reader is and still don't know what this is about.</a><br clear="all" /></xsl:if>
			
			</p>
			</div>
			</div>		
			
			<div class="banbox">
			<div class="padbanbox">			
			<div class="mvb">
			<div class="fltl"><span class="subhead">RSS Feed For: </span></div><a href="#" class="item"><img height="16" hspace="5" vspace="0" border="0" width="16" alt="RSS News feeds" src="http://newsimg.bbc.co.uk/shared/img/v3/feed.gif" title="RSS News feeds" align="left" /><xsl:value-of select="$title"/></a><br clear="all" />
			 </div>
			 
			<div class="fltclear">Below is the latest content available from this feed. 			
			<xsl:if test="contains($title, 'BBC News') or contains($title, 'BBC Election 2005')"><a href="http://news.bbc.co.uk/1/hi/help/rss/3223484.stm#userss">This isn't the feed I want.</a></xsl:if>	
			<xsl:if test="contains($title, 'BBC Sport')"><a href="http://news.bbc.co.uk/sport1/hi/help/rss/3397215.stm#userss">This isn't the feed I want.</a><br clear="all" /></xsl:if>
			</div>		
			
	
			</div>
			</div>		
			
			<div class="mainbox">
				<div class="itembox">
					<div class="paditembox">
					<xsl:apply-templates select="item"/>
					</div>
				</div>	
				<div class="rhsbox">
					<div class="padrhsbox">
					<h2>Subscribe to this feed</h2>
					<p>You can subscribe to this RSS feed in a number of ways, including the following:</p>
					<ul>
					<li>Drag the orange RSS button into your News Reader</li>
					<li>Drag the URL of the RSS feed into your News Reader</li>
					<li>Cut and paste the URL of the RSS feed into your News Reader</li>
					</ul>										
					<xsl:if test="system-property('xsl:vendor')='Transformiix'">					
					</xsl:if>
					<xsl:if test="system-property('xsl:vendor')='Microsoft'">
					<div class="mvb">
					<span class="subhead">One-click subscriptions</span>
					</div>
					<div class="mvb">
					If you use one of the following web-based News Readers, click on the appropriate button to subscribe to the RSS feed.
					</div>
					
					<script language="javascript" type="text/javascript"> 									var url=window.location;
					
					document.write('<a href="http://www.bloglines.com/sub/'+ url +'"><img height="18" width="91" vspace="3" border="0" alt="bloglines" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/bloglines.gif" /></a><br />');
					document.write('<a href="http://www.feedzilla.com/mini/default.asp?ref=bbc&amp;url='+ url +'"><img height="22" width="93" vspace="3" border="0" alt="feedzilla" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/feedzilla.gif" /></a><br />');
					document.write('<a href="http://add.my.yahoo.com/rss?url='+ url +'"><img height="17" width="91" vspace="3" border="0" alt="my yahoo" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/myyahoo.gif" /></a><br />');
	
	document.write('<a href="http://www.newsgator.com/ngs/subscriber/subext.aspx?url='+ url +'"><img height="17" width="91" vspace="3" border="0" alt="newsgator" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/newsgator.gif" /></a><br />');		
	document.write('<a href="http://www.live.com/?add='+ url +'"><img height="17" width="91" vspace="3" border="0" alt="Microsoft Live" src="http://newsimg.bbc.co.uk/shared/bsp/xsl/rss/img/windowslive.gif" /></a><br />');			
					</script>	
		<div class="disclaim">The BBC is not responsible for the content of external internet sites</div>				
					</xsl:if>			
							
					
					</div>		
				</div>	
			</div>	
			
		<div class="footerbox">
		<xsl:if test="contains($title, 'BBC News') or contains($title, 'BBC Election 2005')">	
		<a href="http://news.bbc.co.uk/1/hi/help/3281849.stm" title="Copyright Notice"><img src="http://newsimg.bbc.co.uk/nol/shared/img/nav/v3_bbc_copyright_footer_2005.gif" width="65" height="7" border="0" vspace="3" hspace="5" alt="Copyright Notice" /></a>
      		</xsl:if>
      		<xsl:if test="contains($title, 'BBC Sport')">		 
		<a href="http://news.bbc.co.uk/sport1/shared/bsp/hi/services/copyright/html/default.stm" title="Copyright Notice"><img src="http://newsimg.bbc.co.uk/nol/shared/img/nav/v3_bbc_copyright_footer_2005.gif" width="65" height="7" border="0" vspace="3" hspace="5" alt="Copyright Notice" /></a>
      		</xsl:if>
		</div>
				
		</body>
	</xsl:template>
		
	<xsl:template match="item">
	<div id="item">
	<ul>
			<li>
				<a href="{link}" class="item">
					<xsl:value-of disable-output-escaping="yes" select="title"/>
				</a><br/>			
				<div>
				<xsl:value-of disable-output-escaping="yes" select="description" />					
				</div>	
				</li>
		</ul>
	</div>		
	</xsl:template>
	
</xsl:stylesheet>