<?php
echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>MOVI {idwf}</title>
		<script type="text/javascript" src="{base_url}jscript/yui/build/yuiloader/yuiloader.js" />
<!--        <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yuiloader/yuiloader.js" />-->
		<script type="text/javascript" src="{base_url}jscript/movi/src/movi_1.js" />


		<link rel="stylesheet" type="text/css" href="{base_url}jscript/yui/build/reset-fonts-grids/reset-fonts-grids.css" />

		<style type="text/css">
			#modelviewer {
				height: 500px;
			}

			#modelnavigator {
				position: relative;
				width: 240px;
			}

			.yui-resize-handle {
				top: 100%;
			}

			#content {
				margin-top: 15px;
			}

		</style>
    </head>
    <body class="yui-skin-sam">
		<div id="doc3" class="yui-t5">
			<div id="bd">

				<div class="yui-ge">

		    		<div class="yui-u first">
		    			<div id="modelviewer" />
						<div id="content">

						</div>
		    		</div>

		    		<div class="yui-u">
						<div id="modelnavigator" />
						<div id="toolbar" />
						<div id="zoomslider" />
			    	</div>

				</div>

			</div>
		</div>


		<script type="text/javascript">
                        var base_url='{base_url}';
                        var idcase='{idcase}';
			var modelviewer;
			var toolbar;
                        model={"resourceId":"test-create","properties":{"name":"Create This","documentation":"","auditing":"","monitoring":"","version":"1","author":"Juan Ignacio Borda","language":"english","namespaces":"","targetnamespace":"http:\/\/www.omg.org\/bpmn20","expressionlanguage":"http:\/\/www.w3.org\/1999\/XPath","typelanguage":"http:\/\/www.w3.org\/2001\/XMLSchema","creationdate":"2011-05-11T00:00:00","modificationdate":"2011-05-11T00:00:00"},"stencil":{"id":"BPMNDiagram"},"childShapes":[{"resourceId":"oryx_794088B9-F43E-40B6-8930-C4C7D0F4833A","properties":{"name":"","documentation":"","auditing":"","monitoring":"","eventdefinitionref":"","eventdefinitions":"","dataoutputassociations":"","dataoutput":"","outputset":"","bgcolor":"#b8d070","trigger":"None"},"stencil":{"id":"StartNoneEvent"},"childShapes":[],"outgoing":[{"resourceId":"oryx_CBD01AC5-895A-44BC-BCB3-DE640161229D"}],"bounds":{"lowerRight":{"x":183,"y":189},"upperLeft":{"x":153,"y":159}},"dockers":[]},{"resourceId":"oryx_CF88A3D7-A92E-4DAA-B21C-AB3AABF96843","properties":{"name":"","documentation":"","auditing":"","monitoring":"","categories":"","startquantity":1,"completionquantity":1,"isforcompensation":"","assignments":"","callacitivity":"","tasktype":"None","implementation":"webService","resources":"","messageref":"","rendering":"","callableelement":"","operationref":"","instantiate":"","script":"","script_language":"","bgcolor":"#ffffcc","looptype":"None","testbefore":"","loopcondition":"","loopmaximum":"","loopcardinality":"","loopdatainput":"","loopdataoutput":"","inputdataitem":"","outputdataitem":"","behavior":"all","complexbehaviordefinition":"","completioncondition":"","onebehavioreventref:":"signal","nonebehavioreventref":"signal","properties":"","datainputset":"","dataoutputset":""},"stencil":{"id":"Task"},"childShapes":[],"outgoing":[{"resourceId":"oryx_56DD829C-81AB-4F1C-9CAD-3DD1130BE947"}],"bounds":{"lowerRight":{"x":328,"y":214},"upperLeft":{"x":228,"y":134}},"dockers":[]},{"resourceId":"oryx_CBD01AC5-895A-44BC-BCB3-DE640161229D","properties":{"name":"","documentation":"","auditing":"","monitoring":"","conditiontype":"None","conditionexpression":"","isimmediate":"","showdiamondmarker":""},"stencil":{"id":"SequenceFlow"},"childShapes":[],"outgoing":[{"resourceId":"oryx_CF88A3D7-A92E-4DAA-B21C-AB3AABF96843"}],"bounds":{"lowerRight":{"x":227.15625,"y":175},"upperLeft":{"x":183.609375,"y":173}},"dockers":[{"x":15,"y":15},{"x":50,"y":40}],"target":{"resourceId":"oryx_CF88A3D7-A92E-4DAA-B21C-AB3AABF96843"}},{"resourceId":"oryx_7B7F19FF-8AA1-4145-8BEF-DA0CB6660895","properties":{"name":"","documentation":"","auditing":"","monitoring":"","categories":"","assignments":"","pool":"","lanes":"","gates":"","gates_outgoingsequenceflow":"","gates_assignments":"","bgcolor":"#d0ce70","gatewaytype":"XOR","xortype":"Data","markervisible":"true","defaultgate":"","gate_outgoingsequenceflow":"","gate_assignments":""},"stencil":{"id":"Exclusive_Databased_Gateway"},"childShapes":[],"outgoing":[{"resourceId":"oryx_5FD4FDF1-3811-4944-8236-F02B8F15354D"},{"resourceId":"oryx_7B06541E-B5D4-4137-8289-C95A1D381305"}],"bounds":{"lowerRight":{"x":460,"y":194},"upperLeft":{"x":420,"y":154}},"dockers":[]},{"resourceId":"oryx_56DD829C-81AB-4F1C-9CAD-3DD1130BE947","properties":{"name":"","documentation":"","auditing":"","monitoring":"","conditiontype":"None","conditionexpression":"","isimmediate":"","showdiamondmarker":""},"stencil":{"id":"SequenceFlow"},"childShapes":[],"outgoing":[{"resourceId":"oryx_7B7F19FF-8AA1-4145-8BEF-DA0CB6660895"}],"bounds":{"lowerRight":{"x":419.18750473369,"y":174.43442309149},"upperLeft":{"x":328.51171401631,"y":174.15542065851}},"dockers":[{"x":50,"y":40},{"x":20.5,"y":20.5}],"target":{"resourceId":"oryx_7B7F19FF-8AA1-4145-8BEF-DA0CB6660895"}},{"resourceId":"oryx_5FD4FDF1-3811-4944-8236-F02B8F15354D","properties":{"name":"","documentation":"","auditing":"","monitoring":"","conditiontype":"None","conditionexpression":"","isimmediate":"","showdiamondmarker":false},"stencil":{"id":"SequenceFlow"},"childShapes":[],"outgoing":[{"resourceId":"oryx_CF88A3D7-A92E-4DAA-B21C-AB3AABF96843"}],"bounds":{"lowerRight":{"x":440.5,"y":278},"upperLeft":{"x":278,"y":194.90625}},"dockers":[{"x":20.5,"y":20.5},{"x":440.5,"y":278},{"x":278,"y":278},{"x":50,"y":79}],"target":{"resourceId":"oryx_CF88A3D7-A92E-4DAA-B21C-AB3AABF96843"}},{"resourceId":"oryx_016D2181-B4CB-4DB1-ACA5-3D27BCF68A83","properties":{"name":"","documentation":"","auditing":"","monitoring":"","eventdefinitionref":"","eventdefinitions":"","datainputassociations":"","datainput":"","inputset":"","bgcolor":"#d07070","trigger":"None"},"stencil":{"id":"EndNoneEvent"},"childShapes":[],"outgoing":[],"bounds":{"lowerRight":{"x":568,"y":188},"upperLeft":{"x":540,"y":160}},"dockers":[]},{"resourceId":"oryx_7B06541E-B5D4-4137-8289-C95A1D381305","properties":{"name":"","documentation":"","auditing":"","monitoring":"","conditiontype":"None","conditionexpression":"","isimmediate":"","showdiamondmarker":""},"stencil":{"id":"SequenceFlow"},"childShapes":[],"outgoing":[{"resourceId":"oryx_016D2181-B4CB-4DB1-ACA5-3D27BCF68A83"}],"bounds":{"lowerRight":{"x":539.69922845313,"y":174.4135635064},"upperLeft":{"x":460.12108404687,"y":174.0629989936}},"dockers":[{"x":20.5,"y":20.5},{"x":14,"y":14}],"target":{"resourceId":"oryx_016D2181-B4CB-4DB1-ACA5-3D27BCF68A83"}}],"bounds":{"lowerRight":{"x":1485,"y":1050},"upperLeft":{"x":0,"y":0}},"stencilset":{"url":"\/beta\/ci\/jscript\/bpm-dna2\/stencilsets\/bpmn2.0\/bpmn2.0_2.json","namespace":"http:\/\/b3mn.org\/stencilset\/bpmn2.0#"},"ssextensions":[]};
			MOVI.init(
				function() {

					modelviewer = new MOVI.widget.ModelViewer("modelviewer");
					modelviewer.loadModel("bpm/repository/movi/model/{idwf}/",
						{ onSuccess: init }
					);

				},
				"{base_url}jscript/movi/src",
				undefined,
				["resize"]
			);

			function init() {
				setUpUI();
				doStuffWithModel();
			}

			function setUpUI() {

				// create model navigator
				var	modelnavigator = new MOVI.widget.ModelNavigator("modelnavigator", modelviewer);

				// create toolbar
				toolbar = new MOVI.widget.Toolbar("toolbar", modelviewer);
				toolbar.addButton({
				    icon: "http://bpt.hpi.uni-potsdam.de/pub/TWiki/OryxSkin/hpi.png",
				    tooltip: "my custom button",
				    group: "Group 1",
				    callback: function() { alert("hello world!") }
				});
				toolbar.addButton({
				    icon: "http://bpt.hpi.uni-potsdam.de/pub/TWiki/OryxSkin/hpi.png",
				    tooltip: "my custom button2",
				    group: "Group 1",
				    callback: function() { alert("hello world 2!") }
				});
				toolbar.showGroupCaptions();

				// create zoom slider
				zoomslider = new MOVI.widget.ZoomSlider("zoomslider", modelviewer, {orientation: "vertical"});
			/*
				// enable resizing
				var resize = new YAHOO.util.Resize("modelviewer", {
		            handles: ['b'],
		            minHeight: 150
		        });
				resize.on("resize", function() {
				    modelnavigator.update.call(modelnavigator);
				    zoomslider.onChange.call(zoomslider);
				}, this, true);
				resize.reset();*/
			}

			function doStuffWithModel() {
				// set a marker
				var marker = new MOVI.util.Marker(
					modelviewer.canvas.getShape("oryx_B5C6771E-D9F5-4275-B6A8-4B75C0C1B5A1"),
					{"border": "2px solid red"}
				);
				return;

				// attach an annotation to the marker
				var annotation = new MOVI.util.Annotation(
					marker,
					"&lt;p&gt;This is an annotation.&lt;/p&gt;"
				);
				annotation.show();

				// set another marker and attach an icon
				var marker2 = new MOVI.util.Marker(
					modelviewer.canvas.getShape("oryx_EE31AEEA-63F9-481A-A8D0-E231E06129DD"),
					{"border": "2px solid blue"}
				);
				marker2.addIcon("northwest", "http://bpt.hpi.uni-potsdam.de/pub/TWiki/OryxSkin/hpi.png");

				// enable shape selection
				var multiselect = true;
				var selection = new MOVI.util.ShapeSelect(modelviewer, multiselect);
				selection.onSelectionChanged(function() {
					var resourceIds = "";
					for(var i = 0; i &lt; selection.getSelectedShapes().length; i++) {
						resourceIds += selection.getSelectedShapes()[i].resourceId + ", "
					}
					if(console) console.log("Selected shapes: " + resourceIds);
				})

				// show fullscreen
				var fullscreenViewer = new MOVI.widget.FullscreenViewer(modelviewer);
				toolbar.addButton({
				    icon: "arrow_out.png",
				    caption: 'fullscreen',
				    tooltip: 'View the model in fullscreen mode',
				    group: 'View options',
				    callback: fullscreenViewer.open,
				    scope: fullscreenViewer
				});
			}

		</script>

    </body>
</html>