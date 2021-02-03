function navMonitor(){
		var trekFile = '';
//		var coordSingle = [];
		var trekFigure = [];
		var trekRunner;
		var runIndex = 0;
		var period = 10000; 
		var freshPeriod = 3600000;
		var styleIconEmpty = L.icon({
			iconUrl: "/src/design/main/img/_.gif",
			iconSize: [1, 1],
			iconAnchor: [0, 0],
			popupAnchor: [0, 0],
			});
		var styleIconRD16 = L.icon({
			iconUrl: "/src/design/main/img/png/redDot_16.png",
			iconSize: [16, 16],
			iconAnchor: [8, 8],
			popupAnchor: [0, 0],
/*			shadowUrl: "/src/design/main/img/png/redDotShadow_16.png",
			shadowSize: [16, 16],
			shadowAnchor: [8, 8]		*/
			});
		var _getTimeClass = function(obj){
			var ret = {};
			if((Date.now() - obj.time*1000) >freshPeriod){
				ret.tt =  	'TTold';
				ret.main =  'old';
			} else if (obj.speed!='0'){
				ret.tt =  	'TTmove';									
				ret.main =  'move';									
			} else {
				ret.tt =  	'TTfresh';									
				ret.main =  'fresh';									
			}
			return ret;
			
		}
		var _getObjById = function(id, returnIndex = false){
			//console.log(arguments);
			var ret; 
			var retIndex = -1;
	//		console.log(NV.length);
			NV.forEach(function(element, indx, arr){
				//console.log(element.id);
				if(element.id == id){
					ret = element;
					retIndex = indx;
	//				console.log('find!');
	//				console.log(element.id);
/*					return element;*/
				}
			});
			return (returnIndex) ? retIndex : ret;
		}
		var _cmpTimeDOM = function(a, b){
//			console.log(a); 
			//
			var a_time =  $('#' +  a.id + ' .time').attr("tmStmp");
			var b_time =  $('#' +  b.id + ' .time').attr('tmStmp');
//			console.log(a_time + ' - ' + b_time); 

			if (a_time < b_time) return 1;
			if (a_time > b_time) return -1;			
		}		
		var _cmpNameDOM = function(a, b){
			var a_name =  $('#' +  a.id + ' .label').text();
			var b_name =  $('#' +  b.id + ' .label').text();
			console.log(a_name + ' - ' + b_name); 
			if (a_name < b_name) return 1;
			if (a_name > b_name) return -1;			
		}		
		var _cmpTime = function(a, b){
			if (a.time < b.time) return 1;
			if (a.time > b.time) return -1;			
		}
		var _cmpName = function(a, b){
			var aStr =  a.client + a.name;
			var bStr =  b.client + b.name;
			if (aStr > bStr) return 1;
			if (aStr <bStr) return -1;			
		}
		var _zoomNavMapObj = function(obj){
			var timeClass, iconStyle, timeClassMain = ''; 
			let curZoom = mymap.getZoom();
			var iconType = (curZoom<10) ? 'marker':'label';
			if((obj.draw)/*&&(obj.markerType != iconType)*/){
				switch(iconType){
					case 'marker': {
						obj.setIcon(styleIconRD16);
						obj.unbindTooltip();
						obj.bindTooltip('' + obj.name + '', {permanent: false, direction: 'center'});
					} break;
					case 'label':{
						if((Date.now() - obj.time*1000) >freshPeriod){
							timeClass =  	'TTold';
							timeClassMain =  'old';
						} else if (obj.speed!='0'){
							timeClass =  	'TTmove';									
							timeClassMain =  'move';									
						} else {
							timeClass =  	'TTfresh';									
							timeClassMain =  'fresh';									
						}
						obj.setIcon(styleIconEmpty);
						obj.unbindTooltip();
						obj.bindTooltip('<b>' + obj.name + '</b>', {permanent: true, interactive:true, direction: 'center', className:timeClass}).openTooltip();
					} break;							
				}
				obj.timeClass = timeClassMain;
/*				data.lossState = lossState;
				data.speedStr = speed;				*/
				obj.markerType = iconType;					
			}
//			console.log('zoom: ' + curZoom);
		}
		var _zoomNavMap = function(){
			var timeClass, iconStyle; 
			let curZoom = mymap.getZoom();
			let	bounds = mymap.getBounds();
			var iconType = (curZoom<10) ? 'marker':'label';
			NV.forEach(function(element, indx, arr){
				if((element.draw)&&(element.markerType != iconType)){
					if(bounds.contains(element.getLatLng())){
						switch(iconType){
							case 'marker': {
								element.setIcon(styleIconRD16);
								element.unbindTooltip();
								element.bindTooltip('' + element.name + '', {permanent: false, direction: 'center'});
							} break;
							case 'label':{
								if((Date.now() - element.time*1000) >freshPeriod){
									timeClass =  'TTold';
								} else if (element.speed!='0'){
									timeClass =  'TTmove';									
								} else {
									timeClass =  'TTfresh';									
								}
								element.setIcon(styleIconEmpty);
								element.unbindTooltip();
								element.bindTooltip('<b>' + element.name + '</b>', {permanent: true, interactive:true, direction: 'center', className:timeClass}).openTooltip();
							} break;							
						}
						element.markerType = iconType;					
					}
				}
			});
			console.log('zoom: ' + curZoom);
		}
		var _cmpObj = function(data, itemIndex){
			
/*			console.log(arguments);
			console.log(itemIndex);*/
			var changeCnt = 0;
			var indx = itemIndex;
			let oldObj = NV[indx];
			if(indx >=0){
//				var tmpObj = NV[indx];
				if(((data.lat == '')&&(NV[indx].lat == ''))||((data.lat != '')&&(NV[indx].lat != ''))){
	//				changeDraw = false;
					let changed = false;
					for(var key in data){
						if(NV[indx][key] != data[key]){
							NV[indx][key] = data[key];
							if((key == 'lat')||(key == 'lon')) //изменилась геопозиция 
								NV[indx].setLatLng(new L.latLng(parseFloat(data.lat), parseFloat(data.lon)));
							changed = true;
						}
					}
					if(changed){
						let speed = (data.speed!='0') ? 'Скорость: ' + data.speed + ' км/ч': 'не движется';
						var balCont = data.client + '</br> <b>' + data.name + '</b> ' +  data.model + '</br>';
						balCont += '</br> Время последнего подключения: ' + data.timeStr + '</br>';
						balCont += '</br> ' + speed + '</br>';
						balCont += '</br> Направление движения (азимут): ' + data.angle + '';
						NV[indx].closePopup();
						NV[indx].unbindPopup();
						NV[indx].bindPopup(balCont);
						_zoomNavMapObj(NV[indx]);
						NV[indx].changed = true;
						let timeClass = _getTimeClass(NV[indx]);
//						console.log('data.timeClass is ' + data.timeClass);
//						console.log(timeClass);
						$('#objlFtr_' + data.id+ ' .time').text(data.timeStr);
						$('#objlFtr_' + data.id+ ' .time').attr("tmStmp", data.time);
						$('#objlFtr_' + data.id+ ' .time').removeClass('move fresh old');
						$('#objlFtr_' + data.id+ ' .time').addClass(timeClass.main);
						$('#objlFtr_' + data.id+ ' .speed').text(speed);
						
						changeCnt ++;
					} else{
						NV[indx].changed = false;
					}					
				} else if((data.lat == '')){ //пропала геопозиция 
					var obj = data;
					obj.draw = false;
					obj.changed = true;
					mymap.removeLayer(NV[indx]);	
					NV[indx] = obj;
					changeCnt ++;
				} else  if((data.lat != '')){ //появилась геопозиция 
					var obj =   new L.marker(
						new L.latLng(parseFloat(data.lat), parseFloat(data.lon)), 
						{draggable: false}).addTo(mymap);
					obj.draw = true;
					obj.changed = true;									
					for(var key in data){
						obj[key] = data[key];
					}
					var balCont = data.client + '</br> <b>' + data.name + '</b> ' +  data.model + '</br>';
					balCont += '</br> Время последнего подключения: ' + data.timeStr + '</br>';
					balCont += '</br> Скорость: ' + data.speed + '</br>';
					balCont += '</br> Направление движения (азимут): ' + data.angle + '';
					obj.bindPopup(balCont);
					_zoomNavMapObj(obj);
					NV[indx] = obj;
					changeCnt ++;
				}
				
				if(NV[indx].changed){
					console.log('[changed]!!!');
					console.log(data.name  + ': ' + data.timeStr);
//					console.log(NV[indx]);
				}
				else{
/*					console.log('[not]!!!');
					console.log(tmpObj);
					console.log(NV[indx]);*/
					
				}
				
			}
			return changeCnt;
		}

		var _drawPath = function(coordSingle, obj, date){
			//mymap.removeLayer(trekFigure);
			let color = '#' +ROUT.getRandomInt(0, 65).toString(16) + 
													ROUT.getRandomInt(0, 65).toString(16) + 
													ROUT.getRandomInt(0, 65).toString(16);
			let style = {
							"color": color,
							"weight": 3,
//							"fillColor": "#ccc",
//							"fillOpacity": 0.8,
//							"opacity": 0,
							"smoothFactor": 1,
							"noClip" : true
						};
			var trek = new L.polyline(coordSingle, style).addTo(mymap);
			trek.id = obj.id;
			var balCont = 'Маршрут <b>' + obj.name + '</b> в период с ' +  date[0] + ' по ' +  date[1] + '</br>';
			balCont += '</br> количество точек: ' + coordSingle.length + '</br>';
			trek.bindPopup(balCont);

			mymap.fitBounds(coordSingle);
			trekFigure.push(trek);
//			console.log(trekFigure);
		}
		var _initObj = function(data){
/*			obj.timeClass = '';
			obj.lossState = '';
			obj.speed = '';
			*/


			if(data.lat == ''){
				var obj = data;
				obj.draw = false;
			} else {
				var obj =   new L.marker(
					new L.latLng(parseFloat(data.lat), parseFloat(data.lon)), 
					{draggable: false}).addTo(mymap);
				obj.draw = true;
				obj.changed = false;				
				obj.markerType = 'marker';				
				for(var key in data){
					obj[key] = data[key];
				}
				var balCont = obj.client + '</br> <b>' + obj.name + '</b> ' +  obj.model + '</br>';
				balCont += '</br> Время последнего подключения: ' + obj.timeStr + '</br>';
				balCont += '</br> Скорость: ' + obj.speed + '</br>';
				balCont += '</br> Направление движения (азимут): ' + obj.angle + '';
				obj.bindPopup(balCont);
				obj.setIcon(styleIconRD16);
//				obj.bindTooltip('<b>' + obj.name + '</b>', {permanent: true, direction: 'center'}).openTooltip();
				obj.bindTooltip('' + obj.name + '', {permanent: false, direction: 'center'});
			}
//			console.log(obj);
			return obj;
		}
		var _genObjList = function(objList, hideGeoless = false){
			console.log(arguments);
			var content = '', speed;
			var counter = 0;
			objList.forEach(function(element, indx, arr){
				speed = '';
				data = element;
				if((data.draw)||(!hideGeoless)){
					counter++;
					if(data.time!=''){
						//var timeClass = ((Date.now() - data.time*1000) >freshPeriod)? 'old' : 'fresh';
						if((Date.now() - data.time*1000) >freshPeriod){
							timeClass =  'old';
						} else if (element.speed!='0'){
							timeClass =  'move';									
							//console.log( element.name + '-' + element.speed);
						} else {
							timeClass =  'fresh';									
							//console.log( element.name + '-' + element.speed);
						}
						
					} else {
						var timeClass = 'new';
					}
					if(data.lat == ''){
						var lossState =  'loss';
						var titleActive =  'Позиция объекта не определена';
						var labelClass = '';
						var labelAction = '';
					} else {
						var lossState = 'norm';
						var titleActive = 'Перейти к объекту';
						var labelClass = 'activeLink';
						speed = (data.speed) ? 'Скорость: ' + data.speed + ' км/ч': 'не движется';

						//speed = (data.speed)?'скорость: ' + data.speed + 'км/ч':'';
						var labelAction =  " onClick='NAV.showObj(" + data.id + ");'";
					}
					if(data.changed){
						var changed = ' <span style="color:#f88" title="значение обновилось">!</span> ';
						console.log(data.name + changed);
						data.changed = false;
					} else{
						var changed = '';
					}
					
					var lossState = (data.lat == '')? 'loss' : 'norm';
					data.timeClass = timeClass;
					data.lossState = lossState;
					data.speedStr = speed;
					var titleActive = (data.lat == '')? 'Позиция объекта не определена' : 'Перейти к объекту';
					content += "<li  class='" + data.lossState + "' id='objlFtr_" + data.id + "'><div class = 'contObjList'>";//<nobr>";
					content += "<span class='" +labelClass+ " label' id='objSp_" + data.id + "' " + labelAction;
					content += "title='" + titleActive + "'>" + data.client + '</br> <b>' +   data.name + '</b> ' +  data.model + changed + "</span></br>";
					content += "<span class = 'speed'>" + speed + "</span>";
					content += "<span class='selectDateCont' id='selectDateCont_" + data.id + "'>" +  "</span>";
					content += "<span class='buttDate activeButton' title='Журналы' id='dateTrek_" + data.id + "'>" +  "</span>";
					content += "<span class = 'number'>" + counter + "</span>";
					content += "<span title='Время последнего подключения' tmStmp='" + data.time + "' class = 'time " + data.timeClass + "'>" +data.timeStr  + "</span></div></li>";
				}
			});
			if(content != ''){
//					console.log(content);
				content = '<ul>' + content + '</ul>';				
				$('#objListBoxNav .content').html(content);
//				$('#navObjCnt').html(content);
			}		
			var resTxt = (counter>0)?'Отслеживается <b>' + counter + '</b> ' +ROUT.getCorrectDeclensionRu(counter, 'объект', 'объекта', 'объектов') + ' ' : 'Нет объектов';
			$('#navObjCnt').html(resTxt);
			$('.buttDate').on('click', function (event) {NAV.selectDate(ROUT.GetStrPrt(event.currentTarget.id, '_', 1))});
		}
		var _loadData = function(start = false){
			var content = '';
			var objArr = [];			
			var counter = 0;
			var changed = 0;
			$.post("/spddl/", {type:'navForest'}, function(str){
				str = str.replace(/[\{-\}]/gi, '').replace(/\[/gi, '_{').replace(/\]/gi, '}_');
				var strArr = str.split('_,_');
				strArr.forEach(function(element, indx, arr){
					if(!indx){
						element = element.replace(/\_\{/gi, '{');
					} else if(indx == strArr.length-1){
						element = element.replace(/\}\_/gi, '}');						
					}
					if(element.length>10){
						counter ++;
						element = element.replace(/\":,/gi, '":"",').replace(/\":}/gi, '":""}');
						//console.log(element);
						let data = JSON.parse(element);
						if(data.time!=''){
							var date = new Date(data.time*1000);
							var hours = 	date.getHours();
							var minutes = 	"0" + date.getMinutes();
							var seconds = 	"0" + date.getSeconds();
							var day = 		"0" + date.getDate();
							var month = 	"0" + (1 + date.getMonth());
							var year = 		date.getFullYear();
							data.timeStr = day.substr(-2) + '/' + month.substr(-2) + '/' + year + ' ' + hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);						
						} else {
							data.timeStr = 'не подключался';
						}
						
						if(start){   //инициализация объектов
							NVIndex[data.id] = indx;
							objArr.push(_initObj(data));
						}else {
							objIdex = _getObjById(data.id, true);
							changed += _cmpObj(data, objIdex);
						}
						
					}
				});
				console.log('changed is ' + changed);
				if(start){
					_genObjList(objArr);
					UI.pleaseWait();
				}
				else if(changed>0){
					NAV.sortListDOM();
				}
				if(!start)
					NAV.loadingInProgress = false;
//				UI.pleaseWait();
			});				
			if(start){
				setTimeout('NAV.counter()', period);
				return objArr;
			}
		}


		this.sortType = 'name';
		this.hideGeoless = false;
		this.loadingInProgress = false;
		this.doUpdate = false;
		this.emptyDateInputValue = 'выберите интервал времени';
		this.emptyDateInputClass = '';
		this.selectDateComplete = function(formattedDate, date, inst){			 
			let objId = ROUT.GetStrPrt(inst.el.id, '_', 1);
			$('#searchDate_' + objId + '').show();
			$('#clearDate_' + objId + '').show();
		}
		this.selectDate = function(objId){
//			console.log($('#selectDateCont_' + objId).html());
			var dateElem = '<input type="text" id="selectDate_'+ objId + '" value="">' + 
							'<span class = "searchFilter cursorPointer" id="searchDate_' + objId + '" title="Показать маршрут"></span>' + 
							'<span class = "closeFilter cursorPointer" id="clearDate_' + objId + '" title="Отмена"></span>';
			$('#selectDateCont_' + objId).html(dateElem);
			$('#selectDate_' + objId).val(NAV.emptyDateInputValue);
			$('#selectDateCont_' + objId + ' span').hide();
/*			$('#searchDate_' + objId + ' span').hide();*/
			$('#clearDate_' + objId + '').on('click', function (event) { 
							let objId = ROUT.GetStrPrt(event.target.id, '_', 1); 
							$('#selectDateCont_' + objId + ' span').hide();
							$('#selectDateCont_' + objId).hide();
							$('#dateTrek_' + objId).show();			
							$('#selectDate_' + objId).data('datepicker').destroy();
							if(trekFigure.length){ 
								let pos=-1;
								trekFigure.forEach(function(element, indx, arr){
									if(element.id == objId){										
										mymap.removeLayer(element);	
										pos = indx;
									}
								});
								if(pos>=0){
									trekFigure.splice(pos, 1);
								}
							}
						});
			$('#searchDate_' + objId + '').on('click', function (event) { 
							var dates = [];
							console.log('Draw trek');
//							console.log($('#selectDate_' + objId).data('datepicker').selectedDates);
							$('#selectDate_' + objId).data('datepicker').selectedDates.forEach(function(element, indx, arr){
								dates[indx] = Date.parse(element)/1000;
							});
							var trekArr = [], dateRange =[];
							UI.pleaseWait();
							$.post("/spddl/", {type:'navForestSingle', obj:objId, date: dates}, function(str){
								/*console.log(str);*/
								var objRmt;
								str = str.replace(/[\{-\}]/gi, '').replace(/\[/gi, '_{').replace(/\]/gi, '}_');
								var strArr = str.split('_,_');
								strArr.forEach(function(element, indx, arr){
									if(!indx){
										element = element.replace(/\_\{/gi, '{');
									} else if(indx == strArr.length-1){
										element = element.replace(/\}\_/gi, '}');						
									}
									if(element.length>10){
										counter ++;
										element = element.replace(/\":,/gi, '":"",').replace(/\":}/gi, '":""}');
										let data = JSON.parse(element);
										console.log(data);
										trekArr.push( new L.latLng(parseFloat(data.lat), parseFloat(data.lon)));
										if ((!indx)||(indx === arr.length - 1)){
											let date = new Date(data.time*1000);
											let hours = 	date.getHours();
											let minutes = 	"0" + date.getMinutes();
											let seconds = 	"0" + date.getSeconds();
											let day = 		"0" + date.getDate();
											let month = 	"0" + (1 + date.getMonth());
											let year = 		date.getFullYear();
											dateRange.push(day.substr(-2) + '/' + month.substr(-2) + '/' + year + ' ' + hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2));
										}
										if(!indx){
											objRmt = data;
										}
									}
								});					
								if(trekArr.length>0)
									//_drawPath = function(coordSingle, obj, date){
									_drawPath(trekArr, objRmt, dateRange);
								UI.pleaseWait();
							});
			});
			$('#selectDateCont_' + objId).show();
			$('#dateTrek_' + objId).hide();			
			$('#selectDate_' + objId).datepicker({
				onSelect: function(formattedDate, date, inst){	
					NAV.selectDateComplete(formattedDate, date, inst);
				},	
				onShow: function(dp, animationCompleted){
					if (!animationCompleted) {
//						log('start showing')
					} else {
//						console.log($('#selectDate_' + objId).data('datepicker').selectedDates);
						if($('#selectDate_' + objId).data('datepicker').selectedDates.length==0)
							$('#selectDate_' + objId).val('');
					}
				},
				onHide: function(dp, animationCompleted){
					if (!animationCompleted) {
//						log('start showing')
					} else {
						if($('#selectDate_' + objId).val() == ''){
							$('#selectDate_' + objId).val(NAV.emptyDateInputValue);
						}
					}
				},
				/*onSelect: function(formattedDate, date, inst){	
					NAV.selectDateComplete(formattedDate, date, inst);
				},	*/
				maxDate: new Date(),
				range: true,	
				minutesStep: 15,
				multipleDatesSeparator: ' -',
				position: "top left",
				timepicker:true
			});
			myDatepicker = $('#selectDate_' + objId).data('datepicker');  
			
//			myDatepicker.show();
/*			myDatepickerContaner.show();
			myDatepicker.next();
			console.log(myDatepicker);
			console.log('Date : ' + objId);	*/		
		}
		this.showLoading = function(){
			if(this.loadingInProgress){
				$('#progressShow').text($('#progressShow').text() + '.');
				setTimeout('NAV.showLoading()', 100);
			}
			else{
				$('#progressShow').text('');
				//console.log('stop');
			}
		}
		
/*2018_04_07 сортировка DOM объектов */		
		this.sortListDOM = function(value=this.sortType){
			var $contaner = $('#objListBoxNavContaner ul');
			var $list = $('#objListBoxNavContaner ul li');
//			console.log($list.length);
			if(value == 'time')
				$list.sort(_cmpTimeDOM);
			else if(value == 'name')
				$list.sort(_cmpNameDOM);	
			$list.detach().appendTo($contaner);
			$list.each(function(indx, element){
				$('#' +  element.id + ' .number').text(indx+1);
			});
			
		}
/*2018_03_30 сортировка объектов*/		
		this.sortList = function(value=this.sortType){
/*			if(value == '')
				value = this.sortType;*/
			//e.target.value
			//console.log(e.target.value)
			var arr = NV;
			if(value == 'time')
				arr.sort(_cmpTime);
			else if(value == 'name')
				arr.sort(_cmpName);				
/*			this.hideGeoless = e.target.checked;*/
			this.sortType = value;
			_genObjList(arr, this.hideGeoless);
		};
/*2018_03_30 вкл/выкл объектов без геопозиции*/		
		this.geolessToggle = function(e){
			this.hideGeoless = e.target.checked;
			_genObjList(NV, this.hideGeoless);
		};
		this.showObj = function(id){
			var obj = _getObjById(id);
/*			console.log(NVIndex);
			console.log(NV[NVIndex[id]]);
			var sCoord = NV[NVIndex[id]].getLatLng();*/
			var sCoord = obj.getLatLng();
			mymap.fitBounds(L.latLngBounds(sCoord, sCoord));
			obj.openPopup();	
		};
		this.start = function(){
			UI.pleaseWait();
			NV = _loadData(true);
			$('#objListBoxNav').css({'display':'block',  'height':   $('#mapid').height() - $('#toolBox').height()- 70});
			$('#objListBoxNav .content').css({ 'height':   $('#mapid').height() - $('#toolBox').height()- 100});
			$('#TBMonActionContaner').show();
			$('#TBMonStop').show();
			mymap.on('zoom', _zoomNavMap);	
			mymap.on('move', _zoomNavMap);	
			NAV.doUpdate = true;
//			NAV.counter()
		}
		this.stop = function(){
			NAV.doUpdate = false;
			$('#TBMonActionContaner').hide();
			$('#TBMonStop').hide();
		}

		this.counter = function()
			{
//			console.log(NAV.doUpdate);
			if(NAV.doUpdate)
				{
				this.loadingInProgress = true;
				this.showLoading(); 
//				UI.pleaseWait();
				
				_loadData();
//				NAV.checkUpdate();
				setTimeout('NAV.counter()', period);
//				fullCnt ++;
				}
			}			

		
	}
	
		
