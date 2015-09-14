{if {input_output} == 'Input' }
<h3>
    <i class="fa fa-qrcode"></i>
    {name}
    <br/>
</h3>
<div class="img-polaroid" id="reader"></div>
<h6>Result</h6>
<span id="read"></span>
<span id="result"></span>
<br>

<input type="hidden" id="readHidden" name="readHidden" />
<input type="hidden" id="qr_resourceId" name="qr_resourceId" value="{resourceId}" />

<h6>Read Error (Debug only)</h6>
<span id="read_error"></span>
<br>
<h6>Video Error</h6>
<span id="vid_error"></span>
{else}
<div class="row text-center">
    <img id="qr_{resourceId}" src="{base_url}qr/gen_url/{qr_data}/9/L" class="img-thumbnail qr"/>
    <br/>
    {qr_text}
</div>
    
{/if}