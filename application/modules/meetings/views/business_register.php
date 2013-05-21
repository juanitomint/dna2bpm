<!DOCTYPE html> 
<html> 
	<head id="Head1" runat="server"> 
	<title>Login</title>
</head> 
<body> 
   
<div data-role="page">

    <div data-role="header" data-position="inline" data-nobackbtn="true">
        <h1>Sign up</h1>
        <a href="/m/" class="ui-btn-right">Cancel</a>
    </div>

    <div data-role="content" data-theme="c" data-inset="true">

        <form action="/FormActions/register.ashx" method="get">
            
            <fieldset>
                
                <asp:Literal runat="server" ID="litMessage"></asp:Literal>
                
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value=""  />

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value=""  />

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" value="" />

                <label for="firstName">First name:</label>
                <input type="text" name="firstName" id="firstName" value=""  />

                <label for="firstName">Last name:</label>
                <input type="text" name="lastName" id="lastName" value=""  />

                <p>By submitting this information, I acknowledge that I have 
                read and agree to the <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></p>

                <a href="index.html" data-role="button" data-inline="true" data-theme="b">Login</a>

                <p>Already have an account? <a data-rel="dialog" href="login.aspx">Login</a></p> 

            </fieldset>
            
        </form>

    </div>

</div>

</body>
</html>