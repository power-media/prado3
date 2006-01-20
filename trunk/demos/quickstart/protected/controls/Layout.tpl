<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >

<com:THead Title="PRADO QuickStart Tutorial">
<meta http-equiv="content-language" content="en"/>
</com:THead>

<body>
<com:TForm>
<div id="header">
<div class="title">Prado QuickStart Tutorial</div>
<div class="image"></div>
</div>

<div id="menu">
<a href="?">Home</a> |
<a href="http://www.pradosoft.com">PradoSoft.com</a> |
<com:TLinkButton Text="Hide TOC" Click="toggleTopicPanel" />
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td valign="top">
<div id="content">
<com:TContentPlaceHolder ID="body" />
</div>
</td>
<td valign="top" width="1">
<com:TopicList ID="TopicPanel" />
</td>
</tr>
</table>

<div id="footer">
Copyright &copy; 2005-2006 <a href="http://www.pradosoft.com">PradoSoft</a>.
<br/><br/>
<%= Prado::poweredByPrado() %>
<a href="http://validator.w3.org/check?uri=referer"><img border="0" src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
</div>

</com:TForm>
</body>
</html>