<!<div class="container">
    <form class="form-signin" id="formAuth" action="{module_url}recover/send" method="post">
        <h1 class="form-signin-heading">{lang loginMsg}</h1>
        {if {show_warn}}                
        <div class="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Warning!</strong> {msgcode}
        </div>
        {/if}   
        <!-- username -->
        <div class="div_text">
            <div class="input-prepend">
                <span class="add-on">
                    <i class="icon-envelope">
                    </i>
                </span>
                <input name="mail" id="log inputIcon" value="" class="username span2" placeholder="Email" type="email">
            </div>

        </div>
        

        </div>
       
        <button class="btn  btn-success" type="submit">{lang loginButton}</button>
        <br/>
    </form>

</div> 
<!-- /container -->