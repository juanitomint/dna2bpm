
<table class="table table-bordered">
            <tbody>
            <tr>
                <th>Description</th>
                <th>Fingerprint</th>
                <th>Action</th>
            </tr>
            {keys}
            <tr>
                <td>{description}</td>
                <td>{fingerprint}</td>
                <td><button class="btn btn-xs btn-default" data-cmd="delete" data-fingerprint="{fingerprint}"><i class="fa fa-trash-o"></i> Delete</button></td>
            </tr>
           {/keys}
        </tbody></table>                      

