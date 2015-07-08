/**
 * @author Piergiuseppe Mallozzi
 * @date 06/03/15 21:30:00 PM
 */

package pvsioweb;

import javax.websocket.Session;

public abstract class PVSioweb implements IPVSioCallback {

    private PVSiowebAPI pvSiowebAPI;

    public PVSioweb(){
        System.out.println("Demo Java App for sending sending commands to PVSio through PVSio-web (type 'quit;' to exit)");
        pvSiowebAPI = new PVSiowebAPI(this);
    }

    public PVSioweb(String uri){
        System.out.println("Demo Java App for sending sending commands to PVSio through PVSio-web (type 'quit;' to exit)");
        pvSiowebAPI = new PVSiowebAPI(this, uri);
    }

    public boolean connect(){
        return pvSiowebAPI.connect();
    }

    public boolean close(){
        return pvSiowebAPI.close();
    }

    public boolean send(String message){
        return pvSiowebAPI.send(message);
    }

    public boolean startPVSioProject(String projectName, String theoryName){
        return pvSiowebAPI.startPVSioProject(projectName, theoryName);
    }

    public boolean startPVSioDemo(String demoName, String theoryName){
        return pvSiowebAPI.startPVSioDemo(demoName, theoryName);
    }

    public boolean evalExpr(String expr){
        return pvSiowebAPI.evalExpr(expr);
    }


    /**
     * Executed when a websocket session has been established
     * @param session Session established
     */
    public void onConnect(Session session) {

//        System.out.println("Websocket connected");
    }

    /**
     * Callback when the session is closed
     * @param session Session closed
     */
    public void onClose(Session session) {

        System.out.println("Websocket disconnected");
    }


    /**
     * Callback for error messages
     * @param message Content of the message
     */
    public void onError(String message) {

        System.err.println("Error: " + message);
    }

    /**
     * Executed when an Exception has happened
     * @param t
     */
    public void onError(Throwable t){

        System.err.println(t.toString());
    }

}
