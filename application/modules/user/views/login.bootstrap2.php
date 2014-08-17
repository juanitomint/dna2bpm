 
        <div class="form-box" id="login-box">
            <div class="header bg-navy">{lang loginMsg}</div>
            <form id="formAuth" action="{authUrl}" method="post">
                <div class="body bg-gray">
                <!--  MSG -->
      				{if {show_warn}}
                    <div class="form-group alert alert-warning">
                       <button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong>{msgcode}</strong>	
                    </div>          
					{/if}
				 <!--  NAME -->
                    <div class="form-group">
                        <input type="text" name="username" class="form-control" placeholder="{lang username}"/>
                    </div>
                 <!--  PASS -->    
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="{lang password}"/>
                    </div>      
                 <!--  REMEMBERME -->      
                    <div class="form-group">
                       <input type="checkbox" value="remember-me" > {lang rememberButton}
                    </div>
                </div>
                <div class="footer">                                                               
                    <button type="submit" class="btn bg-olive btn-block">{lang loginButton}</button>  
                    
                    <p> <a href="{module_url}recover" >
	                        {lang forgotPassword}
	                    </a></p>
                    
                </div>
            </form>


        </div>
 
 
