<!-- BTN GROUP -->
<div id="content-header">
    <h1>{inbox_title}</h1>
</div>
<div id="breadcrumb">
    <a href="#" title="Go to Home" class="tip-bottom">
        <i class="icon-home">
        </i> Home</a>
    <a href="#" class="current">Inbox</a>
</div>
<!-- INBOX WIDGET -->
<div class="container-fluid">
    <!-- 2row block -->
    <div class="row-fluid">
        <!-- Start 2nd col -->
        <div class="span12">
            <ul class="msgs">
            {mymsgs}
            <li id="{msgid}">
                <a href="#" data-toggle="tooltip" data-placement="bottom" title="{msg_time}" class="tip"><span class="msg_date" >{msg_date}</span></a>
                <a class="icon {icon_star}" href="#"></a>
                <a class="subject {read}" href="#">{subject}</a>
                <a class="pull-right" href="#" ><i class="fa fa-times"></i></a>
                <div class="detail">
                    <div class="from"><strong>De: </strong><span>{sender}</span></div>
                    <div class="body">{body}</div>   
                </div>
            </li>
            {/mymsgs}
        </ul>
        </div>
        <!-- End 2nd col -->
    </div>
    
</div> 
