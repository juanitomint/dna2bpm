<li class="dropdown messages-menu"><a href="#" class="dropdown-toggle"
	data-toggle="dropdown"> <i class="fa fa-envelope"></i> <span
		class="label label-success">{inbox_count}</span>
</a>
	<ul class="dropdown-menu">
		<li class="header">You have {inbox_count} messages</li>   
		<li>
			
			<ul class="menu">
			{mymsgs}
			<!-- ==== Loop Toolbar MSGs -->
				<li>
					 <a href="#" data-msgid="{msgid}" class="msg">
						<h4 style="margin-left:5px">{subject}<small><i class="fa fa-clock-o"></i> {msg_time}</small></h4>
						<p style="margin-left:5px">{excerpt} ...</p>
					</a>
				</li>
			{/mymsgs}
			<!-- ---- Loop Toolbar MSGs -->


			</ul>
		</li>
        
		<li class="footer"><a href="{base_url}dashboard/inbox">See All Messages</a></li>
	</ul></li>