<!-- BTN GROUP -->
<div id="content-header">
    <h1>{inbox_title}</h1>
</div>
<div id="breadcrumb">
    <a href="#" title="Go to Home" class="tip-bottom">
        <i class="icon-home">
        </i> Home </a>
    <a href="#" class="current">New Message</a>
</div>
<!-- INBOX WIDGET -->
       {if {reply}}
          <input type="hidden" name="reply" value="1" />
         <input type="hidden" name="reply_name" value="{reply_name}" />
         <input type="hidden" name="reply_title" value="{reply_title}" />
         <input type="hidden" name="reply_body" value="{reply_body}" />
         <input type="hidden" name="reply_idu" value="{reply_idu}" />
          <input type="hidden" name="reply_date" value="{reply_date}" />
        {/if}
<div class="container-fluid">
    <!-- 2row block -->
    <div class="row-fluid">
        <!-- Start 2nd col -->

        
    <form class="form-horizontal" id="inbox_new">
        
            

        
    <!-- -->
<!--      <div class="control-group">
        <label for="multiple" class="control-label">Para</label>
        <div class="controls">
          <select name="to" class="select2" multiple="multiple" style="width:400px;">

          </select>
        </div>
      </div>-->


    <div class="control-group">
    <label class="control-label" for="subject">Destinatario</label>
    <div class="controls" >
    <input type="hidden" name="to" class="select2 "  style="width:80%;" multiple="multiple" />
    </div>
    </div>

    <!-- -->
    <div class="control-group">
    <label class="control-label" for="subject">Título</label>
    <div class="controls">
    <input type="text" name="subject" placeholder="Subject" >
    </div>
    </div>
    <!-- -->
    <div class="control-group">
    <label class="control-label" for="body">Cuerpo</label>
    <div class="controls">
   <textarea rows="5" name="body" placeholder="Body"></textarea>
    </div>
    </div>
    <!-- -->
    <div class="control-group">
    <div class="controls">
    <button type="submit" class="btn">Enviar</button>
    </div>
    </div>
    </form>

        <!-- End 2nd col -->
    </div>
    
</div> 
