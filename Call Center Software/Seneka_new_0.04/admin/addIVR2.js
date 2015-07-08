
     function addRow(tableID) {

        var table = document.getElementById(tableID);

        var rowCount = table.rows.length;

        var row = table.insertRow(rowCount);

         //Column 1

        var cell1 = row.insertCell(0);

        var element1 = document.createElement("input");

        element1.type = "button";

        var btnName =  "button" + (rowCount + 1);

        element1.name = btnName;

        element1.setAttribute('value', 'Delete IVR Prompt');  // or element1.value = "button";

        element1.onclick = function () { removeRow(btnName); }

        cell1.appendChild(element1);

         //Column 2    

        var cell2 = row.insertCell(1);

        cell2.innerHTML = '<label>When the caller presses:</label>';

         //Column 3

        var cell3 = row.insertCell(2);
		
		cell3.innerHTML = '<select name="number_choice[]"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="0">0</option></select>';

        var cell4 = row.insertCell(3);
		
		cell4.innerHTML = '<label>then:</label>';
		
		var cell5 = row.insertCell(4);
		
		cell5.innerHTML = '<select name="route_choice[]"><option value="1">route to</option><option value="2">send to voicemail</option><option value="3">hang up call</option><option value="4">forward to phone number</option><option value="5">send to new IRV</option></select> ';
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