<div class="{box_class}">
        <div class="box-header">
            <i class="fa {box_icon}"></i>
            <h3 class="box-title">{title}</h3>
            <div class="box-tools pull-right">      
                {if {button_collapse}=='1'}
                    <button class="btn {btn_class} btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                {/if}
                {if {button_remove}=='1'}
                     <button class="btn {btn_class} btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                {/if}
            </div>
        </div><!-- /.box-header -->
        <div class="box-body" style="{body_style}">
                {content}
        </div><!-- /.box-body -->
</div>