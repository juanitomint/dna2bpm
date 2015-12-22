db.tokens.aggregate(

  // Pipeline
  [
    // Stage 1
    {
      $match: {
        "resourceId" : "oryx_E928EAAD-BC7D-42AA-AF24-1AC1F21DD2D2",
        
        "idwf":"pacc3SDAREND"  
      }
    },

    // Stage 2
    {
      $group: {
      	_id:"$resourceId",
      	min:{$min:"$interval.days"},
          max:{$max:"$interval.days"},
          avg:{$avg:"$interval.days"},
          count:{$sum:1}
           
      }
    },

    // Stage 3
    {
      $project: {
      resourceId:"$_id",min:"$min",max:"$max",avg:"$avg",count:"$count",_id:0
       
      }
    }

  ]

  // Created with 3T MongoChef, the GUI for MongoDB - http://3t.io/mongochef

);
