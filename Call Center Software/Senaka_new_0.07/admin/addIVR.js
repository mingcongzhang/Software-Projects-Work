console.log(numberChoiceArray);
console.log(routeChoiceArray);
console.log(subIVRNameArray);
console.log(subIVRContentArray);


//========static variable========
function makeCounter() {
	var count = 1;
	return function() {
		count++;
		return count;
	}
}
var ct = makeCounter();

//==========initialize table==============
(function(){

	tableFormatter("tableName1", document.getElementById("js"), 1);//initialize table frame

	if(routeChoiceArray != null){//recover action route behavior from database
		//var rowIndexAdder = 0;
		var rowIndexAdder = {};
		for(var i in routeChoiceArray){
			if (typeof routeChoiceArray[i] !== 'function') {
				var tableIndex = parseInt(i.substr(0,i.indexOf("_")));
				if(typeof rowIndexAdder[tableIndex] === 'undefined') rowIndexAdder[tableIndex] = 0;
				var rowIndex = parseInt(i.substr(i.indexOf("_")+1));//not real row index, just an identifier
				var tableName = "tableName"+tableIndex;
				var newRowIndex = rowIndex+rowIndexAdder[tableIndex];
				var routeName = "tableName"+tableIndex+"route"+ newRowIndex;
				if(document.getElementById(routeName) === null){
					for(var j=0;j<=rowIndex;j++){
						newRowIndex = j+rowIndexAdder[tableIndex];
						routeName = "tableName"+tableIndex+"route"+newRowIndex;
						if(document.getElementById(routeName) === null){
						//alert("Adding "+routeName);
							addRow(document.getElementById(tableName));		
						}
					}
				}
				
				document.getElementById(routeName).selectedIndex = routeChoiceArray[i];
				var table = document.getElementById(tableName);
				var row = table.rows[rowIndex+rowIndexAdder[tableIndex]];
				if(routeChoiceArray[i] === "5"){//avoid row number conflict
					rowIndexAdder[tableIndex]+=1;		
				}else if(routeChoiceArray[i] === "4"){
					rowIndexAdder[tableIndex]+=3;
				}
				routeHandler(routeName,row,table);
			}
		}
	}

	if(numberChoiceArray != null){//recover number route behavior from database
		//var rowIndexAdder = 0;
		var rowIndexAdder = {};
		for(var i in numberChoiceArray){
			if(numberChoiceArray[i] !== ""){
				var tableIndex = parseInt(i.substr(0,i.indexOf("_")));
				if(typeof rowIndexAdder[tableIndex] === 'undefined') rowIndexAdder[tableIndex] = 0;
				var rowIndex = parseInt(i.substr(i.indexOf("_")+1));//not real row index, just an identifier
				var tableName = "tableName"+tableIndex;
				var newRowIndex = rowIndex+rowIndexAdder[tableIndex];
				var numberName = "tableName"+tableIndex+"_number_choice_"+ newRowIndex;
				if(document.getElementById(numberName) === null){
					for(var j=0;j<=rowIndex;j++){
						newRowIndex = j+rowIndexAdder[tableIndex];
						numberName = "tableName"+tableIndex+"_number_choice_"+newRowIndex;
						if(document.getElementById(numberName) === null){
							addRow(document.getElementById(tableName));
						
						}
					}
				}
				if(routeChoiceArray[i] === "5"){//avoid row number conflict
					rowIndexAdder[tableIndex]+=1;	
				}else if(routeChoiceArray[i] === "4"){
					rowIndexAdder[tableIndex]+=3;
				}
				document.getElementById(numberName).selectedIndex = numberChoiceArray[i];
				var table = document.getElementById(tableName);
				numberHandler(numberName,table);
			}
		}
	}
	if(routeChoiceArray != null){//delete redundant tempty rows generated from action route recovery
		for(var i in routeChoiceArray){
			var tableIndex = parseInt(i.substr(0,i.indexOf("_")));
			var rowIndex = parseInt(i.substr(i.indexOf("_")+1));//not real row index, just an identifier
			var actualRowLength = document.getElementById("tableName"+tableIndex).rows.length;
			//alert(actualRowLength.id);
			for(var j=actualRowLength;j>=0;j--){
				if(actualRowLength>1){//ignore when there is only one empty row (considered default row) for the current table
					var tempRouteName = "tableName"+tableIndex+"route"+ j;
					var tempNumberName = "tableName"+tableIndex+"_number_choice_"+j;			
					var tempBtnName = "button"+ (j+1);
					if(typeof document.getElementById("tableName"+tableIndex).rows[j] != 'undefined'){
						var firstCellName = document.getElementById("tableName"+tableIndex).rows[j].cells[0].childNodes[0];
						if(typeof firstCellName.name !== 'undefined' && document.getElementById(tempNumberName) !== null){					
							if(document.getElementById(tempNumberName).selectedIndex == "0"  && document.getElementById(tempRouteName).selectedIndex == "0" ){
								//alert(tempRouteName);						
								number_limiter_dec(tempNumberName,document.getElementById("tableName"+tableIndex));
								removeRow(tempBtnName,document.getElementById("tableName"+tableIndex)); 	
								table["rowCount"] -=1;	
								//alert();
							}
						}
					}
				}
			}
		
		}
	}
	if(numberChoiceArray != null){//delete redundant empty rows generated from number route recovery
		for(var i in numberChoiceArray){
			var tableIndex = parseInt(i.substr(0,i.indexOf("_")));
			var rowIndex = parseInt(i.substr(i.indexOf("_")+1));//not real row index, just an identifier
			var actualRowLength = document.getElementById("tableName"+tableIndex).rows.length;
			//alert(actualRowLength.id);
			for(var j=actualRowLength;j>=0;j--){
				if(actualRowLength>1){//ignore when there is only one empty row (considered default row) for the current table
					var tempRouteName = "tableName"+tableIndex+"route"+ j;
					var tempNumberName = "tableName"+tableIndex+"_number_choice_"+j;			
					var tempBtnName = "button"+ (j+1);
					if(typeof document.getElementById("tableName"+tableIndex).rows[j] != 'undefined'){
						var firstCellName = document.getElementById("tableName"+tableIndex).rows[j].cells[0].childNodes[0];
						if(typeof firstCellName.name !== 'undefined' && document.getElementById(tempNumberName) !== null){					
							if(document.getElementById(tempNumberName).selectedIndex == "0"  && document.getElementById(tempRouteName).selectedIndex == "0" ){
								//alert(tempRouteName);						
								number_limiter_dec(tempNumberName,document.getElementById("tableName"+tableIndex));
								removeRow(tempBtnName,document.getElementById("tableName"+tableIndex)); 	
								table["rowCount"] -=1;	
								//alert();
							}
						}
					}
				}
			}
		}
	}
		
}());
//===============ajax server communication========================
// function createAmenities() {
    // if (window.XMLHttpRequest) {
       
		
		// var my_array = { "11","22" };
		// var json = JSON.stringify( my_array );
        // xmlhttp = new XMLHttpRequest();
    // }
    // else {
        
        // xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    // }

    // xmlhttp.onreadystatechange = function () {
        // if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            // document.getElementById('message').innerHTML = xmlhttp.responseText;
        // }
    // }

    // var url = "number_IVR.php";

    // xmlhttp.open("POST", url, true);

    // xmlhttp.send();

// }
//==========table generator================
function tableFormatter(tableName, appendTo,option){
	var table = tableGenerator(tableName);
	if(option === 1){
		$(appendTo).after(table);
	}else{
		$(appendTo).append(table);
	}
	var inputButton = document.createElement("input");
	inputButton.setAttribute("id",tableName+"button");
	inputButton.setAttribute("type","button");
	inputButton.setAttribute("value","New IVR Prompt");;
	inputButton.addEventListener('click',function(){addRow(table,document.getElementById("ivr_div1"))},false);
	$(table).after(inputButton);
	addRow(table);
}

function tableGenerator(table_name) {
	var sOut = document.createElement("table");
	sOut.setAttribute("id",table_name);
	sOut.setAttribute("name","");
	sOut.name = table_name;
	//sOut.style.tableLayout = "fixed";
    return sOut;
}
//=========add new row==========
function addRow(table) {
//alert();
	var rowCount = table.rows.length;
	if(typeof table["rowCount"] === 'undefined') {
		table["rowCount"] = 0;
	}
	// if(table["rowCount"] === 10){
		// alert("You reached maximum number of dial choices!");
	// }else{
		table["rowCount"] += 1;
		var number_name = table.name+'_number_choice_'+rowCount;
		//alert(number_name);
		var route_name = table.name+'route'+rowCount;
		//alert(route_name+" added");
		var btnName =  "button" + (rowCount + 1);
		if(document.getElementById(number_name) !== null){
			for(var i=rowCount;i>=0;i--){
				number_name = table.name+'_number_choice_'+i;
				btnName =  "button" + (i + 1);
				if(document.getElementById(number_name) === null){
					route_name = table.name+'route'+i;
					break;
				}
			}
		}
		var row = table.insertRow(rowCount);
		//alert(rowCount);
		var cell1 = row.insertCell(0);
		var element1 = document.createElement("input");
		element1.type = "button";
			
		element1.name = btnName;	
		element1.setAttribute('value', 'Delete IVR Prompt');  
		cell1.appendChild(element1);
		var cell2 = row.insertCell(1);
		cell2.innerHTML = '<label style="white-space: nowrap;" >When the caller presses:</label>';
		var cell3 = row.insertCell(2);	
		cell3.innerHTML = '<select id = '+number_name+' name = '+number_name+'><option selected="selected" value="">- Select an option -</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="0">0</option></select>';	
		var cell4 = row.insertCell(3);	
		cell4.innerHTML = '<label style="white-space: nowrap;" >then:</label>';	
		var cell5 = row.insertCell(4);	
		cell5.innerHTML = '<select id ='+route_name+'><option selected="selected" value="">- Select an option -</option><option value="1">route to</option><option value="2">send to voicemail</option><option value="3">hang up call</option><option value="4">forward to phone number</option><option value="5">send to new IVR</option></select> '; 
		
		
		//alert(document.getElementById(number_name).selectedIndex+" "+document.getElementById(route_name).selectedIndex);
		
		element1.onclick = function () { 
			number_limiter_dec(number_name,table); //number select option handler on deleting row
			removeRow(btnName,table); 	
			table["rowCount"] -=1;
		}
		document.getElementById(route_name).addEventListener('change',function(){
			routeHandler(route_name,row,table);
		},false);//route select option handler	
	//================number select option handler================	
		document.getElementById(number_name).addEventListener('click',function(){
			numberHandler(number_name,table);
		},false);
	//}
}

function routeHandler(route_name,row,table){
	derived_choice(route_name,row,table);
	var hiddenNum = document.createElement("input");
	hiddenNum.setAttribute("type","hidden");
	hiddenNum.setAttribute("name",route_name);
	hiddenNum.setAttribute("value",document.getElementById(route_name).selectedIndex);
	document.getElementById(route_name).appendChild(hiddenNum);
}

function numberHandler(number_name,table){
	number_limiter_inc(number_name,table);
	if(typeof table["numChoiceArray"] != 'undefined'){
		var bannedNumChoice = [];
		var tempNumChoiceArray = table["numChoiceArray"].map(function(ele){
			return ele.substr(0,(ele.indexOf("_")));
		}); 
		var tempNumChoiceArray2 = table["numChoiceArray"].map(function(ele){
			return ele.substr((ele.indexOf("+")+1));
		}); 
		var tempNumChoiceArray3 = table["numChoiceArray"].map(function(ele){
			return ele.substr(0,(ele.indexOf("+")));
		}); 
		tempNumChoiceArray.forEach(function(val){
			if(val.substr(val.indexOf("+")+1) === table.name){	
				var index = tempNumChoiceArray.indexOf(val);	
				var num = parseInt((table["numChoiceArray"][index]).substr(0,(table["numChoiceArray"][index].indexOf("+"))));
				//alert(num);
				bannedNumChoice.push(num);
			}
		});
		var ban = document.getElementById(number_name).getElementsByTagName("option");
		bannedNumChoice.forEach(function(ele){
			if(ele != 0) ban[ele].disabled = true;
		});		
		tempNumChoiceArray2.forEach(function(ele, ind){
			var unBan = document.getElementById(ele).getElementsByTagName("option");
			for(var i=0;i<unBan.length;i++){
				if(unBan[i].disabled === true){
					var j = tempNumChoiceArray3.indexOf(i+"");
					if(j === -1){
						unBan[i].disabled = false;
					}
				}
			}
		});		
	}
	var hiddenNum = document.createElement("input");
	hiddenNum.setAttribute("type","hidden");
	hiddenNum.setAttribute("name",number_name);
	hiddenNum.setAttribute("value",document.getElementById(number_name).selectedIndex);
	document.getElementById(number_name).appendChild(hiddenNum);
}

function removeRow(btnName,table) {

	try {
		var rowCount = table.rows.length;
		var switchNum = 1;
		var tempPlace = 0;
		while(switchNum != 3){
			if(switchNum === 1){
				for (var i = 0; i < rowCount; i++) {
					var row = table.rows[i];
					var rowObj = row.cells[0].childNodes[0];
					if (rowObj.name == btnName) {
						table.deleteRow(i);
						rowCount--;	
						if(typeof table.rows[i]!== 'undefined'){
							row = table.rows[i];
							rowObj = row.cells[0].childNodes[0];
							if(typeof rowObj.name != 'undefined'){
								if(rowObj.name.indexOf("button") > -1){
									switchNum = 3;
									break;
								}
							}
						}
						switchNum = 2;
						tempPlace = i;
						break;
					}
				}
			}
			if(switchNum === 2){			
				try{
					var row = table.rows[tempPlace];		
					if(typeof row != 'undefined'){
						if (row.id === ("specialRoute")) {
							table.deleteRow(tempPlace);
						}else{
						
							for (var i = tempPlace+2; i >= tempPlace; i--) {
								var row = table.rows[i];		
								if(typeof row !== 'undefined'){
									if (row.id.substr(0,11) == "special_row") {
										table.deleteRow(i);
									}	
								}
							}
						}
					}			
				}catch(e){
					alert(e);
				}finally{
					switchNum =3;	
				}
			}
		}
	}catch (e) {
		alert(e);
	}
}

//============numChoiceArray handler===============
function number_limiter_inc(name,table){
	var numChosen = document.getElementById(name).selectedIndex;
	var numChosenName = numChosen+"+"+name;
	if(table["numChoiceArray"] == null){
		var temp = [];
		temp.push(numChosenName);
		table["numChoiceArray"] = temp;
	}else{
		var temp = table["numChoiceArray"].map(function(ele){return ele.substr(ele.indexOf("+")+1);});
		var index = temp.indexOf(name); 
		if(index > -1){
			(table["numChoiceArray"])[index] = numChosenName;
		}else{
			table["numChoiceArray"].push(numChosenName);
		}
	}
	//console.log(table["numChoiceArray"]);
}

function number_limiter_dec(name,table){

	var numChosen = document.getElementById(name).selectedIndex;

	var numChosenName = numChosen+"+"+name;
	if(typeof table["numChoiceArray"] != 'undefined'){
		var index = table["numChoiceArray"].indexOf(numChosenName);
		if (index > -1) {
			table["numChoiceArray"].splice(index, 1);
		}
	}
	//alert(table["numChoiceArray"]);
}
//============================================================


function derived_choice(name,row,table){
		if(document.getElementById(name).selectedIndex == "0") {
			if(row.rowIndex+1<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					if (table.rows[row.rowIndex+1].id == "specialRoute") {
						table.deleteRow(row.rowIndex+1);				
					}
				}
			}
			if(row.rowIndex+3<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					for(i=row.rowIndex+3;i>=row.rowIndex+1;i--){
						if(table.rows[i].id == "special_row1" || table.rows[i].id == "special_row2" || table.rows[i].id == "special_row3"){
							table.deleteRow(i);	
						}
					}
				}
			}	
			if(row.cells.length>=6){
				for(i = row.cells.length-1; i>=5; i--){
				row.deleteCell(i);	
				}
			}
		}else if(document.getElementById(name).selectedIndex == "1") {
			if(row.rowIndex+1<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					if (table.rows[row.rowIndex+1].id == "specialRoute") {
						table.deleteRow(row.rowIndex+1);
					}
				}
			}
			if(row.rowIndex+3<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					for(i=row.rowIndex+3;i>=row.rowIndex+1;i--){
						if(table.rows[i].id == "special_row1" || table.rows[i].id == "special_row2" || table.rows[i].id == "special_row3"){
							table.deleteRow(i);	
						}
					}
				}
			}
			if(row.cells.length>=6){
				for(i = row.cells.length-1; i>=5; i--){
				row.deleteCell(i);	
				}
			}
			var cell6 = row.insertCell(5);
			var name_route_1_1 = name+"1";
			cell6.innerHTML = '<select id = '+name_route_1_1+'><option selected="selected" value="">- Select an option -</option></select> ';	
			var cell7 = row.insertCell(6);
			var name_route_1_1_1 = name+"1"+"1";
			cell7.innerHTML = '<input class="bigbutton2" type=submit value="Customize Messages" >';
			//button name not defined
		}else if(document.getElementById(name).selectedIndex == "2") {
			if(row.rowIndex+1<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					if (table.rows[row.rowIndex+1].id == "specialRoute") {
						table.deleteRow(row.rowIndex+1);				
					}
				}
			}
			if(row.rowIndex+3<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					for(i=row.rowIndex+3;i>=row.rowIndex+1;i--){
						if(table.rows[i].id == "special_row1" || table.rows[i].id == "special_row2" || table.rows[i].id == "special_row3"){
							table.deleteRow(i);	
						}
					}
				}
			}
			if(row.cells.length>=6){
				for(i = row.cells.length-1; i>=5; i--){
				row.deleteCell(i);	
				}
			}
			var cell6 = row.insertCell(5);
			var name_route_2_1 = name+"2";
			cell6.innerHTML = '<input class="bigbutton2" type=submit value="Customize Messages">';
			//button name not defined
		}else if(document.getElementById(name).selectedIndex == "3") {
			if(row.rowIndex+1<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					if (table.rows[row.rowIndex+1].id == "specialRoute") {
						table.deleteRow(row.rowIndex+1);			
					}
				}
			}
			if(row.rowIndex+3<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					for(i=row.rowIndex+3;i>=row.rowIndex+1;i--){
						if(table.rows[i].id == "special_row1" || table.rows[i].id == "special_row2" || table.rows[i].id == "special_row3"){
							table.deleteRow(i);	
						}
					}
				}
			}
			if(row.cells.length>=6){
				for(i = row.cells.length-1; i>=5; i--){
				row.deleteCell(i);	
				}
			}
			var cell6 = row.insertCell(5);
			var name_route_3_1 = name+"3";
			cell6.innerHTML = '<input class="bigbutton2" type=submit value="Customize Messages">';
			//button name not defined		
		}else if(document.getElementById(name).selectedIndex == "4") {
			if(row.rowIndex+1<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					if (table.rows[row.rowIndex+1].id == "specialRoute") {
						table.deleteRow(row.rowIndex+1);		
					}
				}
			}
			if(row.rowIndex+3<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					for(i=row.rowIndex+3;i>=row.rowIndex+1;i--){
						if(table.rows[i].id == "special_row1" || table.rows[i].id == "special_row2" || table.rows[i].id == "special_row3"){
							table.deleteRow(i);	
						}
					}
				}
			}
			if(row.cells.length>=6){
				for(i = row.cells.length-1; i>=5; i--){
					row.deleteCell(i);	
				}
			}
			var cell6 = row.insertCell(5);
			var name_route_4_1 = name+"4";
			cell6.innerHTML = '<select id = '+name_route_4_1+'><option selected="selected" value="">+001</option></select> ';
			var cell7 = row.insertCell(6);
			var name_route_4_1_1 = name_route_4_1+"1";
			cell7.innerHTML = '<input class="text" placeholder="No dash or space!">';
			var cell8 = row.insertCell(7);
			var name_route_4_1_1_1 = name_route_4_1_1+"1";
			cell8.innerHTML = '<input class="bigbutton2" type=submit value="Customize Messages">';
			//button name not defined	
			//**********Available Agents Greeting
			var added_row1 = table.insertRow(row.rowIndex+1);
			added_row1.setAttribute("id","special_row1");
			var added_cell1 = added_row1.insertCell(0);
			added_cell1.innerHTML = '<label name="special" style="white-space: nowrap;">&nbsp;&nbsp;&nbsp;</label> ';
			var added_cell2 = added_row1.insertCell(1);
			added_cell2.innerHTML += '<label style="white-space: nowrap;">Available Agents Greeting</label> ';
			var added_cell3 = added_row1.insertCell(2);
			added_cell3.innerHTML = '<select><option selected="selected" value="">- Select an option -</option> </select>';
			//**********Voicemail Greeting
			var added_row2 = table.insertRow(row.rowIndex+2);
			added_row2.setAttribute("id","special_row2");
			var added_cell1 = added_row2.insertCell(0);
			added_cell1.innerHTML = '<label name="special" style="white-space: nowrap;">&nbsp;&nbsp;&nbsp;</label> ';
			var added_cell2 = added_row2.insertCell(1);
			added_cell2.innerHTML += '<label style="white-space: nowrap;">Voicemail Greeting</label> ';
			var added_cell3 = added_row2.insertCell(2);
			added_cell3.innerHTML = '<select> <option selected="selected" value="">- Select an option -</option> </select>';
			//**********Outside Business Hours Greeting
			var added_row3 = table.insertRow(row.rowIndex+3);
			added_row3.setAttribute("id","special_row3");
			var added_cell1 = added_row3.insertCell(0);
			added_cell1.innerHTML = '<label name="special" style="white-space: nowrap;">&nbsp;&nbsp;&nbsp;</label> ';
			var added_cell2 = added_row3.insertCell(1);
			added_cell2.innerHTML += '<label style="white-space: nowrap;">Outside Business Hours Greeting</label> ';
			var added_cell3 = added_row3.insertCell(2);
			added_cell3.innerHTML = '<select> <option selected="selected" value="">- Select an option -</option> </select>';
			//console.log(table.innerHTML);
		}else if(document.getElementById(name).selectedIndex == "5") {
			if(row.rowIndex+1<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					if (table.rows[row.rowIndex+1].id == "specialRoute") {
						table.deleteRow(row.rowIndex+1);			
					}
				}
			}
			if(row.rowIndex+3<table.rows.length){
				if(table.rows[row.rowIndex+1].id.length>1){
					for(i=row.rowIndex+3;i>=row.rowIndex+1;i--){
						if(table.rows[i].id == "special_row1" || table.rows[i].id == "special_row2" || table.rows[i].id == "special_row3"){
							table.deleteRow(i);	
						}
					}
				}
			}
			if(row.cells.length>=6){
				for(i = row.cells.length-1; i>=5; i--){
					row.deleteCell(i);	
				}
			}
			//naming
			//var currentTableNum = parseInt(table.id.substr(9));
			//var table_id = "dataTable"+(currentTableNum+1);
			var table_name = "tableName"+ct();
			var ivr_box_name = "ivrMsg"+"_"+table_name;
			tableIndex_num = name.substr(9,name.indexOf("route")-9);
			rowIndex_num = name.substr(name.indexOf("route")+5);
			subIVRCoordinate = tableIndex_num+"_"+rowIndex_num;
			//space taker
			var added_row1 = table.insertRow(row.rowIndex+1);
			added_row1.setAttribute("id","specialRoute");
			var added_cell1 = added_row1.insertCell(0);
			added_cell1.innerHTML = '<label name ="special" style="white-space: nowrap;">&nbsp;&nbsp;&nbsp;</label> ';
			//IVR msg element
			var added_cell2 = added_row1.insertCell(1);	
			var ivr_message_name = ivr_box_name+"name+"+subIVRCoordinate;
			var ivr_message_content = ivr_box_name+"content+"+subIVRCoordinate;
			var tableNum = parseInt(table_name.substr(9));
			var subIvrName="";
			var subIvrContent="";
			for(var i in subIVRNameArray){
				if(subIVRNameArray[i] !== ""){
					var tableIndex = parseInt(i.substr(0,i.indexOf("+")));
					if(tableIndex === tableNum){
						subIvrName=subIVRNameArray[i];
						break;
					}
				}
			}
			for(var i in subIVRContentArray){
				if(subIVRContentArray[i] !== ""){
					var tableIndex = parseInt(i.substr(0,i.indexOf("+")));
					if(tableIndex === tableNum){
						subIvrContent=subIVRContentArray[i];
						break;
					}
				}
			}
			added_cell2.innerHTML = '<div name='+ivr_box_name+' id='+ivr_box_name+' style="border: 2px solid #a1a1a1;padding: 6px;background: #dddddd;width: 290px;height: 170px;border-radius: 25px;"><label style = "display:inline-block;width:250px;font-size:12px;">Sub IVR Message</label><div id="justfortest" style="width: 500px"><input type="hidden" name="save_IVR_msg" value="add"><p></p><label style="font-size:12px;">Name*: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;</label><input type="text" name='+ivr_message_name+' value='+subIvrName+'><p></p><label style="font-size:12px;">TTS Message*:</label><textarea style="width: 200px;height: 90px;" name='+ivr_message_content+ ' >'+subIvrContent+'</textarea><br/><br/></div><br/><br/></div>';
			
			var added_cell3 = added_row1.insertCell(2);
			tableFormatter(table_name, added_cell3, 2);
			//alert(table.rows.length);
	}	
}

