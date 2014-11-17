
<div class="widget-box">
    <div class="widget-title">
        <span class="icon">
            <i class="{widget_icon}">
            </i>
        </span>
        <h5>{widget_title}</h5>
        <span title="Tasks" class="label label-warning tip-left">{widget_count}</span>
    </div>
    <div class="widget-content nopadding">
        <ul class="recent-comments">
            {cases}
            <li>
                <div class="comments 
                     {if {status}=='closed'}    
                     alert alert-success
                     {else}
                     alert alert-block
                     {/if}
                     ">
                    <h5>{date}</h5> 
                    <ul>
                        <li>
                            <a href="#">
                                <span>{name}::{id}::{status}</span>

                            </a>
                        </li>
                    </ul>
                    <a href="{base_url}bpm/engine/run/model/{idwf}/{id}" 
                       class="btn btn-success btn-mini">
                        <i class="icon-play icon-white"></i>
                        Continue
                    </a> 
                    <a href="{base_url}bpm/engine/startcase/model/{idwf}/{id}" 
                       class="btn btn-success btn-mini">
                        <i class="icon-retweet icon-white"></i>
                        Re-Start
                    </a> 


                    <a href="{base_url}bpm/tokens/view/{id}" role="button" class="btn btn-warning btn-mini" title="Show Tokens" >
                        <i class="icon-info-sign icon-white"></i>
                        Tokens
                    </a>





                    <a href="#" class="btn btn-danger btn-mini">
                        <i class="icon-tasks icon-trash icon-white"></i>
                        Close
                    </a>
                </div>
            </li>
            {/cases}

            <li class="viewall">
                <a title="View all comments" class="tip-top" href="#"> + View all + </a>
            </li>
        </ul>
    </div>
</div>