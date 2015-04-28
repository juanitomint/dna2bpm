<div class="box box-success">
    <span class="hidden widget_url">{widget_url}</span>
    <div class="box-header" style="cursor: move;">
        <h3 class="box-title">log</h3>
        <div class="box-tools pull-right" data-toggle="tooltip" title="" data-original-title="Status">
        </div>
        <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 250px;">
            <div class="box-body chat" id="chat-box" style="overflow: auto; width: auto; height: 250px;">
                <!-- chat item -->
                {history}
                <div class="item">
                    <img src="{gravatar}" alt="user image" class="img-thumbnail">
                    <p class="message">
                        <a href="#" class="name">
                            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> {date}</small> {name}
                        </a>
                        {subject}
                        <br/>
                        {files}
                    </p>
                </div>
                <hr/>
                {/history}
                
            </div>
        </div>
        <!-- /.chat -->
        <div class="box-footer">
        </div>
    </div>
</div>