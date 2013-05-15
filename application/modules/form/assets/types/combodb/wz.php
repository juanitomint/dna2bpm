<label for="f[dataFrom]">Tomar datos de Opciones:</label><br/>
<select name="f[dataFrom]" id="dataFrom">
    <option value="">Seleccione una opción</option>
    <?php
    $list = $dnadb->listCollections();
    //$list = $dnadb->command(array('show collections'=>1));
    foreach ($list as $collection) {
        //---remove dbname from collection
        $arr=explode('.',$collection);
        $arr=array_slice($arr,1);
        $collection=implode('.',$arr);
        //----------------------------------
        $sel=($f['dataFrom']==$collection) ? 'selected="selected"' : '';
        echo "<option value='$collection' $sel >$collection</option>\n";
    }
    ?>
</select><br>
<label for="f_json[query]">Filtro:<br/>
    <input type="text" class="" JSONstring="true" name="f_json[query]" id="f_json[query]" size="60" value="<?=htmlentities(json_encode($f['query']));?>" />
</label><br/>
<label for="f_json[fields]">Campos para mostrar<br/>
    <input type="text" class="required" JSONstring="true" name="f_json[fields]" id="f_json[fields]" value="<?=htmlentities(json_encode($f['fields']));?>" />
</label><br/>
<label for="f[fieldValue]">Campo Valor:<br/>
    <input type="text" class="required" name="f[fieldValue]" id="f[fieldValue]" value="<?=$f['fieldValue'];?>" />
</label><br/>
<label for="f_json[sort]">Orden:<br/>
    <input type="text" class="required" JSONstring="true" name="f_json[sort]" id="f_json[sort]" value="<?=htmlentities(json_encode($f['sort']));?>" />
</label><br/>
<label for="f[width]">Alto</label><input name="f[height]" id="f[height]" size="4" maxlength="3" value="<?=($f['height']=='') ? 1 :$f['height'];?>" />