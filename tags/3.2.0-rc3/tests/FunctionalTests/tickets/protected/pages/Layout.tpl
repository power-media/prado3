<!DOCTYPE html PUBLIC 
	"-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<com:THead Title="PRADO Functional Tests">
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<style type="text/css">
	/*<![CDATA[*/
	.defect
	{
		color: #c00;
		font-size: 1.15em;
	}
	body
	{
		font-family: Georgia, "Times New Roman", Times, serif;
	}
	.w3c
	{
		margin-top: 2em;
		display: block;
	}
	/*]]>*/
	</style>
</com:THead>
<body>
<com:TForm>
<h1><com:THyperLink ID="ticketlink" /></h1>

<com:TContentPlaceHolder ID="Content" />
<hr style="margin-top: 2em" />
<com:TJavascriptLogger />
</com:TForm>
<div class="w3c">
<a href="http://validator.w3.org/check?uri=referer">
		Validate XHTML 1.0
</a>
<a href="?page=ViewSource&amp;path=<%= str_replace('.','/', $this->Request->ServiceParameter) %>.page"
	style="margin: 0 1em;"
	onclick="window.open(this.href); return false;" 
	onkeypress="window.open(this.href); return false;">View Source</a>
</div>
</body>
</html>