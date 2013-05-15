// JavaScript Document
//
function validateJSON(value){
   
}
//----Validate Json
jQuery.validator.addMethod("JSONstring", function(value, element, params) {
    valid=true;
    try{
        jQuery.parseJSON(value)
    } catch(err) {
        valid=false;
    }

    return this.optional(element) || valid;

}, jQuery.validator.format("Ivalid JSON string"));
//----Numberidator Custom Scripts-------------------------------------------------
jQuery.validator.addMethod("minParsed", function(value, element, params) { 
    return this.optional(element) || parseInt(value.replace(/\./g,''))> params;
    
}, jQuery.validator.format("Ingrese un valor mayor que {0}"));

jQuery.validator.addMethod("maxParsed", function(value, element, params) {
    return this.optional(element) || parseInt(value.replace(/\./g,''))< params;
}, jQuery.validator.format("Ingrese un valor menor que {0}"));

jQuery.validator.addMethod("CUIT", function(value, element) {

    var cuit=value;
    return this.optional(element) || /^\d{2}[\/-]\d{7,8}[\/-]\d{1,2}$/.test(cuit);
},"Ingrese un numero de CUIT v&acute;lido");
//------------------------------------------------------------------------------

function validaCuit(cuit) {
    if (typeof (cuit) == 'undefined') return true;
    cuit = cuit.toString().replace(/[-_]/g, "");
    if (cuit == '') return true; //No estamos validando si el campo esta vacio, eso queda para el "required"
    if (cuit.length != 11)
        return false;
    else {
        var total = 0;
        var mult = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        for (var i = 0; i < mult.length; i++) {
            total += parseInt(cuit[i]) * mult[i];
        }
        var mod = total % 11;
        var digito = mod == 0 ? 0 : mod == 1 ? 9 : 11 - mod;
    }

    return digito == parseInt(cuit[10]);
}
jQuery.validator.addMethod("CUITDigito", validaCuit, 'Ingrese un numero de CUIT v&aacute;lido');

function remotemsg(value, element, param) {
    if ( this.optional(element) )
        return "dependency-mismatch";
			
    var previous = this.previousValue(element);
    var myobj=this;
    if (!this.settings.messages[element.name] )
        this.settings.messages[element.name] = {};
    //this.settings.messages[element.name].remote = typeof previous.message == "function" ? previous.message(value) : previous.message;
    param = typeof param == "string" && {
        url:param
    } || param;
			
    if ( previous.old !== value ) {
        previous.old = value;
        var validator = this;
        this.startRequest(element);
        var data = {};
        data[element.name] = value;
        $.ajax($.extend(true, {
            url: param,
            mode: "abort",
            async:true,
            port: "validate" + element.name,
            dataType: "json",
            data: data,
            success: function(response) {
                myobj.settings.messages[element.name].remote=response.msg;
                $.validator.messages['remotemsg']=response.msg;
                var valid = response.validate === true;
                if ( valid ) {
                    var submitted = validator.formSubmitted;
                    validator.prepareElement(element);
                    validator.formSubmitted = submitted;
                    validator.successList.push(element);
                    validator.showErrors();
                } else {
                    var errors = {};
                    errors[element.name] = response.msg;
                    validator.showErrors(errors);
                }
                previous.valid = valid;
                validator.stopRequest(element, valid);
            }
        }, param));
        return "pending";
    } else if( this.pending[element.name] ) {
        return "pending";
    }
    return previous.valid;
}
jQuery.validator.addMethod("remotemsg",remotemsg ,'El valor ya existe en la base de datos');		
//-------------------------------------------------------------------------------------------
