
db.case.find({'token_status':{$exists:true}}).forEach(function(item){
ts=item.token_status;
nts=new Array();
printjson(item);
	for (var key in ts){
	 nts.push({resourceId:key,status:ts[key]});
	}
item.token_status=nts;
db['case'].save(item);	
});
