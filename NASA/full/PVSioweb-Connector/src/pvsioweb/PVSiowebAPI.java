/**
 * @author Piergiuseppe Mallozzi
 * @date 06/03/15 17:30:00 PM
 */

package pvsioweb;
import javax.json.Json;
import javax.websocket.ContainerProvider;
import javax.websocket.Session;
import javax.websocket.WebSocketContainer;
import java.net.URI;
import java.util.Date;
import java.util.Random;

public class PVSiowebAPI {

    private WebSocketContainer container = null;
    private Session session = null;
    private IPVSioCallback ipvsioCallback = null;
    private String uri = null;


    /**
     * Constructor with no specific URI
     * @param ipvsioCallback pointer to the interface used for the callback functions
     */
    public PVSiowebAPI(IPVSioCallback ipvsioCallback){
        this.ipvsioCallback = ipvsioCallback;
    }

    /**
     * Constructor with a specific URI
     * @param ipvsioCallback pointer to the interface used for the callback functions
     */
    public PVSiowebAPI(IPVSioCallback ipvsioCallback, String uri){
        this.ipvsioCallback = ipvsioCallback;
        this.uri = uri;
    }

    /**
     * It opens a websocket connection with PVSio-web
     * callback: -> onConnect()
     */
    public boolean connect() {
        if(session == null) {
            try {
                container = ContainerProvider.getWebSocketContainer();
                ClientEndpoint clientEndpoint = new ClientEndpoint(ipvsioCallback);
                if (uri != null) {
                    session = container.connectToServer(clientEndpoint, URI.create("ws://" + uri));
                } else {
                    session = container.connectToServer(clientEndpoint, URI.create("ws://localhost:8082/"));
                }
                return true;
            } catch (Exception e) {
                ipvsioCallback.onError(e);
                return false;
            }
        }
        else{
            ipvsioCallback.onError("A websocket Session with PVSio-web is already opened. \nPlease close the other session using the method close().");
            return false;
        }
    }


    /**
     * It send a message to the websocket session
     * A session must exists (connect())
     */
    public boolean send(String message) {
        if(session != null) {

            try {
                session.getBasicRemote().sendText(message);
                return true;
            } catch (Exception e) {
                ipvsioCallback.onError(e);
                return false;
            }
        }
        else{
            ipvsioCallback.onError("There is no websocket Session opened with PVSio-web");
            return false;
        }
    }

    /**
     * Close current websocket session with PVSio-web
     * A session must exists (connect())
     */
    public boolean close() {
        if(session != null) {
            try {
                session.close();
                session = null;
            } catch (Exception e) {
                ipvsioCallback.onError(e);
                return false;
            }
            return true;
        }
        else{
            ipvsioCallback.onError("There is no websocket Session opened with PVSio-web");
            return false;
        }
    }

    private String uuid() {
        String d = new java.util.Date().toString();
        long date = new Date().getTime();
        String uuid = String.valueOf(date);
        return uuid;
    }

    private String startPVSioProjectCMD(String projectName, String theoryName) {
        javax.json.JsonObject cmd = Json.createObjectBuilder()
                .add("id", uuid())
                .add("time", Json.createObjectBuilder()
                        .add("client", Json.createObjectBuilder().add("sent", new java.util.Date().toString())))
                .add("type", "startProcess")
                .add("data", Json.createObjectBuilder().add("projectName", projectName).add("name", theoryName))
                .build();
        return cmd.toString();
    }

    private String startPVSioDemoCMD(String demoName, String theoryName) {
        javax.json.JsonObject cmd = Json.createObjectBuilder()
                .add("id", uuid())
                .add("time", Json.createObjectBuilder()
                        .add("client", Json.createObjectBuilder().add("sent", new java.util.Date().toString())))
                .add("type", "startProcess")
                .add("data", Json.createObjectBuilder().add("demoName", demoName).add("name", theoryName))
                .build();
        return cmd.toString();
    }


    private String openProjectCMD(String projectName) {
        javax.json.JsonObject cmd = Json.createObjectBuilder()
                .add("id", uuid())
                .add("time", Json.createObjectBuilder()
                        .add("client", Json.createObjectBuilder().add("sent", new java.util.Date().toString())))
                .add("type", "openProject")
                .add("name", projectName)
                .build();
        return cmd.toString();
    }

    private String evalExprCMD(String expr) {
        javax.json.JsonObject cmd = Json.createObjectBuilder()
                .add("id", uuid())
                .add("time", Json.createObjectBuilder()
                        .add("client", Json.createObjectBuilder().add("sent", new java.util.Date().toString())))
                .add("type", "sendCommand")
                .add("data", Json.createObjectBuilder().add("command", expr))
                .build();
        return cmd.toString();
    }

    public boolean startPVSioProject(String projectName, String theoryName){
        System.out.println("Starting PVSio Project: " + projectName + "/" + theoryName + " ...");
        return send(startPVSioProjectCMD(projectName, theoryName));
    }

    public boolean startPVSioDemo(String demoName, String theoryName){
        System.out.println("Starting PVSio Demo: " + demoName + "/" + theoryName + " ...");
        return send(startPVSioDemoCMD(demoName, theoryName));
    }


    public boolean evalExpr(String expr){
        return send(evalExprCMD(expr));
    }



}
