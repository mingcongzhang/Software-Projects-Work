/**
 * @author Piergiuseppe Mallozzi
 * @date 06/04/15 10:30:00 AM
 */

import pvsioweb.PVSioweb;

public class MyPVSioWeb extends PVSioweb {

    public MyPVSioWeb(String uri){
        super (uri);
    }

    @Override
    public void onMessage(String message) {
        System.out.print(message + "\n<PVSio> ");
    }

    
}
