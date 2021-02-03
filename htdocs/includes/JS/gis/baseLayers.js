	var attribution = 'RKO Foundation © <a href="http://www.gisdo.ru">RKO Foundation</a>';
	var ya = L.tileLayer('http://vec0{s}.maps.yandex.net/tiles?l=map&x={x}&y={y}&z={z}', {
		minZoom: 0,
		maxZoom: 19,	
		subdomains   : '1234', 
		isElliptical : true,
/*		crs: L.CRS.EPSG3395,
		crs: L.CRS.EPSG3857,  L.CRS.EPSG4326,*/
		attribution : '' +attribution	
		});	
	var mpDark = L.tileLayer('https://api.tiles.mapbox.com/v4/mapbox.dark/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiZm1lYXQiLCJhIjoiY2l0ejh6Y2QwMDAzczQ2cDZ5ODRjNThjayJ9.HLytSaSnJiptk2u-e9UcGA', 
		{
		/*minZoom: 0,
		maxZoom: 19,	
		subdomains   : '1234', */
		isElliptical : false,
/*		crs: L.CRS.EPSG3395,
		crs: L.CRS.EPSG3857,  L.CRS.EPSG4326,*/
		attribution : '' +attribution	
		});
	var yaSat = L.tileLayer('http://sat0{s}.maps.yandex.net/tiles?l=sat&x={x}&y={y}&z={z}', {
		minZoom: 0,
		maxZoom: 19,	
		subdomains   : '1234', 
		isElliptical : true,
/*		crs: L.CRS.EPSG3395,*/
/*		crs: L.CRS.EPSG3857,  L.CRS.EPSG4326,*/
		attribution : '' +attribution	
		});
	var yaPub = L.tileLayer('http://0{s}.pvec.maps.yandex.net?l=pmap&x={x}&y={y}&z={z}', {
		minZoom: 0,
		maxZoom: 20,	
		subdomains   : '1234', 
		isElliptical : true,
/*		crs: L.CRS.EPSG3395,*/
/*		crs: L.CRS.EPSG3857,  L.CRS.EPSG4326,*/
		attribution : '' +attribution	
		});
	var Dgis = L.tileLayer('http://tile{s}.maps.2gis.com/tiles?x={x}&y={y}&z={z}', {
		minZoom: 0,
		maxZoom: 18,	
		subdomains   : '0123', 
		isElliptical : false,
		
/*		crs: L.CRS.EPSG3395,
		crs: L.CRS.EPSG3857,  L.CRS.EPSG4326,*/
		attribution : '' +attribution	
		});
	var roadmap = L.tileLayer('http://b.tile.openstreetmap.org/{z}/{x}/{y}.png', {
 		isElliptical : false,
   attribution : 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
				'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +attribution
		});
	var cyclemap = L.tileLayer('http://b.tile.thunderforest.com/cycle/{z}/{x}/{y}.png', {
 		isElliptical : false,
   attribution : attribution
		});
	var hybrid_G = 	L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
			maxZoom: 20,
		isElliptical : false,
			attribution : attribution,
			subdomains:['mt0','mt1','mt2','mt3']
		});  
	var api_key_diGlobe = 'pk.eyJ1IjoiZGlnaXRhbGdsb2JlIiwiYSI6ImNpdXc4NGRlazAzeTQyem1tbW13cTd5bngifQ.-DbwqCykHcM6FD93ekaFIw';
	var hybrid_DG = new L.tileLayer('https://{s}.tiles.mapbox.com/v4/digitalglobe.nal0mpda/{z}/{x}/{y}.png?access_token=' + api_key_diGlobe, {
		isElliptical : false,
		minZoom: 1,
		maxZoom: 19,
		attribution: '(c) <a href="http://microsites.digitalglobe.com/interactive/basemap_vivid/">DigitalGlobe</a> , (c) OpenStreetMap, (c) Mapbox, ' + attribution
		});
	var sputnik = L.tileLayer('http://tiles.maps.sputnik.ru/{z}/{x}/{y}.png', {
 		isElliptical : false,
           maxZoom: 19
        });
var pkk = L.tileLayer.wms(
			'http://pkk5.rosreestr.ru/arcgis/services/Cadastre/CadastreWMS/MapServer/WMSServer', {
			layers: '16,15,14,13,11,10,9,22,21,20,19,18,7,6',
			transparent:'false'
		});		
var pkkTr = L.tileLayer.wms(
			'http://pkk5.rosreestr.ru/arcgis/services/Cadastre/CadastreWMS/MapServer/WMSServer', {
			layers: '16,15,14,13,11,10,9,22,21,20,19,18,7,6',
			id: 'pkk',
			transparent:true,
			opacity: 0.5
/*			,
			crs: L.CRS.EPSG4326*/

		});		

	var zoom_control = L.control.zoom({ position: 'topright' }); 
	roadmap.addTo(mymap);
		var baseLayers = {	'OpenStreetMap':roadmap, 
							'Яндекс':ya,
							'Яндекс спутник':yaSat,
							'2GIS':Dgis,
							'mpDark':mpDark,
/*							'Публичная кадастровая карта':pkk,*/
							'Google гибрид':hybrid_G,
/*							'DigitalGlobe':hybrid_DG, */
							'Спутник (Ростелеком)':sputnik};
		var overLayers = {	'Публичная кадастровая карта (прозрачная)':pkkTr};
		var cntrlLayers = new L.Control.Layers(baseLayers, overLayers).addTo(mymap);
//		mymap.on('baselayerchange ', GIS.layerSwitch);
//		mymap.on('overlayadd', GIS.layerSwitch);
//		mymap.on('load', function(){console.log('fired'  );});
//		$(document).ready(function(){console.log('fired'  ); });
//		mymap.on('load', function(){alert('!');});
/*		mymap.addControl(cntrlLayers);*/
		mymap.addControl(zoom_control);
		var popup = L.popup();					
		function onMapClick(e) {
			if(pkkActive)
				{
				var valueDelim = '::';
				var paramDelim = '##';
				var objDelim = '||';
				var strOut = '';
				UI.pleaseWait();
/*				console.log(e.layerPoint);
				console.log(e.latlng.lat );*/
				$.post("/spddl/", {type:'getPKK', lat:e.latlng.lat,  lng:e.latlng.lng }, function(str)
					{	
					UI.pleaseWait();

					if(str != '0')
						{
						var strArr = str.split(objDelim); 
						$(strArr).each(function(indx, element){
						if(strArr[indx].length > 10)
							{					
							var parArr = element.split(paramDelim); 
							$(parArr).each(function(indx, element){
							if(parArr[indx].length > 10)
								{					
								var valArr = element.split(valueDelim); 
								strOut += (strOut != '') ? '</p>': '';
								strOut += '<p><b>' + valArr[0] + '</b>   <span class="value" id="">' +  valArr[1] + '</span>';
								
								}});
							}});

						}
					else
						strOut += '<p><b>Информации по объекту не найдено!</b></p>';
//					popupContentGl = "<div class='balContaner'><div class='balMainInfo'><div class='balName'>" + str + "</div></div></div>";
					popup
						.setLatLng(e.latlng)
						.setContent('<div class="balContaner">' + strOut + '</div>')
						.openOn(mymap);
					});

//				popupContentGl = "<div class='balContaner'><div class='balMainInfo'><div class='balName'>" + e.latlng.toString() + "</div></div></div>";
				
			

/*			
			mymap.removeLayer(popup);
			if((GIS.home.lat != undefined)||(GIS.home.center != undefined))
				popupContentGl = "<div class='balContaner'><div class='balMainInfo'><div class='balName'><span class='activeButton' onClick='GIS.routingToPoint()'>Маршрут сюда</span></div></div></div>";
			else 	
				popupContentGl = "<div class='balContaner'><div class='balMainInfo'><div class='balName'>" + e.latlng.toString() + "</div></div></div>";
			popup
				.setLatLng(e.latlng)
//				.setContent("You clicked the map at " + e.latlng.toString())
				.setContent(popupContentGl)
				.openOn(mymap);
*/				
				}
			else
			{
				mymap.removeLayer(popup);
				console.log(mymap.getZoom());
			}
		}
    mymap.on('baselayerchange', function (e) {
      var center = mymap.getCenter();
      var zoom = mymap.getZoom();
//	  console.log('isElliptical - ' );
//	  console.log(e.layer.options.isElliptical );
      mymap.options.crs = e.layer.options.isElliptical ? L.CRS.EPSG3395 : L.CRS.EPSG3857;
      mymap._resetView(center, zoom, false, false);
    });	  
	mymap.on('overlayadd', function (e) {
		if(e.layer.options.id == 'pkk')
			{
//			console.log('pkk - on');
			$('#mapid').css({'cursor': 'help'});
			pkkActive = true;
			}
		});		
	mymap.on('overlayremove', function (e) {
		if(e.layer.options.id == 'pkk')
			{
			console.log('pkk - off');
			$('#mapid').css({'cursor': 'grab'});
			pkkActive = false;
			}
		});		

	mymap.on('popupopen', function (e) {
		console.log('openPopup!');
		
		$('.newForm').on('click', function (e) {var lId = ROUT.GetStrPrt($(this).prop('id'),'_',1);  FORM.newForm(lId);});
		$('.openMessageList').on('click', function (e) {var lId = ROUT.GetStrPrt($(this).prop('id'),'_',1);  GISz.openDialod(lId);/*FORM.mesgList(lId)*/});
		$('.layerBal').on('click', function (e) {var lId = ROUT.GetStrPrt($(this).prop('id'),'_',1);  GIS.balLayerClick(lId)});
		$('.layerBalZoom').on('click', function (e) {var lId = ROUT.GetStrPrt($(this).prop('id'),'_',1);  GIS.zoomObj(lId)});
		$('.sectorBalShow').on('click', function (e) {var lArr = $(this).prop('id').split('_');  GIS.changeVectorFigure(lArr[1], lArr[0])});
		$('.showPUDetails').on('click', function (e) { e.stopPropagation(); /*var puId = ROUT.GetStrPrt($(this).prop('id'),'_',1); */ GISz.openDialogPU($(this).prop('id'));});
		$('.sectorBalShowAll').on('click', function (e) {let lArr = $(this).prop('id').split('_'); GIS.changeVectorFigureAll(lArr[1], lArr[0])});
		
		
		});
	mymap.on('click', onMapClick);
/*	mymap.on('zoomlevelschange',  function (e) {
			console.log(mymap.getZoom());
		
	});*/
	mymap.addLayer(ULCluster);
	function addToList(data) 
		{
		}
/******************************for drawing sectors 02_2018************************************/		
		