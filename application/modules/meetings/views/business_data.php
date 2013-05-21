<hr/>
<h2>
        id: {1693}
</h2>
<h2>
        Nombre: {1693}
</h2>
<h3>
        C.U.I.T:
        <b>
                {1695}
        </b>
</h3>
{if {accredited}}
<h3>
        Acreditada
</h3>
{else}
<a  
        href="{module_url}accreditation/save/{id}"
        data-role="button"
        data-mini="true"
        data-icon="check" 
        data-inline="true" 
        value="Acreditar" 
        name="ok" >Acreditar
</a>
{/if}
<a 
        id="view-agenda"
        href="{module_url}print_business/{1695}" 
        data-inline="true" 
        data-icon="gear" 
        data-mini="true"
        data-role="button"
        >
        Ver Citas

</a>
<a 
        id="view-wishes"
        href="{module_url}print_wishes/{1695}" 
        data-inline="true" 
        data-icon="gear" 
        data-mini="true"
        data-role="button"
        >
        Ver Deseos
        <span class="ui-li-count">(<?php
$cant = (isset($_ci_vars['7466'])) ? count($_ci_vars['7466']) : 0;
echo $cant;
?>)</span>
</a>
<a 
        id="view-wishes"
        href="http://www.accionpyme.mecon.gob.ar/dna2/RenderEdit/editnew.php?idvista=2513&idap=262&origen=V&id={id}" 
        data-inline="true" 
        data-icon="gear" 
        data-mini="true"
        data-role="button"
        data-ajax="false"
        target="_blank"
        isMenu="true"
        >
        Cargar Deseos
        
</a>
<hr/>