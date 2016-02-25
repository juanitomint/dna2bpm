  $(document).ready(function() {
      $.ajax({
          url: globals.module_url + "repository/dump/model/" + globals.idwf,
          success: function(json) {
              var options = {
                  mode: 'form',
                  indentation: 4,
                  modes: ['code', 'form', 'text', 'tree', 'view'], // allowed modes
                  error: function(err) {
                      alert(err.toString());
                  }
              };
              var container = document.getElementById('jsoneditor');
              globals.jsonEd = new JSONEditor(container, options, json.data);
          }
      });

  });
  $('#saveTask').click(function() {
      url = globals.base_url + 'bpm/repository/save_model/' + globals.idwf;
      var data = {
          data:  JSON.stringify(globals.jsonEd.get())
      }
      $.ajax({
          url: url,
          type: 'POST',
          data: data,
          //dataType:'json',
          success: function(data) {
              $('#results').html(data);
          }
      });

  });