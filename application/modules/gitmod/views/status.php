        <ul id="status" class="todo-list ui-sortable connectedSortable">
            {status}
            <li>
                <!-- drag handle -->
                <span class="handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                </span>
                <span class="text-{class}">[{status}]</span>
                <span class="text-{class} filename">{filename}</span>
                <div class="tools">
                    <a href='#'>
                        <i class="fa fa-reply checkout"></i>
                        Discard
                    </a>
                </div>
            </li>
            {/status}
        </ul>