
<span class="formtitle">Ingrese el CUIT de la empresa para Acreditar</span>
<form 
        name="acc_form" 
        action="{module_url}accreditation/save" 
        method="POST" 
        data-ajax="false"
        >
        <input 
                type="text" 
                name="CUIT" 
                id="CUIT" 
                value="" 
                size="21" 
                placeholder="99-999999-9"
                pattern="[0-9]{2}-[0-9]{8}-[0-9]{1}" required 
                />
</form>



