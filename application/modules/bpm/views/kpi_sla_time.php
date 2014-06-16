<div class="widget-box">
    <div class="widget-title">
        <span class="icon"><i class="icon-time"></i></span>
        <h5>{title}</h5>
    </div>
    {desc}
    <div class="text-center">
        <span id="gauge-{idkpi}" class="gauge_reverse"
              data-label="%"
              data-unitsLabel="%"
              data-min="0"
              data-max= "100"
              data-value="{sla_percent}"
              >{sla_percent}%</span>        
    <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>On time</th>
                    <th>Out time</th>
                    <th>total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">{sla}</td>
                    <td>{sla_out}</td>
                    <td>{total}</td>
                </tr>
            </tbody>
        </table>
    </div> 
    
</div>
