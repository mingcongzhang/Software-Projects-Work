package gov.nasa.worldwindx.examples;

import java.util.Timer;
import javax.swing.*;
import java.awt.Graphics;
import java.awt.Point;
import java.awt.event.*;
import java.util.Timer;
import java.util.TimerTask;
import javax.swing.JComponent;

public class GrowPanel extends JComponent{
	private int x;
	    private int y;
	    private Timer timer;
	    GrowPanel() {
	        x = 10;
	        y = 10;
	        startPaiting();
	    }

	    public void startPaiting() {
	        timer = new Timer();
	        timer.schedule( new TimerTask(){
	            public void run(){
	                changeState();
	                repaint();
	            }
	        },0,  100 ); // or 10000 which is 10 s. 
	    }

	    public void pause(){
	        timer.cancel();
	    }

	    public void paintComponent( Graphics g ){
	        g.fillOval( x, y, 10, 10 );
	    }
	    private void changeState(){
	            x+=10;
	            if( x >= 400 ) {
	                y+=10;
	                x = 0;
	            }
	            if( y >= 300 ){
	                y = 10;
	            }
	    }
}
