<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * kpi
 *
 * Description of the class kpi
 *
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 *         @date Mar 30, 2013
 */
class Kpi extends MX_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'kpi_model' );
		$this->load->library ( 'parser' );
		$this->load->library('pagination');
		$this->load->model ( 'user' );
		$this->load->model ( 'user/group' );
		$this->user->authorize ();
		// ----LOAD LANGUAGE
		$this->types_path = 'application/modules/bpm/assets/types/';
		$this->module_path = 'application/modules/bpm/';
		$this->lang->load ( 'library', $this->config->item ( 'language' ) );
		$this->idu = $this->user->idu;
		$this->base_url = base_url ();
		$this->module_url = base_url () . $this->router->fetch_module () . '/';
		$this->modules_path = APPPATH . 'modules/';
		$this->debug=array();

        // ini_set('display_errors', 1);
        // error_reporting(E_ALL);
        // ini_set('xdebug.var_display_max_depth', 120 );
	}
	function Index() {
	}
	function Data($action, $model) {
		$this->load->model ( 'app' );
		$this->load->helper ( 'dbframe' );
		$segments = $this->uri->segment_array ();
		$debug = (in_array ( 'debug', $segments )) ? true : false;

		$custom = '';
		$types_path = $this->types_path;
		// var_dump($_POST);
		$out = array ();
		// $form = $this->app->get_object($idapp);

		if (isset ( $model )) {
			switch ($action) {
				// ----start READ--------------
				case 'read' :

					$kpi = $this->kpi_model->get_model ( $model );
					if (count ( $kpi )) {
						$forms ['totalcount'] = count ( $kpi );
						include ($types_path . 'base/kpi.base.php');

						foreach ( $kpi as $obj ) {
							$forms ['rows'] [] = $obj;
						}
					} else {
						$forms ['totalcount'] = 0;
						$forms ['rows'] = array ();
					}
					$out = $forms;
					break;
				// ---Start CREATE
				case 'update' :
					$input = json_decode ( file_get_contents ( 'php://input' ) );
					// ---defines $common
					include ($types_path . 'base/kpi.base.php');
					foreach ( $input as $thisKpi ) {
						$thisKpi = ( array ) $thisKpi;
						$dbKpi = $this->kpi_model->get ( $thisKpi ['idkpi'], 'object' );
						$newKpi = array_merge ( $dbKpi, $thisKpi );

						$this->kpi_model->save ( $newKpi );
					}
					$out = array (
							'status' => 'ok'
					);
					break;
				/*
				 * //---Start update case 'update': $out = $_POST; //$debug = true; break;
				 */
				case 'create' :
					include ($types_path . 'base/kpi.base.php');
					$input = json_decode ( file_get_contents ( 'php://input' ) );
					foreach ( $input as $thisKpi ) {
						// ---Create new id for generated form
						$thisKpi->idkpi = $this->app->gen_inc ( 'kpi', 'idkpi' );
						// ---safe set the model id
						$thisKpi->idwf = $model;
						$kpi = new dbframe ( $thisKpi, $common );
						// ---save the new object
						$this->kpi_model->save ( $kpi->toSave () );
					}
					$out = array (
							'success' => true
					);
					break;
				case 'destroy' :
					$input = json_decode ( file_get_contents ( 'php://input' ) );
					foreach ( $input as $thisKpi ) {
						$result = $this->kpi_model->delete ( $thisKpi->idkpi );
					}
					$out = array (
							'success' => true
					);
					break;
			}
			// ----end switch
			if (! $debug) {
				header ( 'Content-type: application/json;charset=UTF-8' );
				echo json_encode ( $out );
			} else {
				var_dump ( $out );
			}
		} else {
			show_error ( "Need to have idobj to get." );
		}
	}
	function Editor($model, $idwf) {
		$this->user->authorize ();
		$debug = (isset ( $this->debug [__FUNCTION__] )) ? $this->debug [__FUNCTION__] : false;
		if ($debug)
			echo '<h2>' . __FUNCTION__ . '</h2>';
		$this->load->library ( 'ui' );
		$level = $this->user->getlevel ( $this->idu );
		$cpData = $this->lang->language;
		$segments = $this->uri->segment_array ();
		// var_dump($level);
		$cpData ['theme'] = $this->config->item ( 'theme' );
		$cpData ['level'] = $level;
		$cpData ['base_url'] = $this->base_url;
		$cpData ['module_url'] = $this->module_url;
		$cpData ['idwf'] = $idwf;
		$cpData ['title'] = 'Key Performance Indicators Browser/Editor';

		$cpData ['css'] = array (
				$this->module_url . 'assets/css/jsoneditor.min.css' => 'JSON-Editor CSS',
				$this->module_url . 'assets/css/kpi.css' => 'KPI special Rules',
				$this->module_url . 'assets/css/extra-icons.css' => 'KPI special Rules'
		);
		$cpData ['js'] = array (
				$this->module_url . 'assets/jscript/jsoneditor.min.js' => 'JSON-Editor',
				$this->module_url . 'assets/jscript/kpi/ext.settings.js' => 'Settings',
				$this->module_url . 'assets/jscript/ionicons.js' => 'FontAwesome icons',
				$this->module_url . 'assets/jscript/kpi/ext.data.js' => 'data Components',
				$this->module_url . 'assets/jscript/kpi/ext.typesview.js' => 'Types Grid',
				$this->module_url . 'assets/jscript/kpi/ext.grid.js' => 'Grid',
				$this->module_url . 'assets/jscript/kpi/ext.load_props.js' => 'Form Porperty loader',
				$this->module_url . 'assets/jscript/kpi/ext.baseProperties.js' => 'Property Grid',
				$this->module_url . 'assets/jscript/ext.model-utils.js' => 'Model utils',
				$this->module_url . 'assets/jscript/kpi/ext.add_events.js' => 'Events for overlays',
				$this->module_url . 'assets/jscript/kpi/ext.viewport.js' => 'viewport',
				$this->base_url . "jscript/jquery/jquery.min.js" => 'JQuery',
				// ----Pan & ZooM---------------------------------------------
				$this->module_url . 'assets/jscript/panzoom/jquery.panzoom.min.js' => 'Panzoom Minified',
				$this->module_url . 'assets/jscript/panzoom/jquery.mousewheel.js' => 'wheel-suppport',
				$this->module_url . 'assets/jscript/panzoom/pnazoom_wheel.js' => 'wheel script',
				// -----------------------------------------------------------------
				$this->base_url . "jscript/bootstrap/js/bootstrap.min.js" => 'Bootstrap JS'
		);

		$cpData ['global_js'] = array (
				'base_url' => $this->base_url,
				'module_url' => $this->module_url,
				'idwf' => $idwf
		);

		$this->ui->makeui ( 'ext.ui.php', $cpData );
	}
	function Get_properties($idkpi = null, $mode = 'json') {
		$this->load->helper ( 'dbframe' );
		$segments = $this->uri->segment_array ();
		$debug = (in_array ( 'debug', $segments )) ? true : false;
		// $debug=true;
		$cpData = array ();
		$cpData = $this->lang->language;
		$thisKpi = array ();
		$custom = '';
		$thisKpi = array (
				''
		);
		if (isset ( $idkpi )) {
			$thisKpi = $this->kpi_model->get ( $idkpi );
		}
		// ---get idwf from post
		$thisKpi ['idwf'] = $this->input->post ( 'idwf' );
		$thisKpi ['type'] = $this->input->post ( 'type' );
		// ---set user
		$thisKpi ['idu'] = (isset ( $thisKpi ['idu'] )) ? $thisKpi ['idu'] : $this->idu;
		$type = (isset ( $thisKpi ['type'] )) ? $thisKpi ['type'] : 'count';

		// ---load base properties from helpers/types/base
		// ---defines $common
		include ($this->types_path . 'base/kpi.base.php');
		// ---load custom properties from specific type
		$type_props = array ();
		$file_custom = $this->types_path . $type . '/properties.php';
		if (is_file ( $file_custom )) {
			if ($debug)
				echo "Loaded Custom:$file_custom<br/>";
			include ($file_custom);
		}

		// ---now define the properties template
		$properties_template = $common + $type_props;
		$kpi = new dbframe ( $thisKpi, $properties_template );

		if (! $debug) {
			switch ($mode) {
				case "object" :
					return $kpi;
					break;
				default :
					header ( 'Content-type: application/json;charset=UTF-8' );
					echo json_encode ( $kpi->toShow () );
			}
		} else {
			var_dump ( 'Obj', $kpi, 'Save:', $kpi->toSave (), 'Show', $kpi->toShow () );
		}
	}
	function Get_template($type = 'count') {
		$this->load->helper ( 'file' );
		$tdata = array ();
		// ---4 safety
		if ($type == 'base')
			$type = '';
			// ----------------------------------------------------------------------
			// ---Load Custom Properties---------------------------------------------
			// ----------------------------------------------------------------------
		$file = $this->module_path . "assets/types/$type/ext.propertyGrid.js";
		if (is_file ( $file )) {
			$customProps = read_file ( $file );
			// $customProps = $this->parser->parse(str_replace('.php', '', $file), $tdata,true);
		} else {
			$customProps = '';
		}
		// ----------------------------------------------------------------------
		// ---Load Base Properties
		// ----------------------------------------------------------------------

		$file = $this->module_path . "assets/jscript/kpi/ext.baseProperties.js";
		if (is_file ( $file )) {
			$baseProps = "// FILE:$file\n";
			$baseProps .= read_file ( $file );
			// $customProps = $this->parser->parse(str_replace('.php', '', $file), $tdata,true);
		} else {
			show_error ( "Cant find base properties file: $file<br/>Sorry can't serve" );
		}
		// ---insert custom props in the base file
		$props = str_replace ( '//{customProps}', $customProps, $baseProps );
		// ----render the code
		echo $props;
	}
	function Test_render($idwf, $idkpi=null) {
		$this->load->model ( 'bpm' );
		$debug = (isset ( $this->debug [__FUNCTION__] )) ? $this->debug [__FUNCTION__] : false;
		if ($debug)
			echo '<h2>' . __FUNCTION__ . '</h2>';
		$this->load->library ( 'ui' );
		$level = $this->user->getlevel ( $this->idu );
		$cpData = $this->lang->language;
		$segments = $this->uri->segment_array ();
		// var_dump($level);
		$cpData ['theme'] = $this->config->item ( 'theme' );
		$cpData ['title'] = "Kpi Preview";
		$cpData ['level'] = $level;
		$cpData ['base_url'] = $this->base_url;
		$cpData ['module_url'] = $this->module_url;
		$cpData ['idwf'] = $idwf;
		$kpis = $this->kpi_model->get_model ( $idwf );
		$cpData ['tiles']='';
		$cpData ['widgets']='';
		// ----PROCESS KPIS
		$kpi_show = array ();
		foreach ( $kpis as $kpi ) {
			// echo $kpi['type'].'<hr/>';
			$kpi_type = 'kpi_' . $kpi ['type'];
			$this->load->library ( $kpi_type );
			$cpData ['tiles'].= $this->$kpi_type->tile ( $kpi );
			$cpData['widgets'].=$this->$kpi_type->widget( $kpi );
		}
		$cpData ['content'] = implode ( $kpi_show );
		// ----define Globals
		$cpData ['global_js'] = array (
				'base_url' => $this->base_url,
				'module_url' => $this->module_url
		);
		$cpData ['js'] = array (
		);
		$this->ui->makeui ( 'dashboard/layout', $cpData );
	}

	function list_status($idwf, $resourceId, $status, $page = 1, $pagesize = 10) {
		$debug = (isset ( $this->debug [__FUNCTION__] )) ? $this->debug [__FUNCTION__] : false;

		if ($debug)
			echo '<h2>' . __FUNCTION__ . '</h2>';
		$this->load->model ( 'bpm' );
		$cpData ['lang'] = $this->lang->language;
		// var_dump($level);
		$cpData ['theme'] = $this->config->item ( 'theme' );
		$cpData ['base_url'] = $this->base_url;
		$cpData ['module_url'] = $this->module_url;
		$cpData ['showPager'] = true;
		// ---syntethize an status kpi
		$kpi = array (
				"type" => "state",
				"idwf" => $idwf,
				"resourceId" => $resourceId,
				"status" => $status,
				'list_template' => '',
				'list_fields' => '',
				'filter' => ''
		);

		// var_dump($kpi);exit;
		// ----if specified pagesize comes from KPI

		
		$detail = $this->base_url . 'bpm/engine/run/model/{idwf}/{idcase}';
		$detail_icon = 'fa-play';
		$cpData ['kpi'] = $kpi;
		$cases = $this->Get_cases ( $kpi );

		$parseArr = array ();
		// -----prepare pagination;
		$total = count ( $cases );
		$parts = array_chunk ( $cases, $pagesize, true );
		$pages = count ( $parts );
		$offset = ($page - 1) * $pagesize;
		$top = min ( array (
				$offset + $pagesize,
				$total
		) );


		// $cpData ['start'] = $offset + 1;
		// $cpData ['top'] = $top;
		$cpData ['qtty'] = $total;
		// ----make content
		$isAdmin = $this->user->isAdmin ();

		for($i = $offset; $i < $top; $i ++) {
			$idcase = $cases [$i];
			$case = $this->bpm->get_case ( $idcase, $kpi ['idwf'] );
			$case ['data'] = $this->bpm->load_case_data ( $case );
			// ---Ensures $case['data'] exists
			$case ['data'] = (isset ( $case ['data'] )) ? $case ['data'] : array ();
			$token = $this->bpm->get_token ( $kpi ['idwf'], $idcase, $kpi ['resourceId'] );
			// ---Flatten data a bit so it can be parsed
			$parseArr [] = array_merge ( array (
					'i' => $i + 1,
					'idwf' => $kpi ['idwf'],
					'idcase' => $idcase,
					'token' => $token ['_id'],
					'resrourceId' => $kpi ['resourceId'],
					'base_url' => $this->base_url,
					'module_url' => $this->module_url,
					'checkdate' => date ( $this->lang->line ( 'dateTimeFmt' ), strtotime ( $case ['checkdate'] ) ),
					'user' => ( array ) $this->user->get_user_safe ( $case ['iduser'] )
			), $case ['data'] );
		}

		if ($kpi ['list_template'] != '') {
			$template = $kpi ['list_template'];
		} else {

			// ----create headers values 4 templates
			$columns = json_decode ( $kpi ['list_fields'] );
			$default_columns = array (
					'#' => 'i',
					'ID' => 'idcase',
					ucfirst ( $this->lang->line ( 'checkdate' ) ) => 'checkdate',
					ucfirst ( $this->lang->line ( 'user' ) ) => 'user lastname} {user name'
			);
			$tdata = ($columns) ? $columns : $default_columns;
			if ($tdata) {
				$header [] = '<th></th>';
				foreach ( $tdata as $key => $value ) {
					$header [] = '<th>' . $key . '</th>';
					$values [] = "<td>{" . $value . "}</td>\n";
				}
				$template = '<table class="table table-striped">';
				$template .= '<thead>';
				$template .= '<tr>' . implode ( $header ) . '</tr>';
				$template .= '</thead>';
				// body
				$template .= '<tbody>';
				$template .= '{cases}<tr><td>';
				//$template .= '<a target="_blank" href="' . $detail . '"><i class="fa ' . $detail_icon . '"></i></a>';
				// helpers
				if ($isAdmin) {
					$template .= ' <a href="' . $this->base_url . 'bpm/manager/mini_report/{idwf}/{idcase}/html" class="reload_widget"><i class="fa fa-plus"></i></a>';
				}
				$template .= '</td>' . implode ( $values ) . "" . "</tr>{/cases}\n";
				$template .= '</tbody>';
				$template .= '</table>';
			} else {
				show_error ( 'KPI:"' . $kpi ['title'] . '" does not have a valid "list_fields" value' );
			}
		}
		// var_dump($parseArr);exit;
		$cpData ['content'] = $this->parser->parse_string ( $template, array (
				'cases' => $parseArr
		), true, true );
		$cpData ['footer'] = "
        <a href='" . $this->base_url . "bpm/bpmui/widget_ministatus' class='reload_widget'>
        <i class='fa fa-arrow-circle-o-left'></i>
        Go back
        </a>";

    	//==== Pagination
   	
    	define("PAGINATION_WIDTH",6);
    	define("PAGINATION_ALWAYS_VISIBLE",true);
    	define("PAGINATION_ITEMS_X_PAGE",$pagesize);

    	$config=array('url'=>$this->base_url . "bpm/kpi/list_status/$idwf/$resourceId/$status",
    			'current_page'=>$page,
    			'items_total'=>$total, // Total items
    			'items_x_page'=>PAGINATION_ITEMS_X_PAGE,
    			'pagination_width'=>PAGINATION_WIDTH,
    			'class_ul'=>"pagination-sm",
    			'class_a'=>"reload_widget",
    			'pagination_always_visible'=>PAGINATION_ALWAYS_VISIBLE
    	);
    	$cpData['pagination']=$this->pagination->index($config);
    	$cpData['items_total']=$total;
		//==



		$this->parser->parse ( 'bpm/widgets/list.kpi.ui.php', $cpData,false,true );
	}

	function list_cases($idkpi, $page = 1, $pagesize = 5) {

		$page=(int)$page;
		$pagesize=(int)$pagesize;

		$debug = (isset ( $this->debug [__FUNCTION__] )) ? $this->debug [__FUNCTION__] : false;
		if ($debug)
			echo '<h2>' . __FUNCTION__ . '</h2>';
		$this->load->model ( 'bpm' );
		$cpData ['lang'] = $this->lang->language;
		// var_dump($level);
		$cpData ['theme'] = $this->config->item ( 'theme' );
		$cpData ['base_url'] = $this->base_url;
		$cpData ['module_url'] = $this->module_url;
		$cpData ['showPager'] = false;
		$kpi = $this->kpi_model->get ( $idkpi );


		// var_dump($kpi);exit;
		// ----if specified pagesize comes from KPI
		$kpi ['list_records'] = (isset ( $kpi ['list_records'] )) ? $kpi ['list_records'] : $pagesize;
		$pagesize = ($kpi ['list_records'] != 0) ? $kpi ['list_records'] : $pagesize;
		$kpi ['list_detail'] = (isset ( $kpi ['list_detail'] )) ? $kpi ['list_detail'] : '';
		if ($kpi ['list_detail'] != '') {
			$detail = $kpi ['list_detail'];
			$detail_icon = 'fa-folder';
		} else {
			$detail = '';
			$detail_icon = 'fa-folder';
		}
			$play_link = $this->base_url . 'bpm/engine/run/model/{idwf}/{idcase}';
			$play_icon = 'fa-play';
		$cpData ['kpi'] = $kpi;
		$cases = $this->Get_cases ( $kpi );


		$parseArr = array ();
		// -----prepare pagination;
		$total = count ( $cases );
		$parts = array_chunk ( $cases, $pagesize, true );
		$pages = count ( $parts );
		$offset = ($page - 1) * $pagesize;
		$top = min ( array (
				$offset + $pagesize,
				$total
		) );



    	//==== Pagination
    	define("PAGINATION_WIDTH",6);
    	define("PAGINATION_ALWAYS_VISIBLE",true);
    	define("PAGINATION_ITEMS_X_PAGE",$pagesize);

    	$config=array('url'=>$this->base_url . 'bpm/kpi/list_cases/' . $idkpi,
    			'current_page'=>$page,
    			'items_total'=>$total, // Total items
    			'items_x_page'=>PAGINATION_ITEMS_X_PAGE,
    			'pagination_width'=>PAGINATION_WIDTH,
    			'class_ul'=>"pagination-sm",
    			'class_a'=>"reload_widget",
    			'pagination_always_visible'=>PAGINATION_ALWAYS_VISIBLE
    	);
    	$cpData['pagination']=$this->pagination->index($config);
    	$cpData['items_total']=$total;

		/**
		 * SORTER
		 */
		 
		 //$sorter=json_decode($kpi['sort_by']);
		 //foreach($cases as $key=>$idcase){
		 //	$case = $this->bpm->get_case ( $idcase, $kpi ['idwf'] );
		 //	$data[$idcase]= $this->bpm->load_case_data ( $case );
		 //	$data[$idcase]['idcase']=$idcase;
		 //	if($sorter){
		 //		foreach ($sorter as $ksort=>$direction){
		 //			$k=$this->parser->parse_string ('{'.$ksort.'}',$data[$idcase]);
		 //			$sort[$ksort][$key]=$k;
		 //		}
		 //	}
		 	
		 //}
		 
		 //	//---sort arrays for order
	 	// if($sorter){
		 //	foreach ($sorter as $ksort=>$direction){
		 //			if($direction==1){
 		// 				array_multisort($sort[$ksort],SORT_ASC,$cases);
		 //			}else{
 		// 				array_multisort($sort[$ksort],SORT_DESC,$cases);
		 //			}
	 	// 	}	
	 	// }
	 	
	 	
		// $cpData ['start'] = $offset + 1;
		// $cpData ['top'] = $top;
		 $cpData ['qtty'] = $total;
		// ----make content
		for($i = $offset; $i < $top; $i ++) {
			$idcase = $cases [$i];
			$case = $this->bpm->get_case ( $idcase, $kpi ['idwf'] );
			$case ['data'] = $this->bpm->load_case_data ( $case );
			// ---Ensures $case['data'] exists
			$case ['data'] = (isset ( $case ['data'] )) ? $case ['data'] : array ();
			$token = $this->bpm->get_token ( $kpi ['idwf'], $idcase, $kpi ['resourceId'] );
			// ---Flatten data a bit so it can be parsed
			$parseArr [] = array_merge ( array (
					'i' => $i + 1,
					'idwf' => $kpi ['idwf'],
					'idcase' => $idcase,
					'token' => $token ['_id'],
					'resrourceId' => $kpi ['resourceId'],
					'base_url' => $this->base_url,
					'module_url' => $this->module_url,
					'checkdate' => date ( $this->lang->line ( 'dateTimeFmt' ), strtotime ( $case ['checkdate'] ) ),
					'user' => ( array ) $this->user->get_user_safe ( $case ['iduser'] )
			), $case ['data'] );
		}


		if ($kpi ['list_template'] != '') {
			$template = $kpi ['list_template'];
		} else {

			// ----create headers values 4 templates
			$columns = json_decode ( $kpi ['list_fields'] );
			$default_columns = array (
					'#' => 'i',
					'ID' => 'idcase',
					ucfirst ( $this->lang->line ( 'checkdate' ) ) => 'checkdate',
					ucfirst ( $this->lang->line ( 'user' ) ) => 'user lastname} {user name'
			);
			$tdata = ($columns) ? $columns : $default_columns;

			if ($tdata) {
				$header [] = '<th></th>';
				$header [] = '<th></th>';
				foreach ( $tdata as $key => $value ) {
					$header [] = '<th>' . $key . '</th>';
					$values [] = "<td>{" . $value . "}</td>\n";
				}
				$template = '<table class="table table-striped">';
				$template .= '<thead>';
				$template .= '<tr>' . implode ( $header ) . '</tr>';
				$template .= '</thead>';
				// body
				$template .= '<tbody>';
				$template .= '{cases}' . '<tr>' . '<td>'.'<a target="_blank" class="'.$idkpi.'" href="' . $play_link . '">' . '<i class="fa ' . $play_icon . '"></i></a></td><td>' . '<a target="_blank" class="'.$idkpi.'" href="' . $detail . '">' . '<i class="fa ' . $detail_icon . '"></i>' . '</a>' . '</td>' . implode ( $values ) . "" . "</tr>{/cases}\n";
				$template .= '</tbody>';
				$template .= '</table>';
			} else {
				show_error ( 'KPI:"' . $kpi ['title'] . '" does not have a valid "list_fields" value' );
			}
		}
		 //var_dump($template,$parseArr);//exit;
		$cpData ['content'] = @$this->parser->parse_string ( $template, array (
				'cases' => $parseArr
		), true, true );
		// ----PROCESS KPIS



	$this->parser->parse ( 'bpm/widgets/list.kpi.ui.php', $cpData );
	}



	function widget($model, $idkpi, $widget = 'box_info') {
		$debug = (isset ( $this->debug [__FUNCTION__] )) ? $this->debug [__FUNCTION__] : false;
		if ($debug)
			echo '<h2>' . __FUNCTION__ . '</h2>';
		$this->load->model ( 'bpm' );
		$kpi = $this->kpi_model->get ( $idkpi );
		// ---set defaults 4 view
		$kpi ['widget_type'] = 'widgets';
		$kpi ['widget'] = ($kpi ['widget'] != '') ? $kpi ['widget'] : $widget;
		if ($kpi) {
			$kpi_type = 'kpi_' . $kpi ['type'];
			$this->load->library ( $kpi_type );
			echo $this->$kpi_type->widget ( $kpi );
		} else {
			echo "Error: There is no kpi: $idkpi";
		}
	}

	/*
	 * This function makes a Tile with kpi data
	 */
	function Tile_kpi($kpi, $tile_file = 'tile-blue') {
		if ($kpi) {
			$this->load->model ( 'bpm' );
			// var_dump($kpi);exit;
			$kpi ['widget_type'] = 'tiles';
			$kpi ['widget'] = (strstr ( $kpi ['widget'], 'tile' )) ? $kpi ['widget'] : $tile_file;
			$kpi_type = 'kpi_' . $kpi ['type'];
			$this->load->library ( $kpi_type );
			echo $this->$kpi_type->tile ( $kpi );
		}
	}

	/*
	 * This function makes a Tile with an idkpi
	 */
	function Tile($model = null, $idkpi = null, $tile_file = 'tile-blue') {
		$debug = (isset ( $this->debug [__FUNCTION__] )) ? $this->debug [__FUNCTION__] : false;
		if ($debug)
			echo '<h2>' . __FUNCTION__ . '</h2>';
		if ($idkpi) {
			$this->load->model ( 'bpm' );
			$kpi = $this->kpi_model->get ( $idkpi );
			if ($kpi) {
				// var_dump($kpi);exit;
				$kpi ['widget_type'] = 'tiles';
				$kpi ['widget'] = (strstr ( $kpi ['widget'], 'tile' )) ? $kpi ['widget'] : $tile_file;
				$kpi_type = 'kpi_' . $kpi ['type'];
				$this->load->library ( $kpi_type );
				echo $this->$kpi_type->tile ( $kpi );
			} else {
				echo 'The referenced KPI:' . $idkpi . ' does not exists.';
			}
		}
	}
	function Render($kpi = null) {
		$debug = false;

		$exists = false;
		// ---load type extension
		if (! method_exists ( $this, $kpi ['type'] )) {
			$file_custom = $this->types_path . $kpi ['type'] . '/kpi_controller.php';
			// ---set defaults 4 view
			$kpi ['widget_type'] = ($kpi ['widget_type'] != '') ? $kpi ['widget_type'] : 'tiles';
			$kpi ['widget'] = ($kpi ['widget'] != '') ? $kpi ['widget'] : 'tile-blue';
			if (is_file ( $file_custom )) {
				// $exists = true;
				if ($debug)
					echo "Loaded Custom Render:$file_custom<br/>";
				require_once ($file_custom);
			} else {
				$rtn = $this->ShowMsg ( '<strong>Warning!</strong>Function:' . $kpi ['type'] . '<br/>' . $kpi ['title'] . '<br/>Does not exists. ', 'alert' );
			}
			$rtn = $kpi ['type'] ( $kpi, $this );
		} else {
			$exists = true;
		}
		if ($exists)
			$rtn = $this->$kpi ['type'] ( $kpi );
		return $rtn;
	}
	function Get_cases($kpi = null) {
		$debug = (isset ( $this->debug [__FUNCTION__] )) ? $this->debug [__FUNCTION__] : false;
		if ($debug)
			echo '<h2>' . __FUNCTION__ . '</h2>';
		if ($kpi) {
			$kpi_type = 'kpi_' . $kpi ['type'];
			$this->load->library ( $kpi_type );
			$rtn =(array) $this->$kpi_type->list_cases ( $kpi );
			return $rtn;
		}
	}

	/*
	 * Most common render goes inline
	 */
    /*
	function get_filter($kpi) {
		$filter = array ();
		switch ($kpi ['filter']) {
			case 'group' :
				break;
			case 'user' :
				$filter = array (
						'idwf' => $kpi ['idwf'],
						'iduser' => $this->idu
				);
				break;
			default : // ---filter by idwf
				$filter = array (
						'idwf' => $kpi ['idwf']
				);
				break;
		}
		// ----process extra filters
		$filter_extra = array ();
		if ($kpi ['filter_extra'] != '') {
			$filter_extra = @json_decode ( $kpi ['filter_extra'] );
			switch (json_last_error ()) {
				case JSON_ERROR_NONE :
					// echo ' - No errors';
					break;
				case JSON_ERROR_DEPTH :
					echo ' - Maximum stack depth exceeded';
					break;
				case JSON_ERROR_STATE_MISMATCH :
					echo ' - Underflow or the modes mismatch';
					break;
				case JSON_ERROR_CTRL_CHAR :
					echo ' - Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX :
					echo ' - Syntax error, malformed JSON';
					break;
				case JSON_ERROR_UTF8 :
					echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
					break;
				default :
					echo ' - Unknown error';
					break;
			}
		}
		$filter = array_merge ( ( array ) $filter_extra, $filter );
        var_dump($filter);
	}
    */
	function Download($idkpi) {
		$debug = false;
		$types_path = $this->types_path;
		$postkpi = $this->kpi_model->get ( $idkpi );
		unset ( $postkpi ['_id'] );
		/*
		 * $type=$postkpi['type']; //---load base properties from helpers/types/base //---defines $common include($types_path . 'base/kpi.base.php'); //---load custom properties from specific type $type_props = array(); if (isset($type)) { $file_custom = $types_path . $type . '/properties.php'; if (is_file($file_custom)) { if ($debug) echo "Loaded Custom:$file_custom<br/>"; include($file_custom); } } $properties_template = $common + $type_props; var_dump($common , $type_props,$postkpi); //----load the data from post $kpi = new dbframe(); $kpi->load($postkpi, $properties_template);
		 */
		if (! $debug) {
			header ( 'Content-Description: File Transfer' );
			header ( 'Content-Type: application/octet-stream' );
			header ( "Content-Disposition: attachment; filename=" . $idkpi . '.json' );
			header ( "Content-Transfer-Encoding: binary" );
		}
		echo json_encode ( $postkpi );
	}
	function Save_properties($data = null, $return = null) {
		$this->load->helper ( 'dbframe' );
		$this->load->model ( 'user/rbac' );
		$this->load->model ( 'app' );
		$segments = $this->uri->segment_array ();
		$debug = (in_array ( 'debug', $segments )) ? true : false;
		$types_path = $this->types_path;
		$postkpi = ($data) ? $data : $_POST;
		$idkpi = $postkpi ['idkpi'];
		// ---get type
		$type = $postkpi ['type'];
		// ----create empty frame according to the template
		$kpi = new dbframe ();
		// ---load base properties from helpers/types/base
		// ---defines $common
		include ($types_path . 'base/kpi.base.php');
		// ---load custom properties from specific type
		$type_props = array ();
		if (isset ( $type )) {
			$file_custom = $types_path . $type . '/properties.php';
			if (is_file ( $file_custom )) {
				if ($debug)
					echo "Loaded Custom:$file_custom<br/>";
				include ($file_custom);
			}
		}
		$properties_template = $common + $type_props;
		// ----load the data from post
		$kpi->load ( $postkpi, $properties_template );
		if ($idkpi == '') {
			// ---create new ID for the frame
			$idkpi = $this->kpi_model->gen_kpi ( $kpi->idwf );
			$kpi->idkpi = $idkpi;
		}
		//$dbkpi = ($this->kpi_model->get ( $idkpi ));
		$addkpi = ($dbkpi) ? $dbkpi : array ();
		$obj = $kpi->toSave () + $addkpi;

		$this->kpi_model->save ( $obj );
		// ----register app in RBAC-REPOSIROTY
		$path = 'modules/bpm/controllers/model/' . $kpi->idwf . '/kpi/' . $kpi->idkpi . '/' . $kpi->title . ' (' . $kpi->type . ')';
		$properties = array (
				"source" => "User",
				"checkdate" => date ( 'Y-m-d H:i:s' ),
				"idu" => $this->idu
		);
		$this->rbac->put_path ( $path, $properties );
		// $kpi->groups = implode(',', $kpi->groups);
		// ----dump results
		if (! $debug) {
			if ($return) {
				return $kpi->toSave ();
			} else {
				header ( 'Content-type: application/json;charset=UTF-8' );
				echo json_encode ( $kpi->toSave () );
			}
		} else {
			var_dump ( $obj );
		}
	}
	
	
	function ShowMsg($msg, $class = 'alert') {
		return '<div class="' . $class . '">
    <button type="button" class="close" data-dismiss="alert">&times;</button>' . $msg . '</div>';
	}
	function import_kpi($module) {
		if ($module && $this->user->isAdmin ()) {
			$this->load->helper ( 'file' );
			$path = APPPATH . "modules/$module/views/kpi/"; // ---don't
			$files = get_filenames ( $path );

			foreach ( $files as $file ) {
				echo "Importing: $file<br/>";
				$content = file_get_contents ( $path . $file );
				$data = json_decode ( $content, true );
				$out = $this->Save_properties ( $data, true );
				// Modules::run('bpm/kpi/save_properties', $data);
				echo "ok!<br/>";
			}
		}
	}
	
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
	    $sort_col = array();
	    
	    foreach ($arr as $key=> $row) {
	        $sort_col[$key] = $row[$col];
	    }
	
	    array_multisort($sort_col, $dir, $arr);
	}

}

/* End of file kpi */
