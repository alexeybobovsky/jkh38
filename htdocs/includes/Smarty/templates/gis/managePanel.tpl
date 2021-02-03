		<div class = 'menuPad' id='menuLayerAddContaner'>
		<h3>Создание нового слоя</h3>
				
			<div class='objPropertiesContaner'>
				<div  id='layerNameI'  class='objPropItem'>
					<div class='objPropName'>
						Название слоя
					</div>
					<div id='layerNameC' class='objPropValue p_valueSelectType'>
						<input type="text" id="layerName" name="layerName" value=''>
					</div>
				</div>
				<div  id='layerIsPublicI'  class='objPropItem'>
					<div class='objPropName'>
						Публичный слой
					</div>
					<div id='layerIsPublicC' class='objPropValue p_valueSelectType'>
						<input type="checkbox" id="layerIsPublic" name="layerIsPublic" value=''  >
					</div>
				</div>
				<div  id='layerFileI'  class='objPropItem'>
					<div class='objPropName'>
						GEOJSON файл
					</div>
					<div id='layerFileC' class='objPropValue p_valueSelectType'>
						<div>
							<div>
								<div id="file_geojson">	<noscript><p>Please enable JavaScript to use file uploader.</p></noscript> </div>
							</div>
							<div id='geojson_res'>	
							</div>
						</div>
					</div>
				</div>
				<div  id='layeAddSubmiti'  class='objPropItem paddingTop20'>
						<span id='layerAddSubmit' class='activeButton'>Создать слой</span>	
				</div>
			</div>

		</div>		
		<div class = 'menuPad'id='menuLayerAddObj'>
		<h3>Добавление нового объекта к слою </h3>
				
			<div class='objPropertiesContaner'>
				<div  id='layerNameI'  class='objPropItem'>
					<div class='objPropName'>
						 Название объекта
					</div>
					<div id='' class='objPropValue p_valueSelectType'>
						<input  id='objName' value=''>
					</div>
				</div>
				<div   class='objPropItem'>
					<div class='objPropName'>
						Тип объекта
					</div>
					<div id='' class='objPropValue p_valueSelectType'>
						<select name=''   id='objType'  >
							<option value='0'>'Точка' 			</option>
							<option value='1' >'Полилиния' </option>
							<option value='2'>'Полигон' 		</option>
							<option value='3'>'Окружность' 		</option>
							<option value='4'>'Прямоугольник' 	</option>							
							<option value='5'>'!-Сектор!'		</option>							
						</select>					
					</div>
				</div>
				<div  class='objPropItem'>
					<div class='objPropName'>
						 Шаблон
					</div>
					<div id='' class='objPropValue p_valueSelectType'>
						<select name=''   id='objTemplate' >
							<option value='0'>'Точка' 			</option>
							<option value='1'>'Полилиния' 		</option>
							<option value='2'>'Окружность' 		</option>
							<option value='3'>'Прямоугольник' 	</option>							
						</select>					
					</div>
				</div>
				<div   class='objPropItem'>
					<div class='objPropName'>
						 Описание
					</div>
					<div id='' class='objPropValue p_valueSelectType'>
						<textarea name=''  id='objAbout' rows="2" cols="15"></textarea>					
					</div>
				</div>
				<div id='pointListContaner'>
					<div  id='noPointsMessage'>
						Точки не установлены! 
					</div>
					<div  id='totalPointsMessage'>
						<div id='totalPointStr'>
							<span id='tpCnt'>Установлено 2 точки</span> 
							<span id='tpLimit'>из 4</span> 
						</div>
						<div id='totalPointMore'>
							<span id='tpMore'class='buttExpand activeButton' title='Подробно'></span> 
						</div>
					</div>
				</div>
																						
				<div  id='layerFileI'  class='objPropItem'>
					<div class='objPropName'>
						Загрузить объекты (GEOJSON файл)
					</div>
					<div id='layerFileC' class='objPropValue p_valueSelectType'>
						<div>
							<div>
								<div id="file_geoObj">	<noscript><p>Please enable JavaScript to use file uploader.</p></noscript> </div>
							</div>
							<div id='fileRes_geoObj'>	
							</div>
						</div>
					</div>
				</div>
				

				<div  id='objAddSubmiti'  class='objPropItem paddingTop20'>
						<span id='objAddSubmit' class='activeButton'>Создать объект</span>	
				</div>
			</div>

		</div>
{literal}								
<script>
var cntUp = 0;
fileUploadParam[cntUp] = new createFileUploadParam('файл', 'geojson');	
	cntUp ++;
fileUploadParam[cntUp] = new createFileUploadParam('файл', 'geoObj');	
</script>
{/literal}
		