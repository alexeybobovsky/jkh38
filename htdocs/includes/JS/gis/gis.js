function addLayers(param)
	{
/*	if(param[0] == 5532){
		console.log('find!!!!')
		console.log(param)
	}*/
	this.id = 			param[0];
	this.name = 		param[1];
	this.info = 		param[2];
	this.isPublic = 	param[3];
	this.isSystem = 	param[4];
	this.parId	 = 		param[5];
	this.childCnt = 	param[6];
	this.objCnt = 		param[7];
	this.parStr = 		param[8];
	this.message = 		[];
	this.messageArch = 	[];
	this.messageAll = 	[];
	}	
function addGeo(param)
	{
	this.id = 			param[0];
	this.lat = 			param[1];
	this.lng = 			param[2];
	this.oId = 			param[3];
	}	
function activeLayer(id, index) /*активный слой, для которого нажата кнопка "доп. действий"*/
	{
	this.id = 			id;
	this.index = 		index;
	}	
function setLayerFilter(id, index) /*активный слой, для которого нажата кнопка "доп. действий"*/
	{
	this.id = 			id;
	this.index = 		index;
	}	
function remoteFigure()
	{ /*объект, геометрию которого грузим только по требованию*/	
	this.remote = true;
	}	
function objOrder(a, b)
	{
		return a['order'] - b['order'];
	}
function gisRoutine()	
	{
	var _bounds = { east: 0,  west: 0,  south: 0,  north: 0};
	var _loadingCoord = 0; //метка загрузки (0 - не активна, 1 - началась, 2 - завершена)	
	var _fitToBounds = false;//выставляется в true когда надо привести к границам
	var _styleUpdate = '';//выставляется при загрузке геометрии границ районов - для устранения бага
	
	var _setBounds = function(bounds){			//2017_11_17 сохранение общих границ текущих объектов со state = 1
		_bounds.east = 	((_bounds.east == 0)||(_bounds.east < bounds[0])) ? bounds[0] : _bounds.east;
		_bounds.west = 	((_bounds.west == 0)||(_bounds.west > bounds[1])) ? bounds[1] : _bounds.west;
		_bounds.south = ((_bounds.south == 0)||(_bounds.south > bounds[2])) ? bounds[2] : _bounds.south;
		_bounds.north = ((_bounds.north == 0)||(_bounds.north < bounds[3])) ? bounds[3] : _bounds.north;
//	console.log('_setBounds');
//	console.log(bounds);
		
	}
	var _getBounds = function(){			//2017_11_17 возврат общих границ текущих объектов со state = 1
		console.log('_getBounds');
		return _bounds;
	}
	var _dropBounds = function(){			//2017_11_17 обнуление общих границ текущих объектов со state = 1
		_bounds = { east: 0,  west: 0,  south: 0,  north: 0};
	}
	var _fitBounds = function(){			//2017_12_05 применение границ к карте
		mymap.fitBounds(L.latLngBounds([_bounds.north, _bounds.west], [_bounds.south, _bounds.east]));				
	}
	var _setObjPar = function(obj, attrArr)			//2017_10_17 Присвоение объекту аттрибутов не связанных с геометрией
		{
//		var obj[];
		if(attrArr['id'] != undefined){
			
			obj['id'] = 			attrArr['id'];
			obj['name'] = 		    attrArr['name'];
			obj['type'] = 		    attrArr['type'];
			obj['info'] = 		    attrArr['info'];
			obj['spec'] = 		   	attrArr['spec'];
			obj['template'] = 	    attrArr['template'];
			obj['lStr'] = 		    attrArr['lStr'];
			obj['lParentStr'] =	    attrArr['lParentStr'];
			obj['order'] 	=	    attrArr['order'];
			obj['state'] = 			attrArr['state']; 			//фигура отключена
			obj['style'] =			attrArr['style'];
			obj['redraw'] =			attrArr['redraw'];
			obj['colorFill'] =		attrArr['colorFill'];
			obj['colorHeat'] =		attrArr['colorHeat'];
			obj['objCnt'] =			attrArr['objCnt'];
			obj['isSystem'] =		attrArr['isSystem'];
			obj['isDistrBorder'] =	attrArr['isDistrBorder'];
		}
		else if (attrArr[0] != undefined){
			obj['id'] = 		attrArr[0];			
			obj['name'] = 		attrArr[1];    
			obj['type'] = 		attrArr[2];    
			obj['info'] = 		attrArr[3];    
			obj['spec'] = 		attrArr[4];    
			obj['template'] = 	attrArr[5];    
			obj['lStr'] = 		attrArr[6];    
			obj['lParentStr'] =	attrArr[7];    	
			obj['order'] 	=	attrArr[8];
			obj['state'] = 		0;							//фигура отключена
			obj['style'] =			{};
			obj['redraw'] =			false;
			obj['colorFill'] =		'';
			obj['colorHeat'] =		'';
			obj['objCnt'] =			0;
			obj['isSystem'] =		false;
			obj['isDistrBorder'] =	false;
		}
		if((obj['remote'] == undefined)||(obj['remote'] == false)){
			
			var balCont = obj['info'];
			var layArr = obj['lStr'].split('_');
			var counter = 0;
			balCont += ((obj.type == 5)||(obj.isDistrBorder))? "<p><span  class='layerBalZoom activeLink' id='zoom_"+ obj.id + "' >Вписать объект в масштаб</span></p>":'';	
//			balCont += ((obj.type == 5)||((obj.type == 0) && (obj.spec != '')))? "<p><span  class='sectorBalShow activeLink' id='sector_"+ obj.id + "' > скрыть сектор </span></p>":'';	
			if((obj.type == 5)||((obj.type == 0) && (obj.spec != ''))){
				var lbl, lblAll, actAll;
				if(obj.type == 5){
					lbl = 'Скрыть сектор';
					lblAll = 'Скрыть все сектора';
					actAll = '0';
				} else {
					lbl = 'Отобразить сектор';
					lblAll = 'Отобразить все сектора';
					actAll = '1';					
				}
				balCont += "<p><span  class='sectorBalShow activeLink' id='sector_"+ obj.id + "' > "+ lbl + " </span></p>";
				balCont += "<p><span  class='sectorBalShowAll activeLink' id='sectorAll_" + actAll + "' > "+ lblAll + " </span></p>";
			}
			$(layArr).each(function(indx, element){
				if(element!=''){
					var lIndex = ULayerIndex[element];
					if(ULayers[lIndex] != undefined){
						var parentRootId = ROUT.GetStrPrt(ULayers[lIndex].parStr, '_', 2);
						if(parentRootId != undefined){
							balCont += (counter>0)? '' : '<h3>Классификация</h3>'
							var parentRoot = ULayers[ULayerIndex[parentRootId]];
							balCont += "<p><span class='layerRoot'>" +parentRoot.name+  " </span> : "+	
										"<span  class='layerBal activeLink' id='layerBal_" + ULayers[lIndex].id + "' >"+ ULayers[lIndex].name+ "</span></p>";	
							counter++;
						}
					}
					
				}
			});		
		obj.bindPopup(balCont);
		obj.bindTooltip(obj['name']);
//		console.log(obj.popup);
		}
			
	}

	var _drawFigure = function(typeS, coordSingle, info, template, objStyle = {}, spec = '')			//2017_10_17 создание объекта с геометрией
		{
			var figure;
//			console.log(arguments);
//			if(objStyle == undefined)
//			console.log('length = ' + Object.keys(objStyle).length);
//			if(objStyle.length == 0){
			if(Object.keys(objStyle).length == 0){
				if(template == 'system'){
//					console.log('system');					
					style = {
										"color": "#888",
										"weight": 1,
										"fillColor": "#ccc",
										"fillOpacity": 0.8,
										"opacity": 0,
										"smoothFactor": 1,
										"noClip" : true
									};
				} else {
					style = {
											"color": "#ee4444",
											"weight": 1,
											"opacity": 0.8,
											"fillOpacity": 0.1,
											"smoothFactor": 1,
											"noClip" : true
									};
				}
			} else {
				style = objStyle;
			}			
//			console.log('objStyle is ');
//			console.log(objStyle);
				
//			console.log(type);
			let type	= parseInt(typeS);
			switch(type){
//			case '0' : //точка
			case 0 : //точка
				{
//				console.log('0');
//				console.log(type);
				//popupContent = strArr2Param[3];
				figure = new L.marker(coordSingle[0], {/*icon: MNG.default_icon,*/ draggable: false, /*title:'Переместить точку №' + (OBJ.pointCnt+1), pointIndex:OBJ.pointCnt*/});
//				console.log(figure)
//				figure.bindPopup(info);
				} break;
//			case '1' : //
			case 1 : //
				{
				figure = new L.polyline(coordSingle, style);//.addTo(mymap);
//				console.log(figure);
				} break;
//			case '2' : //полигон
			case 2 : //полигон
				{

					
//									figure = new L.polygon(coordSingle, GIS.styleBorderRed).addTo(mymap);
//				figure = new L.polygon(coordSingle, style).addTo(mymap);
				figure = new L.polygon(coordSingle, style);
//				console.log(figure);
				} break;
//			case '3' : //Окружность
			case 3 : //Окружность
				{
				var radius = 10000; //meters
				figure = new L.circle(coordSingle[0], radius, GIS.styleBorderRed);//.addTo(mymap);
				} break;
//			case '4' : //Прямоугольник
			case 4 : //Прямоугольник
				{
				figure = new L.rectangle(coordSingle, GIS.styleBorderRed);//.addTo(mymap);
			} break;
//			case '5' : //Сектор
			case 5 : //Сектор
				{					
/*				console.log('5');
				console.log(type);*/
				var azimuth, angle, radius, hAngle;
				if(spec != ''){
					var specArr = spec.split('_');
					azimuth = parseInt(specArr[0]); 
					angle =  parseInt(specArr[1]);
					
				} else {
					azimuth = 0; 
					angle = 90;				
				}
				hAngle = parseInt(angle/2);
				radius = 4000; 				
				style.radius = radius; 
				if (angle<360) 
					figure = new L.semiCircle(coordSingle[0], style).setDirection(azimuth, angle);//.addTo(map);
				else{
					style.startAngle = 		0;
					style.stopAngle = 		360;						
					figure = new L.semiCircle(coordSingle[0], style);
				}
//				figure = circle(coordSingle[0], {renderer: canvas, color: '#0ff'}).setDirection(i * 30, 20).addTo(map);
//				console.log(figure);
				
			} break;
			default: //
				{
					
				} break;			
			}
//		figure.bindPopup('empty');
		return figure;
		}
		
	var _getRemoteGeometry = function(oId, bound = 0)		//2017_10_15 загрузка геометрии объекта
		{
		UI.pleaseWait();
//		console.log('bound is ' + bound);
		var layerDelim = '~~';
		var paramDelim = '##';
		var coordSingle = [];
		var multy = '<mlt>';
		var objId = 0;
		var	objDelim = '||';
		var coordBound = [];
//		console.log(UObjects[oId]);
		$.post("/spddl/", {type:'ULObjectGeo', oId:oId}, function(str) {
			if (str.indexOf(multy) >= 0){
				strClr = ROUT.GetStrPrt(str, multy, 1) ;
				strArrO = strClr.split(objDelim); //объекты
				$(strArrO).each(function(indx, element){
					if(strArrO[indx].length > 10)
						{
//						console.log('mult');
						strArrPoints = element.split(layerDelim); //точки
						$(strArrPoints).each(function(indx, element){
							if(strArrPoints[indx].length > 10){
								strArrP = strArrPoints[indx].split(paramDelim);  //координаты
								_setBounds([parseFloat(strArrP[1]), parseFloat(strArrP[1]), parseFloat(strArrP[0]), parseFloat(strArrP[0])]);
								coordSingle.push( new L.latLng(strArrP[0], strArrP[1]));										
								objId = strArrP[4];
							}							
						});
//						console.log(UObjects[objId]);
						var figure = _drawFigure(UObjects[objId]['type'], coordSingle, UObjects[objId]['info'], 1, UObjects[objId]['style'], UObjects[objId]['spec']); 
						if(_styleUpdate == 'distrFill'){						
							figure.setStyle({fillColor: UObjects[objId].colorFill});
							UObjects[objId].style.fillColor = UObjects[objId].colorFill;
						}
						else if(_styleUpdate == 'distrFillHeat'){
							figure.setStyle({fillColor: UObjects[objId].colorHeat});
							UObjects[objId].style.fillColor = UObjects[objId].colorHeat;
						}							
						_setObjPar(figure, UObjects[objId]);
						UObjects[objId] = figure;
						UObjects[objId]['remote'] = false;
						if(UObjects[objId].type>0){
							mymap.addLayer(figure);
						}
						else{
							ULCluster.addLayer(UObjects[objId]);			
						}
						UObjects[objId].state = 1;
						$('#objUL_' + UObjects[objId].id).addClass('blacked');
//						console.log(UObjects[objId].name);	
//						console.log(UObjects[objId].style);	
						}
					if(bound>0) coordBound = coordBound.concat(coordSingle);
					
					coordSingle = [];
					});
				//console.log(str);
				UI.pleaseWait();

			} 
			else {
				strArrO = str.split(layerDelim); 
				$(strArrO).each(function(indx, element){
					if(strArrO[indx].length > 10)
						{
//						console.log('single');
						strArrP = strArrO[indx].split(paramDelim); 
						_setBounds([parseFloat(strArrP[1]), parseFloat(strArrP[1]), parseFloat(strArrP[0]), parseFloat(strArrP[0])]);
						coordSingle.push( new L.latLng(strArrP[0], strArrP[1]));		
	//					console.log(strArrP);
						}
					});
				var figure = _drawFigure(UObjects[oId]['type'], coordSingle, UObjects[oId]['info'], 1, UObjects[oId]['style'], UObjects[oId]['spec']); //type, coordSingle, info, template
				_setObjPar(figure, UObjects[oId]);
				UObjects[oId] = figure;
				UObjects[oId]['remote'] = false;
				//console.log(UObjects[oId]);
	//			console.log(UObjects[oId]);
				if(UObjects[oId].type>0){
					mymap.addLayer(figure);
				}
				else{
					ULCluster.addLayer(UObjects[oId]);			
				}
	//			console.log(ULCluster);			
				UObjects[oId].state = 1;
				$('#objUL_' + UObjects[oId].id).addClass('blacked');
				if(bound>0) coordBound = coordSingle;
				UI.pleaseWait();
			}
			if(bound>0) {
				_fitBounds();
			}
		_styleUpdate = '';
		_loadingCoord = 2;	

		});
		}


/*2017_12_27 генерация списка отфильтрованный слоёв*/
	var _genFilteredList = function(startId){
		var htmlCont = '';
		var layer, parentList;
		LF.list.forEach(function(element, indx, arr){
			parentList = 'ULParent_';
			element.parents.forEach(function(element, indx, arr){
				parentList += '_'+layer.parId;
				layer = ULayers[ULayerIndex[element]];
				console.log('lvl = ' + indx + '; name' +layer.name);
				htmlCont += 	"	<div class='userLayerContaner " +parentList + " inFilter' id='ulH_" + layer.id + "'>															"+
							"		<div class='accHIconCont ULlvl_" + indx + "' >";
                                             							
				htmlCont += 	(layer.childCnt > 0) ?	"			<span class='accHIcon accHIconDsbl'></span>" : "";
							
				htmlCont += 	"		</div>                                                                  							"+
							"		<div class='lbl'>" + layer.name + "</div>                        							"+
							"		<div class='switchContaner' id='switchContUL_" + layer.id + "' >                                           							"+
							"			<input type='checkbox' class='js-switch' id='switchUL_" + layer.id + "' checked   />"+
							"		</div>                                                                  							"+
							"		<div class='iconContaner'  >                                            							"+
							"			<div id='layerOptUL_" + layer.id + "' class='buttOptions' title='Действия'      							"+
							"onClick='GIS.toggleActionPanelLayerUL(\"ulH_" + layer.id + "\", " +indx + ", 1);' ></div>												"+
							"		</div>                                                                                              "+
							"	</div>                                                                                                  ";
				
			});
		});
		
		return htmlCont;		
	}
	this.home = [];
	this.overSingleObj = 0;
	this.searchedSingleObj = 0;

	this.styleBorderBackground = {
											"color": "#888",
											"weight": 1,
											"fillColor": "#ccc",
											"fillOpacity": 0.8,
											"opacity": 0,
											"smoothFactor": 1,
											"noClip" : true
										};
	this.styleReg = {
											"color": "#222222",
											"fillColor": "#0000ff",
											"weight": 1,
											"fillOpacity": 0.2,
											"opacity": 0.5
										};
	this.styleBorderRed = {
											"color": "#ee4444",
											"weight": 1,
											"opacity": 0.8,
											"smoothFactor": 1,
											"noClip" : true
										};
	this.styleBorderBlue = {
											"color": "#4444ee",
											"weight": 1,
											"opacity": 0.8,
											"smoothFactor": 1,
											"noClip" : true
										};
	var styleBorderLight = {
											"color": "#888888",
											"weight": 1,
											"opacity": 0.8,
											"smoothFactor": 1,
											"noClip" : true
										};
	var styleDistrFill = {
											"color": "#222222",
											"fillColor": "#0000ff",
											"weight": 1,
											"fillOpacity": 0.1,
											"opacity": 0.5
										};
	var styleSector = {
											"color": "#0078ff",
											"fillColor": "#00ff00",											
											"weight": 1,
											"fillOpacity": 0.2,
											"opacity": 0.5
										};
/*2018_07_03 генерация списка отфильтрованных объектов*/
	this.drawFigure = function(obj, type, coord, info, template,  objStyle = {}, spec = ''){
		var tmpObj = _drawFigure(type, coord, info, 1, objStyle, spec); 
		_setObjPar(tmpObj, obj);
		return tmpObj;	
	}
	this.showFilteredObjList = function(){
		let cnt = 0;
		if($('#objListBox').css('display') == 'none') {
	//		$('#objListBox').css({'display':'block', 'width':  UI.windowWidth/3,  'height':  UI.documentHeight-200});
			$('#objListBox').css({'display':'block',  'height':   $('#mapid').height() - $('#toolBox').height()- 70});
			$('#objListBox .content').css({ 'height':   $('#mapid').height() - $('#toolBox').height()- 120});
	//		UI.toCenter('objListBox');
			var str = '';
			$(UObjects).each(function(indx, element){
				if(element != undefined)
					{
					var obj = element;
					if((obj.state == 1)&&(!obj.isSystem)){
						cnt ++;
						str += "<li id='objlFtr_" + obj.id + "'><div class = 'contObjList'><nobr>";
						str += "<span class='activeLink' id='objSp_" + obj.id + "' onClick='GIS.showObjUL(\"" + obj.id +"\");'";
						str += "title='Приблизить объект'>" + obj.name + "</span></nobr></div></li>";
					}
				}
			});	

	//	str += '</div>';
		if(str != '') str = '<ul>' + str + '</ul>';				
		$('#objListBox .content').html(str);
//		$("#objListBox .content").niceScroll({cursorcolor:"#cfcfcf", cursorwidth:"4px"});

		} else{
			$('#objListBox').hide();
		}
//	console.log('cnt = ');
//	console.log(cnt);
//		
	}
/*2018_21_02 скрыть/отобразить все сектора*/
	this.changeVectorFigureAll = function(show, opt){
		console.log(arguments);
		var coordSingle = [];
		var obj, newType, tmpObj;
		if(opt == 'sectorAll'){
			var par = '_901';
			var count = 0;
//			ULCluster.clearLayers ();									
			$(UObjects).each(function(indx, element){
				if(element != undefined){
					if((LF.isObjShowFilter(element)) && (element.lStr.indexOf(par) >= 0)/*(element.lStr == par) *//*&& (element.state == 1)*/){  /*объекты - базовые станции*/
						element.state = 0;
						if(element.type != 0)
							{
							mymap.removeLayer(element);
							}
						else
							{
							ULCluster.removeLayer(element);						
							}
						$('#objUL_' + element.id).removeClass('blacked');					
						obj = element;
						coordSingle = [];
						coordSingle.push(  obj.getLatLng());
						newType = (show == '1') ? 5 : 0; 						
						obj.type = newType;
						tmpObj = _drawFigure( newType, coordSingle, obj.info, 1, obj.style, obj.spec ); 
						_setObjPar( tmpObj, obj );
						UObjects[obj.id] = tmpObj;
						obj = UObjects[obj.id];
						if(obj.type!= 0){
							mymap.addLayer(obj);
						}
						else{
							ULCluster.addLayer(obj);			
						}
					obj.state = 1;															
					}	
				}
			});

		}
	}
/*2018_21_02 скрыть/отобразить сектор*/
	this.changeVectorFigure = function(objId, opt){
		var coordSingle = [];
		console.log(objId);
		console.log(opt);
		if(opt == 'sector'){
			var obj = UObjects[objId];
	//		var coord;
			obj.state = 0;
			if((obj.type == '0')||(obj.type == '5'))
				{
//				console.log(obj.type);
				if	(obj.type == '0')
					ULCluster.removeLayer(obj);	
				else{
					console.log('remove');
					mymap.removeLayer(UObjects[objId]);
				}
	//			coord = obj.getLatLng();
				coordSingle.push(  obj.getLatLng());
				}
			else
				{
				mymap.removeLayer(obj);
				coordSingle.push( obj.getBounds());
				}
			var newType = (obj.type == '5') ? '0' : '5'; 

			var tmpObj = _drawFigure(newType, coordSingle, obj.info, 1, obj.style, obj.spec); 
			obj.type = newType;
			_setObjPar(tmpObj, obj);
			UObjects[objId] = tmpObj;

//			UObjects[objId]['type'] = false;
//			obj = UObjects[obj.id];
			if(UObjects[obj.id].type!= '0'){
				mymap.addLayer(UObjects[obj.id]);
			}
			else{
				ULCluster.addLayer(UObjects[obj.id]);			
			}
			UObjects[obj.id].state = 1;
			
		}
/*		$(UObjects).each(function(indx, element){			
			if(element != undefined){
				
			}
		}*/
	}
/*2018_01_02 рисование границ районов*/
	this.drawDistr = function(options){
//		console.log('drawDistr');
//		console.log(options);
//		var distrLayerPar = '997';
		var remoteArr = [];
//		var remoteArrIndx = [];
		var layerDistr;
		var objCntMin = objCntMax = -1;
		$(UObjects).each(function(indx, element){			
			if(element != undefined){
				if(element.isDistrBorder)  /*объекты - районы*/
					{
					var obj = element;
					UObjects[obj.id].state = 0;
					if(obj.type>0)
						{
						mymap.removeLayer(obj);
						}
					else
						{
						ULCluster.removeLayer(obj);						
						}
					if(options == 'distrClear'){
//						console.log('distrClear');
						//только убрать
					} else{
						if(options == 'distrEmpty'){
							if(obj.type != '1'){
								UObjects[obj.id].type = '1';
								UObjects[obj.id].redraw = true;
							}
//							obj.style = {color: "#666666"}; // styleBorderLight;
//							UObjects[obj.id].style = styleBorderLight;
						} else{
							if(obj.type != '2'){
								UObjects[obj.id].type = '2';
								UObjects[obj.id].redraw = true;
							}
							element.style = styleDistrFill;
							if(options == 'distrFill'){
								element.style.fillColor = element.colorFill;
							} else if(options == 'distrFillHeat'){
								element.style.fillColor = element.colorHeat;								
							}
//						console.log(UObjects[obj.id].style.fillColor);
						}
						
						if(obj.remote == true){
							remoteArr.push(obj.id);
//							console.log(UObjects[obj.id].name + 'id = ' + UObjects[obj.id].id +'; style: ');
//							console.log(UObjects[obj.id].style);
						} else {
						//	console.log(obj);
//							obj.setStyle({color: "#88ee88"});
							if(obj.redraw == true){
								var tmpObj = _drawFigure(obj.type, obj.getLatLngs(), obj.info, 1, obj.style, obj.spec); 
								_setObjPar(tmpObj, obj);
								UObjects[obj.id] = tmpObj;
								obj = UObjects[obj.id];
							}
							obj.setStyle(obj.style);							
							mymap.addLayer(obj); 
							obj.state = 1;
						}
						
					}
				}
			}
//			}
		});
	
	if(remoteArr.length > 0){
		_loadingCoord = 1;
		_styleUpdate = options;
		_getRemoteGeometry(remoteArr, (_fitToBounds == true) ? 1 : 0);		
	}
	}
	this.balLayerClick =  function(obj) { //2017_10_16 нажатие слоя в балуне объекта - показ всех объектов слоя
		console.log(obj);
		var lIndex = ULayerIndex[obj];
		var parentArr = ULayers[lIndex].parStr.split('_');
		var timeout = null;
		var delay = 500;
		LF.clear();					//обнуляем фильтр слоёв
		GIS.toggleAllObjListUL(); 	//закрываем списки слоёв
		GIS.toggleAllObjUL();		//Убираем все объекты с карты
		parentArr.push(obj);
		$(parentArr).each(function(indx, element){
			if((element!='')&&(element>0)){
	//			console.log('element - ' + element);
				var parentLayer = ULayers[ULayerIndex[element]];
				console.log(parentLayer.id + ' - ' + parentLayer.name);
				$('#ulH_' + parentLayer.id).addClass('hEnbl')
				$('#ulH_' + parentLayer.id + ' .accHIcon').toggleClass('accHIconEnbl');
				$('#ulH_' + parentLayer.id + ' .accHIcon').toggleClass('accHIconDsbl');
				GIS.toggleAllObjListUL(parentLayer.id);
			}
		})
		$('#switchUL_' + obj).prop({"checked":true});
		$('#switchULObj').prop({"checked":true});
		
		if(ULSwitch[obj] != undefined){
			ULSwitch[obj].destroy();
			$('#ulH_' + obj + ' .switchery-min').remove();
			ULSwitch[obj] =  new Switchery(document.querySelector('#switchUL_' +obj),{ size: 'min'});	
		}
		_dropBounds();
		_fitToBounds = true;
		
		GIS.toggleAllObjUL('switchUL_' + obj);
		/*
		if (timeout) clearTimeout(timeout);
		timeout = setTimeout(function(){
			console.log('timeout');
			}, delay);	
			*/
	}
	this.layerSwitch = function(e) {
			console.log(showProperties(e));
		/*
			switch (e.layer.options.layerId) {
				case 'adm':  addToList(); break;
				case 'eko':   addToList(); break;
				default : $('#infoContainer').html('');*/
		}		
	this.routingToPoint = function() {
		console.log(popup.getLatLng());
		coord = (GIS.home['center']!= undefined) ? GIS.home['center'] : L.latLng(GIS.home['lat'], GIS.home['lon']);
		console.log(coord);
//		L.Routing.Formatter({language: 'pt'});
		rout = L.Routing.control({
				waypoints: 
				[
				coord,
				L.latLng(popup.getLatLng())
				],
				language: 'ru',
				showAlternatives: true, 
				altLineOptions: {
					styles: [
						{color: 'black', opacity: 0.15, weight: 9},
						{color: 'white', opacity: 0.8, weight: 6},
						{color: 'blue', opacity: 0.5, weight: 2}
					] 				
				}
				});
//		rout.Formatter({language: 'pt'});		
				
		rout.addTo(mymap);
		}
	this.geoKoderOSM = function(str) {
			var action = ($(location).attr('href') == 'http://gis2.localhost/') ? '/getFromFile.php' :'/gis2/getFromFile.php';

			$.post(action, {type:'geoKoderOSM', location:$('#cityLabel').text(), str:str}, function(str)
				{	
				UI.pleaseWait();
				if(str != '')
					{
					parArr = str.split('##');
					for(var k=0; k<parArr.length; k++)        
						{
						parSingle = parArr[k].split('=');
						GIS.home[parSingle[0]] = parSingle[1];
						}
//					pointArr = 	GIS.home['display_name'].split(',');
					adrArr = 	GIS.home['display_name'].split(', ');
					boundArr = 	GIS.home['boundingbox'].split(',');
//					console.log(boundArr);
					GIS.home['point'] = L.marker([GIS.home['lat'], GIS.home['lon']]).addTo(mymap);
//					console.log(GIS.home);
					mymap.fitBounds([[boundArr[0],boundArr[2]],[boundArr[1],boundArr[3]]]);
//					L.rectangle([[boundArr[0],boundArr[2]],[boundArr[1],boundArr[3]]], {color: "#ff7800", weight: 1}).addTo(mymap);	

					$('#detailAdrSearchLabel').html(adrArr[1] + ' ' + adrArr[0]);
					$('#detailAdrSearchContaner').hide();
					$('#detailAdrSearchToggle').hide();			
					$('#detailAdrSearchRenew').show();
					$('#detailAdrSearchLabel').show();			
					
					}
				else	
					GIS.home = [];
			

				});
		}	
	this.showSingleObj = function(type, objIndex)
		{
		console.log(type, objIndex);
		switch(type)
			{
			case 'city' : {	
				obj = $.grep(settlements, function( item, index ){
//					console.log(item);
					if(item.indexSrc == objIndex) return true;					
				});
				
				} break;
			}
		mymap.removeLayer(this.searchedSingleObj);
//		this.searchedSingleObj = 0;
//				mymap.addLayer(arr[k]);
//				arr[k].state=1;

		this.searchedSingleObj = obj[0];
		mymap.addLayer(this.searchedSingleObj);
		mymap.fitBounds(this.searchedSingleObj.getBounds());
		console.log(this.searchedSingleObj.getBounds().getCenter());
		this.home.center = this.searchedSingleObj.getBounds().getCenter();
		}
	this.searchCity = function(objArr, str)
		{
		console.log(str);
		return $.map(objArr, function( item ){
//			console.log(item);				
			if(item.name.indexOf(str)==0)
				{
//				console.log(item);				
				return {label:item.name, value:item.index};			
//				return {value:item.index, label:item.name};			
				}
			})
		}
	this.setTitle = function(type, string)
		{
		var strout='';
		switch (type)
			{
			case 'org' : {strout = 'Станции арендодателя <strong>' + string + '</strong>'}
				break;
			case 'location' : {strout = 'Станции, расположенные в <strong>' + string + '</strong>'}
				break;
			case 'type' : {strout = 'Станции производителя <strong>' + string + '</strong>'}
				break;
			case 'load' : {strout = 'Станции в статусе <strong>' + string + '</strong>'}
				break;
			
			}
		return strout;		
		}

	this.BSListItemClick = function(index)
		{
//		reg[index].poly.openPopup();
	
//		mymap.setView(reg[index].poly.coordPoint);
		mymap.fitBounds(this.overSingleObj.getBounds());
		}
	this.BSListItemOver = function(index)
		{
//		reg[index].poly.setStyle({'fillOpacity' : 0.6});
		if(this.overSingleObj == 0)
			{
			this.overSingleObj = L.polygon(reg[index], this.styleReg).addTo(mymap);
			}
		}
	this.BSListItemOut = function(index)
		{
//		reg[index].poly.setStyle({'fillOpacity' : 0.2});
		mymap.removeLayer(this.overSingleObj);
		this.overSingleObj = 0;
		}
	this.showTitle = function(x, y, title){
		if($('#titlePoly').css('display') != 'block')
			{
			$('#titlePoly').css({'display' : 'block', 'left' : x, 'top' : y});
			$('#titlePoly').text(title);
			}
		}
	this.hideTitle = function(){
			$('#titlePoly').css({'display' : 'none'});
		}

	this.genTextBigList = function(obj)
		{
//		var htmCont = '<ul class="accItem"  id="' + obj.id + '_Contaner" >';
		var htmCont = '';
		var letterArr = [];
		obj.sort(function(a,b)
			{
			 if (a.name < b.name){
				return -1;
			 }else if (a.name > b.name) {
				return  1;
			 }else{
				return  0;
			 /*
				if (a[0] < b[0]) {
				   return -1; 
				}
				else if (a[0] > b[0]) {
				   return 1;
				} 
				else {
				   return 0;
				}*/
			 }
			})
		
//		for(var k=0; k<obj.length; k++)
		for(var k=0; k<300; k++)
			{
			if(obj[k].name)
			htmCont += "<li><span class='activeLink' id='accItem-" + obj.id + "_" + k + "'  onMouseOver='GIS.BSListItemOver(" + obj[k].index + ");' onMouseOut='GIS.BSListItemOut(" + obj[k].index + 
									");' onClick='  GIS.BSListItemClick(" + obj[k].index + ");'>" + obj[k].name + "</span>";
			}
//		htmCont += "</ul>";
		return htmCont;
		}
	this.genLocList = function()
		{
//		var htmCont = "<div class='switchContaner'><label for='switchDistr'>Отображать</label><input type='checkbox' class='js-switch' id='switchDistr' checked /></div> <ul class='accItem'>";		
//		var htmCont = "<ul class='accItem'>";
//		nameReg.sort();
		var htmCont = "";		
		for(var k=0; k<nameReg.length; k++)
			{
			htmCont += "<li><span class='activeLink' id='accItemBS_" + k + "'  onMouseOver='GIS.BSListItemOver(" + k + ");' onMouseOut='GIS.BSListItemOut(" + k + 
									");' onClick='  GIS.BSListItemClick(" + k + ");'>" + nameReg[k] + "</div>";
			}
//		htmCont += "</ul>";
		$('#distrList').html(htmCont);
		var switchDistr = new Switchery(document.querySelector('#switchDistr'), { size: 'small'});		
		}
		
	this.toggleRegAll = function(reg)
		{
		for(var k=0; k<reg.length; k++)        
			{
			if(reg[k]['poly'].state==0)
				{
				mymap.addLayer(reg[k]['poly']);
				reg[k]['poly'].state=1;
				}
			else
				{
				mymap.removeLayer(reg[k]['poly']);
				reg[k]['poly'].state=0;
				}
			}
		}
	this.togglePolyArr = function(arr)
		{
		for(var k=0; k<arr.length; k++)        
			{
			if(arr[k].state==0)
				{
				mymap.addLayer(arr[k]);
				arr[k].state=1;
				}
			else
				{
				mymap.removeLayer(arr[k]);
				arr[k].state=0;
				}
			}
		}	
	this.loadReg = function(reg)
		{
		var state = 0; //отключено по умолчанию
		var east = [], west = [], south = [], north = []; 
		styleReg = 	{
										"color": "#222222",
										"fillColor": "#ff0000",
										"weight": 1,
										"fillOpacity": 0.2,
										"opacity": 0.5
									};
		for(var k=0; k<reg.length; k++)        
			{			
			popupContent = "<div class='balContaner'><div class='balMainInfo'><div class='balName'>" + nameReg[k] + '</div></div></div>';
//			reg[k]['poly'] = L.polygon(reg[k],styleReg).addTo(mymap);
//			reg[k]['poly'] = L.polygon(reg[k],styleReg);
			reg[k]['poly'] = L.polyline(reg[k],styleReg);
			reg[k]['poly'].bindPopup(popupContent);
			reg[k]['poly'].title = nameReg[k];
			reg[k]['poly'].state = state;
			reg[k]['poly'].index = k;
			reg[k]['poly'].on("mouseout", function (e) {this.setStyle({'fillOpacity' : 0.2}); GIS.hideTitle(); /*BSSectorOver=-1; BSIndexOver = -1;*/});   
			reg[k]['poly'].on("mouseover", function (e) { this.setStyle({'fillOpacity' : 0.6});  BSIndexOver = this.index; GIS.showTitle(e.originalEvent.clientX, e.originalEvent.clientY, this.title)});   
			east.push(reg[k]['poly'].getBounds().getEast());
			west.push(reg[k]['poly'].getBounds().getWest());
			south.push(reg[k]['poly'].getBounds().getSouth());
			north.push(reg[k]['poly'].getBounds().getNorth());
			}
		mymap.fitBounds([[north.max(),west.min()],[south.min(),east.max()]]);
		this.genLocList();
		}

	this.erasePanel2 = function()	//2017_05_03 закрытие панели 2
		{
//		console.log(typeof MNG);
		if(typeof MNG === 'undefined')
			{
			$('.menuPad').hide();
			UI.menu2PadToggle();			
			}
		else
			{
			console.log(typeof MNG);
			MNG.erasePanelObj(1, true);	
			}
		}	
	this.toggleULFilter = function(id, show)	//2017_12_14 установка фильтра
		{
//		console.log(LF);
		var alreadyInFilter = LF.isLayerFilter(id);
		if((alreadyInFilter)&&(show == false)) //откл
			{
			LF.removeLayerFilter(id);	
			}
		else if ((!alreadyInFilter)&&(show == true)) //вкл
			{
			LF.setLayerFilter(ULayers[ULayerIndex[id]]);	
			}
//		console.log(LF);
		}
	this.toggleULFilterOld = function(id, show)	//2017_04_27 Устар установка фильтра
		{
		_setLayerFilter(ULayers[ULayerIndex[id]]);
			
			var pos = $.inArray(id, ULFilter);
			if((pos>=0)&&(show == false)) //откл
				{
				ULFilter.splice(pos, 1);
				}
			else if ((pos < 0)&&(show == true)) //вкл
				{
				ULFilter.push(id);
				}
				
			$(ULFilter).each(function(indx, element){
			});
		}
	this.genLayerListUL = function()	//2017_03_15 генерация списка пользовательских слоёв
		{
		var htmCont = '';
		ULSwitch.length = 0;
		$(ULayers).each(function(indx, element){
			
			htmCont += 	"	<div class='userLayerContaner' id='ulH_" + element.id + "'>" + 
						"		<div class='accHIconCont' >" + 
						"			<span class='accHIcon accHIconDsbl'></span>		" + 
						"		</div>" + 
						"		<div class='lbl'> " + element.name + "</div>" + 
						"			<div class='switchContaner' style='height:22px;'>" + 
						"				<input type='checkbox' class='js-switch' id='switchUL_" + element.id + "'  />" + 
						"			</div>" + 
						"	</div>" + 
						"	<div  class='userLayerContent' id='ulC_" + element.id + "'>" + 
						"		<div  class='actionUL'>" + 
						"			<div id='zoomDefUL_" + element.id + "' onClick='GIS.zoomDef_UL(" + element.id + ")'  class='buttZoomDef activeButton' title='Уместить слой на экране'></div>" + 
						"			<div id='searchUL_" + element.id + "' class='buttSearch activeButton' title='Поиск объекта'></div>" + 
						"			<div id='infoUL_" + element.id + "' 	class='buttInfo activeButton' title='Информация о слое'></div>";
			if(MNG != undefined)			
				htmCont += 	"			<div id='editUL_" + element.id + "' 	onClick='MNG.ULEditLayer(this)' class='buttEdit activeButton' title='Изменить свойства слоя'></div>" + 
							"			<div id='addObjUL_" + element.id + "' onClick='MNG.ULAddObj(this)' class='buttAdd activeButton' title='Добавить объекты'></div>";
			htmCont +=	"		</div>" + 
						"		<div  class='objListUL' id='ulO_" + element.id + "'>asdasd" + 
						"		</div>" + 
						"	</div>"; 
				});
		$('#layerList').slideUp('fast', function (){$('#layerList').html(htmCont);
			$(ULayers).each(function(indx, element){
				ULSwitch[element.id] =  new Switchery(document.querySelector('#switchUL_' + element.id),{ size: 'min'});	
				});
//			$('.userLayerContent').hide();	
			UI.initUserLayersPanel();
			$('[id ^= switchUL_]').on("change", function (event){UI.pleaseWait(); console.log('extend'); GIS.toggleAllObjUL(event.currentTarget.id);});
			});
		$('#layerList').slideDown('slow');				
		}		

	this.zoomObj = function(id)	//2018_01_15 масштабирование всех объектов
		{
/*		if(lId == undefined)
			lId = ULLayerOver.id;*/
		var east = [], west = [], south = [], north = []; 
//		var coord = {};
		var sCoord;
		var count = 0;
		var borderRegion;
		console.log('zoomAll: ');
//		$(UObjects).each(function(indx, element){
		if(UObjects[id] != undefined)
			{
			var obj = UObjects[id];					
			if(obj.type>0)
				{
				east.push(obj.getBounds().getEast());
				west.push(obj.getBounds().getWest());
				south.push(obj.getBounds().getSouth());
				north.push(obj.getBounds().getNorth());
				}
			else
				{
//						console.log(obj);	
				sCoord = obj.getLatLng();
				east.push(sCoord.lng);
				west.push(sCoord.lng);
				south.push(sCoord.lat);
				north.push(sCoord.lat);					
				}
			}
		mymap.fitBounds([[north.max(),west.min()],[south.min(),east.max()]]);
		}	
	this.zoomAll = function()	//2018_01_15 масштабирование всех объектов
		{
/*		if(lId == undefined)
			lId = ULLayerOver.id;*/
		var east = [], west = [], south = [], north = []; 
//		var coord = {};
		var sCoord;
		var count = 0;
		var borderRegion;
		console.log('zoomAll: ');
		$(UObjects).each(function(indx, element){
			if(element != undefined)
				{
				var obj = element;					
				if((obj.state>0)&&((!obj.isSystem)||(obj.isDistrBorder)))
					{				
					count ++;
					
					if(obj.type>0)
						{
						east.push(obj.getBounds().getEast());
						west.push(obj.getBounds().getWest());
						south.push(obj.getBounds().getSouth());
						north.push(obj.getBounds().getNorth());
						}
					else
						{
//						console.log(obj);	
						sCoord = obj.getLatLng();
						east.push(sCoord.lng);
						west.push(sCoord.lng);
						south.push(sCoord.lat);
						north.push(sCoord.lat);					
						}
					}
				else if((obj.isSystem)||(!obj.isDistrBorder))
					borderRegion = obj;					
				}
			});	
		console.log('count: ' + count);
		if(!count){
			console.log('borderRegion.id: ' + borderRegion.id);
//			sCoord = borderRegion.getBounds();
			east.push(borderRegion.getBounds().getEast());
			west.push(borderRegion.getBounds().getWest());
			south.push(borderRegion.getBounds().getSouth());
			north.push(borderRegion.getBounds().getNorth());
				
		}
			

//		console.log(north.max()+'; '+west.min()+'; '+south.min()+'; '+east.max());
		mymap.fitBounds([[north.max(),west.min()],[south.min(),east.max()]]);
		}	
	this.zoomDef_UL = function(lId)	//2017_02_15 масштабирование слоя
		{
		if(lId == undefined)
			lId = ULLayerOver.id;
		var east = [], west = [], south = [], north = []; 
		var sCoord;
		var count = 0;
		console.log(ULLayerOver);
		console.log('zoomDef_UL: ' + lId);
		$(UObjects).each(function(indx, element){
			if(element != undefined)
				{
				var obj = element;					
				count ++;
				if((obj.lStr.indexOf('_' + lId) >= 0)||(obj.lParentStr.indexOf('_' + lId) >= 0))
					{				
					
					if(obj.type>0)
						{
						east.push(obj.getBounds().getEast());
						west.push(obj.getBounds().getWest());
						south.push(obj.getBounds().getSouth());
						north.push(obj.getBounds().getNorth());
						}
					else
						{
//						console.log(obj);	
						sCoord = obj.getLatLng();
						east.push(sCoord.lng);
						west.push(sCoord.lng);
						south.push(sCoord.lat);
						north.push(sCoord.lat);					
						}
					}
				}
			});	
//		console.log('count: ' + count);

//		console.log(north.max()+'; '+west.min()+'; '+south.min()+'; '+east.max());
		mymap.fitBounds([[north.max(),west.min()],[south.min(),east.max()]]);
		}
	this.showObjUL = function(objId)	//2017_02_15 вкл и приближение объекта
		{
		console.log('showObjUL:');
		console.log('objId: ' + objId);
//		$(UObjects).each(function(indx, element){
		if(UObjects[objId] != undefined)
//		if(element.id == objId)
			{
			var element = UObjects[objId];
			if(element.remote == true){
				_loadingCoord = 1;
				_getRemoteGeometry(objId, 1);
//					element.remote = false;
			} else {
			if(element.type>0)
				{
				mymap.fitBounds(element.getBounds());				
				if(element.state == 0) mymap.addLayer(element);
				}
			else
				{
				if(element.state == 0) ULCluster.addLayer(element);						
				var sCoord = element.getLatLng();
				mymap.fitBounds(L.latLngBounds(sCoord, sCoord));	
				}
			if(element.state == 0)
				{
				$('#objUL_' + element.id).addClass('blacked');
				element.state = 1;
				$('#switchULObj').prop({"checked":true});
				switchULObj.destroy();
				$('#objFloatBar .switchery-min').remove();
//				$('.switchery-min').remove();
				switchULObj = new Switchery(document.querySelector('#switchULObj'), { size: 'min'});
				}
			}
			}
//			});					
		}
	this.toggleObjVisibleUL = function(objId)	//2017_02_15 вкл/выкл объекта
		{
//		console.log('toggleObjVisibleUL:');
		if(UObjects[objId] != undefined)
			{
//			var geoRemote;
//			console.log('objId: ' + objId);
			var element = UObjects[objId];
//			console.log(element);			
			if(element.remote == true){
				_loadingCoord = 1;
				_getRemoteGeometry(objId);
//					element.remote = false;
			} else {
			if(element.state == 0)
				{
				if(element.type>0)
					{
					mymap.addLayer(element);
					}
				else{
				ULCluster.addLayer(element);	
				}
				element.state = 1;
				$('#objUL_' + element.id).addClass('blacked');

				}
			else if(element.state == 1)
				{
				if(element.type>0)
					{
					mymap.removeLayer(element);
					}
				else{
					ULCluster.removeLayer(element);	
				}
				element.state = 0;
				$('#objUL_' + element.id).removeClass('blacked');
				}
			}
			}
		}
	this.toggleObjEditUL = function(objId)	//2017_02_15 вкл/выкл панели редактирования объектов
		{
		console.log('toggleObjEditUL:');
		console.log('objId: ' + objId);
		}
	
	this.toggleActionPanelLayerUL = function(layerId, index, show)	//2017_04_06 вкл/выкл панели управления для опользовательских слоёв
		{
/*		console.log( $('#' + objId).offset());
		console.log( $('#' + objId).position());*/
		console.log( 'toggleActionPanelLayerUL' );
		console.log( layerId );
		
//		$('#objFloatBar').offset($('#' + objId).offset());
//		$('#objFloatBar').offset($('#' + objId).offset());
		if (show > 0)
			{
			$('#layerFloatBar').show();
//			var pos =  $('#' + layerId).offset();
//			var left =  pos.left + $('#' + layerId).outerWidth();//- $('#objFloatBar').outerWidth();
//			console.log('left: ' + pos.left + '; width: '+  $('#' + layerId).outerWidth());
			$('#' + layerId).append($('#layerFloatBar'));
//			$('#layerFloatBar').offset({top:(pos.top - 5),  left:(left/* - $('#objFloatBar').outerWidth()*/)});			
			ULLayerOver = new activeLayer(ROUT.GetStrPrt(layerId, '_', 1), index);
			}
		else 
			{
//			ULObjListOver = '';
			$('#layerFloatBar').hide();			
			}
//			$('#' + objId).hide();			
		}
	this.toggleActionPanelObjUL = function(objId, show)	//2017_02_13 вкл/выкл панели управления для объекта объектов пользовательских слоёв
		{
		if (show > 0)
			{
			$('#objFloatBar').show();
			ULObjListOver = ROUT.GetStrPrt(objId, '_', 1);
			var pos =  $('#objLiUL_' + ULObjListOver + ' .contObjList').offset();
			var left =  pos.left + $('#objLiUL_' + ULObjListOver + ' .contObjList').outerWidth();//- $('#objFloatBar').outerWidth();
			$('#objFloatBar').offset({top:(pos.top - 5),  left:(left/* - $('#objFloatBar').outerWidth()*/)});			
			if(UObjects[ULObjListOver] != undefined)
				{
				var element = UObjects[ULObjListOver];
				switchULObj.destroy();
				$('#objFloatBar .switchery-min').remove();
				if(element.state == 0)
					$('#switchULObj').prop({"checked":false});
				else if(element.state == 1)
					$('#switchULObj').prop({"checked":true});
				switchULObj = new Switchery(document.querySelector('#switchULObj'), { size: 'min'});
				}
			}
		else 
			{
			$('#objFloatBar').hide();			
			}
		}
	this.showObjListUL = function()	//2017_04_09  спиcок объектов пользовательских слоёв
		{
//		if(lId == undefined)
		var lArr = [];
		var lId = ULLayerOver.id;
		var htmCont = '';
		var counter = 0;
		$('#menuLayerObjList h3').text('Объекты слоя "' + ULayers[ULLayerOver.index].name + '"');
		$('.menuPad').hide();
		$('#menuLayerObjList').show();
		$('.userLayerContaner').removeClass('hSelected');
		$('#ulH_' + ULLayerOver.id).addClass('hSelected');;					
		if(UI.menu2PadActive() == false)
		{
			UI.menu2PadToggle();
		}
//		$('#menuLayerObjList .objPropertiesContaner').text('Layer ' + ULLayerOver); 
//		console.log(ULLayerOver);
//		$(UO2L[ULayers[ULLayerOver.index].id]).each(function(indx, element){
		$(UObjects).each(function(indx, element){			
			if(element != undefined)
				{
				var obj = element;
				if((obj.lStr.indexOf('_' + lId) >= 0)||(obj.lParentStr.indexOf('_' + lId) >= 0))
					{
					lArr[counter] = obj;
					counter++;
					}
				}
			});
		lArr.sort(objOrder);
		$(lArr).each(function(indx, element){			
						var obj = element;
						htmCont += "<li id='objLiUL_" + obj.id + "'>" + 
						"<div class = 'contObjList'><nobr><div class='emptySpace'></div><span class='activeLink";
						htmCont += (element.state == 1) ? " blacked": "";
						htmCont += "' id='objUL_" + obj.id + "' " + 
						"onClick='GIS.showObjUL(\"" + obj.id + "\");'  onMouseOver='GIS.toggleActionPanelObjUL(\"objUL_" + obj.id + "\", 1);'  onMouseOut='GIS.toggleActionPanelObjUL(\"objUL_" + obj.id + "\", 0);' " + 
						"title='" + obj.name + "'>" + obj.name + "&nbsp;&nbsp;</span></div></nobr>" + 
						"<div class='fade' onMouseOver='GIS.toggleActionPanelObjUL(\"objUL_" + obj.id + "\", 1);' ></div>" + 
						"" + 
						"</li>";
	//						}
				});
		if(htmCont != '') htmCont = '<ul>' + htmCont + '</ul>';
//		console.log(htmCont);
		$('#ulO_0').html(htmCont);	
		$('.objListUL ul li .fade').on('mouseover', function (event) {event.stopPropagation();$(this).css({'display' : 'block'});});
		$('.userLayerContent').css('height',  $('#accList').innerHeight()-$('#menuLayerObjList h3').outerHeight()-32);	
		$('.userLayerContent').css('max-height',  '10000px');				
		}
	this.toggleAllObjListUL = function(lId = 0)	//2017_02_12 вкл/выкл спиcка пользовательских слоёв
		{
//////////////////////////////////////////////////////////////////////////////////////////
//устранить баг с потерей плавающей панели управления объектом после сворачивания слоя!!!			
//////////////////////////////////////////////////////////////////////////////////////////
		var htmCont = '';
		var layerLvl, lvlNew;
		console.log('toggleAllObjListUL - ' + lId);
		if(lId == 0){
//			console.log('close all layers');
			$('#ULContaner').append($('#layerFloatBar'));
			$('.userLayerContaner').each(function(indx, element){
				if($(element).hasClass("ULParent_0")) {
//					console.log('top layer ');
//					$(element).trigger('click');	
					if($(element).hasClass('hEnbl'))	
						$(element).trigger('click');
					$(element).removeClass('inFilter');
						
				}
				else{
					$(element).remove();					
				}
			});			
		}
		else {
		if($('#ulH_' + lId).hasClass('hEnbl'))			
			{
/*			_genFilteredList();*/
			console.log('hasClass');
			var checked = filterClass = '';
			var layerLvl =  ROUT.GetStrPrt($('#ulH_' + lId + ' .accHIconCont').attr("class"), 'ULlvl_', 1); 
			var lParent =  	ROUT.GetStrPrt(ROUT.GetStrPrt($('#ulH_' + lId).attr("class"), 'ULParent_', 1), ' ', 0); 
			lvlNew = parseInt(layerLvl)+1;
			$('.userLayerContaner').each(function(indx, element){
				if(($(element).attr("class").indexOf('_' + lId)>=0) &&
					($('#' + element.id + ' .accHIconCont').hasClass('ULlvl_' + lvlNew))){
					console.log(element.id);
					$(element).remove();
				}
			});			
			$(ULayers).each(function(indx, element){

			if((element.parId == lId) && (element.objCnt > 0)){

/*					проверка на активный фильтр									*/
				if(LF.isLayerFilter(element.id)){
					checked = ' checked ';
					filterClass = ' inFilter';
				} else if(LF.isParentsFilter(element.id)){
					checked = '';
					filterClass = ' inFilter';				
				} else {
					checked = '';
					filterClass = '';									
				}
/*					проверка на активный фильтр									*/
				var messAdd = (element.message.length > 0) ? '<span title="оперативные сообщения"> (' + element.message.length + ') </span> ' : '';
					
				htmCont += 	"	<div class='userLayerContaner ULParent_" +lParent + "_" + lId + filterClass + "' id='ulH_" + element.id + "'>															"+
							"		<div class='accHIconCont ULlvl_" + lvlNew + "' >";
                                             							
				htmCont += 	(element.childCnt > 0) ?	"			<span class='accHIcon accHIconDsbl'></span>" : "";
							
				htmCont += 	"		</div>"+
							"		<div class='lbl'>" + element.name + messAdd +"</div>" +
							"		<div class='switchContaner' id='switchContUL_" + element.id + "' >"+
							"			<input type='checkbox' class='js-switch' id='switchUL_" + element.id + "' " + checked + " />"+
							"		</div>                                                                  							"+
							"		<div class='iconContaner'  >                                            							"+
							"			<div id='layerAddMessage_" + element.id + "' class='buttNewForm' title='Создать сообщение'      							"+
							"onClick='FORM.newForm(\"" + element.id + "\");' ></div>												"+
							"		</div>                                                                                              "+
							"		<div class='iconContaner'  >                                            							"+
							"			<div id='layerOptUL_" + element.id + "' class='buttOptions' title='Действия'      							"+
							"onClick='GIS.toggleActionPanelLayerUL(\"ulH_" + element.id + "\", " +indx + ", 1);' ></div>												"+
							"		</div>                                                                                              "+
							"	</div>                                                                                                  ";
				}
			});	
			$('#ulH_' + lId).after(htmCont);	
/*****			$('#ulH_' + lId).after(_genFilteredList());	 на перспективу************************************/
			$('[class *= ULlvl_' + lvlNew + ']').css({'padding-left' : (lvlNew*15) + 'px'});

			$(ULayers).each(function(indx, element){
//				if((element.parId == lId) && (element.childCnt > 0)){
				if((element.parId == lId) && (element.objCnt > 0)){
					ULSwitch[element.id] =  new Switchery(document.querySelector('#switchUL_' + element.id),{ size: 'min'});	
					$('#switchContUL_' + element.id + ' .switchery').on("click", 	function (event) {console.log('#switchUL_' + element.id + ''); event.stopPropagation();});
					$('#switchUL_' + element.id).on("change", function (event){GIS.toggleAllObjUL(event.currentTarget.id);});
					$('#layerOptUL_' + element.id).on('click', function (event) {event.stopPropagation();});
					if(element.childCnt > 0)
						UI.initULElement($('#ulH_' + element.id));
					}
				});
 			}
		else
			{
			console.log('noClass');
			$('#ULContaner').append($('#layerFloatBar'));
			$('.userLayerContaner').each(function(indx, element){
				if(($(element).attr("class").indexOf('_' + lId)>=0) &&
					(!$(element).hasClass("inFilter"))){
					$(element).remove();
				}
			});
			}
		}
		$("#ULContaner").niceScroll({cursorcolor:"#cfcfcf", cursorwidth:"4px"});
		$("#ULContaner").css({display:"inherit"});
		}	
	this.toggleAllObjUL = function(lIdStr = '')	//2017_02_12 вкл/выкл объектов пользовательских слоёв
		{
		if(lIdStr == ''){//удаление всех объектов
			$(UObjects).each(function(indx, element){ 
				if((element != undefined) && (element.isSystem != true))
					{
					element.state = 0;
					if(element.type>0)
						{
						mymap.removeLayer(element);
						}
					else
						{
						ULCluster.removeLayer(element);						
						}
					$('#objUL_' + element.id).removeClass('blacked');
					}
				});			
		}
		else {
			var count = 0;
			var lId =  ROUT.GetStrPrt(lIdStr, 'switchUL_', 1);
			var show = $('#' + lIdStr).prop("checked");
			this.toggleULFilter(lId, show);
			var parentListStr = ROUT.GetStrPrt(ROUT.GetStrPrt($('#ulH_' + lId).attr("class"), 'ULParent_', 1), ' ', 0); 
			var parentList =   	parentListStr.split('_'); 
//			console.log(parentList);
			if(show){
				$('#ulH_' + lId).addClass("inFilter");
				parentList.forEach(function(element, indx, arr){
				if(element >100){
					$('#ulH_' + element).addClass("inFilter");
					}
				});
			} 
			else {
				$('#ulH_' + lId).removeClass("inFilter");
//				console.log(LF);				
//				if()
				parentList.push(lId);
				parentList.reverse();
				parentList.forEach(function(element, indx, arr){
					if((element!=0)){
						$('#ulH_' + element).removeClass("inFilter");
						}
				});
				LF.list.forEach(function(element, indx, arr){
					var fParStr = ROUT.GetStrPrt(ROUT.GetStrPrt($('#ulH_' + element.id).attr("class"), 'ULParent_', 1), ' ', 0)
					var parentList =   	fParStr.split('_'); 
					parentList.forEach(function(element, indx, arr){
					if(element >100){
						if(!$('#ulH_' + element).hasClass("inFilter"))
							$('#ulH_' + element).addClass("inFilter");
						}
					});					
//					console.log(element.id + ': ' + fParStr);
				});
				
			}
//			console.log(ULFilter);
//			if(ULFilter.length>0)
			if(LF.isActive())
				{
				var remoteArr = [];
/****************************temporary*********************************************************/				
				$(UObjects).each(function(indx, element){ 
					if((element != undefined) && (element.isSystem != true))
						{
						element.state = 0;
						if(element.type>0)
							{
							mymap.removeLayer(element);
							}
						else
							{
							ULCluster.removeLayer(element);						
							}
						$('#objUL_' + element.id).removeClass('blacked');
						}
					});			
/****************************temporary*********************************************************/				
				$(UObjects).each(function(indx, element){
					if(element != undefined)
						{
						var obj = element;
/*						if(element.isSystem == true)
							console.log(' [isSystem] : '+element.name + ' : ' + element.id);*/
							
						if((LF.isObjShowFilter(obj))&&(!obj.isSystem))
//						if(_isObjShowFilter(obj))
							{
								console.log(' [ADD] '+obj.name + ' : ' + obj.id);
							if(obj.remote == true){							
								remoteArr.push(obj.id);
							} else {						
							obj.state = 1;

							if((obj.type!=0) && (obj.type!=5))
								{
	//							obj.getBounds().getNorth();
								_setBounds([obj.getBounds().getEast(),obj.getBounds().getWest(),
											obj.getBounds().getNorth(), obj.getBounds().getNorth()]);
								mymap.addLayer(obj);
								}
							else
								{
								var sCoord = obj.getLatLng();
								_setBounds([L.latLngBounds(sCoord, sCoord).getEast(),L.latLngBounds(sCoord, sCoord).getWest(),
											L.latLngBounds(sCoord, sCoord).getSouth(), L.latLngBounds(sCoord, sCoord).getNorth()]);
								if(obj.type ==0)
									ULCluster.addLayer(obj);						
								else
									mymap.addLayer(obj);					
									
								}
							$('#objUL_' + obj.id).addClass('blacked');
							
							}
							count++;
							}
						else if(obj.isSystem == false)
							{
							if(obj.state>0)
								console.log(' [REMOVE] '+obj.name + ' : ' + obj.id);
							if(obj.type != 0)
								{
								mymap.removeLayer(obj);
								}
							else
								{
								ULCluster.removeLayer(obj);						
								}
							$('#objUL_' + obj.id).removeClass('blacked');
							obj.state = 0;
							}
						}
					});	
				if(_fitToBounds == true)
					{
					_fitBounds();
					}
				if(remoteArr.length > 0){
					_loadingCoord = 1;
					_getRemoteGeometry(remoteArr, (_fitToBounds == true) ? 1 : 0);
				}
				UI.TBFilterContent(1, count);
				UI.TBClick('TBfilter'); 

				}
			else 
				{
				$(UObjects).each(function(indx, element){ 
					if((element != undefined) && (element.isSystem != true))
						{
						element.state = 0;
						if(element.type>0)
							{
							mymap.removeLayer(element);
							}
						else
							{
							ULCluster.removeLayer(element);						
							}
						$('#objUL_' + element.id).removeClass('blacked');
						}
					});	
				UI.TBFilterContent(0);
				}
			}
		}


	this.loadUL = function()		//2017_02_09 загрузка пользовательских слоёв
		{ 
		var layerDistr;
		var objCntMin = objCntMax = -1;
		var state = 0; //отключено по умолчанию
		var borderArr = [];
		var coordSingle = []; var coordArr = []; var semArr = [];
		var name, type, popupContent, objId, lIndex, lId;
		var counter = 0;
		var deltaLat, deltaLong;
		var east = [], west = [], south = [], north = []; 
		var lats = []; var lngs = [];  
		var	o2l = [];
//		var sItem = [];
		var layerDelim = '~~';
		var paramDelim = '##';
		var objDelim = '||';
		var geoDelim = '^^';
		var paramDelimObj = '%%';
		var paramDelimGeo = '$$';
		var objStart = '<<obj>>';
		var holeDelimGeo = '**';
		var featureDelimGeo = '++';
		
		var obj2LayerStart = '<<obj2layer>>';
		var BoundsStart = '<<bounds>>';
		var messStart = '<<mess>>';
		var strArr0 = [], strArr1 = [], strArr1Param = [], strArr2 = [], strArr2Param = [], strArr3 = [], strArr3Param = [];
//		UI.pleaseWait();
//		$.post("/spddl/", {type:'ULObjects', startStr:startStr}, function(str) 
		$.post("/spddl/", {type:'ULObjectsZHKH', startStr:startStr}, function(str) 
			{
//			UI.pleaseWait();
		
//			console.log(str);
			strArrMess = str.split(messStart); 
//			console.log(strArrMess[1]);
			MESS = JSON.parse(strArrMess[1])[0];
			strArr0 = strArrMess[0].split(BoundsStart); 
			strArr1 = strArr0[0].split(objStart); 
			strArr2 = strArr1[0].split(layerDelim);
			$(strArr2).each(function(indx, element){
				if(strArr2[indx].length > 10)
					{					
					strArr1Param = strArr2[indx].split(paramDelim); 						
					ULayers[indx] = new addLayers(strArr1Param);
					ULayers[indx].messageActive = 0;
					ULayerIndex[strArr1Param[0]] = indx;
					}
				});
//			console.log(MESS);
//			var 
			$(MESS).each(function(indx, element){
				if(element.distId != undefined){
					ULayers[ULayerIndex[element.distId]].messageAll.push(element);
					if(element.status>0) 
						ULayers[ULayerIndex[element.distId]].message.push(element);
//					console.log(ULayers[ULayerIndex[element.distId]]);
				}
//				ULayers[element.distId]);
			});
			//console.log(MESS);
			strArr3 = strArr1[1].split(objDelim);
			$(strArr3).each(function(indx, element){
				if(strArr3[indx].length > 10)
					{
					strArr2Param = strArr3[indx].split(paramDelim); 						
					if((strArr2Param[8].length > 10)||(strArr2Param[8] == 'remote'))
						{
						if(strArr2Param[8].length > 10){
//							console.log('!!!!figure ' + strArr2Param[0] + '; ' +strArr2Param[9]);
//							console.log(strArr2Param);
							coordSingle = [];
							switch(strArr2Param[9]){
								case '0' : {	//
									//console.log('single');
									strArr5 = strArr2Param[8].split(geoDelim); 
									$(strArr5).each(function(indx, element){
										if(strArr5[indx].length > 10){
											strArr3Param = strArr5[indx].split(paramDelimGeo); 
											coordSingle.push( new L.latLng(strArr3Param[1], strArr3Param[0]));
										}
									});									
								} break;
								case '1' : {	//only holes
//									console.log('only holes');
									strArrGeoFeature = strArr2Param[8].split(holeDelimGeo);
									$(strArrGeoFeature).each(function(indx, element){
//										console.log(element);
										if(strArrGeoFeature[indx].length > 10){
											coordHole = [];
											strArr5 = element.split(geoDelim); 
											$(strArr5).each(function(indx, element){
												if(strArr5[indx].length > 10){
													strArr3Param = strArr5[indx].split(paramDelimGeo); 
													coordHole.push( new L.latLng(strArr3Param[1], strArr3Param[0]));
												}											
											});		
											
											coordSingle.push(coordHole);
											
										}										
									});																				
								} break;
								case '2' : {	//only features
//									console.log('only features');
									strArrGeoFeature = strArr2Param[8].split(featureDelimGeo);
									$(strArrGeoFeature).each(function(indx, element){
										if(strArrGeoFeature[indx].length > 10){
											coordFeature = [];
											coordHole = [];
											strArr5 = element.split(geoDelim); 
											$(strArr5).each(function(indx, element){
												if(strArr5[indx].length > 10){
													strArr3Param = strArr5[indx].split(paramDelimGeo); 
													coordHole.push( new L.latLng(strArr3Param[1], strArr3Param[0]));
												}											
											});
											coordFeature.push(coordHole);
											coordSingle.push(coordFeature);
										}										
									});																													
								} break;
								case '3' : {	//holes and features
//									console.log('holes and features');
									strArrGeoFeature = strArr2Param[8].split(featureDelimGeo);
									$(strArrGeoFeature).each(function(indx, element){
										if(strArrGeoFeature[indx].length > 10){
											coordFeature = [];
											strArrGeoHole = element.split(holeDelimGeo);
											$(strArrGeoHole).each(function(indx, element){
												if(strArrGeoFeature[indx].length > 10){
													coordHole = [];
													strArr5 = element.split(geoDelim); 
													$(strArr5).each(function(indx, element){
														if(strArr5[indx].length > 10){
															strArr3Param = strArr5[indx].split(paramDelimGeo); 
															coordHole.push( new L.latLng(strArr3Param[1], strArr3Param[0]));
														}											
													});
													coordFeature.push(coordHole);
												}
											});
											coordSingle.push(coordFeature);
										}										
									});																																						
								} break;
							}
							figure = _drawFigure(strArr2Param[2], coordSingle, '', 'system'); //type, coordSingle, info, template
							}
						else{
//							console.log('remote ' + strArr2Param[0]);
							figure = new remoteFigure(strArr2Param[0]);
						}
							
//						objId = counter;
						strArr2Param.push(indx);
						objId = strArr2Param[0];
						UObjects[objId] = figure;
						_setObjPar(UObjects[objId], strArr2Param);
						UObjects[objId]['remote'] = (figure.remote == undefined) ? false : true;						
						UObjects[objId]['isSystem'] = (figure.remote == false) ? true : false;
						if(UObjects[objId].lStr=='_7000') //Граница области
						{
							UObjects[objId].addTo(mymap);
							//console.log(UObjects[objId]);
						}
						if((UObjects[objId].type==2)&&(UObjects[objId].lStr.indexOf('_' + distrLayerPar) >= 0))  /*объекты - районы*/{
							borderArr.push(objId);
							//UObjects[objId]['isSystem'] = true;
							UObjects[objId]['isDistrBorder'] = true;
							//UObjects[objId]['isDistrBorder'] = true;
							
							if(UObjects[objId].info == ''){
								//console.log(UObjects[objId]);
								layerDistr = ULayers[ULayerIndex[ROUT.GetStrPrt(UObjects[objId].lStr, '_', 2)]];
								//console.log(layerDistr.message.length);
								ULayers[ULayerIndex[ROUT.GetStrPrt(UObjects[objId].lStr, '_', 2)]].borderObj = UObjects[objId];
//								console.log()
								UObjects[objId].objCnt = parseInt(layerDistr.message.length);								
								if((objCntMin<0)||(parseInt(layerDistr.message.length)<objCntMin))
									objCntMin = parseInt(layerDistr.message.length);
								if((objCntMax<0)||(parseInt(layerDistr.message.length)>objCntMax))
									objCntMax = parseInt(layerDistr.message.length);
								
								if((objCnt.min<0)||(parseInt(layerDistr.message.length)<objCnt.min))
									objCnt.min = parseInt(layerDistr.message.length);
								if((objCnt.max<0)||(parseInt(layerDistr.message.length)>objCnt.max))
									objCnt.max = parseInt(layerDistr.message.length);
//								ULayers[ULayerIndex[ROUT.GetStrPrt(UObjects[objId].lStr, '_', 2)]].objCnt = ;
								UObjects[objId].info = UObjects[objId].objCnt + ' оперативных донесений';							
							}
							
						}  else if(UObjects[objId]['spec'] =='mo')  /*объекты - Городские МО*/{
							//UObjects[objId].type=3;
							borderArr.push(objId);
							UObjects[objId]['isDistrBorder'] = true;
							//console.log(UObjects[objId]);
							layerDistr = ULayers[ULayerIndex[ROUT.GetStrPrt(UObjects[objId].lStr, '_', 4)]];
							//console.log(layerDistr);
							ULayers[ULayerIndex[ROUT.GetStrPrt(UObjects[objId].lStr, '_', 4)]].borderObj = UObjects[objId];
							
							UObjects[objId].objCnt = parseInt(layerDistr.message.length);								
							if((objCntMin<0)||(parseInt(layerDistr.message.length)<objCntMin))
								objCntMin = parseInt(layerDistr.message.length);
							if((objCntMax<0)||(parseInt(layerDistr.message.length)>objCntMax))
								objCntMax = parseInt(layerDistr.message.length);
							
							if((objCnt.min<0)||(parseInt(layerDistr.message.length)<objCnt.min))
								objCnt.min = parseInt(layerDistr.message.length);
							if((objCnt.max<0)||(parseInt(layerDistr.message.length)>objCnt.max))
								objCnt.max = parseInt(layerDistr.message.length);
							
							UObjects[objId].info = UObjects[objId].objCnt + ' оперативных донесений';							
							
						}
						
						else if((UObjects[objId].lStr.indexOf('_' + sectorLayerPar) >= 0))  /*объекты - сектора*/{
							UObjects[objId]['style'] = styleSector;

						}							
						searchIndex[counter] = new indexLocal(strArr2Param[0], strArr2Param[1]);
						
//						searchArr[counter] = strArr2Param[1];
						counter++;
						}					
					}
				});				
			/*if(borderArr.length > 0){
				$(borderArr).each(function(indx, element){
//					console.log(UObjects[element].name + 'id = ' + UObjects[element].id +'; style: ');
//					console.log(UObjects[element].style);
					var red = green = 0;
					var redH = greenH = '';
					var obj = UObjects[element];
					//console.log(obj.name, obj.objCnt, objCntMax, objCntMin);
					var val =  parseInt(obj.objCnt);
					if(val> (objCntMax/2)){
						red = 255;
						green = 255 - (val - objCntMax/2)*255/(objCntMax/2); 
						if(green<1) green = 1; 
					}
					else{
						red = val*255/(objCntMax/2)
						green = 255;
					}
					redH = 		parseInt(red).toString(16);
					greenH = 	parseInt(green).toString(16);
					if(redH.length == 1) 	redH = '0' + redH;
					if(greenH.length == 1) 	greenH = '0' + greenH;
//					console.log(element + ': '+UObjects[element].name)
//					console.log('before: ' + UObjects[element].style.fillColor);
					UObjects[element].style = styleDistrFill;
					UObjects[element].colorHeat = '#' +redH + greenH + '00';
					UObjects[element].colorFill = '#' +ROUT.getRandomInt(62, 254).toString(16) + 
													ROUT.getRandomInt(63, 255).toString(16) + 
													ROUT.getRandomInt(64, 253).toString(16);
				});	
			}*/
			
		
			strArr0Param = strArr0[1].split(paramDelim);
//			console.log(strArr0Param[0] + '; '+ strArr0Param[1] + '; '+ strArr0Param[2] + '; '+ strArr0Param[3]);
			deltaLat = parseFloat((parseFloat(strArr0Param[2]) - parseFloat(strArr0Param[0]))/2);
			deltaLng = parseFloat((parseFloat(strArr0Param[1]) - parseFloat(strArr0Param[3]))/2);
//			console.log(deltaLat + '; '+deltaLng);
			mymap.fitBounds([[strArr0Param[0], strArr0Param[1]], 
							[strArr0Param[2], strArr0Param[3]]]);
//			_setBounds([parseFloat(strArr0Param[0]), parseFloat(strArr0Param[1]), parseFloat(strArr0Param[2]), parseFloat(strArr0Param[3])]);				
//			mymap.fitBounds([[north.max(),west.min()],[south.min(),east.max()]]);
				
//			console.log('deltaLat = ' + deltaLat + '; deltaLng = ' + deltaLng);
				
			mymap.setMaxBounds([[parseFloat(strArr0Param[0]- deltaLat), parseFloat(parseFloat(strArr0Param[1]) + parseFloat(deltaLng))], 
							[ parseFloat( parseFloat(strArr0Param[2]) + parseFloat(deltaLat)), parseFloat(strArr0Param[3]- deltaLng) ]]);
			mymap.setMinZoom(5);
							
			$('#mapid').css({'cursor': 'grab'});
			$("#waitBox").css({'display' : 'none'});
			
			initSearchObj(searchIndex);	
			
// 			initSearchLocal('searchObj', searchArr);	
//			GIS.colorCalc();
//			GIS.drawMO();
//			GISz.drawDistrList();
			GISz.startUpdate();
//			GISz.checkMessage();
			
			});
		}
	this.colorCalc = function(){
		$(ULayers).each(function(indx, element){
			if(element.borderObj != undefined){
				element.colorHeatOld = (element.colorHeat != undefined) ? element.colorHeat : 'new';
				element.borderObj.colorHeatOld = (element.borderObj.colorHeat != undefined) ? element.borderObj.colorHeat : 'new';
				var red = green = 0;
				var redH = greenH = '';
				var val =  element.message.length;
				if(val> (objCnt.max/2)){
					red = 255;
					green = 255 - (val - objCnt.max/2)*255/(objCnt.max/2); 
					if(green<1) green = 1; 
				}
				else{
					red = val*255/(objCnt.max/2)
					green = 255;
				}
				//console.log(objCnt);
				redH = 		parseInt(red).toString(16);
				greenH = 	parseInt(green).toString(16);
				//console.log(val, redH, greenH);
				if(redH.length == 1) 	redH = '0' + redH;
				if(greenH.length == 1) 	greenH = '0' + greenH;
				element.borderObj.colorHeat = '#' +redH + greenH + '00';
				element.colorHeat = '#' +redH + greenH + '00';
				element.redraw  = (element.borderObj.colorHeat == element.borderObj.colorHeatOld)? false : true;
//				console.log(element.redraw, element.name, element.message.length, element.borderObj.colorHeatOld, element.borderObj.colorHeat);

			}		
		});

	}

	this.drawMO = function(){
		$(ULayers).each(function(indx, element){
			if((element.borderObj != undefined)&&(element.redraw)){
				//console.log('redraw!');
				//console.log(element);
				//console.log(element.name, element.borderObj.colorHeat);
				var obj = element.borderObj;
				if((obj.state)&&(GISz.firstLoad)){
					if(obj.type>0){
						mymap.removeLayer(obj);
						//console.log(obj.name);
						}
					else
						{
						ULCluster.removeLayer(obj);						
						}	
					element.borderObj.state = 0;
				}
				var actMsgStr = (element.message.length > 0) ? "Зарегистрировано <span>" + element.message.length  + "</span>"+ 
					ROUT.getCorrectDeclensionRu(element.message.length , ' оперативное донесение' , ' оперативных донесения' , ' оперативных донесений' ) : "оперативных донесений нет";
				balCont = "<h3>"+ element.name+ "</h3>" + 
					"<p>&nbsp;</p><p><span  class='openMessageList activeLink' title='Открыть журнал' id='msg_"+ element.id + "' >" + actMsgStr + 
					" </span></p><p>&nbsp;</p>";
				var popup = L.popup().setContent(balCont);
				if(!GISz.firstLoad)
					element.borderObj.unbindPopup();
				element.borderObj.bindPopup(popup);
				element.borderObj.bindTooltip(element.name);
				element.borderObj.style = styleDistrFill;
				element.borderObj.style.fillColor = element.borderObj.colorHeat;
				element.borderObj.setStyle({"color": "#222222", 									
											"weight": 1,
											"fillOpacity": 0.1,
											"opacity": 0.5,
											"fillColor": element.borderObj.colorHeat});	
				if(GISz.firstLoad){
					if(element.borderObj.type>0){
						mymap.addLayer(element.borderObj);
						//console.log(obj.name);
						}
					else
						{
						ULCluster.addLayer(element.borderObj);						
						}
					element.borderObj.state = 1;
				}			
			}
			//console.log('no change');				
		});
	}
	this.__drawMO = function(){
		$(ULayers).each(function(indx, element){
			if((element.borderObj != undefined)&&(element.redraw)){
				console.log('redraw!');
				console.log(element);
				//console.log(element.name, element.borderObj.colorHeat);
				var obj = element.borderObj;
				if(obj.state){
					if(obj.type>0){
						mymap.removeLayer(obj);
						//console.log(obj.name);
						}
					else
						{
						ULCluster.removeLayer(obj);						
						}	
					element.borderObj.state = 0;
				}
				var actMsgStr = (element.message.length > 0) ? "Зарегистрировано <span>" + element.message.length  + "</span>"+ 
					ROUT.getCorrectDeclensionRu(element.message.length , ' оперативное донесение' , ' оперативных донесения' , ' оперативных донесений' ) : "оперативных донесений нет";
				balCont = "<h3>"+ element.name+ "</h3>" + 
					"<p>&nbsp;</p><p><span  class='openMessageList activeLink' title='Открыть журнал' id='msg_"+ element.id + "' >" + actMsgStr + 
					" </span></p><p>&nbsp;</p>";
				element.borderObj.bindPopup(balCont);
				element.borderObj.bindTooltip(element.name);
				element.borderObj.style = styleDistrFill;
				element.borderObj.style.fillColor = element.borderObj.colorHeat;
				element.borderObj.setStyle({"color": "#222222",											
											"weight": 1,
											"fillOpacity": 0.1,
											"opacity": 0.5,
											"fillColor": element.borderObj.colorHeat});				
				if(element.borderObj.type>0){
					mymap.addLayer(element.borderObj);
					//console.log(obj.name);
					}
				else
					{
					ULCluster.addLayer(element.borderObj);						
					}	
				element.borderObj.state = 1;	
			}
			console.log('no change');
				
		});

	}
}