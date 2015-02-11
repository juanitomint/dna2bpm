<!-- MAILBOX BEGIN -->

                    <div class="mailbox row">
                        <div class="col-xs-12">
                            <div class="box box-solid">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-4">

                                            <!-- compose message btn -->
                                            {if {usercan_create}}
                                            <a class="btn btn-block btn-primary load_modal" href="{base_url}inbox/new_msg" title="New Message"><i class="fa fa-pencil" ></i> {lang compose}</a>
                                            {/if}
                                            <!-- Navigation - folders-->
                                            <div style="margin-top: 15px;">
                                                <ul class="nav nav-pills nav-stacked">
                                                    <li class="header">{lang folders}</li>
                                                    <li id="bt_inbox" class="{if {folder}=='inbox'}active{/if}"><a href="{base_url}inbox/print_folder/inbox" class="ajax" data-target="inbox-list"><i class="fa fa-inbox"></i> {lang inbox} (<span class='inbox_count'>{inbox_count}</span>)</a></li>
                                                    <li id="bt_outbox" class="{if {folder}=='outbox'}active{/if}"><a href="{base_url}inbox/print_folder/outbox" class="ajax" data-target="inbox-list"><i class="fa fa-mail-forward"></i> {lang sent} (<span class='sent_count'>{sent_count}</span>)</a></li>
                                                    <li id="bt_star" class="{if {folder}=='star'}active{/if}" ><a  href="{base_url}inbox/print_folder/star" class="ajax" data-target="inbox-list"><i class="fa fa-star"></i> {lang starred} (<span class='star_count'>{star_count}</span>)</a></li>
                                                    <li id="bt_trash" class="{if {folder}=='trash'}active{/if}"><a href="{base_url}inbox/print_folder/trash" class="ajax" data-target="inbox-list"><i class="fa fa-folder"></i> {lang trash} (<span class='trash_count'>{trash_count}</span>)</a></li>
                                                </ul>
                                            </div>
                                        </div><!-- /.col (LEFT) -->
                                        <div class="col-md-9 col-sm-8">
                                            <div class="row pad">
                                                <div class="col-sm-6">
                                         
                                                    <label style="margin-right: 10px;">
                                                        <input type="checkbox" id="check-all"/>
                                                    </label>
                                                    <!-- Action button -->

                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown">
                                                            <i class="fa fa-check" style="margin-right:5px"></i> {lang action} <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu" id="msg_action">
                                                            <li><a href="#" data-action="read"><i class="fa fa-eye"></i>  {lang mark_as_read}</a></li>
                                                            <li><a href="#" data-action="unread"><i class="fa fa-eye-slash"></i>  {lang mark_as_unread}</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#" data-action="inbox"><i class="fa fa-inbox"></i>  {lang move_to} {lang inbox}</a></li>
                                                            <li><a href="#" data-action="junk"><i class="fa fa-folder"></i>  {lang move_to} {lang trash}</a></li>
                                                            <li class="divider"></li>
                                                            <li><a href="#" data-action="delete"><i class="fa fa-trash-o" style="color:#f00"></i> {lang delete}</a></li>
                                                        </ul>
                                                    </div>
                                                  <!-- TagS button -->

                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown">
                                                            <i class="fa fa-tag" style="margin-right:5px"></i> {lang h_tags} <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu" id="msg_tag">
                                                            <li><a href="#" data-action="tag" data-priority="extreme"><i class="fa fa-fire" style="color:#f00"></i> {lang extreme_priority}</a></li>
                                                            <li><a href="#" data-action="tag" data-priority="high"><i class="fa fa-fire" style="color:#f60"></i> {lang high_priority}</a></li>
                                                            <li><a href="#" data-action="tag" data-priority="normal"><i class="fa fa-fire" style="color:#00A600"></i> {lang normal_priority}</a></li>
                                                            <li><a href="#" data-action="tag" data-priority="low"><i class="fa fa-fire" style="color:#50AEDD"></i> {lang low_priority}</a></li>
                                                        <li class="divider"></li>
                                                         <li><a href="#" data-action="tag" data-priority="notag"><i class="fa fa-trash-o" style="color:#f00"></i> {lang remove_tag}</a></li>
                                                        
                                                        </ul>
                                                    </div>
                                                    <!-- Refresh button -->
                            							<button type="button" class="btn btn-default btn-sm btn-flat " id="bt_refresh">
                                                             <i class="fa fa-refresh" style="margin-right:5px"></i> {lang refresh} 
                                                        </button>
                                                        
										
                                                </div>
                                                <div class="col-sm-6 search-form">
                                                    <form action="#" class="text-right" name="form_search">
                                                        <div class="input-group">                                                            
                                                            <input type="text" class="form-control input-sm" placeholder="{lang search}" id="search">
                                                            <div class="input-group-btn">
                                                                <button  name="q" type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                                                            </div>
                                                        </div>                                                     
                                                    </form>
                                                </div>
                                            </div><!-- /.row -->

                                            <div class="table-responsive dummy_msgs">
                                            {my_msgs}       
                                                                               
                                            </div><!-- /.table-responsive -->
                                        </div><!-- /.col (RIGHT) -->
                                    </div><!-- /.row -->
                                </div><!-- /.box-body -->
                                <div class="box-footer clearfix">

                                </div><!-- box-footer -->
                            </div><!-- /.box -->
                        </div><!-- /.col (MAIN) -->
                    </div>
                    <!-- MAILBOX END -->
                    