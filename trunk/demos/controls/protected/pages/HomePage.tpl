<%@ MasterClass="Pages.master.MasterPage" %>
<com:TContent id="header" >
<com:TForm>
<com:THiddenField Value="test" />
<h1>Welcome! <%=$this->User->Name %></h1>
<com:TLiteral Text="<literal>" Encode="true"/>
<com:TTextBox ID="TextBox" Text="textbox" />
<com:TLabel Text="test" AssociatedControlID="checkbox"/><br/>
<com:System.Web.UI.WebControls.TButton text="Toggle Button" ForeColor="red" Font.size="18" Click="testClick" /> (requires membership)<br/>
<com:TCheckBox Text="Checkbox" ID="checkbox"/><br/>
<com:TImage ImageUrl=<%~/fungii_logo.gif %> />
<com:TImageButton ImageUrl=<%~/fungii_logo.gif %> /><br/>
<com:THyperLink Text="Visit a 'classless' page" NavigateUrl="?sp=page.plain" /> |
<com:THyperLink Text="Visit member only page" NavigateUrl="?sp=page.private.member" />
<com:TLinkButton Text="Click Me" Click="linkClicked" />
</com:TForm>
</com:TContent>