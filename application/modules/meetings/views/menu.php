<div class="content-primary">
        <div id="result"/>

</div>
</div>
<div class="content-secondary">

        <h2>Empresas</h2>
        <ul data-role="listview" >
                <li  data-icon="gear">
                        Buscar CUIT
                        {ignore}
                        <form id="view-cuit" action="{module_url}business/get_data_cuit/" method="post" data-ajax="false">
                                <input 
                                        type="search" 
                                        data-mini="true"
                                        name="cuit" 
                                        id="CUIT" 
                                        value="" 
                                        size="21" 
                                        placeholder="99-999999-9" 
                                        pattern="[0-9]{2}-[0-9]{8}-[0-9]{1}" 
                                        required 
                                        />
                        </form>
                        {/ignore}
                </li>
                <li  data-icon="gear">
                        Buscar Nombre
                        {ignore}
                        <form id="view-cuit" action="{module_url}print_business" method="post" data-ajax="false">
                                <input 
                                        type="search" 
                                        data-mini="true"
                                        name="seach-name" 
                                        id="search-name" 
                                        value="" 
                                        size="21" 
                                        placeholder="Escriba nombre aqui" 
                                        required
                                        />
                        </form>
                        {/ignore}
                </li>
                <li  data-icon="gear">
                        <form id="view-table" action="print_tables" data-ajax="false">
                                Ver Mesa
                                <input 
                                        placeholder="M1....M{tables}" 
                                        type="search" 
                                        name="table" 
                                        id="search-table" 
                                        value="" 
                                        data-mini="true" 
                                        required
                                        />
                        </form>
                </li>
                <li>
                        Libres:<br>
                        <form>

                                {intervals}
                                <a 
                                        data-mini="true" 
                                        data-role="button" 
                                        data-inline="true"
                                        href="{module_url}free_at/{interval}"
                                        >
                                        {interval}
                                </a>
                                {/intervals}
                        </form>
                </li>
                <li><a href="{module_url}business/registered">Registradas<span class="ui-li-count">{business_total}</span></a></li>
                <li><a href="{module_url}business/accredited">Acreditadas<span class="ui-li-count">{accredited_business}</span></a></li>
                <li><a href="print_tables">Mesas<span class="ui-li-count">{Used_Tables}/{available_tables}</span></a></li>
                <li>
                        <a href="{module_url}print_meetings">
                                Reuniones
                                <span class="ui-li-count">
                                        {Wishes_Granted}/{Total_wishes}
                                </span>
                        </a>
                </li>
        </ul>


        <h2>Herramientas</h2>
        <ul data-role="listview">
                <li  data-icon="gear"><a href="{module_url}run">Procesar</a></li>
                <li  data-icon="gear"><a href="{module_url}merge">Importar Datos</a></li>
                <li  data-icon="gear"><a href="{module_url}pdf_business" isMenu="true" data-ajax="false" target="_blank">Imprimir Empresas</a></li>
                <li  data-icon="gear"><a href="{module_url}pdf_tables" isMenu="true" data-ajax="false" target="_blank">Imprimir Mesas</a></li>
                <!--<li  data-icon="gear"><a href="{module_url}mark_empresas">Generar Test</a></li>-->



                <li  data-icon="gear">
                        <a href="{module_url}stats" id="stats">
                                Stats
                        </a>
                </li>
        </ul>
        <br/>
        <br/>
        <br/>
        <br/>&nbsp;
</div>

