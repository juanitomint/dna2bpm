<!-- MAILBOX BEGIN -->
<input type="hidden" id="whereiam" value="{folder}"/>
                    <div class="mailbox row">
                        <div class="col-xs-12">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-4">

                                            <!-- compose message btn -->
                                            <a class="btn btn-block btn-primary load_modal" href="{base_url}inbox/new_msg" title="New Message"><i class="fa fa-pencil" ></i> Compose Message</a>
                                            <!-- Navigation - folders-->
                                            <div style="margin-top: 15px;">
                                                <ul class="nav nav-pills nav-stacked">
                                                    <li class="header">Folders</li>
                                                    <li class="{if {folder}=='inbox'}active{/if}"><a href="{base_url}dashboard/inbox"><i class="fa fa-inbox"></i> Inbox ({inbox_count})</a></li>
                                                    <li class="{if {folder}=='outbox'}active{/if}"><a href="{base_url}dashboard/inbox/outbox"><i class="fa fa-mail-forward"></i> Sent</a></li>
                                                    <li class="{if {folder}=='star'}active{/if}" ><a  href="{base_url}dashboard/inbox/star"><i class="fa fa-star"></i> Starred</a></li>
                                                    <li class="{if {folder}=='trash'}active{/if}"><a href="{base_url}dashboard/inbox/trash"><i class="fa fa-folder"></i> Trash</a></li>
                                                </ul>
                                            </div>
                                        </div><!-- /.col (LEFT) -->
                                        <div class="col-md-9 col-sm-8">
                                            <div class="row pad">
                                                <div class="col-sm-6">
                                                {if {folder}!='outbox'}
                                                    <label style="margin-right: 10px;">
                                                        <input type="checkbox" id="check-all"/>
                                                    </label>
                                                    <!-- Action button -->

                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown">
                                                            <i class="fa fa-check" style="margin-right:5px"></i> Action <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu" id="msg_action">
                                                            <li><a href="#" data-action="read"><i class="fa fa-eye"></i>  Mark as read</a></li>
                                                            <li><a href="#" data-action="unread"><i class="fa fa-eye-slash"></i>  Mark as unread</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#" data-action="inbox"><i class="fa fa-inbox"></i>  Move to inbox</a></li>
                                                            <li><a href="#" data-action="junk"><i class="fa fa-folder"></i>  Move to trash</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#" data-action="delete"><i class="fa fa-trash-o" style="color:#f00"></i> Delete</a></li>
                                                        </ul>
                                                    </div>
                                                  <!-- TagS button -->

                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown">
                                                            <i class="fa fa-tag" style="margin-right:5px"></i> Tags <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu" id="msg_tag">
                                                            <li><a href="#" data-action="tag" data-priority="extreme"><i class="fa fa-fire" style="color:#f00"></i> Extreme priority</a></li>
                                                            <li><a href="#" data-action="tag" data-priority="high"><i class="fa fa-fire" style="color:#f60"></i> High priority</a></li>
                                                            <li><a href="#" data-action="tag" data-priority="normal"><i class="fa fa-fire" style="color:#00A600"></i> Normal priority</a></li>
                                                            <li><a href="#" data-action="tag" data-priority="low"><i class="fa fa-fire" style="color:#50AEDD"></i> Low priority</a></li>
                                                        <li class="divider"></li>
                                                         <li><a href="#" data-action="tag" data-priority="notag"><i class="fa fa-trash-o" style="color:#f00"></i> Remove tag</a></li>
                                                        
                                                        </ul>
                                                    </div>
												{/if}
                                                </div>
                                                <div class="col-sm-6 search-form">
                                                    <form action="#" class="text-right">
                                                        <div class="input-group">                                                            
                                                            <input type="text" class="form-control input-sm" placeholder="Search">
                                                            <div class="input-group-btn">
                                                                <button type="submit" name="q" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                                                            </div>
                                                        </div>                                                     
                                                    </form>
                                                </div>
                                            </div><!-- /.row -->

                                            <div class="table-responsive">
                                                <!-- THE MESSAGES -->
                                                
                                                <table class="table table-mailbox">
                                                 {if {folder}=='outbox'}
                                                	{mymsgs}
                                                    <tr class="{read} msg tag_{tag}" data-msgid="{msgid}">                                     
                                                        <td class="">{from_name}</td>
                                                        <td class="subject ">{subject}</td>
                                                        <td class="time">{msg_time}</td>
                                                    </tr>
                                                    {/mymsgs}
                                                {else}
                                               		 {mymsgs}
                                                    <tr class="{read} msg tag_{tag}" data-msgid="{msgid}">
                                                        <td class="small-col"><input type="checkbox" /></td>
                                                        <td class="small-col"> <a class="{icon_star}" href="#"></a></td> 
                                                        <td class="">{from_name}</td>
                                                        <td class="subject ">{subject}</td>
                                                        <td class="time">{msg_time}</td>
                                                    </tr>
                                                    {/mymsgs}
                                                {/if}
                                                </table>
                                                
                                            </div><!-- /.table-responsive -->
                                        </div><!-- /.col (RIGHT) -->
                                    </div><!-- /.row -->
                                </div><!-- /.box-body -->
                                <div class="box-footer clearfix">
                                    <div class="pull-right">
												{pagination}
                                    </div>
                                </div><!-- box-footer -->
                            </div><!-- /.box -->
                        </div><!-- /.col (MAIN) -->
                    </div>
                    <!-- MAILBOX END -->
                    