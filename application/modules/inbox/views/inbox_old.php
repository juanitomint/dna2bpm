<?php
$this->load->helper('html');
$this->load->helper('url');
?>

<!-- Grid  -->
<link rel="STYLESHEET" type="text/css" href="{base_url}jscript/dhtml/grid/dhtmlxgrid.css" />
<script  type="text/javascript" src="{base_url}jscript/dhtml/grid/dhtmlxcommon.js"></script>
<script  type="text/javascript" src="{base_url}jscript/dhtml/grid/dhtmlxgrid.js"></script>
<script  type="text/javascript" src="{base_url}jscript/dhtml/grid/dhtmlxgridcell.js"></script>
<script  type="text/javascript" src="{base_url}jscript/dhtml/grid/ext/dhtmlxgrid_start.js"></script>


<script type="text/javascript">
    //--- START $(document).ready
    $(document).ready(function(){

        mygrid = new dhtmlXGridFromTable('inbox-block');
        mygrid.setSkin('xp');


        // Prende la Estrellita
        $("[name='star']").click(function(){
            $(this).toggleClass("inbox-item-star_on");
        });

        $("[name='readmark']").click(function(){
            $(this).toggleClass("inbox-item-readmark_on");
        });

        $("#bt-archivar").click(function(){alert("Archivar");});
        $("#bt-leido").click(function(){alert("Marcar como leido");});
        $("input:checkbox").change(function(){
        (this.checked)?($(this).parent().addClass("inbox-item-selected")):($(this).parent().removeClass("inbox-item-selected"));
        });

        });



    //--- END $(document).ready
</script>
<!-- Bloques Tabs  -->
<div id="inbox-tab1">
    <div id="toolbar">
        <button class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" id="bt-archivar">Archivar</button>
        <button class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" id="bt-leido">Leido</button>
        <select><option>Mover a</option><option>Copiar a</option></select>
        <select><option>Etiquetas</option><option>Importante</option></select>
        <select><option>Mas acciones</option><option>Accion 1</option></select>
    </div>
    <br/>
    <!---provisional toma Started cases by me -->
    <table border="0" class="inbox-block" id="inbox-block" unevenrow="odd_row" evenrow="even_row"
 imgpath="{base_url}jscript/dhtml/grid/imgs/" width="100%">
            <thead>
                <tr>
                    <th type="ch" width="30" align="center">&nbsp;</th>
                    <th type="ro" width="30" align="center">&nbsp;</th>
                    <th type="ro" width="*" align="left">{title}</th>
                    <th type="ro" width="40" align="center">{restart}</th>
                    <th type="ro" width="40" align="center">{readmark}</th>
                    <th type="ro" width="100" align="center">{date}</th>
                    <th type="ro" width="100" align="center">{status}</th>
                </tr>
            </thead>
            <tbody>
                {cases}
                <tr>
                    <td><input type="checkbox" /></td>
                    <td><a href="#" name="star" class="inbox-item-star" style="height:27px"></a></td>
                    <td>
                        <a class="inbox-item-title" href="{base_url}bpm/engine/run/model/{idwf}/{case}">{name}:{case}</a>
                        <a class="inbox-item-title" href="{base_url}bpm/engine/tokens/model/{idwf}/{case}">TOKENS</a>
                    </td>
                    <td ><a class="inbox-item-restart" href="{base_url}bpm/engine/startcase/model/{idwf}/{case}"></a></td>
                    <td><a href="#" name="readmark" class="inbox-item-readmark inbox-item-readmark_on"></a></td>
                    <td><span class="inbox-item-date">{checkdate}</span></td>
                    <td><span class="inbox-item-title">{status}</span></td>
                </tr>
                {/cases}
            </tbody>
        </table>
    

    
</div>

