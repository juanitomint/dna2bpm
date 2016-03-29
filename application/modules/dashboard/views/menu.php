<ul class="sidebar-menu">
	<li>
        <a href="{module_url}"> <i class="fa fa-dashboard"></i>
			<span>Dashboard</span>
	</a>
    </li>
    {if {is_admin}}
    	<li>
            <a href="{module_url}kitchensink"> <i class="fa fa-flask"></i>
        		<span>KitchenSink</span>
        	</a>
    	</li>
	{/if}
	<li>
        <a href="{module_url}tasks"> <i class="fa fa-tasks"></i>
			<span>{lang Tasks}</span>
	</a>
    </li>
    <li>
        <a href="{module_url}inbox" > <i class="fa fa-envelope"></i>{lang Inbox}</a>
    </li>
    <li>
        <a href="{base_url}calendar" > <i class="fa fa-calendar"></i>{lang Calendar}</a>
    </li>
</ul>
{menu_custom}
