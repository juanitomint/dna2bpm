<!-- BTN GROUP -->
<div id="content-header">
    <h1>{inbox_title}</h1>
</div>
<div id="breadcrumb">
    <a href="#" title="Go to Home" class="tip-bottom">
        <i class="icon-home">
        </i> Home </a>
    <a href="#" class="current">{inbox_title}</a>
</div>
<input type="hidden" name="whereim" value="{inbox_title}" >
<!-- INBOX WIDGET -->
<div class="container-fluid">
    <!-- 2row block -->
    <div class="row-fluid">
        <!-- Start 2nd col -->
        <div class="span12">
            <ul class="msgs">
            {mymsgs}
            <li id="{msgid}">
                <a href="#"  title="{msg_time}" ><span class="msg_date muted" >{msg_date}</span></a>
                {if {inbox_title}==Inbox}
                 <a class="icon {icon_star}" href="#"></a>
                {/if}
                <a class="subject {read}" href="#">{subject}</a>  
                {if {inbox_title}==Trash}                 
                    <a class="btn btn-default btn-mini pull-right " href="#" data-msgid="{msgid}" title="recover" name="recover"><i class="icon icon-retweet"></i></a>                 
                {/if}
                {if {inbox_title}!=Outbox}                 
                    <a class="btn btn-default btn-mini pull-right " href="#" data-msgid="{msgid}" title="delete" name="delete"><i class=" icon-trash"></i></a>     
                    <a class="btn btn-default btn-mini pull-right " href="{module_url}inbox/new_msg/{msgid}" data-msgid="{msgid}" title="reply" name="reply"><i class="icon icon-reply"></i></a>             
                {/if}

                 <div class="detail">
                    {if {inbox_title}==Outbox}
                    <div class="from"><strong>To: </strong><a href="#" data-idu="{to}"><span>{to_name}</span></a></div>
                    {else}
                    <div class="from"><strong>From: </strong><a href="#" data-idu="{from}"><span>{from_name}</span></a></div>
                    {/if}
                    <div class="body">{body}</div>   
                </div>  
            </li>
            {/mymsgs}
        </ul>
        </div>
        <!-- End 2nd col -->
    </div>
    
</div> 
