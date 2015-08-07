/**
 * @author Piergiuseppe Mallozzi
 * @date 06/04/15 10:30:00 AM
 */

public class FlightSimulator {


    public static void main(String[] args) {
        MyPVSioWeb myPVSioWeb = new MyPVSioWeb("localhost:8082/");
        if(myPVSioWeb.connect()){
//          myPVSioWeb.startPVSioDemo("TCAS/pvs", "top");
            myPVSioWeb.startPVSioDemo("BBraun/pvs", "bbraun_space");
            java.util.Scanner scanner = new java.util.Scanner(System.in);
            String expr = "";
            while(expr.equals("quit;") == false){
                expr = scanner.next();
                myPVSioWeb.evalExpr(expr);
            }
        } else {
            System.err.println("PVSio-web is not running. Please start PVSio-web first by executing ./start.sh from the pvsio-web folder.");
        }

    }
    
    
}
