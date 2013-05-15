//--- set all workflow as general folder
db.workflow.update({},{$set:{folder:'General'}},true,true);
//--- set all workflow as Test folder if name contains 'test'
db.workflow.update({'data.properties.name':/test/i},{$set:{folder:'Test'}},true,true);