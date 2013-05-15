<?php
//$filters = array(
//    '4970' => '$idu',
//    '4939' => array(
//        '$ne' => 40
//    )
//);
//var_dump($filters);
$cond_arr = array(
    'equal' => ' ',
    'not equal' => '{ "$ne" : "$value"}',
    '>' => '{ "$gt" : "$value"}',
    '>=' => '{ "$gte": "$value"}',
    '<' => '{ "$lt" : "$value"}',
    '<=' => '{ "$lte": "$value"}',
    'exists' => '{"$exists":true}',
    'not exists' => '{"$exists":false}',
    'all' => '{ "$all": [ $value ] }',
    'in' => '{ "$in":  $value  }',
    'not in' => '{ "$nin":  $value }',
);
$verbs = array(
    '$ne' => 'not equal',
    '$gt' => '>',
    '$gte' => '>=',
    '$lt' => '<',
    '$lte' => '<=',
    '"$exists":true' => 'exists',
    '"$exists":false' => 'not exists',
    '$all' => 'all',
    '$in' => 'in',
    '$nin' => 'not in',
);
?>
<form action="{base_url}/dna2/be/decode_filters" method="post">

    <table class="ui-widget" id="jTable">
        <thead class="ui-widget-header">
            <tr>
                <th>{Frame}</th>
                <th>{Cond}</th>
                <th>{Value}</th>
                <th></th>
            </tr>
        </thead>
        <tbody class="ui-widget-content">
<?php
foreach ($filters as $key => $value) {
?>
            <tr>
                <td>
                    <input type="text" name="frame[]" size="6" value="<?= $key; ?>"/>
                </td>
                <td>
<?php
            $selected = 'equal';
            //---search verb
            $search = json_encode($value);
            foreach ($verbs as $noun => $sel) {
                if (strstr($search, $noun))
                    $selected = $sel;
            }
            //var_dump('$search',$search,'$selected',$selected,$cond_arr[$selected]);
            //---prepare value
            if (is_array($value)) {
                list($verb, $noun) = each($value);
                if($verb=='$in' or $verb=='$nin'){
                $value = json_encode($noun);
                } else {
                    $value=$noun;
                }
                //---clear value if is exists
                if(in_array($selected, array('exists','not exists'))) $value='';
                //var_dump($noun,$value);
            }
?>
                    <select name="cond[]">
<?php
            
            //var_dump($search, $selected);
            foreach ($cond_arr as $key => $text) {
                $sel = ($key == $selected) ? 'selected' : '';
                echo "<option value='$text' $sel>$key</option>\n";
            }
?>

                    </select>
                </td>
                <td>
                   <input type="text" name="value[]" size="20" value="<?=$value;?>" />
                </td>
                <td>
                    <a class="fg-button ui-state-default fg-button-icon-left ui-corner-all json_remove" href="#">
                        <span class="ui-icon ui-icon-trash"></span>
                        remove
                    </a>
                </td>
            </tr>
<?php } ?>
            <tr style="display:none">
                <td>
                    <input type="text" name="frame[]" size="6" value=""/>
                </td>
                <td>

                    <select name="cond[]">
<?php
                    //---prepare value
                    if (is_array($value)) {
                        list($verb, $noun) = each($value);
                        $value = json_encode($noun);
                    }
                    //var_dump($search, $selected);
                    foreach ($cond_arr as $key => $text) {

                        echo "<option value='$text'>$key</option>\n";
                    }
?>

                    </select>
                </td>
                <td>
                    <input type="text" name="value[]" size="20" value=""/>
                </td>
                <td>
                    <a class="fg-button ui-state-default fg-button-icon-left ui-corner-all json_remove" href="#">
                        <span class="ui-icon ui-icon-trash"></span>
                        remove
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</form>

