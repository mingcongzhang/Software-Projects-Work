package gov.nasa.worldwindx.examples;

import gov.nasa.larcfm.ACCoRD.KinematicBands;
import gov.nasa.worldwind.*;
import gov.nasa.worldwind.animation.*;
import gov.nasa.worldwind.avlist.*;
import gov.nasa.worldwind.awt.WorldWindowGLCanvas;
import gov.nasa.worldwind.event.*;
import gov.nasa.worldwind.geom.*;
import gov.nasa.worldwind.globes.Globe;
import gov.nasa.worldwind.layers.*;
import gov.nasa.worldwind.render.*;
import gov.nasa.worldwind.render.airspaces.*;
import gov.nasa.worldwind.util.*;
import gov.nasa.worldwind.view.BasicViewPropertyLimits;
import gov.nasa.worldwind.view.orbit.OrbitView;
import gov.nasa.worldwind.view.orbit.OrbitViewInputSupport;
import gov.nasa.worldwind.view.orbit.OrbitViewInputSupport.OrbitViewState;
import gov.nasa.worldwindx.examples.util.ExtentVisibilitySupport;

import javax.swing.*;
import javax.swing.Box;
import javax.swing.Timer;

import com.jogamp.opengl.util.Animator;

import pvsioweb.MyPVSioWeb;

import java.awt.*;
import java.awt.event.*;
import java.util.*;

public class Rotable extends UserFacingIcon {

	Angle heading;
	
	public Rotable(String url, Position pos) {
		super(url, pos);
	}
	
	public Angle getHeading() {
		return heading;
	}
	
}
