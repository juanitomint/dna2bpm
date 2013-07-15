<!<div class="container">
    <form class="form-signin" id="formAuth" action="{module_url}recover/save_new_pass" method="post">
        <h1 class="form-signin-heading">{lang loginMsg}</h1>
        <!--{if {show_warn}}                
        <div class="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Warning!</strong> {msgcode}
        </div>
        {/if}   -->
        <!-- username -->
        <div class="div_text">
            <div class="input-prepend">
                <span class="add-on">
                    <i class="icon-lock">
                    </i>
                </span>
                <input name="password1" id="log inputIcon" value="" class="username span2" placeholder="nueva contrase&ntilde;a" type="password">
            </div>
            <div class="input-prepend">
                <span class="add-on">
                    <i class="icon-repeat">
                    </i>
                </span>
                <input name="password2" id="log inputIcon" value="" class="username span2" placeholder="repetir contrase&ntilde;a" type="password">
                <input name="token" id="token" value="{token}" type="hidden">
            </div>

        </div>
        

        </div>
       
        <button class="btn  btn-success" type="submit">{lang loginButton}</button>
        <br/>
    </form>

</div> 
<!-- /container -->