<div class="container">
    <div class="well well-large span6 offset3">
        <form  action="{module_url}recover/send" method="post">
            <h1>{lang loginMsg}</h1>
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
                    <i class="icon-envelope"></i>
                    </span>
                    <input name="mail" id="log inputIcon" value="" class="username span5" placeholder="Email" type="email">
                </div><br/>
                    <button class="btn btn-success" type="submit">{lang loginButton}</button>



            </div>
        </form>
    </div> 
</div> 

<!-- /container -->