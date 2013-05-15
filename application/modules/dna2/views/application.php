<!-- BTN GROUP -->
<div id="content-header">
    <h1>{app_title}</h1>

    <!--                <div class="btn-group">
                        <a class="btn btn-large tip-bottom" title="Manage Files">
                            <i class="icon-file">
                            </i>
                        </a>
                        <a class="btn btn-large tip-bottom" title="Manage Users">
                            <i class="icon-user">
                            </i>
                        </a>
                        <a class="btn btn-large tip-bottom" title="Manage Comments">
                            <i class="icon-comment">
                            </i>
                            <span class="label label-important">5</span>
                        </a>
                        <a class="btn btn-large tip-bottom" title="Manage Orders">
                            <i class="icon-shopping-cart">
                            </i>
                        </a>
                    </div>-->
</div>
<div id="breadcrumb">
    <a href="{module_url}" title="Go to Home" class="tip-bottom">
        <i class="icon-home">
        </i> Home</a>
    <a href="#" class="current">Application</a>
</div>
<div class="container-fluid">

    <div class="widget-title">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab1">Tasks</a></li>
            <li class=""><a data-toggle="tab" href="#tab2">Profile</a></li>
            <li class=""><a data-toggle="tab" href="#tab3">Messages</a></li>
        </ul>
    </div>
    <div class="widget-content tab-content">
        <div id="tab1" class="tab-pane active">
            {app_models}


            <div class="widget-box collapsible">
                <div class="widget-title">
                    <a href="#collapse{idwf}" data-toggle="collapse">
                        <span class="icon"><i class="icon-arrow-right"></i></span>
                    </a>
                    <span class="label label-info">{sum}</span>

                    <h5>
                        <a title="Start new case" class="tip-top" href="{base_url}bpm/engine/newcase/model/{idwf}">
                            <strong>
                                {name}
                            </strong>

                        </a>
                    </h5>
                </div>
                <div class="collapse in" id="collapse{idwf}">
                    <div class="widget-content">
                        <p>{documentation}</p>
                    </div>
                </div>
                
                {kpi}
                
                <ul>
                    {mytasks}
                    <li role="presentation">
                        <a href="#">
                            <img src="{base_url}{icon}" style="vertical-align: middle" />
                            {title}
                        </a>
                    </li>
                    {/mytasks}
                </ul>
            </div>

            {/app_models}
        </div>
        <div id="tab2" class="tab-pane">This is a Tab Two Content</div>
        <div id="tab3" class="tab-pane">This is a Tab Three Content</div>
    </div>                            




    <div class="row-fluid">
        <div id="footer" class="span12">
            2012 &copy; DNA&sup2; Admin. Brought to you by <a href="https://wrapbootstrap.com/user/diablo9983">diablo9983</a>
        </div>
    </div>
</div>
