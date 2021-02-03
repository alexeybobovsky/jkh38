var JKHIcon = L.Icon.extend({
	options: {
//		shadowUrl: '/src/design/main/img/gis/shadow.png',
		iconSize:     [26, 32],
//		shadowSize:   [29, 32],
		iconAnchor:   [13, 31],
//		shadowAnchor: [10, 20], 
		popupAnchor:  [-3, -33]
	}
});
var DVCML; //Device metriks labels

function messStat(id, state, commentsCnt){
	this.id = id;
	this.state = state;
	this.commentsCnt= commentsCnt;
}
function messUp(id, upType){
	this.id = id;
	this.type = upType;
}
function gisZ(){
	this.period = 30000;
	this.formSended = false;
	this.doUpdate = false;
	this.shownMessCnt = 0;
	this.firstLoad = true;
	this.updated = false;
	this.startUpdate = function(){
		GISz.doUpdate = true;
		GISz.checkMessage();
	}
	var styleDistrFill = {
											"color": "#222222",
											"fillColor": "#0000ff",
											"weight": 1,
											"fillOpacity": 0.1,
											"opacity": 0.5
										};
	var objTmp;
/*2018_01_02 рисование границ  районов*/
	var _upMessStat = function(id, comments, state){ 
		$(MStat).each(function(indx, element){
			if(element.id == id) {
				element.commentsCnt= comments;
				element.state= state;				
			}			
		});
	}
	var _checkMessStat = function(mess){ 
		let ret = 1;
//		console.log(MStat);
		if(MStat.length>0){
			let messCnt = (mess.comments != 0) ? mess.comments.length : 0 ;
			$(MStat).each(function(indx, element){
				let messCntEl = 0;
				if(element.id == mess.messId) {
//					console.log('find');
					if(messCnt != element.commentsCnt) 
						ret = 2;
					if(element.state != mess.status) 
						ret = 3;
					if(ret==1)
						ret = 0;					
				}
	//		console.log(ret, messCnt, messCntEl);
			});
		}
		return ret;
	}	
	this.clearUpBox = function(){
		MUp.length = 0;
		$('#upMessCont').html('');	
	}
	this.drawUpList = function(){
		var htmCont = '';
		this.shownMessCnt = 0;
/*		$(MUp).each(function(indx, element){
		});*/
	}
	this.drawMessList = function(){
		var htmCont = '';
		this.shownMessCnt = 0;
		$(MESS).each(function(indx, element){
//			let messCnt = (element.comments != 0) ? element.comments.length : 0 ;
			let up = (GISz.firstLoad) ? 1 : _checkMessStat(element);
			let upStr = '';
			switch(up){
				case 2:{
				}
				case 3:{ //update
					_upMessStat(element.messId, (element.comments != 0) ? element.comments.length : 0, element.status);
					upStr = (up == 2) ? 'добавлен комментарий' : 'Донесение закрыто';
				} break;
				case 1:{ //new mess
					MStat.push(new messStat(element.messId, element.status, (element.comments != 0) ? element.comments.length : 0  ));
					upStr =  'Создано новое донесение';
				} break;
				case 0:{
				}
				default:{}
			}
			if((up>0)&&(!GISz.firstLoad)){
				MUp.push(new messUp(element.messId, up));
				var htmContUp = "	<div class='messUp ' id='mUp_" + element.messId + "' onClick='GISz.openDialod(\"" + element.distId + "\");  FORM.showMess(\"" + element.messId + "\");' >";
				let loc = element.l_name;
				loc += (element.distId != element.npId) ? ', ' + element.npName : '';
				htmContUp += 	"" +
							"		<div class='lbl'> <div class='lblHeader'>" + upStr + "</div> <div class='lblBody'>Донесение №"  + element.messId + ". " + loc + ". <strong>" + element.reason + "</strong> - " + element.zhkhSectorName + " (" + element.hardware +  ")</div>" +
							"	</div>";			
				let upContent = $('#upMessCont').html();
				console.log(element.messId, up);
				console.log(upContent);
				$('#upMessCont').html(htmContUp + upContent);
				console.log('display = ');
				console.log($('#upMessCont').css('display'));
				if( $('#upMessBox').css('display') == 'none')
					$('#upMessBox').slideToggle("fast");
//				$('#bodyCont').html('(' + GISz.shownMessCnt + ')');					
			}
			
//			console.log(up);
			let location = element.l_name;
			let diffsec = (Date.now()-element.incidentDate_ts)/(1000*3600);
			var timeClass;
			var closedClass = '';
			if(($("#showClosedOD").prop("checked"))||(!($("#showClosedOD").prop("checked"))&&(element.status != 0))){
				GISz.shownMessCnt ++;
				if(element.status == 0){
					timeClass = ' closedInterval';
					closedClass = ' closed';
				}else {
					if(diffsec<24){
						timeClass = ' dateInterval';
					}else if (diffsec<168){
						timeClass = ' weekInterval';				
					}else{
						timeClass = ' oldInterval';
					}
				}
//				let commAdd = (element.comments == 0) ? 'комментариев пока нет' : element.comments.length + ' ' + 'комментариев';
				let commAdd = (element.comments != 0) ? element.comments.length + ' ' + ROUT.getCorrectDeclensionRu(element.comments.length , ' комментарий' , ' комментария' , ' комментариев' ) : "комментариев нет";
				location += (element.distId != element.npId) ? ', ' + element.npName : '';
				htmCont += 	"	<div class='mess messContaner " + timeClass + closedClass +  "' id='ulH_" + element.messId + "' onClick='GISz.openDialod(\"" + element.distId + "\");  FORM.showMess(\"" + element.messId + "\");' >"+
							"		<div class='accHIconCont ULlvl_" + element.messId + "' >";
				htmCont += 	"		</div>"+
							"		<div class='lbl'>Донесение <strong>№"  + element.messId + "</strong> от<span>" + element.incidentDate_ru + "</span>. "  + location + ". <strong>" + element.reason + "</strong> - " + element.zhkhSectorName + " (" + element.hardware +  ")  </br>" + commAdd +" </div>" +
							"		<div class='iconContaner'  >                                            							"+
							"		</div>                                                                                              "+
							"	</div>                                                                                                  ";			
				}
//			}
		});
		$('#messList').html(htmCont);	
		$('#messCnt').html('(' + GISz.shownMessCnt + ')');	
/*		console.log('MUp is ');		
		console.log(MUp);	*/	
	}
	this.drawDistrList = function(lId=997){ 
//		console.log('drawDistrList');
		var balCont = '';
		var htmCont = '';
		var layerLvl, lvlNew;

		var checked = filterClass = '';
//		var layerLvl =  ROUT.GetStrPrt($('#ulH_' + lId + ' .accHIconCont').attr("class"), 'ULlvl_', 1); 
//		var lParent =  	ROUT.GetStrPrt(ROUT.GetStrPrt($('#ulH_' + lId).attr("class"), 'ULParent_', 1), ' ', 0); 
		var lParent =  	0; 
		lvlNew = 1;
		$('.userLayerContaner').each(function(indx, element){
			if(($(element).attr("class").indexOf('_' + lId)>=0) &&
				($('#' + element.id + ' .accHIconCont').hasClass('ULlvl_' + lvlNew))){
//				console.log(element.id);
				$(element).remove();
			}
		});			
		$(ULayers).each(function(indx, element){
		if((element.parId == lId) && (element.objCnt > 0)){
			/*var actMsgStr = (element.message.length > 0) ? "Зарегистрировано <span>" + element.message.length  + "</span>"+ 
				ROUT.getCorrectDeclensionRu(element.message.length , ' оперативное донесение' , ' оперативных донесения' , ' оперативных донесений' ) : "оперативных донесений нет";
			balCont = "<h3>"+ element.name+ "</h3>" + 
				"<p>&nbsp;</p><p><span  class='openMessageList activeLink' title='Открыть журнал' id='msg_"+ element.id + "' >" + actMsgStr + 
				" </span></p><p>&nbsp;</p>";
			if(element.borderObj != undefined) {
				element.borderObj.bindPopup(balCont);
				element.borderObj.bindTooltip(element.name);

			}*/
			//console.log(element);
			if((element.borderObj != undefined)&&(element.borderObj.state == 1)){
				checked = ' checked ';
//				filterClass = ' inFilter';
				}
			var messAdd = (element.message.length > 0) ? '<span title="оперативные сообщения"> (' + element.message.length + ') </span> ' : '';
				
			htmCont += 	"	<div class='userLayerContaner ULParent_" +lParent + "_" + lId + filterClass + "' id='ulH_" + element.id + "'>															"+
						"		<div class='accHIconCont ULlvl_" + lvlNew + "' >";
																	
			htmCont += 	(element.childCnt > 0) ?	"			<span class='accHIcon accHIconDsbl'></span>" : "";
						
			htmCont += 	"		</div>"+
						"		<div class='lbl'>" + element.name + messAdd +"</div>" +
/*						"		<div class='lbl'>" + element.name + '_' + element.id + '_' + messAdd +"</div>" +				*/
/*						"		<div class='switchContaner' id='switchContUL_" + element.id + "' >"+
						"			<input type='checkbox' class='js-switch' id='switchUL_" + element.id + "' " + checked + " />"+
						"		</div>                                                                  							"+*/
						"		<div class='iconContaner'  >                                            							"+
						"			<div id='layerAddMessage_" + element.id + "' class='buttNewForm' title='оперативные сообщения'      							"+
						"onClick='GISz.openDialod(\"" + element.id + "\");' ></div>												"+
						"		</div>                                                                                              "+
/*						"		<div class='iconContaner'  >                                            							"+
						"			<div id='layerOptUL_" + element.id + "' class='buttOptions' title='Действия'      							"+
						"onClick='GIS.toggleActionPanelLayerUL(\"ulH_" + element.id + "\", " +indx + ", 1);' ></div>												"+
						"		</div>                                                                                              "+*/
						"	</div>                                                                                                  ";
			}
		});	
		$('#layerList').html(htmCont);	
/*****			$('#ulH_' + lId).after(_genFilteredList());	 на перспективу************************************/
		$('[class *= ULlvl_' + lvlNew + ']').css({'padding-left' : (lvlNew*15) + 'px'});
		$('.userLayerContaner').on('click', function (event) {event.stopPropagation(); 	
												var lId = ROUT.GetStrPrt(event.currentTarget.id, '_', 1);  
												GISz.showDistrBorderZoom(lId, $('#switchUL_' + lId).prop("checked"))
												});
		$('.userLayerContaner').on('mouseover', function (event) {event.stopPropagation(); 	
																	var lId = ROUT.GetStrPrt(event.currentTarget.id, '_', 1);  
																	GISz.showDistrBorder(lId, $('#switchUL_' + lId).prop("checked"))});
		$('.userLayerContaner').on('mouseout', function (event) {event.stopPropagation(); 	
																	var lId = ROUT.GetStrPrt(event.currentTarget.id, '_', 1);  
																			
																	GISz.hideDistrBorder(lId, $('#switchUL_' + lId).prop("checked"))});
		
		
		
		
		$('#layerList .switchContaner').each(function(indx, element){
			var id = ROUT.GetStrPrt(element.id, '_', 1);
			ULSwitch[element.id] =  new Switchery(document.querySelector('#switchUL_' + id),{ size: 'min'});
			$('#switchUL_' + id).on("change", function (event){event.stopPropagation(); GISz.toggleDistrVisible(ROUT.GetStrPrt(event.currentTarget.id, '_', 1));});			
			$('.switchery').on('click', function (event) {event.stopPropagation();});
			$('.iconContaner').on('click', function (event) {event.stopPropagation();});
//			$('#switchContUL_' + element.id).on('click', function (event) {event.stopPropagation();});
/*						if(ULSwitch[obj] != undefined){
							ULSwitch[obj].destroy();
							$('#ulH_' + obj + ' .switchery-min').remove();
							ULSwitch[obj] =  new Switchery(document.querySelector('#switchUL_' +obj),{ size: 'min'});	
						}*/
			
		});

/*		$(ULayers).each(function(indx, element){
//				if((element.parId == lId) && (element.childCnt > 0)){
			if((element.parId == lId) && (element.objCnt > 0)){
//				ULSwitch[element.id] =  new Switchery(document.querySelector('#switchUL_' + element.id),{ size: 'min'});	
//				$('#switchContUL_' + element.id + ' .switchery').on("click", 	function (event) {console.log('#switchUL_' + element.id + ''); event.stopPropagation();});
//				$('#switchUL_' + element.id).on("change", function (event){GIS.toggleAllObjUL(event.currentTarget.id);});
//				$('#layerOptUL_' + element.id).on('click', function (event) {event.stopPropagation();});
				if(element.childCnt > 0)
					UI.initULElement($('#ulH_' + element.id));
				}
			});	*/	
	}
	this.showDistrBorderZoom = function(objId, switched){	//2017_02_15 вкл/выкл объекта
		if(ULayers[ULayerIndex[objId]].borderObj != undefined){
			obj = ULayers[ULayerIndex[objId]].borderObj;
			mymap.fitBounds(obj.getBounds());			
		}
	}
	this.toggleDistrVisible = function(objId)	//2017_02_15 вкл/выкл объекта
		{
		if(ULayers[ULayerIndex[objId]].borderObj != undefined)
			{
			obj = ULayers[ULayerIndex[objId]].borderObj;
			console.log(ULayers[ULayerIndex[objId]].borderObj);
			var element = obj;
			if(element.state == 0)
				{
//				console.log('on');
				mymap.addLayer(element);
				element.state = 1;
//				$('#objUL_' + element.id).addClass('blacked');
				}
			else if(element.state == 1)
				{
//				console.log('off');
				mymap.removeLayer(element);
				element.state = 0;
//				$('#objUL_' + element.id).removeClass('blacked');
				}
			}
		}
	this.showDistrBorder = function(objId, switched)	//2017_02_15 вкл/выкл объекта
		{
		if(ULayers[ULayerIndex[objId]].borderObj != undefined)
			{
//			console.log(arguments);
			obj = ULayers[ULayerIndex[objId]].borderObj;
			if(switched){
				obj.setStyle({"weight" : 5, "color": "#ee4444"});
			} else {
				objTmp = GIS.drawFigure(obj, 1, obj.getLatLngs(), obj.info, 1,  {"weight" : 5, "color": "#ee4444", "opacity": 0.5}, obj.spec)
				mymap.addLayer(objTmp);
			}
			}
		}
	this.hideDistrBorder = function(objId, switched)	//2017_02_15 вкл/выкл объекта
		{
		if(ULayers[ULayerIndex[objId]].borderObj  != undefined)
			{
			obj = ULayers[ULayerIndex[objId]].borderObj;
			if(switched){
				obj.setStyle({"weight" : 1, "color": "#222222"}); 
			}  
			mymap.removeLayer(objTmp);
			}
		}
	this.openDialod = function(lId)	//2017_02_15 вкл/выкл объекта
		{			
		homeDistr = {};	
		homeDistr.name= ULayers[ULayerIndex[lId]].name;
		homeDistr.id= lId;
//		console.log(USER);
		FORM.genMessListDisp(lId); 	
		$( "#tabs" ).tabs();
		$( "#tabs" ).tabs( "disable", "#messContaner" );
//		$( "#tabs" ).tabs( "option", "active", 1 );
		if(USER.role>3)			
			$( "#tabs" ).tabs( "disable", "#formContaner" );
//		$( "#tabs" ).tabs( "option", "active", 0);
		$( "#tabs" ).off( "tabsactivate"); 
		$( "#tabs" ).on( "tabsactivate", function( event, ui ) {FORM.panelChanged(ui.oldPanel[0].id, ui.newPanel[0].id, event);}); 

		UI.toggleLockingPad();
		UI.toCenter('formBox');	
		UI.togglePanel('', 'formBox', 1, '');

		$( "#tabs" ).show();
		$( "#metriks" ).hide();		
		}		

	this.openDialogPU = function(idPar)	//2017_02_15 вкл/выкл объекта
		{			

		var idArr = idPar.split('_');	
		var oId = idArr[1];
		var dId = idArr[2]; 
		console.log(idPar, oId, dId);
		let content = '';
		$(GObj).each(function(index, el){ 
			
			if((el.id == oId)&&(el.deviceList.length>0)){
				$(el.deviceList).each(function(index, elem){
					console.log(elem.metriks.length);
					if(elem.deviceSystem == 'eldis24')
						content += "<div class='objTitle' id='panObjTitle'>	Показания счётчика "  +  elem.resourceName + ": " + elem.deviceName +
							" </div>	<div class='objPropertiesContaner'>";
					else if(elem.deviceSystem == 'simple'){
						content += "<div class='objTitle' id='panObjTitle'>	Показания счётчика "  +  elem.name + " " +
							" </div>	<div class='objPropertiesContaner'>";
						content += "<div   class='objPropItem'> <div class='objPropName'>Дата снятия показаний" + 
							"</div><div class='objPropValue p_valueSelectType'>" + elem.dateUp_ru + "</div></div>";
					}
					if(elem.metriks.length>0){ 
						if(elem.deviceSystem == 'eldis24')
							content += GISz.drawMetricsEldis(elem.metriks);
						else if(elem.deviceSystem == 'simple')
							content += GISz.drawMetricsSimple(elem.metriks);
							
						console.log(content);
					} else {
						content += "<div   class='objPropItem'>Показатели отсутствуют</div>";
					}
					content += '</div>';
					$("#messBodyPU").html(content);
				});
			}
		});
		$( "#tabs" ).hide();
		$( "#metriks" ).show();

		UI.toggleLockingPad();
		UI.toCenter('formBox');	
		UI.togglePanel('', 'formBox', 1, '');
		
		}

	this.drawMetricsSimple = function(metriks, period = 0)	//2017_02_15 вкл/выкл объекта
		{
		let content = '';	
		for (key in metriks){
				if(metriks[key].value != undefined){
					content += "<div   class='objPropItem'> <div class='objPropName'>" + metriks[key].name + ", " + metriks[key].measureUnit + 
						"</div><div class='objPropValue p_valueSelectType'>" + metriks[key].value + "</div></div>";
				}
			}
//		});
		return content;
		}		
	this.drawMetricsEldis = function(metriks, period = 0)	//2017_02_15 вкл/выкл объекта
		{
		var params;
		let content = '';
		if(!period)
			params = metriks[metriks.length-1];
		else{
			$(metriks).each(function(index, elem){
				if(elem.dateWithTimeBias == period) {
					params = elem;			
				}
			});
		}
//		$(params).each(function(index, elem){
		for (key in params){
			value = params[key];
			if((value != null)&&(DVCML.SomeJsonObject.Items[key] != undefined)){
				let label = (DVCML.SomeJsonObject.Items[key] == undefined) ? key : DVCML.SomeJsonObject.Items[key];
				content += "<div   class='objPropItem'> <div class='objPropName'>" + label + 
					"</div><div class='objPropValue p_valueSelectType'>" + value + "</div></div>";
			}
		}	
//		});
		return content;
		}		
	this.checkGetJSONDvc = function()	//2017_02_15 вкл/выкл объекта
		{			
		$.post("/spddl/", {type:'getDevices', start:'2019-10-16 10:00:00'}, function(str){
			console.log(str);
			});
		}			
	this.checkGetJSONObjsAll = function()	//2017_02_15 вкл/выкл объекта
		{		
		$.post("/spddl/", {type:'geoObj', cmd:'getObjects'}, function(str){ 
			var strArr = JSON.parse(str);
			var imgPath = '/src/design/main/img/gis/';
			let styleDef = {
										"color": "#ff2222",
										"fillColor": "#0000ff",
										"weight": 1,
										"fillOpacity": 0.2,
										"opacity": 0.5
									};
			$(strArr).each(function(indx, element){
				var fig;
				let geoData = JSON.parse(element.geoData);
				let color = 	(("COLOR" in geoData.features[0].properties)&&(geoData.features[0].properties.COLOR!='')) ? geoData.features[0].properties.COLOR : styleDef.color;
				let weight = 	(("WEIGHT" in geoData.features[0].properties)&&(geoData.features[0].properties.WEIGHT!='')) ? geoData.features[0].properties.WEIGHT : styleDef.weight;
				let fillColor = (("FILLCOLOR" in geoData.features[0].properties)&&(geoData.features[0].properties.FILLCOLOR!='')) ? geoData.features[0].properties.FILLCOLOR : styleDef.fillColor;
				let idObject = geoData.features[0].properties.ID_OBJECT;
				if(geoData.features[0].geometry.type == 'Point'){				
					let iconName = imgPath + geoData.features[0].properties.NAME_PIC + '.png';
						console.log(iconName);
						fig = new L.geoJSON(geoData.features[0], {
							onEachFeature: function (feature, layer) {
									layer.setIcon(new JKHIcon({iconUrl: iconName}));
								}
						});
						console.log(fig)
					} else {
						//console.log('no point');					
						let styleReg = {
													"color": color,
													"fillColor": fillColor,
													"weight": weight,
													"fillOpacity": 0.2,
													"opacity": 0.5
												};
		//				if((featureColl.features[0].geometry.type == 'Point')||(element.id>24)){
						fig = new L.geoJSON(geoData.features[0], styleReg);
					}
				let deviceEldis24 = (geoData.features[0].properties.ID_ELDIS24 != undefined)?true:false;
				fig.name = 			element.name;
				fig.id = 			element.idObj;
				fig.jkhSector_id = 	element.jkhSector_id;
				fig.np_id = 		element.np_id;
				fig.deviceList = [];
//				fig.deviceEldis24 = (geoData.features[0].properties.ID_ELDIS24 != undefined)?true:false;
//				if(deviceEldis24) fig.deviceList = [];
//				console.log(fig); 
				balCont = 'Инфо об объекте ' + fig.name + ' : ' + element.idObj;
				var popup = L.popup().setContent(balCont);
//				var popup = L.popup().function(balCont){ console.log(balCont); };
				fig.bindPopup(popup);
				fig.bindTooltip(fig.name); 

				GObj.push(fig);
				mymap.addLayer(fig);
				
/*				if(fig.hasDevice)	
					ULCluster.addLayer(fig);	
				else				
					mymap.addLayer(fig);
				
//					}*/
			});
/*		mymap.on('popupopen', function(e) {
//				   console.log(e.popup._source.feature);
		   console.log(e);
		});*/
		setTimeout('GISz.getDevicesList()', 500);	

//		console.log(GObj);
/*		figure = new L.geoJSON(test, styleReg);
		mymap.addLayer(figure);*/
		}); 
	}	
	this.checkUpJSON = function()	//2017_02_15 вкл/выкл объекта
		{
		var dist = '1110'
//		var obj = {"type":"FeatureCollection","features":[{"type":"Feature","properties":{"stroke":"#8a2060","stroke-width":2,"stroke-opacity":1,"fill":"#2f337b","fill-opacity":0.5},"geometry":{"type":"Polygon","coordinates":[[[104.14558410644531,52.29504228453735],[104.1507339477539,52.29420237796669],[104.15382385253906,52.29735194548279],[104.14901733398438,52.30071123730178],[104.14421081542969,52.30260072696773],[104.14077758789062,52.298191792327415],[104.14558410644531,52.29504228453735]]]}}]};
		var obj = '{ "type": "FeatureCollection","features": [{ "type": "Feature","geometry": {"type": "Point", "coordinates": [53.113951662666, 103.158858114705]},"properties": {"ID_OBJECT": "99025622322399119","NAME_OBJECT": "Школа №22","NAME_PIC": "school","NAME_LAYERS": "Образовательные учреждения"}}]}';
//		var obj = {"type":"FeatureCollection","features":[{ "type": "Feature","geometry": {"type": "Point", "coordinates": [53.113951662666, 103.158858114705]},"properties": {"ID_OBJECT": "99025622322399119","NAME_OBJECT": "School №22","NAME_PIC": "school","NAME_LAYERS": "Leaning department"}}]};
		$.post("/spddl/", {type:'geoObj', cmd:'add', npId:'8515', name:'Котельная', jkhSectorId:'4', obj:obj , dist:dist}, function(str){ 
			console.log(str);
		});
	
	}
	this.checkGetJSONSingle = function()	//2017_02_15 вкл/выкл объекта
		{
		var objId = 5;
		$.post("/spddl/", {type:'geoObj', cmd:'getSingle', objId:objId }, function(str){ 
			console.log(str);
		});
	
	}
	this.checkGetJSONOfNPJKH = function()	//2017_02_15 вкл/выкл объекта
		{
		var np =  8515; 
		var jkhSectorId = 4;
		$.post("/spddl/", {type:'geoObj', cmd:'getObjJSONOfSector', npId:np, jkhSectorId:jkhSectorId }, function(str){ 
			console.log(str);
		});
	
	}

	this.getDevicesList = function(){
		console.log('there are ' + GObj.length + ' geo objects' );
//		var distr = []; 
		$.post("/spddl/", {type:'getDevices'}, function(str){
//			console.log(str);
			var dvc = JSON.parse(str);
//			console.log(dvc);
			$(GObj).each(function(index, el){
				let baloonAdd = '';
				if(el.id == 49)
					console.log(el.id);
				$(dvc.SomeJsonObject.Items).each(function(indx, element){
//					console.log(element);					
//					console.log(element.objId);					
					if((el.id == element.objId)){
						if((element.deviceID != undefined)&&(element.deviceID.length > 12))
							element.deviceSystem = 'eldis24';
						else 
							element.deviceSystem = 'simple';

						console.log(el);
						console.log(element);
//					if((el.deviceEldis24)&&(el.id == element.objId)){
						el.deviceList.push(element);
						let emptyAdd = '';
						emptyAdd = (element.metriks.length>0) ? '' : ' (нет показателей)';
						if(baloonAdd == '') baloonAdd += "<h3>Устройства учёта</h3>";
						if(element.deviceSystem == 'eldis24')
							baloonAdd += "<p><span  class='showPUDetails activeLink' id='pu_"+ element.objId +  "_" + element.deviceId + "' > " + element.resourceName + ": " + element.deviceName  + emptyAdd +  " </span></p>";
						else if (element.deviceSystem == 'simple')
							baloonAdd += "<p><span  class='showPUDetails activeLink' id='pu_"+ element.objId +  "_" + element.deviceId + "' > " + element.name + emptyAdd +  " </span></p>";
//						baloonAdd += "<p><span  class='sectorBalShow activeLink' id='pu_"+ element.deviceId + "' > "+ element.deviceName + " </span></p>";
					}
				});
				if(baloonAdd != ''){
//					ULCluster.removeLayer(el);	
					el.unbindPopup();
					let balCont = '<h2>' + el.name +' ' + '(' + el.id + ')' + '</h2>';
					var popup = L.popup().setContent(balCont + baloonAdd);
					el.bindPopup(popup);	
				}
//			ULCluster.addLayer(el);						 
			});
		});
		$.post("/spddl/", {type:'getDevices', cmd:'metriksLabel'}, function(str){
			DVCML =  JSON.parse(str);
//			console.log(DVCML);
		});
	}
	this.checkDevices = function(){
		console.log('there are ' + GObj.length + ' geo objects' );
//		var distr = []; 
		$.post("/spddl/", {type:'getDevices'}, function(str){
			var dvc = JSON.parse(str);
			console.log(dvc);
			$(dvc.SomeJsonObject.Items).each(function(indx, element){
				$(GObj).each(function(index, el){
					if((el.deviceEldis24)&&(el.id == element.objId)){
						console.log(el.id);
						if(el.deviceList.length == 0){
							el.deviceList.push(element);							
						} else {
							let skip = false;
							$(el.deviceList).each(function(indx, elmnt){
								if(elmnt.deviceId == element.deviceId)  skip = true;
							});
							if(!skip)
								el.deviceList.push(element);
						}						
						balCont = 'Инфо об объекте ' + el.name + ' : ' + element.deviceName;
						
						var balCont = el.name;
						console.log(el.deviceList);
							balCont += "<p><span  class='sectorBalShow activeLink' id='sector_"+ element.deviceId + "' > "+ element.deviceName + " </span></p>";
						var popup = L.popup().setContent(balCont);
						el.bindPopup(popup);
						
//						el.popup.setContent(balCont);

						/**/
										
					}
				
				});
			});

		});
	}
	this.checkMessage = function(){
		GISz.updated = true;
		if(GISz.doUpdate){
//			console.log('checkMessage');
			var distr = []; 
			$.post("/spddl/", {type:'getMessAll'}, function(str){ 
				$(ULayers).each(function(indx, element){
					element.message = [];
					element.messageAll = [];
				});				
				MESS = JSON.parse(str);
				//console.log(MESS.length);
				$(MESS).each(function(indx, element){					
					ULayers[ULayerIndex[element.distId]].messageAll.push(element);
					if(element.status>0)
						ULayers[ULayerIndex[element.distId]].message.push(element);
				});
				GIS.colorCalc();
				GIS.drawMO();
				GISz.drawDistrList();
				GISz.drawMessList();
				if (GISz.firstLoad) 
					GISz.firstLoad = false;
				GISz.updated = false;
			});
/*			if(GObj.length >0 )
				GISz.checkDevices();
			else 
				console.log('no geo objects');*/
			
//			GISz.drawDistr('distrFillHeat');
//			GISz.drawDistrList();
//			GIS.drawMO();
		}
		setTimeout('GISz.checkMessage()', GISz.period);	
	}
}
