<div class="small-box bg-teal {update_class}">
<span class="hidden widget_url">{widget_url}</span>
    <div class="inner">
        <div class="row">
        <div class="col-md-12">
        <h4>Buscador</h4>
        <form class="form-extra" accept-charset="utf-8" method="post" action="{base_url}crefis/buscar/pp">
            <div class="col-lg-9 input-group input-group-sm">
                <span class="input-group-addon">#PP</span>
                <input type="text"  class="form-control" name="query" placeholder="ej: 003/1014 ó nombre empresa ó cuit:30-11634893-7" />
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-info btn-flat btn-search">Buscar</button>
                </span>
            </div>
        </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ul class="list-unstyled" style="margin-top:10px">
                    <li>
                        <a href="{base_url}crefis/listar_pp" class="load_tiles_after">Solicitudes</a>
                        (<a href="{base_url}crefis/listar_pp/xls"><icon class="fa fa-download"></icon> xls</a>)
                    </li>
                    <li>
                        <a href="{base_url}crefis/listar_pde" class="load_tiles_after">Proyectos</a>
                        <span class="pull-right>">
                            (<a href="{base_url}crefis/listar_pde/xls"><icon class="fa fa-download"></icon> xls</a>)
                        </span>
                    </li>
                </ul>
          </div> 
        </div> 
    
    </div>
    <div class = "icon">
        <i class = "ion ion-search">
        </i>
    </div>
    <a class="small-box-footer" >
        <i class="fa fa-arrow-circle-right" style="opacity:0"></i>
    </a>
</div>