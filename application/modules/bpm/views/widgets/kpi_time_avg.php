<div class="box box-info">
    <div class="box-header">
        <span class="icon"><i class="icon-time"></i></span>
        <h3 class="box-title">{title}</h3>
    </div>
    {desc}
    <div class="text-center">
        <span id="gauge-{idkpi}" class="gauge"
              data-label="days"
              data-unitsLabel="days"
              data-min="0"
              data-max= "{max}"
              data-value="{avg}"
              ></span>        
    <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th class="text-center">min</th>
                    <th class="text-center">avg</th>
                    <th class="text-center">max</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">{min_real}</td>
                    <td>{avg_formated}</td>
                    <td>{max_real}</td>
                </tr>
            </tbody>
        </table>
    </div> 
    
</div>
