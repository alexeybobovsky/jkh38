function layerFilter(){
	this.list = [];
	this.rootUniq = {};
	this.setLayerFilter = function (layer){ /*2017_12_14  создаёт объект фильта по слоям*/	
		var rootId = id = 0;
		var parentArr = 	layer.parStr.split('_');
		if(parentArr[1] == '997'){ 				//районы
			rootId = 		parentArr[1];			
		} else if (parentArr[1] == '999'){ 		//классификаторы
			rootId = 		parentArr[2];	
		} else if (parentArr[1] == '900'){ 		//базовые станции
			rootId = 		parentArr[1];	
		}
		id = 	layer.id;
		var newObj = {};
		parentArr.splice(0, 1);
		this.list.push({id:id, rootId:rootId, parents:parentArr});
		this.rootUniq[String(rootId)] = 1;
	}
	
	this.clear = function (id){ 	/*2018_01_16  сброс фильтра*/
		this.list.length=0;
		this.rootUniq = {};		
	}	
	this.isActive = function (id){ 	/*2017_12_21  проверка активности фильтра*/
		return (this.list.length>0) ? true : false;		
	}	
	this.isParentsFilter = function (id){ 	/*2017_12_26  проверка наличия только предков в фильтре*/
		var result = false;
		this.list.forEach(function(element, indx, arr){
			if(ROUT.in_array(id, element.parents)){
				result = true;
				}
			});
		return result;		
	}	
	this.isLayerParentsFilter = function (id){ 	/*2017_12_26  проверка наличия слоя и предков в фильтре*/
		var result = false;
		this.list.forEach(function(element, indx, arr){
			if((element.id == id) || (ROUT.in_array(id, element.parents))){
				result = true;
				}
			});
		return result;		
	}	

	this.isLayerFilter = function (id){ 	/*2017_12_14  проверка наличия слоя в фильтре*/
		var result = false;
		this.list.forEach(function(element, indx, arr){
			if(element.id == id){
				result = true;
				}
			});
		return result;		
	}	

	this.removeLayerFilter = function (id){ 	/*2017_12_14  Удвление  слоя из фильтра*/
		this.rootUniq = {};
		this.list.forEach(function(element, indx, arr){
			if(element.id == id){
//				console.log('remove: id = ' + element.id + ' rootId = ' + element.rootId);
				this.list.splice(indx, 1);
				}
		}, this);
		this.list.forEach(function(element, indx, arr){
//				console.log('add: id = ' + element.id + ' rootId = ' + element.rootId);
				this.rootUniq[String(element.rootId)] = 1;
		}, this);
	}	
/*2017_12_14  проверка видимости объекта в фильтре строгое совпадение с каждым rootId и совпадение с любым из id*/
	this.isObjShowFilter = function (obj){ 	
//		var result = false; 
		var show = true;
//		var uniqRoot = lastRoot =  0;
		var searched =[];
		this.list.forEach(function(element, indx, arr){
			if(((obj.lStr.indexOf('_' + element.id) >= 0)||(obj.lParentStr.indexOf('_' + element.id) >= 0))&&
				(obj.lParentStr.indexOf('_' + element.rootId) >= 0)){
					if(!ROUT.in_array(element.rootId, searched))
						searched.push(element.rootId);
				}		
		});
		for (var key in this.rootUniq) {
			if(!ROUT.in_array(key, searched))
				show = false;
		};
		return show;
	}
	

}