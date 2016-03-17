<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">Resultados: <span>({count})</span></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-default btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nro Solicitud</th>
                    <th>Nombre</th>
                    <th>CUIT</th>
                    <th>Fecha Presentacion Proyecto</th>
                    <th>Fecha Presentacion Doc</th>
                    <th>Evaluador</th>
                    <th>Fecha Primera Evaluación</th>
                    <th>Fecha Última Evaluación</th>
                    <th>Estado</th>
                    <th>Caso</th>
                </tr>
            </thead>
            <tbody>
                {proyectos}
                <tr>
                    </td>
                    <td>{Nro}</td>
                    <td>{nombre}</td>
                    <td>{cuit}</td>
                    <td>{fechapresentacionPP}</td>
                    <td>{fechapresentacion}</td>
                    <td>{evaluador}</td>
                    <td>{fechaprimereval}</td>
                    <td>{fechaultimoeval}</td>
                    <td>{estado}</td>
                    <td>{case}</td>
                </tr>
                {/proyectos}

            </tbody>
        </table>
    </div>
</div>
