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

//========deep copier===========
function clone(obj) {
    if (null == obj || "object" != typeof obj) return obj;
    var copy = obj.constructor();
    for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
    }
    return copy;
}
//==========initialize first table==============
(function(){
	tableFormatter("dataTable1","tableName1", document.getElementById("js"), 1);
	//$.post("number_IVR.php",{name:"tableName1" });
	//console.log(document.getElementById("dataTable1").innerHTML);
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
function tableFormatter(tableID,tableName, appendTo,option){
	var table = tableGenerator(tableID, tableName);
	if(option === 1){
		$(appendTo).after(table);
	}else{
		$(appendTo).append(table);
	}
	var inputButton = document.createElement("input");
	inputButton.setAttribute("id",tableID+"button");
	inputButton.setAttribute("type","button");
	inputButton.setAttribute("value","New IVR Prompt");;
	inputButton.addEventListener('click',function(){addRow(table,document.getElementById("ivr_div1"))},false);
	$(table).after(inputButton);
	addRow(table);
}

function tableGenerator(table_id, table_name) {
	var sOut = document.createElement("table");
	sOut.setAttribute("id",table_id);
	sOut.setAttribute("name","");
	sOut.name = table_name;
    return sOut;
}
//=========add new row==========
function addRow(table) {
	var rowCount = table.rows.length;
	if(typeof table["rowCount"] === 'undefined') {
		table["rowCount"] = 0;
	}
	if(table["rowCount"] === 10){
		alert("You reached maximum number of dial choices!");
	}else{
		table["rowCount"] += 1;
		var number_name = table.name+'_number_choice_'+rowCount;
		var row = table.insertRow(rowCount);
		var cell1 = row.insertCell(0);
		var element1 = document.createElement("input");
		element1.type = "button";
		var btnName =  "button" + (rowCount + 1);	
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
		var route_name = table.name+'route'+rowCount;
		cell5.innerHTML = '<select id = '+route_name+'><option selected="selected" value="">- Select an option -</option><option value="1">route to</option><option value="2">send to voicemail</option><option value="3">hang up call</option><option value="4">forward to phone number</option><option value="5">send to new IVR</option></select> '; 
		element1.onclick = function () { 
			number_limiter_dec(number_name,table); //number select option handler on deleting row
			removeRow(btnName,table); 	
			table["rowCount"] -=1;
		}
		document.getElementById(route_name).addEventListener('change',function(){
			derived_choice(route_name,row,table);
			var hiddenNum = document.createElement("input");
			hiddenNum.setAttribute("type","hidden");
			hiddenNum.setAttribute("name",route_name);
			hiddenNum.setAttribute("value",document.getElementById(route_name).selectedIndex);
			document.getElementById(route_name).appendChild(hiddenNum);
		},false);//route select option handler
		
	//================number select option handler================	
		document.getElementById(number_name).addEventListener('click',function(){
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
		},false);
	}
}

function removeRow(btnName,table) {

	try {
		var rowCount = table.rows.length;
		var switchNum = 1;
		var tempPlace = 0;
		while(switchNum != 3){
			switch(switchNum){
					case 1:
						for (var i = 0; i < rowCount; i++) {
							var row = table.rows[i];
							var rowObj = row.cells[0].childNodes[0];
							if (rowObj.name == btnName) {
								table.deleteRow(i);
								rowCount--;
								switchNum = 2;
								tempPlace = i;
								break;
							}
						}
					case 2:				
						try{
							for (var i = tempPlace+2; i >= tempPlace; i--) {
								var row = table.rows[i];
								var currentTableNum = parseInt(table.id.substr(9));			
								if(typeof row != 'undefined'){
									if (row.id == "specialRoute") {
										table.deleteRow(i);
										break;
									}
									if (row.id.substr(0,7) == "special") {
										table.deleteRow(i);
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
			//table["numChoiceArray"] = table["numChoiceArray"].filter(function(elem, pos) {return table["numChoiceArray"].indexOf(elem) == pos;}); 
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
			//nameing
			var currentTableNum = parseInt(table.id.substr(9));
			var table_id = "dataTable"+(currentTableNum+1);
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
			added_cell2.innerHTML = '<div name='+ivr_box_name+' style="border: 2px solid #a1a1a1;padding: 6px;background: #dddddd;width: 290px;height: 170px;border-radius: 25px;"><label style = "display:inline-block;width:250px;font-size:12px;">Sub IVR Message</label><div id="justfortest" style="width: 500px"><input type="hidden" name="save_IVR_msg" value="add"><p></p><label style="font-size:12px;">Name*: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;</label><input type="text" name='+ivr_message_name+'><p></p><label style="font-size:12px;">TTS Message*:</label><textarea style="width: 200px;height: 90px;" name='+ivr_message_content+ '></textarea><br/><br/></div><br/><br/></div>';
			
			
			
			var added_cell3 = added_row1.insertCell(2);
			
			tableFormatter(table_id, table_name, added_cell3, 2);
//			alert(added_cell2.innerHTML);
//			$(added_cell2).find('#tablerepeated').remove(); 

	}	
}

function nestedIvrHandling(divEle){
//new ID generation
	var count = ct();//static counter increase by 1
	//var oldIvrDivID = "ivr_div"+count;
	divEle.setAttribute("id","extraCell"+count);
	var oldTableID = "dataTable"+count;
	var newCount = count + 1;
	var newIvrDivID = "ivr_div"+newCount;
	var newTableID = "dataTable"+newCount;
//new attributes configuration
	divEle.setAttribute("id",newIvrDivID);
	var tableEle = document.getElementById(oldTableID);
	tableEle.setAttribute("id",newTableID);
	alert(document.getElementsByTagName("table").length);
	$("#extraCell"+count).remove();
	
	
	
	
}
