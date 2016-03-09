<div class="box-body" style="">
    <form id="form_add_key"> 
       <!-- Description -->
        <div class="form-group">
            <label>Description</label>
            <input class="form-control" name="description"  placeholder="Description">
        </div>
                     

        <!-- Mensaje -->
        <div class="form-group">
            <label>Public Key</label>
            <textarea class="form-control" name="public_key" rows="6" placeholder="Public Key"></textarea>
        </div>
        
                                
        <div class="callout callout-info" id="fingerprint" style="display:none">
                <h4>Fingerprint generated</h4>
                <p></p>
        </div>
        <!-- Send -->                    
        <div class="input-group-btn">
            <button  type="submit" class="btn btn-default btn-flat btn-block"><i class="fa fa-floppy-o"></i> Save Key</button>
        </div>                        
    </form>   
</div>