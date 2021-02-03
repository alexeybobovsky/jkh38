function formRoutine(){
	var cloneMessId = 0;
	var clone = {};
	var REASON = {};
	var tIndex = 0;
	var doEdit = false;
	var cloneMessType = '';
	var formName = '';
	var mesField = [];
		mesField['l_name'] = "Муниципальное образование";
		mesField['np'] = "Населенный пункт";
		mesField['incidentDate'] = "Время и дата нарушения";
		mesField['zhkhSectorName'] = "Отрасль ЖКХ";
		mesField['infrastructureName'] = "Пострадавшая инфраструктура";
		mesField['reason'] = "Причина выхода из строя оборудования";
		mesField['hardware'] = "Объект, на котором произошло нарушение";
		mesField['hardwareType'] = "Тип вышедшего из строя оборудования";
		mesField['initiator'] = "ФИО и должность инициатора";
		mesField['consumerNum'] = "Общее количество пострадавших";
		mesField['orgName'] = "Наименование предприятия, устраняющего нарушение";
		mesField['orgUsedForce'] = "Задействованные силы и средства предприятия, которое устраняет технологическое нарушение";
		mesField['dateCreate'] = "Время и дата передачи сообщения о технологическом нарушении";
		mesField['bossOfWork'] = "ФИО и должность руководителя работ по устранению нарушения";
		mesField['bossOfCity'] = "ФИО руководителя муниципалитета, контролирующего устранене";
		mesField['badNewsMen'] = "ФИО, должность, наименование организации лица, несвоевременно передавшего в информацию о нарушении";
		mesField['user_name'] = "Пользователь системы, создавший запись";
		mesField['status'] = "Статус донесения";
		mesField['closeDate'] = "Время и дата устранения нарушения";
		mesField['comments'] = "Коментарии";
	var mesField__ = {
			l_name : {'label' : 'Муниципальное образование', 'type' : 'i'},
			np : {'label' : 'Населенный пункт'},
			zhkhSectorName : {'label' : 'Отрасль ЖКХ'},
			orgName : {'label' : 'Наименование предприятия, устраняющего нарушение'},
			orgUsedForce : {'label' : 'Задействованные силы и средства предприятия, которое устраняет технологическое нарушение'},
			dateCreate : {'label' : 'Время и дата передачи сообщения о технологическом нарушении'},
			incidentDate : {'label' : 'Время и дата нарушения'},
			reason : {'label' : 'Причина выхода из строя оборудования'},
			hardware : {'label' : 'Объект, на котором произошло нарушение'},
			hardwareType : {'label' : 'Тип вышедшего из строя оборудования'},
			initiator : {'label' : 'ФИО и должность инициатора'},
			consumerNum : {'label' : 'Общее количество пострадавших'},
			bossOfWork : {'label' : 'ФИО и должность руководителя работ по устранению нарушения'},
			bossOfCity : {'label' : 'ФИО руководителя муниципалитета, контролирующего устранене'},
			badNewsMen : {'label' : 'ФИО, должность, наименование организации лица, несвоевременно передавшего в информацию о нарушении'},
			user_name : {'label' : 'Пользователь системы, создавший запись'},
			status : {'label' : 'Статус донесения'},
			closeDate : {'label' : 'Время и дата устранения нарушения'},
			comments : {'label' : 'Коментарии'}
		};

	this.getMessage = function(messId = 0){
		var messSingle = {};
		if (messId > 0) {
			$(MESS).each(function(indx, element){
			if(element.messId == messId)
				messSingle = element;
			});
		}
		return messSingle;
	}
	this.panelChanged = function(oldP, newP, evnt){
//			if(evnt.originalEvent!=undefined)
		if(oldP!=newP){
			var manual = (evnt.originalEvent!=undefined) ? true : false;
	//		console.log(manual);
			switch(newP){
				case 'formContaner' :{
					$('#messBody').html('');
					var formEl = {};
					$( "#tabs" ).tabs( "disable", "#messContaner" );
					if(manual){
						//console.log(manual, cloneMessId);
//						formEl = this.getFormElrments();
					} else {
//						console.log(manual, cloneMessId);
//								console.log(messId);
//		var clone = {};
//		var cloneFields = 
		/*				if(cloneMessId){							
							$(MESS).each(function(indx, element){
							if(element.messId == cloneMessId)
								clone = element;
							});
						}*/
//						formEl = this.getFormElrments();
//						cloneMessId	= "";			
//						console.log(formEl);
						
//						$('#infrastructureId').val('change');
//						$('#infrastructureId').trigger('change');
//						$('#reasonId').trigger('change');							
					}
					formEl = this.getFormElrments();
					console.log(formEl);
					$("#mainForm ").html('');
					$("#mainForm ").dform(formEl);
					$("#tempereture").mask('000', {'translation': {0: {pattern: /[-+0-9*]/}}});
	//				console.log($('#incidentDate'));
	//				console.log($.timepicker);  
	 
					$("#formSubmit").off('click');
					$("#formSubmit").on('click' , function (event) {
						var doPost = true;						
						$('#mainForm input:visible').each(function(indx, element){
							if($('#' +element.id).hasClass("needed")){
//								console.log(element.id);
//								console.log( $('#' +element.id).val());
								if($('#' +element.id).val()==''){
									console.log('is empty!');
									console.log(element.id, $('#' +element.id).val());
									doPost = false;	 
								} 
							}
						});
						
						if(doPost){
							UI.pleaseWait();			
							var formData = $("#mainForm ").serializeJSON();
									console.log('formData');
									console.log(formData);							
							$.post("/spddl/", {type:'addForm', data:formData}, function(str)
								{
								UI.pleaseWait();			
								console.log(str); 			
								if(str == 1){
									if($('mymap').selector==undefined){
										UI.showMessage('info', 'Оперативное донесение доставлено');
										$("#mConfirm").off('click');
										$("#mConfirm").on('click' , function (event) {
												window.location.href = window.location.href; 										
										});
									} else {
										GISz.checkMessage();
										UI.togglePanel('', 'formBox', 1, '');
										UI.showMessage('info', 'Оперативное донесение доставлено');									
									}
									} else {
										if($('mymap').selector==undefined){
											UI.showMessage('error', 'Произошла ошибка в процессе доставки оперативного донесения '+ str);		
										} else {
											alert('Произошла ошибка в процессе доставки оперативного донесения: '+ str);													
										}
									}			
							});
						} else
							if($('mymap').selector==undefined){
								UI.showMessage('error', 'Заполнены не все поля! Проверьте корректность заполнения и повторите отправку.');				
							} else {
								alert('Заполнены не все поля! Проверьте корректность заполнения и повторите отправку.');
							}
								
							
					});	
					$("#np").off('change');
					$("#np").on('change', function (event) {
						$("#npId").val(event.currentTarget.value); 
					});
					if((clone.messId!=undefined)&&(clone.npId!='0')){
//						console.log('n_' + clone.npId);
//						if(clone.npId)
						$("#np").val('n_' + clone.npId); 
							
					}
					else {
						$("#npId").val($("#np").val()); 						
//						console.log($("#npId").val());
					}
					$("#type").off('change');
					$("#type").on('change', function (event) {
						console.log(event.currentTarget.value);
						console.log(clone);
						var strOpt = strOpt2Lvl = '', count = count2Lvl = 0;
						var infraKey = ReasKey = 0;
						infraKey = ((clone.messId!=undefined)) ? clone.infrastructureId : 0;
						for (key in REASON){
							if((REASON[key].sectorId == event.currentTarget.value) &&(REASON[key].parentId == 0)){
								if(!count){
									for (key2 in REASON){
//										console.log(REASON[key2],  REASON[key]);
										if(((clone.messId!=undefined)&&(REASON[key2].parentId == clone.infrastructureId)) || ((clone.messId==undefined)&&(REASON[key2].parentId == REASON[key].id))){
											/*
											if(!count2Lvl){
												$('#reasonId').val(REASON[key2].id);
												$('#reasonName').val(REASON[key2].name);
												$('#reasonNew').val('');
												$('#reasonNew').hide();												
											}*/
//										"7" : ((clone.messId>0)&&(clone.zhkhSectorId == 7))?{"selected" : "selected","html" : "Тепловые сети / Котельные"}:"Тепловые сети / Котельные",
											
//											strOpt2Lvl += '<option value="' + REASON[key2].id + '">' + REASON[key2].name + '</option>';											
											strOpt2Lvl += '<option value="' + REASON[key2].id + '" '; 
											strOpt2Lvl += ((clone.messId!=undefined)&&(REASON[key2].id == clone.reasonId)) ? 'selected' : '';
											strOpt2Lvl += '>' + REASON[key2].name + '</option>';											
											count2Lvl ++;
										}
									}
									
								}								
//								console.log(REASON[key].name);
//								strOpt += '<option value="' + REASON[key].id + '">' + REASON[key].name + '</option>';
								strOpt += '<option value="' + REASON[key].id + '" ';
								strOpt += ((clone.messId!=undefined)&&(REASON[key].id == clone.infrastructureId)) ? 'selected' : '';
								strOpt += '>' + REASON[key].name + '</option>';
								count++;
							}
						}
						
//						console.log(formEl.html);
//						console.log(strOpt);
//						console.log(strOpt2Lvl);
						if(strOpt != ''){
							$('#infrastructureId').replaceWith('<select id="infrastructureId" name="infrastructureId" class="ui-dform-select">'  + strOpt + '</select>');
							$('#infrastructureName').val($('#infrastructureId option:selected' ).text());
							if(strOpt2Lvl != ''){
//								strOpt2Lvl += '<option value="0">Другое</option>';								
								$('#reasonId').replaceWith('<select id="reasonId" name="reasonId" class="ui-dform-select">'  + strOpt2Lvl + '</select>');
								$('#reasonName').val($('#reasonId option:selected' ).text());								
							}
							$("#infrastructureId").off('change');
							$("#infrastructureId").on('change', function (event) {
								$('#infrastructureName').val($('#infrastructureId option:selected' ).text());
//								console.log('infrastructureId', $('#infrastructureName').val());
								var strOpt = strOpt2Lvl = '';
								var count2Lvl = 0;
								if(event.currentTarget.value == 0){
								/*	$('#reasonLvl2').hide();
									$('#reasonId').val(0);
									$('#reasonNew').show();
									console.log('is null');*/
								} else {
									for (key2 in REASON){
//										console.log(REASON[key2],  REASON[key]);
										if(REASON[key2].parentId == event.currentTarget.value){
											/*
											if(!count2Lvl){
												$('#reasonId').val(REASON[key2].id);
												$('#reasonName').val(REASON[key2].name);
												$('#reasonNew').val('');
												$('#reasonNew').hide();												
											}*/
											strOpt2Lvl += '<option value="' + REASON[key2].id + '">' + REASON[key2].name + '</option>';											
											count2Lvl ++;
										}
									}
									if(strOpt2Lvl != '')
									{
										$('#reasonId').replaceWith('<select id="reasonId" name="reasonId" class="ui-dform-select">'  + strOpt2Lvl + '</select>');																		
										$('#reasonId').show();
									} else {
									}
								}
								$("#reasonId").off('change');
								$("#reasonId").on('change', function (event) {
									$('#reasonName').val($('#reasonId option:selected' ).text());
								});
								$('#reasonId').trigger('change');										
//								console.log(event.currentTarget.value);										
							});
//							$('#infrastructureId').trigger('change');	

							
							
					} else {
							$('#infrastructureId').replaceWith('<input type="text" id="infrastructureName" name="infrastructureName" class="ui-dform-text" placeholder="Начинается с "');
						
					}
/*						var strOpt = strOpt2Lvl = '', count = count2Lvl = 0;
						for (key in REASON){
							if((REASON[key].sectorId == event.currentTarget.value) &&(REASON[key].parentId == 0)){
								if(!count){
									for (key2 in REASON){
//										console.log(REASON[key2],  REASON[key]);
										if(REASON[key2].parentId == REASON[key].id){
											
											if(!count2Lvl){
												$('#reasonId').val(REASON[key2].id);
												$('#reasonName').val(REASON[key2].name);
												$('#reasonNew').val('');
												$('#reasonNew').hide();												
											}
											strOpt2Lvl += '<option value="' + REASON[key2].id + '">' + REASON[key2].name + '</option>';											
											count2Lvl ++;
										}
									}
									
								}								
//								console.log(REASON[key].name);
								strOpt += '<option value="' + REASON[key].id + '">' + REASON[key].name + '</option>';
								count++;
							}
						}	
//						console.log(strOpt2Lvl);
						if(strOpt != ''){
							strOpt += '<option value="0">Другое</option>';
							$('#reasonLvl1').replaceWith('<select id="reasonLvl1" name="reasonLvl1" class="ui-dform-select">'  + strOpt + '</select>');
							if(strOpt2Lvl != ''){
//								strOpt2Lvl += '<option value="0">Другое</option>';								
								$('#reasonLvl2').replaceWith('<select id="reasonLvl2" name="reasonLvl2" class="ui-dform-select">'  + strOpt2Lvl + '</select>');								
								$('#reason2lvlArrow').show();
								$('#reasonLvl2').show();
							}
//							$('#reasonNew').show();
							$("#reasonLvl1").on('change', function (event) {
								var strOpt = strOpt2Lvl = '';
								var count2Lvl = 0;
								if(event.currentTarget.value == 0){
									$('#reasonLvl2').hide();
									$('#reasonId').val(0);
									$('#reasonNew').show();
									console.log('is null');
								} else {
									for (key2 in REASON){
//										console.log(REASON[key2],  REASON[key]);
										if(REASON[key2].parentId == event.currentTarget.value){
											
											if(!count2Lvl){
												$('#reasonId').val(REASON[key2].id);
												$('#reasonName').val(REASON[key2].name);
												$('#reasonNew').val('');
												$('#reasonNew').hide();												
											}
											strOpt2Lvl += '<option value="' + REASON[key2].id + '">' + REASON[key2].name + '</option>';											
											count2Lvl ++;
										}
									}
									if(strOpt2Lvl != '')
									{										
										$('#reasonLvl2').replaceWith('<select id="reasonLvl2" name="reasonLvl2" class="ui-dform-select">'  + strOpt2Lvl + '</select>');																		
										$('#reason2lvlArrow').show();
										$('#reasonLvl2').show();
									} else {
										$('#reasonLvl2').hide();
										$('#reasonId').val(event.currentTarget.value);
										$('#reasonName').val($('#reasonLvl1 option:selected' ).text());
										$('#reason2lvlArrow').hide();
										$('#reasonNew').val('');
										$('#reasonNew').hide();																						
									}									
								}
									
								console.log(event.currentTarget.value);								
							});						
							$("#reasonLvl2").on('change', function (event) {
								console.log(event.currentTarget.value);
							});						
						} else {
							$('#reasonLvl2').hide();
							$('#reasonLvl1').hide();
							$('#reason2lvlArrow').hide();
							$('#reasonId').val(0);
							$('#reasonNew').show();							
						}
						
*/


/*	выбор пострадавших объектов  - отключено до лучших времён
						$.post("/spddl/", {type:'geoObj', cmd:'getObjectsOfNpSector', jkhSector:event.currentTarget.value, npId:$('#npId').val() }, function(str){
							//console.log(str);
							if (str != 0){
								var strArr = JSON.parse(str);
								jkhObj = [];
								var strOpt = '';
								$(strArr).each(function(indx, element){
									if(!indx){
										$('#hardwareId').val(element.id);
										$('#hardwareName').val(element.name);
										$('#hardwareNew').val('');
										$('#hardwareNew').hide();
									}
//									console.log(element.id, element.name);
									strOpt += '<option value="' + element.id + '">' + element.name + '</option>';									
									jkhObj.push({label:element.name , value:element.id});
								});
								if(strOpt != ''){

									//$(".ui-autocomplete").on('click', function (event) {UI.Keep()});	
									strOpt += '<option value="0">Другое</option>';
									$('#hardware').replaceWith('<select id="hardware" name="hardware" class="ui-dform-select">'  + strOpt + '</select>');
//									$('#hardwareNew').hide();
									//$('#hardwareNew').val('');
									$("#hardware").on('change', function (event) {
										$('#hardwareId').val(event.currentTarget.value);

										$('#hardwareName').val($('#hardware option:selected' ).text());
										if(event.currentTarget.value == '0'){
											//$('#hardwareNew').val('');
											$('#hardwareNew').show();
										} else {
											$('#hardwareNew').hide();
											$('#hardwareNew').val('');
										}
											
									});
									
								}								
							} else {

								$('#hardware').replaceWith('<input type="text" id="hardware" name="hardware" class="ui-dform-text" placeholder="укажите название">');	
								$('#hardwareId').val(0);		
								$("#hardware").on('change', function (event) {	});		
								$('#hardwareNew').hide();
							}
						});
*/						
						
					});
					$("#ui-datepicker-div").off('click');
					$("#ui-datepicker-div").on('click', function (event) {UI.Keep()});
					$(".ui-corner-all, .ui-datepicker-calendar, .ui-timepicker-div, .ui-datepicker-buttonpane, .ui-widget-content").on('click', function (event) {UI.Keep()});
					$(".ui-autocomplete").off('click');				
					$(".ui-autocomplete").on('click', function (event) {UI.Keep()});				
					$('#type').trigger('change');
					clone = FORM.getMessage(); 		
				} break;
				case 'messContaner' :{				
				} break;
				case 'zhournalContaner': {
					$( "#tabs" ).tabs( "disable", "#messContaner" );				
				} break;
			}

		}
	}
//	this.getFormElrments = function(messId){
	this.getFormElrments = function(){
//		console.log(clone);
/*		var clone = {};
//		var cloneFields = 
			if(messId){
				
				$(MESS).each(function(indx, element){
				if(element.messId == messId)
					clone = element;
				});
//			elements1.np.value = clone.np;

			}*/
		
		var NPid = 0, NPname='';
		if(availableNP.length<1){
			NPid = 		'n_' + homeDistr.id;
			NPname = 	homeDistr.name;
			}
		else if(clone.messId) {
			NPid = clone.npId;
			NPname = clone.np;
		}
//		console.log(clone);
				
		var elements1 = {"action" : "index.html",
						"method" : "get",
						"html" :[
							{
								"name" : "formODId",
								"id" : "formODId",
								"type" : "hidden",
								"placeholder" : "",
								"value" :  "operMessage"
								
							},
							{
								"name" : "userId",
								"id" : "userId",
								"type" : "hidden",
								"placeholder" : "",
								"value" :  userId
								
							},
							{
								"name" : "typeId",
								"id" : "typeId",
								"type" : "hidden", 
								"placeholder" : "",
								"value" :  ''
								
							},
							{
								"name" : "hardwareId",
								"id" : "hardwareId",
								"type" : "hidden", 
								"placeholder" : "",
								"value" :  '0'								
							},
							{
								"name" : "hardwareName",
								"id" : "hardwareName",
								"type" : "hidden", 
								"placeholder" : "",
								"value" :  ''								
							},							
						/*	{
								"name" : "reasonId",
								"id" : "reasonId",
								"type" : "hidden", 
								"placeholder" : "",
								"value" :  '0'								
							},*/
							{
								"name" : "reasonName",
								"id" : "reasonName",
								"type" : "hidden", 
								"placeholder" : "",
								"value" :  ''								
							},
							{
								"name" : "infrastructureName",
								"id" : "infrastructureName",
								"type" : "hidden", 
								"placeholder" : "",
								"value" :  ''								
							},
							{
								"name" : "orgId",
								"id" : "orgId",
								"type" : "hidden",
								"placeholder" : "",
								"value" :  (clone.messId>0)?clone.orgId:"",
								
							},
							{
								"name" : "distId",
								"id" : "distId",
								"type" : "hidden",
								"placeholder" : "",
								"value" :  homeDistr.id
								
							},
							{
								"name" : "npId",
								"id" : "npId",
								"type" : "hidden",
								"placeholder" : "",
								"value" : NPid,
								
							},
							{
								"name" : "distName",
								"id" : "txt-distName",
								"caption" : "Муниципальное образование <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"disabled" : "",
								"class" : "",
								"placeholder" : "",
								"value" :  homeDistr.name
								
		}]};
//		console.log(listNP);
//		if(availableNP.length>0)
	/*		elements1.html.push({
									"name" : "np",
									"id" : "np",
									"caption" : "Населенный пункт <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
									"type" : "text",
									"class" : "needed",
									"placeholder" : "Начинается с ",
									"value": NPname,
									"autocomplete" : {	
										"source" : availableNP,
										"autoFocus": true,
										"select" : function(event, ui){
//											console.log(arguments);
											$("#npId").val(ui.item.value); 
											$("#np").val(ui.item.label);
											return false; 
										},
										minLength: 0}
																			
								});	*/
			console.log('list NP is ', listNP, listNP.length);
			if(listNP!=0){
					elements1.html.push({
								"name" : "np",
								"id" : "np",
								"caption" : "Населенный пункт <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "select",
								"class" : "needed",
								"options" : listNP 
									});
			} else {
					elements1.html.push({
								"name" : "np",
								"id" : "np",
								"type" : "hidden",
								"placeholder" : "",
								"value" : NPid,
								
							});				
			}
		Array.prototype.push.apply(elements1.html, [
							{
								"name" : "initiator",
								"id" : "initiator",
								"caption" : "ФИО и должность инициатора <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"value": (clone.messId>0)?clone.initiator:"",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "диспетчер, Иванова Анна Сергеевна"								
							},
							{
								"name" : "incidentDate",
								"id" : "incidentDate",
								"caption" : "Время и дата нарушения <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "25.01.2019 09:45",
								"datetimepicker" : 	{
									timeFormat: "HH:mm",
									maxDate: 0,
									showSecond: 0, 
									showMillisec: null,
									showMicrosec: null,
									showTimezone: null,
									prevText: '<Пред',
									nextText: 'След>',
									currentText: 'Сейчас',
									monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
									'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
									monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
									'Июл','Авг','Сен','Окт','Ноя','Дек'],
									dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
									dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
									dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
									weekHeader: 'Не',
									dateFormat: 'dd.mm.yy',
									firstDay: 1,
									isRTL: false,
									showMonthAfterYear: false,
									yearSuffix: ''
								}
							},
							{
								"name" : "type",
								"id" : "type",
								"value": (clone.messId>0)?clone.np:"",
								"caption" : "Отрасль ЖКХ <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "select",
									"options" : {
										"7" : ((clone.messId>0)&&(clone.zhkhSectorId == 7))?{"selected" : "selected","html" : "Тепловые сети / Котельные"}:"Тепловые сети / Котельные",
										"1" : ((clone.messId>0)&&(clone.zhkhSectorId == 1))?{"selected" : "selected","html" : "Водоснабжение / Водоотведение"}:"Водоснабжение / Водоотведение",
										"2" : ((clone.messId>0)&&(clone.zhkhSectorId == 2))?{"selected" : "selected","html" : "Газоснабжение"}:"Газоснабжение",
										"6" : ((clone.messId>0)&&(clone.zhkhSectorId == 6))?{"selected" : "selected","html" : "Электроснабжение"}:"Электроснабжение"
									}
/*									"options" : {
										"7" : ((messId>0)&&(clone.zhkhSectorId == 7))?{"selected" : "selected","html" : "Теплоснабжение"}:"Теплоснабжение",
										"1" : ((messId>0)&&(clone.zhkhSectorId == 1))?{"selected" : "selected","html" : "Водоснабжение/Водоотведение"}:"Водоснабжение/Водоотведение",
										"2" : ((messId>0)&&(clone.zhkhSectorId == 2))?{"selected" : "selected","html" : "Газоснабжение"}:"Газоснабжение",
										"3" : ((messId>0)&&(clone.zhkhSectorId == 3))?{"selected" : "selected","html" : "Канализация"}:"Канализация",
										"4" : ((messId>0)&&(clone.zhkhSectorId == 4))?{"selected" : "selected","html" : "Котельные"}:"Котельные",
										"5" : ((messId>0)&&(clone.zhkhSectorId == 5))?{"selected" : "selected","html" : "Тепловые сети"}:"Тепловые сети",
										"6" : ((messId>0)&&(clone.zhkhSectorId == 6))?{"selected" : "selected","html" : "Электроснабжение"}:"Электроснабжение"
									}*/
							},
							
							/*{
								"name" : "reason",
								"id" : "reason",
								"caption" : "Причина выхода из строя оборудования <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"value": (messId>0)?clone.reason:"",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "авария"
							},*/
							{
								"name" : "infrastructureId",
								"id" : "infrastructureId",
								"caption" : "Пострадавшая инфраструктура <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"value": (clone.messId>0)?clone.infrastructureId:"",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "авария"
							},						
							{
								"name" : "reasonId",
								"id" : "reasonId",
								"caption" : "Причина нарушения <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"value": (clone.messId>0)?clone.reasonId:"",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "авария"
							},						
/*
							{
								"name" : "reasonLvl2",
								"id" : "reasonLvl2",
								"value": "",
								"caption" : "",
								"type" : "text",
								"style" :"display:none",
								"class" : "lvl2",
								"placeholder" : "укажите название"
							},								
								{
								"type" : "div",
								"id" : "reason2lvlArrow",
								"style" :"display:none",
								"class" : "rowNum",
								"html" : "-->"
							},							
							{
								"name" : "reasonNew",
								"id" : "reasonNew",
								"value": "",
								"caption" : "",
								"type" : "text",
								"style" :"display:none",
								"class" : "needed ",
								"placeholder" : "Опишите причину выхода из строя оборудования "
							},		*/					
							{
								"name" : "hardware",
								"id" : "hardware",
								"value": (clone.messId>0)?clone.hardware:"",
								"caption" : "Объект, на котором произошло нарушение <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "укажите название"
							},
							{
								"name" : "hardwareNew",
								"id" : "hardwareNew",
								"value": "",
								"caption" : "",
								"type" : "text",
								"style" :"display:none",
								"class" : "needed",
								"placeholder" : "укажите название"
							},
							{
								"name" : "hardwareType",
								"id" : "hardwareType",
								"value": (clone.messId>0)?clone.hardwareType:"",
								"caption" : "Тип вышедшего из строя оборудования <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "трансформатор, генератор, задвижка..."
							},
							{
								"name" : "potrebiteli",
								"id" : "userCnt",
								"value": (clone.messId>0)?clone.consumerNum:"",
								"caption" : "Общее количество пострадавших потребителей <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "жителей, домов, соц. объектов, объекты жизнеобеспечения)"
							}						]);
		if(availableOrg.length>0)
			elements1.html.push({
									"name" : "org",
									"id" : "org",
									"caption" : "Наименование предприятия, устраняющего нарушение <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
									"type" : "text",
									"class" : "needed",
									"placeholder" : "Начинается с ",
									"value":  (clone.messId>0)?clone.orgName:"",
									"autocomplete" : {				
										"source" : availableOrg,
										"select" : function(event, ui){
//											console.log(arguments);
											$("#orgId").val(ui.item.value); 
											$("#org").val(ui.item.label);
											return false; 
										},
										minLength: 0}
																			
								});
		else 
			elements1.html.push({
								"name" : "org",
								"id" : "org",
								"value": (clone.messId>0)?clone.orgName:"",
								"caption" : "Наименование предприятия, устраняющего нарушение <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"class" : "needed",
								"placeholder" : ""
							});
							
							
		Array.prototype.push.apply(elements1.html, [
							{
								"name" : "orgUsedForce",
								"id" : "orgUsedForce",
								"value": (clone.messId>0)?clone.orgUsedForce:"",
								"caption" : "Задействованные силы и средства предприятия, которое устраняет технологическое нарушение <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "25 человек, 2 бригады, 4 единицы техники"
							},							
							{
								"name" : "bossOfWork",
								"id" : "bossOfWork",
								"value": (clone.messId>0)?clone.bossOfWork:"",
								"caption" : "ФИО и должность руководителя работ по устранению нарушения <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "Иванов Пётр Сергеевич, главный инженер"
							},
							{
								"name" : "bossOfCity",
								"id" : "bossOfCity",
								"value": (clone.messId>0)?clone.bossOfCity:"",
								"caption" : "ФИО руководителя муниципалитета, контролирующего устранение <span class='red' style='color: #F22;' title='Обязательный параметр'>*</span>",
								"type" : "text",
								"class" : "needed",
								"placeholder" : "Сидоров Антон Ильич"
							},
							{
								"name" : "tempereture",
								"id" : "tempereture",
								"value": (clone.messId>0)?clone.tempereture:"",
								"caption" : "Температура наружного воздуха, С",
								"type" : "text",
								"placeholder" : "-5"
							},
							{
								"name" : "badNewsMen",
								"id" : "badNewsMen",
								"value": (clone.messId>0)?clone.badNewsMen:"",
								"caption" : "ФИО, должность лица, несвоевременно передавшего в информацию о нарушении",
								"type" : "text",
								"placeholder" : "Петров Иван Сергеевич, главный инженер"
							}
						]);
		
//			console.log(elements1);
			return elements1;
		}
	this.formSended = false;
	this.showCommentForm = function(messId){
		var elements1 = {
						"html" :[

							{
								"name" : "messId",
								"id" : "messId",
								"type" : "hidden",
								"placeholder" : "",
								"value" :  messId
								
							},
							{
								"name" : "userId",
								"id" : "userId",
								"type" : "hidden",
								"placeholder" : "",
								"value" :  userId
								
							},
							{
								"name" : "commBody",
								"id" : "commBody",
								"caption" : "Коментарий",
								"title": "Содержимое коментария",
								"type" : "textarea",
								"rows" : "6",
								"placeholder" : "Содержимое коментария"
							},
							{
								"name" : "commClose",
								"id" : "commClose",
								"type" : "checkboxes",
								"options" : {
									"close" : "Закрыть оперативное донесение",
								}
							},						
							{
								"name" : "commSubmit",
								"id" : "commSubmit",
								"type" : "span" ,
								"html" : " Сохранить "
							}
							/*,							
							{
								"name" : "commClose",
								"id" : "commClose",
								"caption" : "Закрыть оперативное донесение",
								"type" : "checkboxes",
								
							}*/
							]
						};
		$("#commFormF ").html();
		$("#commForm ").show();
		$("#commFormF ").dform(elements1);
		$("#commSubmit").off('click');
		$("#commSubmit").on('click' , function (event) {
				UI.pleaseWait();			
				var formData = $("#commFormF ").serializeJSON();
				$.post("/spddl/", {type:'addComment', data:formData}, function(str)
					{
					UI.pleaseWait();			
					console.log(str); 			
					if(str == 1){
								if($('mymap').selector==undefined){
									UI.showMessage('info', 'Коментарий успешно доставлен');
									$("#mConfirm").off('click');
									$("#mConfirm").on('click' , function (event) {window.location.href = window.location.href; });
								} else {
									GISz.checkMessage();
									UI.togglePanel('', 'formBox', 1, '');
									UI.showMessage('info', 'Коментарий успешно доставлен');									
								}
							//console.log(formData); 			
						} else {
							UI.showMessage('error', 'Произошла ошибка в процессе доставки коментария '+ str);												
						}			
				});
//			}
		});
		
	}

	this.showMess = function(messId){
//				console.log(messId);
		$( "#tabs" ).tabs( "enable", "#messContaner" );
		$( "#tabs" ).tabs( "option", "active", 2 );
		var content = contentComm = '';
		$(MESS).each(function(indx, element){
			var value = '';
			if(element.messId == messId){
//				console.log(element);
//				console.log(element.np);
				if(((element.np == '')||(element.np == undefined))&&(element.npName != undefined))
					element.np = element.npName;
				if(element.np == undefined){
					if((element.npId>0)&&(availableNP.length > 0)){
						$(availableNP).each(function(index, el){
						if (el.value == element.npId)
							element.np = el.label;
						});
					}
					else element.np = '';
				}
				content += "<div class='objTitle' id='panObjTitle'>	Донесение №"  + element.number +  " от " + element.incidentDate_ru + " (" + element.zhkhSectorName  + '  ' +  element.np + ") </div>	<div class='objPropertiesContaner'>";
						for (key in mesField){
							if((element[key]!=null)&&(element[key]!=undefined)&&(key!='comments')){
								if(key == 'status')
									value = (element[key]==1) ? 'Активное': 'В архиве';
								else 
									value = element[key];
								content += "<div   class='objPropItem'> <div class='objPropName'>" + mesField[key] + 
							"</div><div class='objPropValue p_valueSelectType'>" + value + "</div></div>";
							} else if ((key =='comments')&&(element[key]!=0)) {
//								console.log(element[key]);
								contentComm += "<h2> " + mesField[key] + "</h2>" + '<div class="commDateH">Дата добавления</div><div class="commBodyH">Содержание</div><div class="commAuthH">Автор</div>';
								element[key].forEach(function(el, index, arr){
									contentComm += "<div class='commentItem'><div class='commDate'>" + el.date + "</div><div class='commBody'>"
										+ el.text + "</div><div class='commAuth'>" +  el.user_name + "</div></div>" ;
								});
							}
					}
				if(USER.role<4){
					content += "<div class='commAdd'><span class='activeButton' title='Создать новое оперативное донесение по образцу этого' id='cloneMess'> новое по образцу </span>";
					if(element.status==1)
						content += "<span class='activeButton' title='добавить комментарий к денесению' id='commAd'> добавить комментарий </span>";
					content += "</div><div id='commForm'><form  id='commFormF' ></form> </div>";
					content += "</div>";
				}
			}
		});
		
		$('#messBody').html(content + contentComm);
		
		$('#commAd').off('click');
		$('#cloneMess').off('click');
		$('#commAd').on('click', function (event) { FORM.showCommentForm(messId); $('#commAd').off('click');}) ;
		$('#cloneMess').on('click', function (event) {
			event.stopPropagation();	/*cloneMessId = messId;*/ 
			console.log(clone);
			clone = FORM.getMessage(messId); 
			$("#tabs").tabs( "option", "active", 1 ); 
			$('#cloneMess').off('click');
		}) ;
	}

	this.genMessListDisp = function(lId){
			$.post("/spddl/", {type:'getMess', cmd:'getReasons'}, function(str){
//				console.log(str);
				REASON = JSON.parse(str)
//				console.log(REASON);
			});
			$.post("/spddl/", {type:'getODLists', distr:lId}, function(str){
			var content='';
			var open =   0;
			var closed = 0;
			var DATA = JSON.parse(str);
			availableNP=[];
			availableOrg=[];
//			listNP=[];
			$(DATA.npList).each(function(indx, element){
				availableNP.push({label:element.l_name , value:element.l_id});
/*				let elLabel = 'n_' + element.l_id;
				listNP.push({elLabel:element.l_name});	*/			
			});
			listNP = DATA.listNP;
			$(DATA.orgList).each(function(indx, element){
				availableOrg.push({label:element.name , value:element.id});
			});
//			console.log(DATA);
			console.log('availableNP is ', availableNP);
	/*		console.log(availableOrg);*/
//			listNP = availableNP;
			console.log('listNP is ', listNP);
			if(DATA.messList ==0)
				content='<h1>Оперативные донесения отсутствуют</h1>';
			else {
				$(DATA.messList).each(function(indx, element){
//					console.log(element.status, open, closed);
					if((element.status>0)&&(open==0)){
					content +='<h3>Активные донесения</h3>';						
						open++;
					}else if ((element.status==0)&&(closed==0)){
					content +='<h3>Архив донесений</h3>';						
						closed++;						
					}
					console.log(element); 
					if((element.npId>0)&&(availableNP.length > 0)){
						$(availableNP).each(function(index, el){
						if (el.value == element.npId)
							element.np = el.label;
						});
	//					console.log(element.npId, element.np );
						}
					else element.np = '';
						content += "<div class='message '><span class='activeLink showMess' id='mess_"+element.messId+"'>Донесение №"  + element.number +  " от " + element.incidentDate_ru + " (" + element.zhkhSectorName  + '  ' +  element.np + ") </span></div>"; 
				});
			}
//			$("#mainList").html(content);
			$("#mainForm ").html('');
			$("#mainList").html(content);
			$("#tabs" ).tabs( "disable", "#messContaner" );
			$("#tabs" ).tabs( "option", "active", 0 );
			$(".showMess").on('click', function (event) { FORM.showMess(ROUT.GetStrPrt(event.currentTarget.id, '_', 1)); }) ;
		});		
	}
	this.genMessListOperator = function(lId){
			$.post("/spddl/", {type:'getMess', cmd:'getReasons'}, function(str){
//				console.log(str);
				REASON = JSON.parse(str)
				console.log(REASON);
			});
			$.post("/spddl/", {type:'getMessAll', distr:lId}, function(str){
			var content='';
			var open =   0;
			var closed = 0;
			MESS = JSON.parse(str);
			if(MESS ==0)
				content='<h1>Оперативные донесения отсутствуют</h1>';
			else {				
				$(MESS).each(function(indx, element){
//					console.log(element.status, open, closed);
					if((element.status>0)&&(open==0)){
					content +='<h3>Активные донесения</h3>';						
						open++;
					}else if ((element.status==0)&&(closed==0)){
					content +='<h3>Архив донесений</h3>';						
						closed++;						
					}
//					console.log(element);
					if((element.npId>0)&&(availableNP.length > 0)){
						$(availableNP).each(function(index, el){
						if (el.value == element.npId)
							element.np = el.label;
						});
	//					console.log(element.npId, element.np );
						}
					else element.np = '';
						content += "<div class='message '><span class='activeLink showMess' id='mess_"+element.messId+"'>Донесение №"  + element.number +  " от " + element.incidentDate_ru + " (" + element.zhkhSectorName  + '  ' +  element.np + ") </span></div>"; 
				});
			}
			$("#mainList").html(content);
			$(".showMess").off('click') ;
			$(".showMess").on('click', function (event) { FORM.showMess(ROUT.GetStrPrt(event.currentTarget.id, '_', 1)); }) ;
		});		
	}
	this.mesgList = function(lId){
		var layer = ULayers[ULayerIndex[lId]];	
		UI.toggleLockingPad();
		UI.toCenter('formBox');	
		UI.togglePanel('', 'formBox', 1, '');
		$('#formBox_title_text').text('Журнал оперативных донесений. ' + layer.name);
		$('#formSubmit').text(' Закрыть ');
		$('#formSubmit').on('click', function (event) {$('#closePanelImg').trigger('click');});		
		$("#mainForm ").html('');
		$("#mainForm ").hide('');
		var content = '';
		$(layer.message).each(function(indx, element){
			content += "<div class='message '><span class='activeLink'>Донесение №"+ (indx+1) + " </span></div>"; //+ element.
		});
		$("#mainList").html(content);
		$("#mainList").show('');
//		$("#formSubmit").css('display:none');
		
		console.log(layer);
//		alert(lId);
	}	
}
