<select name="f[default]" id="f[default]">
    <option value="">Seleccione una opción</option>
    <?php
    session_start();
    require_once('../../Connections/dnadb.php');
    if($_REQUEST[idop]) {
        $selected=$_REQUEST[selected];
        $options=$dnadb->options;
        $thisop = $options->findOne(array(idop=>(int)$_REQUEST[idop]));
        $arr=$thisop[data];
        //var_dump($thisop,$arr);
        foreach($arr as $oparr) {
            list($value,$txt,$idrel)=$oparr;
            $sel=($value==$selected) ? "selected" : "";
            echo "<option value='$value' $sel >$txt</option>\n";
        }
    }
    ?>
</select>