// JavaScript Document

//-----función para ocultar controles

function toHide(nombrecontrol){
$("input[name='"+nombrecontrol+"']").attr("disabled",true);
$("#BLOCK_"+nombrecontrol+' *').addClass("ignore");
$("#BLOCK_"+nombrecontrol).hide();
return true;
}

//-----función para Mostrar controles

function toShow(nombrecontrol){
$("input[name='"+nombrecontrol+"']").attr("disabled",false);
$("#BLOCK_"+nombrecontrol+" *").removeClass("ignore");
$("#BLOCK_"+nombrecontrol).slideDown("slow");
return true;
}

//-----función para lockear controles

function toLock(nombrecontrol){
$("#BLOCK_"+nombrecontrol+" *").attr("disabled",true);
$("#BLOCK_"+nombrecontrol+" .titulopreg").addClass('locked');
return true;
}

//-----función para des-lockear controles

function toUnLock(nombrecontrol){
$("#BLOCK_"+nombrecontrol+" *").attr("disabled",false);
$("#BLOCK_"+nombrecontrol+" .titulopreg").removeClass('locked');
return true;
}
function updatecombo(idpreg,objname,value){
sel=$("#"+objname).val();
//alert('idpreg:'+idpreg+'objname:'+objname+'->'+sel);
$('#'+objname).attr("disabled",true);
$("#div_"+idpreg).append("<img src='images/loader18.gif' align='absmiddle'/> Cargando...");
url='filtercombo.php?imin='+imin+'&idpreg='+idpreg+'&filter='+value+'&sel='+sel;
$("#div_"+idpreg).load(url);
}

//----Para el tamaóo de la pantalla
function getScreenSize() {
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;
  }
 return Array( myWidth, myHeight );
}

//------Para abrir un thickBox desde JS
function openTB(title,url){
//alert(getScreenSize()[0]-100,getScreenSize()[1]-100);
tb_show(title,url+'&width='+(getScreenSize()[0]-100)+'&height='+(getScreenSize()[1]-100));
}

//-------Chequea todos los checks-------
function checkAll(nombrecontrol){
myobj=document.getElementById(nombrecontrol+"_all");
				var checked_status = myobj.checked;
				$("input[name="+nombrecontrol+"\\[\\]]").each(
                function(){
					this.checked = checked_status;
				});
			}

<!-- para Entidades -->
//------Elimina duplicados en un control
function norepeat(source,dest){
retval=false;
	if($("#"+source).val() && $("#"+dest).val()){
	myids=$("#"+source).val().split('*').filter(isempty);
		for (i=0;i<=myids.length;i++){
			//console.log('Analizo:'+myids[i]);
				if(myids[i]!='' && $("#"+dest).val().indexOf(myids[i])!=-1){
				//console.log("Borro:"+myids[i]+' de '+source);
				delsel(myids[i],source);
				retval=true;
				}
		}
	} else {
	retval=false;
	}
return retval;
}

//------Agrega el id al control deseado----------
function addsel(id,idpreg,nombrecontrol){
//alert ('adsel id:'+id+' idpreg:'+idpreg+' nombrecontrol:'+nombrecontrol);
id=id+'';
mydiv=document.getElementById('cards'+idpreg); // el div
myhidden=document.getElementById(nombrecontrol); // el hidden
myids=new Array();
addid=new Array();
    if(myhidden!=null){
		if(myhidden.value!=null) myids=myhidden.value.split('*');
		//console.log(typeof id);
                addid=id.split('*');
                //console.debug('myids:',myids,addid);

               for (index=0;index<addid.length;index++){
                //console.log("buscaANDO:"+addid[index]+' en '+myids);
                    if(myids.indexOf(addid[index]+'')==-1) myids.push(addid[index]);
                }

                myids=myids.filter(isempty);
		myhidden.value=myids.join('*');
		// ahora refresco el div de las tarjetitas
		url='filtersubform.php?nosubhead=true&idpreg='+idpreg+'&id='+id+'&valor='+myhidden.value+'&nombrecontrol='+nombrecontrol+'&idap='+idap;
		$("#cards"+idpreg).load(url,'',function(){
								doSubFormPreview();
                                                                $("#"+nombrecontrol).trigger('change');
								});

	} else {
     alert("Error: subform:"+nombrecontrol+" no existe");
        }
}

//-----Elimino el id del control
function delsel (id,nombrecontrol){
myids=$("#"+nombrecontrol).val().split('*').filter(isempty);
//console.debug(myids);
$("#BLOCK_"+nombrecontrol+" #child_"+nombrecontrol+id).remove();
	for (i=0;i<=myids.length;i++){
		if(myids[i]==id){
		myids.splice(i,1);
		}
	}
//console.debug(myids);
$("#"+nombrecontrol).val(myids.join('*'));
$("#"+nombrecontrol).trigger('change');
}


function doSubFormPreview(){
$(".subformPreview").colorbox({
        width:"90%",
        height:"100%",
        iframe:true,
        initialWidth:"400",
        initialHeigth:"400",
        scrolling:true,
        onComplete:$.fn.colorbox.resize()
        });
}
<!-- para Entidades -->