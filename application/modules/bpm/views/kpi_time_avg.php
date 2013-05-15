<div class="widget-box">
    <div class="widget-title">
        <span class="icon"><i class="icon-time"></i></span>
        <h5>{title}</h5>
    </div>
    {desc}
    <div class="text-center">
        <span id="gauge-{idkpi}" class="gauge"
              data-label="days"
              data-unitsLabel="days"
              data-min="0"
              data-max= "{max}"
              data-value="{avg}"
              >{avg}</span>        
    <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>min</th>
                    <th>avg</th>
                    <th>max</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">{min}</td>
                    <td>{avg_formated}</td>
                    <td>{max}</td>
                </tr>
            </tbody>
        </table>
    </div> 
    
</div>
