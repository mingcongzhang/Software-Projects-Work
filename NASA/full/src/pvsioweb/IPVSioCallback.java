/**
 * @author Piergiuseppe Mallozzi
 * @date 06/03/15 20:30:00 PM
 */

package pvsioweb;

import javax.websocket.Session;

public interface IPVSioCallback {

    /**
     * Executed when a websocket session has been established
     * @param session Session established
     */
    void onConnect(Session session);


    /**
     * Executed when message has been received from PVSio-web
     * @param message String containing the message received
     */
    void onMessage(String message);

    /**
     * Executed when an Exception has happened
     * @param t
     */
    void onError(Throwable t);

    /**
     * Callback for error messages
     * @param message Content of the message
     */
    void onError(String message);

    /**
     * Callback when the session is closed
     * @param session Session closed
     */
    void onClose(Session session);

}
