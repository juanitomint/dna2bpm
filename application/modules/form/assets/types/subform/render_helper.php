<?php

function edit_subform($frame, $value) {
    $CI = & get_instance();
    return '------ subform';
    $retstr = '';
    $required = '';
    $disabled = '';
    $base_url = base_url();
    $segments = $CI->uri->segment_array();
    $assoc = $CI->uri->uri_to_assoc(4);
    $nosubhead = (isset($frame['nosubhead'])) ? true : false;
    $nobrowse = (isset($frame['nobrowse'])) ? true : false;
    $nodelete = (isset($frame['nodelete'])) ? true : false;
    $mydir = $base_url . 'dna2/render/edit/' . $frame['object'];

//---ensure array----
    $value = (array) $value;
    //----Get object
    $form = $CI->mongowrapper->db->forms->findOne(array('idobj' => $frame['object']));
    //var_dump($form);
//---get Entity data
    $entity = $CI->mongowrapper->db->entities->findOne(array('ident' => $form['ident']));


    //if ($idparent) $mydir.= "&idparent=$id";
//escribo un hiden para los agregados dinamicos
    if (isset($frame['required']))
        $required = ($frame['required']) ? getRequiredStr($frame['type']) : null;

    $retstr.="<input type='hidden' asdads id='" . $frame['cname'] . "' $required value='" . implode('*', $value) . "' name='" . $frame['cname'] . "' />\n";
//---div de los iconos
    $retstr.="<table class='searchTable'><thead><tr>";
//icono Buscar Primero
    $filesearch = "$base_url/procesos/entidades/" . $entity['name'] . '.php';
    $searchFirst = is_file($filesearch);
    if ($searchFirst) {
        $filesearch = "$base_url/procesos/entidades/" . $entity['name'] . ".php?idap=$idap&idpreg=$idpreg&nombrecontrol=$nombrecontrol";
        $action = "javascript:openTB('', '$filesearch&keepThis=true&TB_iframe=true');";
    }
//--------------Punto de inserci�n PRE-EDIT para insertar cosas Antes de mostrar------
    $file = glob("$base_url/procesos/" . $frame['idframe'] . "*/" . $entity['name'] . '.php');
    if (count($file)) {
        if (file_exists($file[0])) {
            $searchFirst = true;
            $retstr.="<a href=\"javascript:openTB('$mydir','$mydir&keepThis=true&TB_iframe=true');\" >";
            $action = str_replace($base_url, $base_url, "javascript:openTB('$file[0]','$file[0]?idap=$idap&idpreg=$idpreg&nombrecontrol=$nombrecontrol&keepThis=true&TB_iframe=true');");
        }
    }
//------------------------------------------------------------------------------------


    if ($searchFirst) {//---si se busca primero

        $retstr.="<td>";
        $retstr.="<a href=\"$action\">";
        $retstr.="<img src=\"$base_url/ImagenCustom/Search.gif\" border=\"0\" align=\"absmiddle\">";
        $retstr.="&nbsp;" . ucfirst($CI->lang->line('search'));
        $retstr.="</a></td>\n";

        //icono agregar nuevo deshabilitado
        $retstr.="<td id='disabled_$idpreg'>";
        $retstr.="<img src=\"$base_url/Icons/add-disabled.gif\" border=\"0\" align=\"absmiddle\">";
        $retstr.="&nbsp;" . ucfirst($CI->lang->line('addNew'));
        $retstr.="</td>\n";
    } else { //---no hay proceso para buscar primero
        //icono agregar nuevo
        $retstr.="<td>";
//	$retstr.="<a href=\"javascript:addnew($idpreg,'$nombrecontrol','$mydir');\">";
//	$retstr.="<a href=\"$mydir&keepThis=true&TB_iframe=true\" title=\"".$myent->Fields('nombre')."\" class=\"thickbox\">";
        $retstr.="<a href=\"$mydir/new\" class='fg-button ui-state-default fg-button-icon-left ui-corner-all subformAddNew' >";
        $retstr.="<span class=\"ui-icon ui-icon-circle-plus subformadd\"></span>";
        $retstr.=ucfirst($CI->lang->line('addNew'));
        $retstr.="</a></td>\n";

        /*
         * a class="fg-button ui-state-default fg-button-icon-left ui-corner-all" href="#"><span class="ui-icon ui-icon-circle-plus"></span>Previous</a>
         *
         */
    }
//icono pastesel
//icono agregar nuevo oculto

    $retstr.="<td style=\"visibility:hidden;height=0px\" id='enabled_" . $frame['idframe'] . "'>";
    //---link para el Thickbox
    $retstr.="<a href=\"javascript:openTB('$mydir','$mydir&keepThis=true&TB_iframe=true');\" >";
    $retstr.="<img src=\"$base_url/ImagenCustom/add.gif\" border=\"0\" align=\"absmiddle\">";
    $retstr.="&nbsp;Agregar nuevo";
    $retstr.="</a></td>\n";
//--- si tiene limite de cantidad


    $retstr.="</th></tr></thead></table>";//fin div iconos
    $retstr.="<div id='cards" . $frame['idframe'] . "'>"; //para las tarjetitas un contenedor
    if ($nosubhead)
        $retstr = "";
//-----comienza para mostrar
    if ($value) {

//--echo $subforms->RecordCount()."<br>";
        $idparent = (isset($segments[5])) ? $segments[5]:null;
        $excludetype = array(6, 7, 12, 14, 15);

        ///get descriptions 4 columns
        $query = array('idframe' => array('$in' => $form['frames']));
        //$fields = array('idframe', 'desc', 'type', 'idop');
        $fields = array();
        $selframes = $CI->mongowrapper->db->frames->find($query, $fields);
        $desc = array();
        //var_dump('frames',json_encode($query),$fields);
        while ($arr = $selframes->getNext()) {
            //var_dump($arr);echo'<hr/>' ;
            $frames [$arr ['idframe']] = $arr;
            $desc [$arr ['idframe']] = (isset($arr['title']))?$arr['title']:'';
            $type[$arr['idframe']] = $arr['type'];
        }
        $query = array('id' => array('$in' => $value));
        $rows = (isset($frame['cols'])) ? $frame['cols'] : 4;
        $fields = array_slice(array_map('toString', $form['frames']), 0, $rows);
        $fields[] = 'id';
        $sort = array($form['frames'][0] => 1);
        $subforms = $CI->mongowrapper->db->selectCollection($form['container'])->find($query, $fields);
        //var_dump('container',$form['container'],'$query',$query,'$fields',$fields,'$sort',$sort);
        $subforms = $subforms->sort($sort);
        //var_dump($form[container],$query,$fields,$sort,$subforms);
        //-------------------------------------------------
        //-------- INCLUDE---------------------------------

        include ('subFormsTable.php');
        //-------------------------------------------------
    }//----end if($valor)
    if (!$nosubhead)
        $retstr.="</div>"; //termina el div de tarjetitas solo si muestra todos
 return $retstr;
}

function view_subform($frame, $value) {
    $frame['nosubhead']=true;
    $frame['nodelete']=true;
    $frame['nobrowse']=true;
    return edit_subform($frame, $value);
}
?>
