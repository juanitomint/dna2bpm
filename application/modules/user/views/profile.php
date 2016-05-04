
 <form id="form_profile" class="form-horizontal" action="{module_url}profile/save" enctype="multipart/form-data" method="POST" >

<!--  ==== NICK ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label"> {lang Nick} </label>
    <div class="col-sm-10">
	    <input type="text" id="nick" name="nick" class="form-control" placeholder="Nick" value="{nick}" disabled="disabled">
    </div>
  </div>

<!--  ==== FILE ==== -->
<div class="form-group">
    <label class="col-sm-2 control-label">{lang picture}</label>
    <div class="col-sm-2">
    <img src="{avatar}" id="avatar" class="avatar" >
    </div>
    <div class="col-sm-8">
     {if "{disabled}"==""}
		  <div class="form-group">
					<div id="filelist">{lang uploader_error}</div>
					<br />
					<div id="container">
					    <a id="pickfiles" class="btn btn-primary btn-xs" name="pickfiles" href="javascript:;"><i class="fa fa-files-o"></i> {lang SelectFile}</a>
					    <a id="uploadfiles" class="btn btn-primary btn-xs" name="uploadfiles" href="javascript:;"><i class="fa fa-cloud-upload"></i> {lang UploadFile}</a>
					</div>

			</div>
	{/if}
</div>
</div>

  <!--  ==== PASSW ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang Password}</label>
    <div class="col-sm-10">
	    <input type="password"  name="passw" id="passw" value="" class="form-control" {disabled}>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang Confirm} {lang Password}</label>
    <div class="col-sm-10">
	    <input type="password"  name="passw2" id="passw2" value="" class="form-control" {disabled}>
    </div>
  </div>


    <!--  ==== NAME==== -->

	  <div class="form-group">
	    <label class="col-sm-2 control-label">{lang Name}</label>
	    <div class="col-sm-10">
		     <input type="text" required name="name" id="name" value="{name}" class="form-control" {disabled}>
	    </div>
	  </div>

   <!--  ==== LASTNAME ==== -->

		  <div class="form-group">
		    <label class="col-sm-2 control-label">{lang Lastname}</label>
		    <div class="col-sm-10">
			     <input type="text"  name="lastname" id="lastname" value="{lastname}" class="form-control" {disabled}>
		    </div>
		  </div>



    <!--  ==== GENDER ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang Gender}</label>
    <div class="col-sm-10 form-inline">
			<div class="radio">
			  <label>
			 	 <input type="radio" name="gender" id="female" value="female" {checkedF} {disabled}>
			    Femenino
			  </label>
			</div>
			<div class="radio">
			  <label>
			    <input type="radio" name="gender" id="male" value="male" {checkedM} {disabled}>
			    Masculino
			  </label>
			</div>
    </div>
  </div>

<!--  ==== DNI ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang idnumber}</label>
    <div class="col-sm-10">
	      <input type="number" name="idnumber" id="idnumber" value="{idnumber}" class="form-control" disabled="disabled">
    </div>
  </div>

 <!--  ==== BIRTHDAY ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang Birthday}</label>
    <div class="col-sm-10">
		<div class="input-group">
		  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
		  <input class="calendar form-control" type="text"  name="birthdate" id="birthdate" value="{birthdate}" {disabled}>
		</div>
	</div>
  </div>

<!--  ==== EMAIL ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang Email}</label>
    <div class="col-sm-10">
		<input type="text" id="email" name="email" value="{email}" class="form-control" {disabled}>
    </div>
  </div>
  <!--  ==== NOTIFICACIONS BY MAIL ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang notification_by_mail}</label>
    <div class="col-sm-10 form-inline">
			<div class="radio">
			  <label>
			 	 <input type="radio" name="notification_by_email" id="noti_yes" value="yes" {check_notiY} {disabled}>
			    {lang yes}
			  </label>
			</div>
			<div class="radio">
			  <label>
			    <input type="radio" name="notification_by_email" id="noti_no" value="no" {check_notiN} {disabled}>
			    {lang no}
			  </label>
			</div>
    </div>
  </div>


<!--  ==== TELEFONO ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang Phone}</label>
    <div class="col-sm-10">
       <input type="text" name="phone" value="{phone}" class="form-control" {disabled}>
    </div>
  </div>

<!--  ==== CELLPHONE ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang CellPhone}</label>
    <div class="col-sm-10">
       <input type="text" name="celular" value="{celular}" class="form-control" {disabled}>
    </div>
  </div>

<!--  ==== ADDRESS ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang Address}</label>
    <div class="col-sm-10">
      <input type="text" name="address" value="{address}" class="form-control" {disabled}>
    </div>
  </div>


 <!--  ==== CPA ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang CPA}</label>
    <div class="col-sm-10">
      <input type="text" name="cp" value="{cp}" class="form-control" {disabled}>
    </div>
  </div>

   <!--  ==== CITY ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang City}</label>
    <div class="col-sm-10">
      <input type="text" name="city" value="{city}" class="form-control" {disabled}>
    </div>
  </div>

     <!--  ==== SIGNATURE ==== -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang Signature}</label>
    <div class="col-sm-10">
    <textarea name="signature" class="form-control" {disabled}>{signature}</textarea>
    </div>
  </div>

     <!--  ==== SUBMIT ==== -->
     {if "{disabled}"==""}
  <div class="form-group">
      <label class="col-sm-2 control-label"></label>
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary form-control">{lang save}</button>
    </div>

  </div>
  {/if}


</form>

