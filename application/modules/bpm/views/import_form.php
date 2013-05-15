<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <style type="text/css">
            .msgBox{
                border: 1px solid #7F9DB9;
                background-color: #94e1fb;

            }
        </style>
        <!-- JQuery -->
        <script type="text/javascript" src="{base_url}jscript/ui/js/jquery-1.4.2.min.js"></script>
        <link rel="stylesheet" type="text/css" href="{base_url}jscript/dhtmlx/dhtmlxvault/codebase/dhtmlxvault.css" />

        <script language="JavaScript" type="text/javascript" src="{base_url}jscript/dhtmlx/dhtmlxvault/codebase/dhtmlxvault.js"></script>


        <script type="text/javascript">
            var base_url='{base_url}';
            $(document).ready(function(){
                //---create vault control
                vault=new dhtmlXVaultObject(); 
                vault.setImagePath(base_url+"jscript/dhtmlx/dhtmlxvault/codebase/imgs/");
                vault.setServerHandlers(base_url+"vault/UploadHandler", base_url+"vault/GetInfoHandler",base_url+ "vault/GetIdHandler");
                vault.create("vaultDiv");
                //---import buttons
                $('#importZip').click(function(){
                    idwf=$('#zip_file').val();
                    $('#newModelMsg').html("<img src='"+base_url+"css/ajax/loadingAnimation.gif'/>");
                    url=base_url+'bpm/repository/import/model/'+idwf;
                    $.post(url,'',function(data){
                        $('#newModelMsg').html(data.msg);
                        window.parent.load_tree(idwf);
                        window.parent.load_dataview();
                        window.parent.sb.setText(data.msg);
                        window.parent.win.close();
                    });
                });
               //---import from URL
               $('#importURL').click(function(){
                    var idwf=$('#url').val();
                    $('#newModelMsg').html("<img src='"+base_url+"css/ajax/loadingAnimation.gif'/>");
                    url=base_url+'bpm/repository/import/model/';
                    $.post(url,{file_import:idwf},function(data){
                        $('#newModelMsg').html('');
                        window.parent.load_tree(idwf);
                        window.parent.load_dataview();
                        window.parent.sb.setText(data.msg);
                        window.parent.win.close();
                    });
                });
            });
        </script>
    </head>

    <body>
        <div id="newModelMsg" class="msgBox" ></div>
        <table>
            <tbody>
                <tr>
                    <td>Zip File</td>
                    <td>
                        <select name="zip_file" id="zip_file" title="please select a file">
                            <option value=''>{SelectOne}</option>
                            <?php
                            $files = get_filenames('images/zip');
                            foreach ($files as $file)
                                echo "<option value='$file'>$file</option>\n";
                            ?>
                        </select>
                        <td>
                        <button id="importZip">{Import}</button>
                        </td>

                    </td>
                </tr>
                <tr>
                    <td>URL:</td>
<!--                    TODO add validation-->
                    <td><input type="text" size="20" id="url"/>
                        <td>
                        <button id="importURL">{Import}</button></td>
                        </td>
                </tr>
                <tr>
                    <td colspan="3">File:
                        <form ENCTYPE="multipart/form-data" ACTION="{base_url}vault/UploadHandler" METHOD=POST>
                            <div id="vaultDiv"></div>
                        </form>
                    </td>

                </tr>
            </tbody>
        </table>
    </body>
</html>