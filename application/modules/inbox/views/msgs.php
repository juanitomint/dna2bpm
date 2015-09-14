
<!-- THE MESSAGES -->
<input type="hidden" id="whereiam" value="{folder}" />
<table class="table table-mailbox">
	{if {folder}=='outbox'} {mymsgs}
	<tr class="{read} msg tag_{tag}" data-msgid="{msgid}">
		<td class="">{from_name}</td>
		<td class="subject ">{subject}</td>
		<td class="time">{msg_time}</td>
	</tr>
	{/mymsgs} {else} {mymsgs}
	<tr class="{read} msg tag_{tag}" data-msgid="{msgid}">
		<td class="small-col"><input type="checkbox" /></td>
		<td class="small-col"><a class="{icon_star}" href="#"></a></td>
		<td class="">{avatar}{from_name}</td>
		<td class="subject ">{subject}</td>
		<td class="time">{msg_time}</td>
	</tr>
	{/mymsgs} {/if}
</table>
<div class="pull-right">{pagination}</div>