var counter = 1;
var limit = 10;
function addInput(divName){
     if (counter == limit)  {
          alert("You have reached the limit of adding " + counter + " inputs");
     }
     else {
          var newdiv = document.createElement('div');
          newdiv.innerHTML = '<br><label>When the caller presses:</label><select name="number_choice[]"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="0">0</option></select>';
		  newdiv.innerHTML += '&nbsp;<label>then:</label><select name="route_choice[]"><option value="1">route to</option><option value="2">send to voicemail</option><option value="3">hang up call</option><option value="4">forward to phone number</option><option value="5">send to new IRV</option></select> ';
          document.getElementById(divName).appendChild(newdiv);
          counter++;
     }
}