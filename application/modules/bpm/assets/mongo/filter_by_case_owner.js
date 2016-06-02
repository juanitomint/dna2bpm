db.tokens.aggregate(

  // Pipeline
  [
    // Stage 1
    {
      $match: {
        "resourceId":"oryx_5180C462-CF50-4AE0-A884-8B1CBD989D4F","status":"finished"}
    },

    // Stage 2
    {
      $lookup: {
          "from" : "case",
          "localField" : "case",
          "foreignField" : "id",
          "as" : "cases"
      }
    },

    // Stage 3
    {
      $match: {
       "cases.iduser":-924582358
      }
    },

    // Stage 4
    {
      $project: {
      _id:0,
      case:'$case'
      }
    }

  ]

  // Created with 3T MongoChef, the GUI for MongoDB - http://3t.io/mongochef

);
