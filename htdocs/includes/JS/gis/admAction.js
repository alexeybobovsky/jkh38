function gisObject()	
	{
	this.type;
	this.template;
	this.info;
	this.pointCnt;
	this.pointLimit;
	this.layer;
//	this.points = [];
	this.figure;
	this.points = new Array();
	this.vector = new Array();
	this.addPoint = function(index, lat, lng)
		{
		this.index = index;
		this.lat = lat;
		this.lng = lng;
		$('#pointLng_' + index).val(lng);
		$('#pointLat_' + index).val(lat);
		$('#pointDelete_' + index).show();
		}
	this.init = function(lId)
		{
		
		if(lId === undefined)
			this.layer = 0;
		else
			this.layer  = lId;
		this.type = 0;
		this.pointLimit = 1;
		this.template = 0;
		this.info = '';
		this.pointCnt = 0;
		}
	this.clearNewObj = function()
		{
		this.vector = [];		
		this.points = [];		
		this.pointCnt = 0;
		this.type = 0;
		this.pointLimit = 1;
		this.template = 0;
		this.info = '';
		delete this.figure;
		}
	this.pointRemove = function(e)
		{
//		console.log(showProperties(e.originalEvent.target));		
		var indx =  (e.id === undefined) ? ROUT.GetStrPrt(e.originalEvent.target.id, '_', 1): ROUT.GetStrPrt(e.id, '_', 1) ;
		this.points.splice(indx, 1);
		this.pointCnt--;
/*		MNG.ULgenObjPointList();*/
		MNG.ULgenObjMarkerList();
		MNG.ULgenObjFigure();				
		allow = ((this.pointLimit>0)&&(this.pointCnt >= this.pointLimit)) ? 0 : 1;	
		MNG.ULsetCursorForAddObj(allow);
		MNG.ULgenPointCounterTtl();
		if(this.pointCnt<1)
			{
			$("#noPointsMessage").show();
			$("#totalPointsMessage").hide();
			}
		}
	this.pointDragEnd = function(e)
		{
//		console.log('old: '+ OBJ.points[e.target.options.pointIndex].lat + '; '+ OBJ.points[e.target.options.pointIndex].lng);
		OBJ.points[e.target.options.pointIndex].lng = e.target._latlng.lat;
		OBJ.points[e.target.options.pointIndex].lat = e.target._latlng.lng;
		$('#pointLng_' + e.target.options.pointIndex).val(e.target._latlng.lat);
		$('#pointLat_' + e.target.options.pointIndex).val(e.target._latlng.lng);
//		console.log('new: '+ OBJ.points[e.target.options.pointIndex].lat + '; '+ OBJ.points[e.target.options.pointIndex].lng);
//		$('#pointDelete_' + e.target.options.pointIndex).show();
		MNG.ULgenObjFigure();				
		}
	this.setType = function(type)
		{
//		console.log('[setType]: type == ' + type);
		this.type = type;
		var allow;
		switch(type)
			{			
			case '0' : //точка
				{
				this.pointLimit = 1;
				//allow = (this.pointCnt >= this.pointLimit) ? 0 : 1;	
				} break;
			case '1' : //Ломаная
				{
				this.pointLimit = 0;
				//allow = 1;	
				} break;
			case '2' : //Замкнутая ломаная
				{
				this.pointLimit = 0;
				//allow = 1;	
				} break;
			case '3' : //Окружность
				{
				this.pointLimit = 1;
				//allow = (this.pointCnt >= this.pointLimit) ? 0 : 1;	
				} break;
			case '4' : //Прямоугольник
				{
				this.pointLimit = 2;
				//allow = (this.pointCnt >= this.pointLimit) ? 0 : 1;	
				} break;
			case '5' : //Сектор
				{
				this.pointLimit = 1;
				//allow = (this.pointCnt >= this.pointLimit) ? 0 : 1;	
				} break;			
			}
		allow = ((this.pointLimit>0)&&(this.pointCnt >= this.pointLimit)) ? 0 : 1;	
		MNG.ULsetCursorForAddObj(allow);
		var pointLimit = this.pointLimit;
		if((this.pointLimit!=0)&&(this.pointCnt >= this.pointLimit))
			{
			this.points.length = this.pointLimit;
			this.pointCnt = this.pointLimit;
/*			MNG.ULgenObjPointList();*/
			MNG.ULgenObjMarkerList();
			}
		MNG.ULgenObjFigure();				
		MNG.ULgenPointCounterTtl();				
		}	
	}

function gisManage()	 
	{
	this.showPointList = false;
	this.showAddObj = false;
	this.showAddLayer = false;
	this.curEdLayer = 0;
	this.curEdObj = 0;
	this.svg_html_default = '<div style="margin:0px;padding:0px;height:8px;width:8px;border-style:solid;border-color:#FFFFFF;border-width:1px;background-color:#424242"</div>';

	this.default_icon = L.divIcon({
		iconUrl: '/src/design/main/img/png/map_marker_big.png',
		iconSize: [20, 23],
		iconAnchor: [20, 23],
		className: 'leaflet-mouse-marker'
	});
	this.styleIconPink = L.icon({
		iconUrl: "/src/design/main/img/png/marker-icon-pink.png",
		iconSize: [25, 41],
		iconAnchor: [12, 41],
		popupAnchor: [0, 0],
		shadowUrl: "/src/design/main/img/png/marker-shadow.png",
		shadowSize: [41, 41],
		shadowAnchor: [12, 41]		
		});
	this.styleIconBlue = L.icon({
		iconUrl: "/src/design/main/img/png/marker-icon.png",
		iconSize: [25, 41],
		iconAnchor: [12, 41],
		popupAnchor: [0, 0],
		shadowUrl: "/src/design/main/img/png/marker-shadow.png",
		shadowSize: [41, 41],
		shadowAnchor: [12, 41]		
		});
		
	this.ULEditObj = function(objId)
		{
		console.log('ULEditObj : ' + objId);
		$('#menuLayerAddObj h3').text('Редактирование объекта');
		if(this.curEdObj != objId)
			{
			this.curEdObj = objId;
			}
		if(this.showAddObj == false)
			{
			$('.menuPad').hide();
			$('#menuLayerAddObj').show();
			if(UI.menu2PadActive() == false)
				UI.menu2PadToggle();
			else if(this.showAddLayer == true)
				this.erasePanelObj(0, true);
//			UI.menu2PadToggle();
			mymap.on('click', MNG.onMapCl_addObj);	
			MNG.ULsetCursorForAddObj(1);
			this.showAddObj 	= 	true;
			this.showAddLayer 	= 	false;
			
			}		
		}	
	this.ULEditLayer = function(obj)
		{
		console.log('ULEditLayer ' + obj.id);
		$('#menuLayerAddContaner h3').text('Редактирование слоя');
		var newLayer = ROUT.GetStrPrt(obj.id, '_', 1);
		if(this.curEdLayer != newLayer)
			{
			this.curEdLayer = newLayer;
			}
		if(this.showAddLayer == false)
			{		
			$('.menuPad').hide();
			$('#menuLayerAddContaner ').show();
			if(UI.menu2PadActive() == false)
				UI.menu2PadToggle();		
			else if(this.showAddObj == true)
				this.erasePanelObj(0, true);
			this.showAddLayer 	= 	true;			

			}
		}	
	this.onMapCl_addObj = function(e)
		{
		mymap.removeLayer(popup);
																						
		var attachedBlock = "	<div  id='pointContaner_" +OBJ.pointCnt + "' onMouseMove='alert(this.id)' class='pointContaner'>														" +	
				"		<div class='pointLeft'>                                                                             " +	
				"			<div class='pointLabel'>Точка <span id='pointNumber_" +OBJ.pointCnt + "'>" + (OBJ.pointCnt + 1) + "</span></div>		                    " +	
				"		</div>                                                                                              " +	
				"		<div class='pointMiddle'>                                                                           " +	
				"			<div  id='layerNameI" +OBJ.pointCnt + "'  class='objPropItem'>														" +
				"				<div class='objPropName'>																    " +
				"					 Долгота																			    " +
				"				</div>																					    " +
				"				<div id='layerNameC" +OBJ.pointCnt + "' class='objPropValue p_valueSelectType'>							    " +
				"					<input  id='pointLng_" +OBJ.pointCnt + "' value=''>														" +
				"				</div>																					    " +
				"			</div>																						    " +
				"			<div  id='layerNameI" +OBJ.pointCnt + "'  class='objPropItem'>													    " +
				"				<div class='objPropName'>																    " +
				"					 Широта																				    " +
				"				</div>																					    " +
				"				<div id='layerNameC" +OBJ.pointCnt + "' class='objPropValue p_valueSelectType'>							    " +
				"					<input  id='pointLat_" +OBJ.pointCnt + "'  value=''>				                                        " +
				"				</div>																					    " +
				"			</div>																							" +
				"		</div>                                                                                              " +
				"		<div class='pointRight'>                                                                            " +
				"			<div class='pointDelete' id='pointDelete_" +OBJ.pointCnt + "' title='Удалить точку'></div>	                        " +
				"		</div>                                                                                              " +
				"	</div>					                                                                                ";
		var popupContentGl = "<div class='balContaner'><div class='balMainInfo'>" + 
								"<div class='balName'>Точка №" + (OBJ.pointCnt + 1)  + "</div>" + 
								"<div>Широта: " + e.latlng.lat  + "</div>" + 
								"<div>Долгота: " + e.latlng.lng  + "</div>" + 								
								"<div><span id='pointDelPop_" +OBJ.pointCnt + "' class='activeButton' onClick='OBJ.pointRemove(this);' >Удалить</span></div>" + 								
								"</div></div>";
								
		if((OBJ.pointLimit==0)||((OBJ.pointLimit>0)&&(OBJ.pointCnt < OBJ.pointLimit)))
			{
			if(this.showPointList == true)
				{
				if($(".pointContaner").length > 0)		
					$(".pointContaner:last").after(attachedBlock);	
				else
					{

					$("#pointListContaner").append(attachedBlock);						
					}
				}
			if(OBJ.pointCnt==0)
				{
				$("#noPointsMessage").hide();
				$("#totalPointsMessage").show();
				}
//			.on("change", 	function (event) {OBJ.setType($(this).context.value);/* MNG.drawObjectForm();*/});	
			$('#pointContaner_' + 	OBJ.pointCnt).on('click', 	 function (event) {alert(event); MNG.pointHighlight(event);});
			$('#pointDelete_' + 	OBJ.pointCnt).on('click', 	 function (event) {OBJ.pointRemove(event);});
			OBJ.points[OBJ.pointCnt] = new OBJ.addPoint(OBJ.pointCnt, e.latlng.lng, e.latlng.lat);
			OBJ.vector[OBJ.pointCnt] = new L.marker(e.latlng, {/*icon: MNG.default_icon,*/ draggable: true, title:'Переместить точку №' + (OBJ.pointCnt+1), pointIndex:OBJ.pointCnt}).addTo(mymap);
			OBJ.vector[OBJ.pointCnt].bindPopup(popupContentGl);
			OBJ.vector[OBJ.pointCnt].on('dragend', OBJ.pointDragEnd);
			OBJ.vector[OBJ.pointCnt].on("mouseover",	function(event){$(event.originalEvent.target).css('cursor','move');}); //crosshair
			OBJ.pointCnt++;
			MNG.ULgenObjFigure();				
			}
		else
			{
			console.log('Overload!!! limit = ' + OBJ.pointLimit + '; cnt = ' + OBJ.pointCnt);
			}
		allow = ((OBJ.pointLimit>0)&&(OBJ.pointCnt >= OBJ.pointLimit)) ? 0 : 1;	
		MNG.ULsetCursorForAddObj(allow);
		MNG.ULgenPointCounterTtl();
		}
	this.ULsetCursorForAddObj = function(state)
		{
		var cursor;
		if(state > 0 )
			cursor = 'crosshair';
		else if(state == 0 )
			cursor = 'not-allowed';
		else
			cursor = 'grab';		
		$('#mapid').css('cursor', cursor);
		}
	this.ULgenObjFigure = function()
		{
//		console.log('Points: ' + showProperties(OBJ.points));
//		var type = OBJ.type;
		var draw = 0;
		var geometry = [];
		if(OBJ.figure !== undefined)
			{
			mymap.removeLayer(OBJ.figure);						
			}
		if((OBJ.pointLimit!=1)&&(OBJ.pointCnt>1))
			{
			switch(OBJ.type)
				{			
				case '1' : //Замкнутая ломаная -полигон
					{
					$(OBJ.points).each(function(indx, element){
						geometry[indx] = new L.latLng(OBJ.points[indx].lng, OBJ.points[indx].lat);
						});
					OBJ.figure = new L.polyline(geometry, GIS.styleBorderRed).addTo(mymap);
					} break;
				case '2' : //Замкнутая ломаная -полигон
					{
					$(OBJ.points).each(function(indx, element){
						geometry[indx] = new L.latLng(OBJ.points[indx].lng, OBJ.points[indx].lat);
						});
					OBJ.figure = new L.polygon(geometry, GIS.styleBorderRed).addTo(mymap);
					} break;
				case '4' : //Прямоугольник
					{
					var bounds = [L.latLng(OBJ.points[0].lng, OBJ.points[0].lat), L.latLng(OBJ.points[1].lng, OBJ.points[1].lat)];
					OBJ.figure = new L.rectangle(bounds, GIS.styleBorderRed).addTo(mymap);
					} break;
				default: //
					{
					} break;			
				}			
			}
		else if((OBJ.pointLimit == 1)&&(OBJ.pointCnt>0))
			{
			switch(OBJ.type)
				{			
				case '0' : //точка
					{
					OBJ.figure = new L.marker(L.latLng(OBJ.points[0].lng, OBJ.points[0].lat), {draggable: true, title:'Переместить'}).addTo(mymap);						
					} break;
				case '3' : //Окружность
					{
					var radius = 1000; //meters
					OBJ.figure = new L.circle(L.latLng(OBJ.points[0].lng, OBJ.points[0].lat), radius, GIS.styleBorderRed).addTo(mymap);
					} break;
				case '5' : //Сектор
					{
					} break;
				default: //
					{
					} break;			
				}								
			}
/*		console.log('OBJ is: ' + showProperties(OBJ));
		console.log('geometry is: ' + showProperties(geometry));*/
		
		}	
	this.ULtogglePointList = function() //вкл/выкл списка точек
		{
//		console.log(this.showPointList);
		if(!this.showPointList)
			{
			this.showPointList = true;
			$('#tpMore').removeClass('buttExpand');
			$('#tpMore').addClass('buttMinimize');
			$('#tpMore').attr('title', 'Свернуть');
			this.ULgenObjPointList();
			}
		else
			{
			this.showPointList = false;
			$('#tpMore').addClass('buttExpand');
			$('#tpMore').removeClass('buttMinimize');
			$('#tpMore').attr('title', 'Подробно');
			$(".pointContaner").slideUp(500,  function() {$(".pointContaner").remove();
			});									
												
			}
		}	
	this.ULgenPointCounterTtl = function() //генерация счётчика точек
		{
		var cntStr = (OBJ.pointCnt > 1) ? 'Установлено ' : 'Установлена ';
		cntStr += OBJ.pointCnt;
		cntStr += ROUT.getCorrectDeclensionRu(OBJ.pointCnt, ' точка', ' точки', ' точек');
		$('#tpCnt').text(cntStr);
		$('#tpLimit').text( (OBJ.pointLimit==0) ? '' : 'из ' + OBJ.pointLimit);		
		if(this.showPointList == true)
			this.ULgenObjPointList();
		}	
	this.ULgenObjMarkerList = function()
		{
		var popupContentGl = '';
		$(OBJ.vector).each(function(indx, element){
			mymap.removeLayer(OBJ.vector[indx]);
			});
		OBJ.vector.length = 0;
		$(OBJ.points).each(function(indx, element){
//			var latlngStr = OBJ.points[indx].lat+'~'+OBJ.points[indx].lng;
			popupContentGl = "<div class='balContaner'><div class='balMainInfo'>" + 
									"<div class='balName'>Точка №" + (indx+1)  + "</div>" + 
									"<div>Широта: " + OBJ.points[indx].lat  + "</div>" + 
									"<div>Долгота: " + OBJ.points[indx].lng  + "</div>" + 								
									"<div><span id='pointDelPop_" +indx + "' class='activeButton' onClick='OBJ.pointRemove(this);' >Удалить</span></div>" + 								
									"</div></div>";
			OBJ.vector[indx] = new L.marker(L.latLng(OBJ.points[indx].lng, OBJ.points[indx].lat), {draggable: true, title:'Переместить точку №' + (indx+1), pointIndex:indx}).addTo(mymap);
			OBJ.vector[indx].on('dragend', OBJ.pointDragEnd);			
			OBJ.vector[indx].on("mouseover",	function(event){$(event.originalEvent.target).css('cursor','move');}); //crosshair
			OBJ.vector[indx].bindPopup(popupContentGl);
//			console.log('str = ' + latlngStr)
			});
		
		}
	this.clearNewObj = function()
		{
		$(".pointContaner").remove();									
		$(OBJ.vector).each(function(indx, element){
			mymap.removeLayer(OBJ.vector[indx]);
			});
//		mymap.removeLayer(OBJ.figure);									
		}
	this.ULgenObjPointList = function()
		{
		$(".pointContaner").remove();									
		$(OBJ.points).each(function(indx, element){
		OBJ.points[indx].index = indx;
		var attachedBlock = "	<div  id='pointContaner_" +indx + "'  "+ 
				"onMouseOver='MNG.pointHighlight(this.id)' onMouseOut='MNG.pointDefault(this.id)' class='pointContaner'>														" +	
				"		<div class='pointLeft'>                                                                             " +	
				"			<div class='pointLabel'>Точка <span id='pointNumber_" +indx + "'>" + (indx + 1) + "</span></div>		                    " +	
				"		</div>                                                                                              " +	
				"		<div class='pointMiddle'>                                                                           " +	
				"			<div  id='layerNameI" +indx + "'  class='objPropItem'>														" +
				"				<div class='objPropName'>																    " +
				"					 Долгота																			    " +
				"				</div>																					    " +
				"				<div id='layerNameC" +indx + "' class='objPropValue p_valueSelectType'>							    " +
				"					<input  id='pointLng_" +indx + "' value='" +OBJ.points[indx].lng + "'>														" +
				"				</div>																					    " +
				"			</div>																						    " +
				"			<div  id='layerNameI" +indx + "'  class='objPropItem'>													    " +
				"				<div class='objPropName'>																    " +
				"					 Широта																				    " +
				"				</div>																					    " +
				"				<div id='layerNameC" +indx + "' class='objPropValue p_valueSelectType'>							    " +
				"					<input  id='pointLat_" +indx + "'  value='" +OBJ.points[indx].lat + "'>				                                        " +
				"				</div>																					    " +
				"			</div>																							" +
				"		</div>                                                                                              " +
				"		<div class='pointRight'>                                                                            " +
				"			<div class='pointDelete' id='pointDelete_" +indx + "' title='Удалить точку'></div>	                        " +
				"		</div>                                                                                              " +
				"	</div>					                                                                                ";
			                                                                                                                
			$("#pointListContaner").append(attachedBlock);									                                
			$('#pointDelete_' + indx).on('click', 	 function (event) {OBJ.pointRemove(event);});                           
//			$('#pointContaner_' + 	indx).on('mousemove', 	 function (event) {MNG.pointHighlight(event);});
//			$('#pointContaner_' + 	indx).on('mouseout', 	 function (event) {MNG.pointDefault(event);});
			});	                                                                                                            
			
		}
	this.ULAddObj = function(obj) //нажатие кнопки добавления объекта
		{
		$('#menuLayerAddObj h3').text('Добавление нового объекта к слою');
		if(this.showAddObj == false)
			{
			$('.menuPad').hide();
			$('#menuLayerAddObj').show();
			if(UI.menu2PadActive() == false)
				UI.menu2PadToggle();		
//			UI.menu2PadToggle();
			mymap.on('click', MNG.onMapCl_addObj);	
			MNG.ULsetCursorForAddObj(1);
			this.showAddObj 	= 	true;
			this.showAddLayer 	= 	false;
			
			}
		
		}	
	this.ULAddLayer = function(layer) //нажатие кнопки добавления слоя
		{		
		if(layer === undefined)
			{}
		$('.menuPad').hide();
		$('#menuLayerAddContaner ').show();
		if(UI.menu2PadActive() == false)
			UI.menu2PadToggle();		
		else if(this.showAddObj == true)
			this.erasePanelObj(0, true);
		this.showAddLayer 	= 	true;		
		$('#menuLayerAddContaner h3').text('Создание нового слоя');
		}
	
	this.initElements = function()
		{
		//console.log('init');
		$('#menuLayerAddContaner').width($('#menu2Pad').innerWidth());
		$('#layerAddSubmit').bind("click", 		function () {MNG.submitAddLayerForm(actionAL)});
		$('#objAddSubmit').bind("click", 		function () {console.log('go!'); MNG.submitAddObjForm(actionAO)});
/*		$('#menu2Pad .closeBtn').bind("click", 	function (event) {
			MNG.erasePanelObj(1, true);	
			});*/
		$('#layerAddButton').bind("click", 	function (event) {MNG.ULAddLayer()});			
		}
	this.checkAddLayerForm = function()
		{}
	this.submitAddObjForm = function(action)
		{		
		UI.pleaseWait();
		var oName = 	$('#objName').val();
		var oType = 	$('#objType').val();
		var oAbout = 	$('#objAbout').val();
		var oTemplate = $('#objTemplate').val();
		var oSpec = '';
//		console.log('oName - ' + oName + '; oAbout' + oAbout);
		
		$.post(action, {lId:UL_selected, oName:oName, oType:oType,  oAbout:oAbout,  oSpecParam:oSpec, oTemplate:oTemplate,  points:OBJ.points}, function(str) 
			{
				
			UI.pleaseWait();
			console.log(str);
			var pId, pName;
			var errStr = ROUT.GetStrPrt(str, '<error!/>', 1);
//			console.log(errStr);
			if(errStr == undefined)
				{		
				if(str.indexOf('<delim!/>') >=0) 
					{
					var parArr = str.split('<delim!/>');
					pId = parArr[0];
					pName = parArr[1];
					}
				else
					{
					pId = str;
					pName = oName;						
					}
//				var UObject;
				var UObject = OBJ.figure;
				UObject['id'] = 		pId;
				UObject['name'] = 		pName;
				UObject['type'] = 		oType;
				UObject['info'] = 		oAbout;
				UObject['spec'] = 		oSpec;
				UObject['template'] = 	oTemplate;
				UObject['lId'] = 		UL_selected;
				UObject['state'] = 		1;						//фигура включена
				UObjects.push(UObject);
				MNG.erasePanelObj(1, false);
			/*	MNG.clearNewObj();
				OBJ.clearNewObj();
				MNG.ULsetCursorForAddObj(-1);	//если не активировано продолжение....
				UI.menu2PadToggle();    		//если не активировано продолжение....*/
				
				GIS.toggleAllObjListUL(UL_selected);
				}
			else
				{	
				UI.showMessage('error', errStr);
				}
			});	
		}
	this.submitAddLayerForm = function(action)
		{		
		UI.pleaseWait();
		var lName = $('#layerName').val();
		var lPublic = ($('#layerIsPublic').prop('checked')) ? 1 : 0; 
		console.log('lName - ' + lName + '; lPublic' + lPublic);
		$.post(action, {lName:lName, lPublic:lPublic}, function(str) 
			{
			UI.pleaseWait();
			if(str == '' )
				{
				MNG.erasePanelObj(1, true);	
				}
			else
				{	
				UI.showMessage('error', str)
				console.log(str)
				}
			});	
		}
	this.pointHighlight = function(id)
		{
//		var indx =  (e.id === undefined) ? ROUT.GetStrPrt(e.originalEvent.target.id, '_', 1): ROUT.GetStrPrt(e.id, '_', 1) ;
		var indx =  ROUT.GetStrPrt(id, '_', 1) ;
//		console.log(showProperties(e.originalEvent.target));		
		if	( OBJ.vector[indx]=== undefined) {}
		else  
			{
//			console.log('pointHighlight ' + indx);		
			OBJ.vector[indx].setIcon(MNG.styleIconPink);
			}
		}
	this.pointDefault = function(id)
		{
		var indx =  ROUT.GetStrPrt(id, '_', 1) ;
		if	( OBJ.vector[indx]=== undefined) {}
		else  
			{
			OBJ.vector[indx].setIcon(MNG.styleIconBlue);
			}
		}
	this.erasePanelObj = function(panelHide, figureDel)
		{
		if(this.showAddObj == true)
			{
			MNG.clearNewObj();
			if((figureDel == true)&&(OBJ.figure !== undefined))
				mymap.removeLayer(OBJ.figure);
			OBJ.clearNewObj();
			MNG.ULsetCursorForAddObj(-1);			//если не активировано продолжение....
			mymap.off('click', MNG.onMapCl_addObj);	//если не активировано продолжение....

			$("#noPointsMessage").show();
			$("#totalPointsMessage").hide();
			this.showAddObj = false;
			}
		else if(this.showAddLayer == true)
			{
			this.showAddLayer = false;
			}
		if(panelHide>0)
			{
			$('.menuPad').hide();
			UI.menu2PadToggle();			
			}

		}
	this.uploaded_GEOJSON = function(id, fileName, responseJSON) //загружен файл GEOJSON
		{
		console.log(showProperties(responseJSON, 'responseJSON'));
//		console.log(showProperties(responseJSON.GeoCollection[0], 'responseJSON'));
		if(responseJSON['success'])
			{
			$(responseJSON.GeoCollection).each(function(indx, element){			
						if(element != undefined)
							{
							var obj = element;
							var figure;
							var pointCnt = 0;
							var points = [];
							var pId = 10000;
							$('#objName').val(obj.oName);
							
							$(obj.geo).each(function(indx, element){		
							if(element != undefined)
								{
								var point = element;
//								points[indx] = new OBJ.addPoint(OBJ.pointCnt, point.lng, point.lat);
								points[indx] = new L.latLng(point.lng, point.lat);

//							console.log(obj);
/*							if((obj.lStr.indexOf('_' + lId) >= 0)||(obj.lParentStr.indexOf('_' + lId) >= 0))
								{
								lArr[counter] = obj;
								counter++;
								}*/
								}
								});
/*							console.log(points);
							console.log(obj.type);*/
							switch(obj.oType)
								{			
								case 0 : //точка
									{
									figure = new L.marker(L.latLng(obj.geo[0].lng, obj.geo[0].lat), {draggable: true, title:'Переместить'}).addTo(mymap);						
									} break;
								case 1 : //
									{
									figure = new L.polyline(points, GIS.styleBorderRed).addTo(mymap);
									} break;
								case 2 : //Замкнутая ломаная -полигон
									{
									figure = new L.polygon(points, GIS.styleBorderRed).addTo(mymap);
									console.log(figure);
									
									} break;
								case 3 : //Окружность
									{
									var radius = 1000; //meters
									figure = new L.circle(L.latLng(obj.geo.lng, obj.geo.lat), radius, GIS.styleBorderRed).addTo(mymap);
									} break;
								case 4 : //Прямоугольник
									{
									var bounds = [L.latLng(OBJ.points[0].lng, OBJ.points[0].lat), L.latLng(OBJ.points[1].lng, OBJ.points[1].lat)];
									figure = new L.rectangle(bounds, GIS.styleBorderRed).addTo(mymap);
									} break;
								case 5 : //Сектор
									{
									} break;
								default: //
									{
									} break;			
								}
							var UObject = figure;
							UObject['id'] = 		obj.oId;
							UObject['name'] = 		obj.oName;
							UObject['type'] = 		obj.type;
							UObject['info'] = 		'';
							UObject['spec'] = 		'';
							UObject['template'] = 	'';
							UObject['lId'] = 		obj.layerId;
							UObject['state'] = 		1;						//фигура включена
							UObject['lStr'] =  		obj.lStr;	
							UObject['lParentStr'] =  obj.lParentStr;	
/*							$(ULayers).each(function(indx, element){
								if(element.id == obj.layerId)
									{
//									console.log('str = ' + element.parStr);
									UObject['lParentStr'] =  element.parStr;	
//									console.log(element['lStr']);
									}
								});*/
							UObjects.push(UObject);
							MNG.erasePanelObj(1, false);
							}
							
						});		
			
			}
		}		
	}

///////////загрузчики файлов////////////	
function createFileUploadParam(title, name)
	{
	this.title = title; 
	this.name =  name;
	}  
function createFileUploader(title, type, action)
	{
//	alert(title + ' ' + type + ' ' + action);
	this.obj =  new qq.FileUploader({
				element: document.getElementById('file_' + type),
				action: action,
	/*		onComplete: function(id, fileName, responseJSON){alert(fileName)},*/
//				allowedExtensions: ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf'],
				allowedExtensions: ['js', 'json', 'ini'],
				buttonLabel: title,
				onComplete: function(id, fileName, responseJSON){MNG.uploaded_GEOJSON(id, fileName, responseJSON)},
				onSubmit: function(id, fileName){this.params.layerId = ULLayerOver.id},
				
				type: type,
				sizeLimit: 7000000, // max size   
				debug: false,
				params: {
						element: type, 
						layerId: 0
						}
			/*});*/}); 	
	}  
