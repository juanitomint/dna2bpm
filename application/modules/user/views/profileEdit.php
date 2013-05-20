<!-- / Breadcrumbs -->
<ul class="breadcrumb navbar-static-top">
    <li><a href="{base_url}dna2/dashboard">Dashboard</a> <span class="divider">/</span><a href="{module_url}profile/index">Profile</a> <span class="divider">/</span> Edit <span class="divider">/</span></li>
</ul>


<div class="container">

    <form class="form-horizontal" action="{module_url}profile/save" enctype="multipart/form-data" method="POST" >
        <legend>Editar usuario</legend>

        <div class="control-group">
            <label class="control-label" for="nick">Nick</label>
            <div class="controls">
                <input type="text" id="nick" name="nick" value="{nick}" >
            </div>
        </div>


        <div class="fileupload fileupload-new control-group" data-provides="fileupload">
            <label class="control-label" for="foto">Foto </label>
            <div class="fileupload-new thumbnail" style="width: 120px; height: 120px;">
                <img src="{base_url}{avatar}" /></div>
            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 120px; max-height: 120px; line-height: 20px;">

            </div>
            <div class="control-group">
                <span class="btn btn-file controls">
                    <span class="fileupload-new">Select image</span>
                    <span class="fileupload-exists">Change</span>
                    <input type="file" name="avatar"/>
                </span>
                <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
            </div>

        </div>

        <div class="control-group">
            <label class="control-label" for="passw">Password</label>
            <div class="controls">
                <input type="password" id="inputPassword" name="passw" value="{passw}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="nombre">Nombre</label>
            <div class="controls">
                <input type="text" id="nombre" name="nombre" value="{name}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="apellido">Apellido</label>
            <div class="controls">
                <input type="text" id="apellido" name="apellido" value="{lastname}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="apellido">Genero</label>
            <div class="controls">
                
                <input type="radio" name="gender" id="female" value="female" {checkedF}>
                Femenino
             
                <input type="radio" name="gender" id="male" value="male" {checkedM}>
                Masculino
               
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="dni">DNI</label>
            <div class="controls">
                <input type="number" id="dni" name="dni" value="{idnumber}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="fechanac">Fecha de nacimiento</label>
            <div class="controls">
                <input type="date" id="fechanac" name="fechanac" value="{birthdate}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputEmail">Email</label>
            <div class="controls">
                <input type="text" id="inputEmail" name="inputEmail" value="{email}">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="nombre">Tel&eacute;fono</label>
            <div class="controls">
                <input type="tel" id="telefono" name="telefono" value="{phone}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="celular">Celular</label>
            <div class="controls">
                <input type="number" id="celular" name="celular" value="{celular}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="domicilio">Domicilio</label>
            <div class="controls">
                <input type="text" id="domicilio" name="domicilio" value="{address}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="cp">CP</label>
            <div class="controls">
                <input type="text" id="cp" name="cp" value="{cp}">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="ciudad">Ciudad</label>
            <div class="controls">
                <input type="text" id="ciudad" name="ciudad" value="{city}">
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn">Submit</button>
            </div>
        </div>



    </form>
    <footer>
        <p>&copy; Dna2 2013</p>
    </footer>

</div>