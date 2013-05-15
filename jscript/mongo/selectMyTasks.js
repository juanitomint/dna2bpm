db.tokens.find({
    type:'Task',
    $or:[{'data.assign':1},{'data.idgroup':1}]
})