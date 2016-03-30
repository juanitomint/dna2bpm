<div class="alert alert-{class} {if {dismissable}}alert-dismissable{/if}" data-id={_id} {if {icon}}{else}style="margin-left:0px"{/if}>
	{if {icon}}
		<i class="fa {icon_class}"></i>
	{/if}

    {if {dismissable}}
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    {/if}
    {body}
</div>