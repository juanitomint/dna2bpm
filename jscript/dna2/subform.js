// JavaScript Document
//-----para los subform rules del validate-------------
$.validator.addMethod("subFormMin", function(value,element,param) {
//console.log("subFormMin",value,element,param);
myids=new Array();
		if(element.value!=null) myids=value.split('*').filter(isempty);		
		//console.log(myids.length+'>='+ param,myids.length>=param);
		return myids.length >= param;
	}, jQuery.format("* por favor cargue al menos {0} !"));
	
$.validator.addMethod("subFormMax", function(value,element,param) {
myids=new Array();
		if(element.value!=null) myids=value.split('*').filter(isempty);		
		//console.log(myids.length,param);
		return myids.length <= param;
	}, jQuery.format("* no puede agregar más de {0} !"));

$.validator.addMethod("subFormEq", function(value,element,param) {
myids=new Array();
		if(element.value!=null) myids=value.split('*').filter(isempty);		
		//console.log(myids.length,param);
		return myids.length == param;
	}, jQuery.format("* no puede agregar más de {0} !"));
//-----------------------------------------------------