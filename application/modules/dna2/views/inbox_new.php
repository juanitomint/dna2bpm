<!-- BTN GROUP -->
<div id="content-header">
    <h1>{inbox_title}</h1>
</div>
<div id="breadcrumb">
    <a href="#" title="Go to Home" class="tip-bottom">
        <i class="icon-home">
        </i> Home</a>
    <a href="#" class="current">New Message</a>
</div>
<!-- INBOX WIDGET -->
<div class="container-fluid">
    <!-- 2row block -->
    <div class="row-fluid">
        <!-- Start 2nd col -->

    <form class="form-horizontal" id="inbox_new">
    <!-- -->
    <div class="control-group">
    <label class="control-label" for="to">To</label>
    <div class="controls">
    <select  multiple="multiple">   
        <option>Option 1</option>
        <option>Option 2</option>
        <option>Option 3</option>
        <option>Option 4</option>
        <option>Option 5</option>
    </select>
    </div>
    </div>
        <!-- -->
    <div class="control-group">
    <label class="control-label" for="to">To</label>
    <div class="controls">
    <input type="text" name="to" placeholder="To">
    </div>
    </div>
    <!-- -->
    <div class="control-group">
    <label class="control-label" for="subject">Subject</label>
    <div class="controls">
    <input type="text" name="subject" placeholder="Subject">
    </div>
    </div>
    <!-- -->
    <div class="control-group">
    <label class="control-label" for="body">Body</label>
    <div class="controls">
   <textarea rows="5" name="body"></textarea>
    </div>
    </div>
    <!-- -->
    <div class="control-group">
    <div class="controls">
    <button type="submit" class="btn">Send</button>
    </div>
    </div>
    </form>

        <!-- End 2nd col -->
    </div>
    
</div> 
