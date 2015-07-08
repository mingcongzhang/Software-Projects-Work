
function addRow(tableID,divID) {
	var div = document.getElementById(divID);
	
	var table = document.getElementById(tableID);

	var rowCount = table.rows.length;
	
	//if(rowCount<10){

	var row = table.insertRow(rowCount);

	var cell1 = row.insertCell(0);

	var element1 = document.createElement("input");

	element1.type = "button";

	var btnName =  "button" + (rowCount + 1);

	element1.name = btnName;

	element1.setAttribute('value', 'Delete IVR Prompt');  // or element1.value = "button";

	element1.onclick = function () { removeRow(btnName); }

	cell1.appendChild(element1);

	var cell2 = row.insertCell(1);

	cell2.innerHTML = '<label style="white-space: nowrap;" >When the caller presses:</label>';

	var cell3 = row.insertCell(2);
	
	var number_name = 'number'+rowCount;

	cell3.innerHTML = '<select name="number_choice[]"><option selected="selected" value="">- Select an option -</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="0">0</option></select>';

	var cell4 = row.insertCell(3);
	
	cell4.innerHTML = '<label style="white-space: nowrap;" >then:</label>';
	
	var cell5 = row.insertCell(4);
	
	var route_name = 'route'+rowCount;

	cell5.innerHTML = '<select name='+route_name+' id='+route_name+' ><option selected="selected" value="">- Select an option -</option><option value="1">route to</option><option value="2">send to voicemail</option><option value="3">hang up call</option><option value="4">forward to phone number</option><option value="5">send to new IRV</option></select> ';
						document.getElementById(route_name).addEventListener('change',function(){derived_choice(route_name,row,table, div)},false);

//}else{
//	alert("You have reached limit of 10 IVR Prompts!");
//}
}

function removeRow(btnName) {

	try {

		var table = document.getElementById('dataTable');

		var rowCount = table.rows.length;

		for (var i = 0; i < rowCount; i++) {

			var row = table.rows[i];

			var rowObj = row.cells[0].childNodes[0];

			if (rowObj.name == btnName) {

				table.deleteRow(i);

				rowCount--;

			}

		}

	}

	catch (e) {

		alert(e);

	}

}

function derived_choice(name,row,table,div){
		
		if(document.getElementById(name).selectedIndex == "0") {
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
			added_cell1.innerHTML = '<label style="white-space: nowrap;">&nbsp;&nbsp;&nbsp;</label> ';
			var added_cell2 = added_row1.insertCell(1);
			added_cell2.innerHTML += '<label style="white-space: nowrap;">Available Agents Greeting</label> ';
			var added_cell3 = added_row1.insertCell(2);
			added_cell3.innerHTML = '<select><option selected="selected" value="">- Select an option -</option> </select>';
			//**********Voicemail Greeting
			var added_row2 = table.insertRow(row.rowIndex+2);
			added_row2.setAttribute("id","special_row2");
			var added_cell1 = added_row2.insertCell(0);
			added_cell1.innerHTML = '<label style="white-space: nowrap;">&nbsp;&nbsp;&nbsp;</label> ';
			var added_cell2 = added_row2.insertCell(1);
			added_cell2.innerHTML += '<label style="white-space: nowrap;">Voicemail Greeting</label> ';
			var added_cell3 = added_row2.insertCell(2);
			added_cell3.innerHTML = '<select> <option selected="selected" value="">- Select an option -</option> </select>';
			//**********Outside Business Hours Greeting
			var added_row3 = table.insertRow(row.rowIndex+3);
			added_row3.setAttribute("id","special_row3");
			var added_cell1 = added_row3.insertCell(0);
			added_cell1.innerHTML = '<label style="white-space: nowrap;">&nbsp;&nbsp;&nbsp;</label> ';
			var added_cell2 = added_row3.insertCell(1);
			added_cell2.innerHTML += '<label style="white-space: nowrap;">Outside Business Hours Greeting</label> ';
			var added_cell3 = added_row3.insertCell(2);
			added_cell3.innerHTML = '<select> <option selected="selected" value="">- Select an option -</option> </select>';
			
			
		}else if(document.getElementById(name).selectedIndex == "5") {
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
			//alert("5");
			var added_row1 = table.insertRow(row.rowIndex+1);
			added_row1.setAttribute("id","special_row1");
			var added_cell1 = added_row1.insertCell(0);
			added_cell1.innerHTML = '<label style="white-space: nowrap;">&nbsp;&nbsp;&nbsp;</label> ';
			
			
			var added_cell2 = added_row1.insertCell(1);
			alert("Hello");
			
			added_cell2.innerHTML = div.innerHTML;
			added_cell2.innerHTML =added_cell2.innerHTML.replace(/dataTable/g,"dataTable1");
			added_cell2.innerHTML =added_cell2.innerHTML.replace(/ivr_div/g,"ivr_div1");
			added_cell2.innerHTML =added_cell2.innerHTML.replace(/expandcollapse_1/g,"expandcollapse_2");
			//div.replace("dataTable","dataTable1");
			//div.replace("ivr_div","ivr_div1");
			
			alert(div.innerHTML);
			
			
	}	
}