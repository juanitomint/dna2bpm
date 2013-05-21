<ol data-role="listview">
        {wishes}
        <li>
                <h3 class='ui-li-heading'>{1693}</h3>
                <p>
                        CUIT: {1695}<br/>
                        Productos:{1715}<br/>
                        
                        {if {accredited}}
                        <span data-role="button" data-icon="check" data-inline="true" data-mini="true">Acreditada</span>
                        {else}
                        <span data-role="button" data-icon="delete" data-inline="true" data-mini="true">No Acreditada</span>
                        {/if}
                </p>

        </li>
        {/wishes}
</ul>