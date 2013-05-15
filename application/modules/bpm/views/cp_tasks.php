<div id="tasks_container">
    <div id="tasks_brief" >
             <h5>{byStatus}</h5>
            <ul id="tasks_by_status">
                <li>
                    <a class="filter-task" filter="all" href="#"> {MyTasks} ({SumTasks})</a>
                    <ul>
                        <li><a class="filter-task" filter="user" href="#">{Pending} ({brief user})</a></li>
                        <li><a class="filter-task" filter="finished" href="#">{Finished} ({brief finished})</a></li>
                    </ul>
                </li>

            </ul>
            

        <h5>{byGroup}</h5>
        <ul id="tasks_by_group">
                {groups}
                <li>{title} ({qtty})</li>
                {/groups}
        </ul>
    </div>
    <div id="tasks">

        <ul>
            {cases}
            <li>
                <h3>
                    <span class="calendar1">{date}</span> | <a href="{base_url}bpm/engine/run/model/{idwf}/{id}" target="_blank">{name}</a>::{id}::{status}
                </h3>
                {if {wfadmin}}
                <span class="inbox-item-tokens" /><a href="{base_url}bpm/repository/tokens/model/{idwf}/{id}" target="_blank">Tokens</a>
                <span class="inbox-item-restart"/><a href="{base_url}bpm/engine/startcase/model/{idwf}/{id}" title="">Restart</a>
                {/if}
                <ul>
                    {mytasks}
                    <li>
                        <img src="{base_url}{icon}" style="vertical-align: middle" />
                        {title}
                        {if {claimable}}
                        <button class="claimTask" title="claim" idwf="{idwf}" case="{case}" resourceId="{resourceId}">{claim}</button>
                        {/if}
                        {if {refusable}}
                        <button class="refuseTask" title="refuse" idwf="{idwf}" case="{case}" resourceId="{resourceId}">{refuse}</button>
                        {/if}
                        <!--manual task -->
                        {if {wfadmin}}
                        <button class="manualTask" title="refuse" idwf="{idwf}" case="{case}" resourceId="{resourceId}">{manual}</button>
                        {/if}
                        <!--manual task -->
                    </li>
                    {/mytasks}

                </ul>
                <br/>
                <br/>
            </li>
            {/cases}
        </ul>
    </div>
</div>
