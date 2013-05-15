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
        <script type="text/javascript">
            $(document).ready(function(){
                $('#newModel').click(function(){
                    idwf=$('#idwf').val();
                    url='{base_url}bpm/repository/check_model/'+idwf;
                    $.post(url,'', function(data){
                        if(data.ok){
                            url='{base_url}bpm/repository/add';
                            post_data={
                                idwf:$('#idwf').val(),
                                name:$('#name').val(),
                                folder:$('#folder').val()
                            };
                            $.post(url,post_data,function(){
                                window.parent.sb.setText('Added Model:'+idwf+' OK!');
                                window.parent.load_tree(idwf);
                                window.parent.load_dataview();
                                window.parent.win.close();
                            });
                        } else {
                            $("#newModelMsg").html('Error:'+idwf+' already exists');
                        }
                    })
                });
            });
        </script>
    </head>

    <body>
        <div id="newModelMsg" class="msgBox" ></div>
        <table>
            <tbody>
                <tr>
                    <td>ID</td>
                    <td><input type="text" size="20" id="idwf"/></td>
                </tr>
                <tr>
                    <td>{name}</td>
                    <td><input type="text" size="20" id="name"/></td>
                </tr>
                <tr>
                    <td>{folder}</td>
                    <td><select name="folder" id="folder">
                            {folders}
                            <option value="{folder}">{folder}</option>
                            {/folders}
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <button id="newModel">{save}</button>
    </body>    
</html>
