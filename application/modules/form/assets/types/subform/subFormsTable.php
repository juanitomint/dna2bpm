<?php

$retstr.="<table id='table_" . $frame['cname'] . "' class='tablesorter'><thead><tr>";
if (!$nobrowse)
    $retstr.="<th class=\"{sorter: false}\" >&nbsp;</th>";
$rows = (isset($frame['cols'])) ? $frame['cols'] : 4;
$desc = array_slice($desc, 0, $rows, true);
$retstr.="<th>" . implode('</th><th>', $desc) . "</th>";
//---4 delete icon
if (!$nodelete)
    $retstr.="<th>&nbsp;</th>";
//---Table Header -------------------------------------------------------
$retstr.="</tr></thead><tbody>";
//------empieza el bucle de los id's
$i = 0;
$j = 0;
while ($arr = $subforms->getNext()) {
    //var_dump('arr',$arr);
    $i++;
    $retstr.="<tr id='child_" . $frame['cname'] . $arr['id'] . "' class='row-$i'>";
//----------------iconos-----------------
    // tarjetita ver subform
    if (!$nobrowse) {
        $retstr.="<td>";
        $url = base_url() . 'dna2/render/go/' . $form['idobj'] . '/' . $arr['id'] . "/child/" . $frame['idframe'] . "/childcont/" . $frame['cname'] . "/idparent/$idparent/nonav/true";
        $retstr.="<a title=\"" . ucfirst($CI->lang->line('viewData')) . ":$url\" href=\"$url\"  alt=\"" . ucfirst($CI->lang->line('viewData')) . "\" class=\"subformPreview\">";
        $retstr.=" <span class=\"ui-corner-all ui-state-default ui-icon ui-icon-folder-collapsed subformbrowse\"></span>";
//$retstr.="<img src=\"$basedir/Icons/24x24/Document 2 Search.gif\" border=\"0\" align=\"absmiddle\" alt=\"Ver Datos\">";
        $retstr.="</a>\n";
        $retstr.="</td>";
    }
    //---------------------------------------
    //----GET all needed frames 4 this ID
    $val = $CI->app->getall($arr['id'], $form['container'], $form['frames']);
    //----------------------------------------
    //---------------------------------------
    foreach ($desc as $idframe => $title) {
        $callfunc = 'view_' . $type[$idframe];
        //echo "** TRY: $callfunc<hr>";
        $alt = '';
        if (function_exists($callfunc)) {
            $alt = $callfunc(
                            $frames[$idframe],
                            (isset($val[$idframe])) ? $val[$idframe] : null
                            );
        }
        $retstr.="<td class='col-$j'>" . $alt . "</td>";
        $j++;
    }
    if (!$nodelete) {
        // tarjetita delete
        $retstr.='<td>';
        $retstr.="<a href=\"javascript:delsel(" . $arr['id'] . ",'" . $frame['cname'] . "')\" title=\"" . ucfirst($CI->lang->line('delete')) . "\" class=\"subformDelete\">";
        $retstr.=" <span class=\"ui-corner-all ui-state-default ui-icon ui-icon-closethick subformdelete\"></span>";
        //$retstr.="<img src=\"$base_url/Icons/24x24/Document 2 Delete.gif\" border=\"0\" align=\"absmiddle\" alt=\"Eliminar Relación\">";
        $retstr.="</a>";
        $retstr.='</td>';
    }
    $retstr.="</tr>";
}
//----fin bucle id's
$retstr.="</tbody></table>";
?>