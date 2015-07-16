package gov.nasa.worldwindx.examples;

import java.awt.BasicStroke;
import java.awt.Color;
import java.awt.Graphics;
import java.awt.Graphics2D;

import javax.swing.JFrame;
import javax.swing.JPanel;

public class DrawArc extends JPanel
{
    public DrawArc()                       // set up graphics window
    {
        super();

    }

    public void paint(Graphics g) {
		 g.setColor(Color.decode("#D2691E"));

		 ((Graphics2D) g).setStroke(new BasicStroke(5));
	    g.drawArc (10, 10, 200, 200,0,110);   

	    //drawArc(int x, int y, int width, int height, int startAngle, int arcAngle) 
	   // Draws the outline of a circular or elliptical arc covering the specified rectangle.
	/*    x the x coordinate of the upper-left corner of the arc to be drawn.
	    y the y coordinate of the upper-left corner of the arc to be drawn.
	    width the width of the arc to be drawn.
	    height the height of the arc to be drawn.
	    startAngle the beginning angle.
	    arcAngle the angular extent of the arc, relative to the start angle.*/
	  }


}
