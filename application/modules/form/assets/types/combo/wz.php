<label for="f[widop]">Tomar datos de Opciones:</label><br/>
<select name="f[idop]" id="idop">
    <option value="">Seleccione una opción</option>
    <?php
    $options=$dnadb->options;
    $cursor = $options->find();
    $cursor = $cursor->sort(array("title" => 1));
    while($arr=$cursor->getNext()) {
        $sel=($arr[idop]==$f[idop]) ? "selected" : "";
        echo "<option value='$arr[idop]' $sel >$arr[title]</option>\n";
    }
    ?>
</select><br>
<label for="f[default]">Valor por defecto:<br/>
    <div id="default"></div>
</label>
<label for="f[width]">Alto</label><input name="f[height]" id="f[height]" size="4" maxlength="3" value="<?=($f['height']=='') ? 1:$f['height'];?>" />