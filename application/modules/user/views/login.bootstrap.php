<div class="container">

    <form class="form-signin" id="formAuth" action="{authUrl}" method="post">
        <h2 class="form-signin-heading">Please sign in</h2>
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
                    <i class="icon-user">
                    </i>
                </span>
                <input name="username" id="log inputIcon" value="" class="username span2" type="text">
            </div>

        </div>
        <!-- password -->
        <div class="div_text">
            <div class="input-prepend">
                <span class="add-on">
                    <i class="icon-lock">
                    </i>
                </span>
                <input name="password" id="pwd inputIcon" class="password span2" type="password">
            </div>

        </div>
        <label class="checkbox">
            <input type="checkbox" value="remember-me"> Remember me
        </label>
        <button class="btn  btn-success" type="submit">Sign in</button>
        <br/>
    </form>

</div> 
<!-- /container -->