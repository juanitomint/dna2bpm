            <div id="administrador">

                <script type="text/javascript">

                //+++++++++++++++++++++++++++++++++++++++++++++++++++
                // Ventana de administracion de usuarios

                function openAgendas(){
                    var win = dhxWins.createWindow('admin', 1, 1, 600, 400); 
                    dhxWins.window("admin").attachURL('{module_url}admin/');
//                    var tb = dhxWins.window('admin').attachTabbar();
//                    tb.setImagePath("{module_url}assets/jscript/dhtmlxSuite/dhtmlxTabbar/codebase/imgs/");
//                    tb.setHrefMode("ajax-html");
//                    tb.addTab("agendas","Agendas","100px");
//                    tb.addTab("permisos","Permisos","100px");
//                    tb.setTabActive("agendas");
//                    tb.enableAutoReSize(true);
//                    tb.setContentHref("agendas","{module_url}admin/");
//                     dhxWins.window('admin').centerOnScreen();
                }

//                function openEstadisticas(){
//                var win2 = dhxWins.createWindow("win-agendas", 1, 1, 750, 500);
//                dhxWins.window("win-agendas").attachURL('logListado.php');
//                dhxWins.window("win-agendas").centerOnScreen();
//                dhxWins.window("win-agendas").setText("{statistics}");
//                }
//
//                function permisos(id,permiso){
//                var win2 = dhxWins.createWindow("win-agendas2", 1, 1, 640, 370);
//                var url = "userBox2.php?id="+id+"&"+"permiso="+permiso;
//                dhxWins.window("win-agendas2").attachURL(url);
//                dhxWins.window("win-agendas2").centerOnScreen();
//                if(permiso=="users_r"){
//                dhxWins.window("win-agendas2").setText("Permisos lectura Agenda "+id );
//                }else{
//                dhxWins.window("win-agendas2").setText("Permisos escritura Agenda "+id );
//                }
//                }


                </script>
                <div id="userBoxHolder" style="position:absolute;top:150px;left:150px;"></div>
                <div class="item-menu"><p id="menu_administrador">{admin_menu}</p></div>
                <div class="item-menu"><a href='#' id="administrador-agendas" onclick="openAgendas()">{admin_agendas}</a></div>
                <div class="item-menu"><a href='#' id="administrador-estadisticas" onclick="openEstadisticas()">{statistics}</a></div>
              </div>