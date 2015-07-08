/**
 * @author Piergiuseppe Mallozzi
 * @date 06/03/15 20:30:00 PM
 */

package pvsioweb;
import javax.json.Json;
import javax.json.JsonObject;
import javax.json.JsonReader;
import javax.websocket.*;
import java.io.StringReader;

/**
 * Websocket Client Endpoint
 */
@javax.websocket.ClientEndpoint
public class ClientEndpoint {

    IPVSioCallback ipvsioweb;

    public ClientEndpoint(IPVSioCallback ipvsioweb){
        this.ipvsioweb = ipvsioweb;
    }

    @OnOpen
    public void onOpen(Session session) {

        ipvsioweb.onConnect(session);
    }

    @OnMessage
    public void onMessage(String message, Session session) {

        try (JsonReader reader = Json.createReader(new StringReader(message))) {
            JsonObject jsonMessage = reader.readObject();

            String ans;

            try { // PVSio response

                ans = jsonMessage.get("data").toString();
                ans = ans.replace("\",\"", "\n");

            } catch (Exception e) { // other

                ans = jsonMessage.getString("data");
            }

            ans = ans.substring(1, ans.length()-1);


            if(ans.startsWith("\"") && ans.endsWith("\"")){
                ans = ans.substring(1, ans.length()-1);
            }

            ipvsioweb.onMessage(ans);
        }
        catch (Exception e){
            onError(e);
        }
    }

    @OnError
    public void onError(Throwable t) {

        ipvsioweb.onError(t);
    }

    @OnClose
    public void onClose(Session session) {

        ipvsioweb.onClose(session);
    }


}