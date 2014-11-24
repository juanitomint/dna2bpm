
    <ul id="staged" class="todo-list ui-sortable connectedSortable droptrue"> 
        <li>
            <!-- drag handle -->
                <span class="text-success drag-below"><i class="fa fa-arrow-circle-down"></i> Drag files below this <i class="fa fa-arrow-circle-down"></i></span>
        </li>
        {staged}
        <li>
            <!-- drag handle -->
                <span class="handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>
                 <span class="text-{class}">[{status}]</span>
                <span class="text-{class} filename">{filename}</span>
        </li>
        {/staged}
    </ul>
    <hr/>
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#gitModal" data-whatever="@mdo">Commit</button>
    
<!-- Row -->