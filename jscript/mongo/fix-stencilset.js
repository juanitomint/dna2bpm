print('###   FIX STENCILSET ABSOLUTE PATHS   ###');
db.workflow.find({}).forEach(function(bpm){ 
	print('Replace:'+bpm.data.stencilset.url);
	bpm.data.stencilset.url=bpm.data.stencilset.url.replace('\/beta\/ci\/',"../../");
	print('By:'+bpm.data.stencilset.url);
	db.workflow.save(bpm);
});
