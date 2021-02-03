function trekRoutine(){
		var trekFile = '';
		var coordSingle = [];
		var trekFigure;
		var trekRunner;
		var runIndex = 0;
		var period = 200; 
/****реализовано только вытаскивание координат трека**/		
		var _parseKML = function(data){ 
			coordSingle = [];
			$('#TBTrekShow').hide();
			$('#TBTrekRun').hide();
			$(data).find('coordinates').each(function(){
				let coordArr = $(this).html().split(' ');
				coordArr.forEach(function(element, indx, arr){
					if(element.length>10){
						let coordTriade = element.split(',');
						coordSingle.push( new L.latLng(parseFloat(coordTriade[1]), parseFloat(coordTriade[0])));	
					}
				});
            });         
//			console.log(coordSingle);
			if(coordSingle.length > 0){
				$('#TBTrekShow').show();
				$('#TBTrekRun').show();
			}
//            $('#content_div').html(html); // выводим данные
		}
		var _getFileContent = function(file){
//			console.log(arguments);
			var fileReader = new FileReader();
			fileReader.onload = function(event) {
				_parseKML(event.target.result);
				UI.pleaseWait();
				return event.target.result;
				}
			fileReader.readAsText(file);
		}
		this.showRun = function(){
			if(runIndex<coordSingle.length){
				trekRunner.setLatLng(coordSingle[runIndex]);
				runIndex ++;
				setTimeout('TREK.showRun()', period);				
			}
		}
		this.getPath = function(event){
			UI.pleaseWait();
			let textContent = _getFileContent(event.target.files[0]);
		}
		this.tunPath = function(event){
			//mymap.removeLayer(trekRunner);
			trekRunner =  new L.marker(coordSingle[runIndex], {draggable: false}).addTo(mymap);
			runIndex = 1;
			this.showRun();
			
		}
		this.drawPath = function(event){
			//mymap.removeLayer(trekFigure);
			let style = {
							"color": "#f22",
							"weight": 3,
//							"fillColor": "#ccc",
//							"fillOpacity": 0.8,
//							"opacity": 0,
							"smoothFactor": 1,
							"noClip" : true
						};
			trekFigure = new L.polyline(coordSingle, style).addTo(mymap);
			mymap.fitBounds(coordSingle);
//			console.log(trekFigure);
		}
		
	}
	
	function counter()
		{
		if(fullCnt<devNumber)
			{
			fullCnt ++;
			}
		else
			fullCnt = 1;
		checkUpdate(fullCnt);
		setTimeout('counter()', period);
		}			
/*			$(data).find('coordinates').each(function(){
                var id_user = $(this).find('role').attr('id_user'); // получаем значение атрибута id_user
                var first_name = $(this).find('first_name').html(); // получаем значение тега first_name
                var last_name = $(this).find('last_name').html(); // получаем значение тега last_name
                var role = $(this).find('role').html(); // получаем значение тега role
                html += "<label>ID: "+id_user+"</label><br/>";
                html += "<label>Пользователь: "+first_name+" "+last_name+"</label><br/>";
                html += "<label>Права: "+role+"</label>";   
                html += "<hr/>";  
            });         
$('#check').on('change', function (event) {
	hash = textContentV = '';
	var files = event.target.files;
//			console.log(event.target.files);
	for (var i = 0; i < files.length; i++) {
		let file = files[i];
		//fileName =  file.name; //console.log(file);
		var fileReader = new FileReader();
		fileReader.onload = function(event) {
			textContentV = event.target.result;
			$('#btnV').prop('disabled', false);
			
		}
	fileReader.readAsText(file);
//			fileReader.readAsArrayBuffer (file);
	hash = GetStrPrt(GetStrPrt(file.name, 'TX ', 1), ' -- Date', 0);
	}
//			verifyApostile(hash, textContentV);
});
*/