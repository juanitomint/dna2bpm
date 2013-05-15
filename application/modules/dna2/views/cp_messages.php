        <?php
        /* TODO */
// xxxxxxxxxxxxxx   Manejo de mensajes Mensajes

        $SQL = "SELECT * FROM pm WHERE idm NOT IN (SELECT idm FROM pm2read WHERE iduser=$idu) AND context LIKE '%$imin%' ORDER BY checkdate";
        $rsm = $forms2->Execute($SQL) or die($forms2->ErrorMsg() . "<br>$SQL<br>" . __FILE__ . ' line:' . __LINE__);
        echo "<div id=\"mensajes\">";
        if ($rsm->RecordCount()) {
            $i = 0;
            while (!$rsm->EOF) {
                $i++;
                $name = "dialog" . $i;
                echo "<div class=\"mensaje\" onclick=\"javascript:abrirMsg('$name');\" id=\"mensaje" . $rsm->Fields('idm') . "\"><p class=\"mensaje-titulo\">" . utf8_encode($rsm->Fields('subject')) . "</p></div><div class='cb'></div>";
                echo "<div id='$name' class='dialogo' idm='" . $rsm->Fields('idm') . "'>" . utf8_encode($rsm->Fields('body')) . "</div>";
                $rsm->MoveNext();
            }        }
        echo "</div>";

// Fin Mensajes
        ?>