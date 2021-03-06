/*
 * Copyright (C) 2012 United States Government as represented by the Administrator of the
 * National Aeronautics and Space Administration.
 * All Rights Reserved.
 */
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

/**
 * KeepingObjectsInView demonstrates keeping a set of scene elements visible by
 * using the utility class
 * {@link gov.nasa.worldwindx.examples.util.ExtentVisibilitySupport}. To run
 * this demonstration, execute this class' main method, then follow the
 * on-screen instructions.
 * <p/>
 * The key functionality demonstrated by KeepingObjectsVisible is found in the
 * internal classes {@link Demo.ViewController} and
 * {@link gov.nasa.worldwindx.examples.Demo.ViewAnimator}.
 *
 * @author dcollins
 * @version $Id: KeepingObjectsInView.java 1171 2013-02-11 21:45:02Z dcollins $
 */
public class Demo extends ApplicationTemplate {
	public static RenderableLayer rl = new RenderableLayer();

	public static class AppFrame extends ApplicationTemplate.AppFrame {
		protected Iterable<?> objectsToTrack;
		protected ViewController viewController;
		protected ViewController vc;
		protected RenderableLayer helpLayer;
		static ArrayList<KinematicBands> bandInfo;
		public static int animPos = 0;
		public static ArrayList<ArrayList<gov.nasa.larcfm.ACCoRD.TrafficState>> trafficArray;
		static ArrayList<gov.nasa.larcfm.ACCoRD.OwnshipState> ownArray;

		Animator animator;
		double rotationRate = 70; // degrees per second
		long lastTime;
		Position lastPosition;
		Position eyePosition = Position.fromDegrees(0, 0, 20000000);

		public static Timer animationStart;
		public static JLabel jl = new JLabel();
		public static int fr;

		public static void run(int from) {
			fr = from;
			animPos = 0;
			if (jl.getParent() != ApplicationTemplate.menubar) {
				
				ApplicationTemplate.menubar.add(jl);
			}else{
				ApplicationTemplate.menubar.remove(jl);
				ApplicationTemplate.menubar.setVisible(false);
				ApplicationTemplate.menubar.setVisible(true);
			}
			MingcongProject mingcong = new MingcongProject(from);
			bandInfo = mingcong.getBandInfo();
			trafficArray = mingcong.getTrafficArray();
			ownArray = mingcong.getOwnArray();

			animationStart.start();
		}

		public AppFrame() throws InterruptedException {

			int x = this.getWidth();
			int y = this.getHeight();
			// System.out.println(x + " " + y);
			// this.getLayerPanel().update(g);

			// System.out.println(bandInfo.get(0).track(0, "deg"));
			// System.out.println(ownPos.lat()+" "+ownPos.lon());
			// Create an iterable of the objects we want to keep in view.
			// Set up a layer to render the icons. Disable WWIcon view
			// clipping, since view tracking works best when an
			// icon's screen rectangle is known even when the icon is outside
			// the view frustum.
			IconLayer iconLayer = new IconLayer();
			iconLayer.setViewClippingEnabled(false);
			iconLayer.setName("Icons To Track");
			insertBeforePlacenames(this.getWwd(), iconLayer);

			// Set up a layer to render the markers.
			RenderableLayer shapesLayer = new RenderableLayer();
			shapesLayer.setName("Shapes to Track");
			insertBeforePlacenames(this.getWwd(), shapesLayer);

			this.getLayerPanel().update(this.getWwd());

			// Set up a SelectListener to drag the spheres.
			this.getWwd().addSelectListener(new SelectListener() {
				protected BasicDragger dragger = new BasicDragger(getWwd());

				public void selected(SelectEvent event) {
					// Delegate dragging computations to a dragger.
					this.dragger.selected(event);

					if (event.getEventAction().equals(SelectEvent.DRAG)) {
						disableHelpAnnotation();
						viewController.sceneChanged();
					}
				}
			});

			// Set up a view controller to keep the objects in view.
			this.viewController = new ViewController(this.getWwd());
			this.viewController.setObjectsToTrack(this.objectsToTrack);
			// Set up a layer to render the objects we're tracking.
			// this.addObjectsToWorldWindow(this.objectsToTrack);
			// Set up swing components to toggle the view controller's
			// behavior.
			// this.initSwingComponents();

			// Set up a one-shot timer to zoom to the objects once the app
			// launches.
			Timer timer = new Timer(800, new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					// enableHelpAnnotation();
					viewController.gotoScene();
				}
			});
			timer.setRepeats(false);
			timer.start();

			// create timer to update compass arcs
			Timer arcTimer = new Timer(1000, new ActionListener() {
				public void actionPerformed(ActionEvent ae) {
					if (animPos >= bandInfo.size()) { // don't go out of
												// bounds
						animPos = bandInfo.size() - 1;
					}
					System.out.println(animPos);

					KinematicBands kb = bandInfo.get(animPos);
					CompassLayer.arcs = new ArrayList<CompassLayer.Arc>();
					for (int i = 0; i < kb.trackLength(); ++i) {
						double start = kb.track(i, "deg").low;
						double end = kb.track(i, "deg").up;
						// boolean near = kb.trackRegion(i).name() ==
						// "NEAR";
						int near;
						if (kb.trackRegion(i).name() == "NEAR") {
							near = 1;
						} else if (kb.trackRegion(i).name() == "RECOVERY") {
							near = 2;
						} else {
							near = 0;
						}
						CompassLayer.arcs.add(new CompassLayer.Arc(Math
								.toRadians(start), Math.toRadians(end),
								near));
					}

					// Add the objects to track to the layers.
					objectsToTrack = createObjectsToTrack(
							ownArray.get(animPos),
							trafficArray.get(animPos));
					iconLayer.removeAllIcons();
					shapesLayer.removeAllRenderables();
					for (Object o : objectsToTrack) {
						if (o instanceof WWIcon)
							iconLayer.addIcon((WWIcon) o);
						else if (o instanceof Renderable)
							shapesLayer.addRenderable((Renderable) o);
					}
					Angle la = Angle.fromDegreesLatitude(ownArray
							.get(animPos).getPosition().latitude());
					Angle lo = Angle.fromDegreesLongitude(ownArray
							.get(animPos).getPosition().longitude());

					getWwd().getView().stopAnimations();

					Position animateTo = new Position(la, lo, ownArray
							.get(animPos).getPosition().altitude());
					System.out.println("\nSecond: " + animPos
							+ "\nPosition:\n\tLat: "
							+ animateTo.getLatitude() + "\n\tLo: "
							+ animateTo.getLongitude() + "\n\tAlt: "
							+ animateTo.getAltitude());

					ApplicationTemplate.menubar.remove(jl);
					// ApplicationTemplate.menubar.remove(3);
					int temp = animPos + fr;
					jl = new JLabel("Time: " + temp + "  Latitude: " + animateTo.getLatitude() + "  Longitude: " + animateTo.getLongitude() + "  Altitude: " + animateTo.getAltitude());
					ApplicationTemplate.menubar.add(jl);
					if (animPos == 0) {
						getWwd().getView().setEyePosition(
								new Position(animateTo.getLatitude(),
										animateTo.getLongitude(),
										120000));
					} else {
						// look at correct location
						OrbitView orbitView = (OrbitView) getWwd()
								.getView();

						OrbitViewState viewState = computeViewState(
								lastPosition, animateTo, getWwd()
										.getModel().getGlobe());
						if (viewState != null) {
							Angle heading = BasicViewPropertyLimits.limitHeading(
									viewState.getHeading(),
									orbitView.getViewPropertyLimits());
							Angle pitch = BasicViewPropertyLimits.limitPitch(
									viewState.getPitch(),
									orbitView.getViewPropertyLimits());
							orbitView.setHeading(heading);
							// orbitView.setPitch(pitch);

						}

						getWwd().getView().goTo(animateTo,
								animateTo.getAltitude() * 30);
						// ////////////++++++++++++++++++++++++++++++++++++++++++++++++++
					}
					lastPosition = animateTo;
					getLayerPanel().update(getWwd());
					animPos++;

				}
			});

			animationStart = new Timer(1000, new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					arcTimer.start();
				}
			});
			animationStart.setRepeats(false);

			// this.update(getGraphics());

			// new java.util.Timer().schedule(new java.util.TimerTask() {
			// @Override
			// public void run() {
			// System.out.println("Fixed");
			// Iterable<?> ot;
			// ot = createObjectsToTrack(100.0,100.0);
			// //ViewController viewController;
			// // Set up a view controller to keep the objects in view.
			// vc = new ViewController(getWwd());
			// vc.setObjectsToTrack(ot);
			// // Set up a layer to render the objects we're tracking.
			// addObjectsToWorldWindow(ot);
			// //initSwingComponents();
			// Timer timer = new Timer(1000, new ActionListener() {
			// public void actionPerformed(ActionEvent e) {
			// // enableHelpAnnotation();
			// vc.gotoScene();
			// }
			// });
			// timer.setRepeats(false);
			// timer.start();
			// }
			// }, 5000);
			// this.getLayerPanel().update(this.getWwd());

			// System.out.println("Fixed");
			// Iterable<?> ot;
			// ot = createObjectsToTrack(100.0,100.0);
			// //ViewController viewController;
			// // Set up a view controller to keep the objects in view.
			// viewController = new ViewController(getWwd());
			// viewController.setObjectsToTrack(ot);
			// // Set up a layer to render the objects we're tracking.
			// addObjectsToWorldWindow(ot);
			// //initSwingComponents();
			// timer = new Timer(1000, new ActionListener() {
			// public void actionPerformed(ActionEvent e) {
			// // enableHelpAnnotation();
			// viewController.gotoScene();
			// }
			// });
			// timer.setRepeats(false);
			// timer.start();

			// getWwd().getModel().getGlobe().getTessellator()
			// .setUpdateFrequency(5000);
			//
			// // Add a rendering listener to update the eye position each
			// // frame. It's implementation is the
			// // stageChanged method below.
			// getWwd().addRenderingListener(this);
			//
			// // Use a JOGL Animator to spin the globe
			// lastTime = System.currentTimeMillis();
			// animator = new Animator();
			// animator.add((WorldWindowGLCanvas) getWwd());
			// animator.start();

		}

		// @Override
		// public void stageChanged(RenderingEvent event) {
		// if (event.getStage().equals(RenderingEvent.BEFORE_RENDERING)) {
		// // The globe may not be instantiated the first time the
		// // listener is called.
		// if (getWwd().getView().getGlobe() == null)
		// return;
		//
		// long now = System.currentTimeMillis();
		// double d = rotationRate * (now - lastTime) * 1.0e-3;
		// lastTime = now;
		//
		// double longitude = eyePosition.getLongitude().degrees;
		// longitude += d;
		// if (longitude > 180)
		// longitude = -180 + (180 - longitude);
		//
		// eyePosition = Position.fromDegrees(
		// eyePosition.getLatitude().degrees, longitude,
		// eyePosition.getAltitude());
		// Position groundPos = new Position(
		// eyePosition.getLatitude(),
		// eyePosition.getLongitude(), 0);
		// getWwd().getView().setOrientation(eyePosition, groundPos);
		// }
		// }

		OrbitViewState computeViewState(Position eye, Position center,
				Globe globe) {
			Vec4 eyePoint = globe.computePointFromPosition(eye);
			Vec4 centerPoint = globe.computePointFromPosition(center);
			Vec4 normal = globe.computeSurfaceNormalAtLocation(
					center.getLatitude(), center.getLongitude());
			return OrbitViewInputSupport.computeOrbitViewState(globe,
					eyePoint, centerPoint, normal);
		}

		protected void enableHelpAnnotation() {
			if (this.helpLayer != null)
				return;

			this.helpLayer = new RenderableLayer();
			// this.helpLayer.addRenderable(createHelpAnnotation(getWwd()));
			insertBeforePlacenames(this.getWwd(), this.helpLayer);
		}

		protected void disableHelpAnnotation() {
			if (this.helpLayer == null)
				return;

			this.getWwd().getModel().getLayers().remove(this.helpLayer);
			this.helpLayer.removeAllRenderables();
			this.helpLayer = null;
		}

		// protected void addObjectsToWorldWindow(Iterable<?> objectsToTrack)
		// {
		// // Set up a layer to render the icons. Disable WWIcon view
		// // clipping, since view tracking works best when an
		// // icon's screen rectangle is known even when the icon is outside
		// // the view frustum.
		// IconLayer iconLayer = new IconLayer();
		// iconLayer.setViewClippingEnabled(false);
		// iconLayer.setName("Icons To Track");
		// insertBeforePlacenames(this.getWwd(), iconLayer);
		//
		// // Set up a layer to render the markers.
		// RenderableLayer shapesLayer = new RenderableLayer();
		// shapesLayer.setName("Shapes to Track");
		// insertBeforePlacenames(this.getWwd(), shapesLayer);
		//
		// this.getLayerPanel().update(this.getWwd());
		//
		// // Add the objects to track to the layers.
		// for (Object o : objectsToTrack) {
		// if (o instanceof WWIcon)
		// iconLayer.addIcon((WWIcon) o);
		// else if (o instanceof Renderable)
		// shapesLayer.addRenderable((Renderable) o);
		// }
		//
		// // Set up a SelectListener to drag the spheres.
		// this.getWwd().addSelectListener(new SelectListener() {
		// protected BasicDragger dragger = new BasicDragger(getWwd());
		//
		// public void selected(SelectEvent event) {
		// // Delegate dragging computations to a dragger.
		// this.dragger.selected(event);
		//
		// if (event.getEventAction().equals(SelectEvent.DRAG)) {
		// disableHelpAnnotation();
		// viewController.sceneChanged();
		// }
		// }
		// });
		// }

		protected void initSwingComponents() {
			// Create a checkbox to enable/disable the view controller.
			// JCheckBox checkBox = new JCheckBox("Enable view tracking",
			// true);
			// checkBox.setAlignmentX(Component.LEFT_ALIGNMENT);
			// checkBox.addActionListener(new ActionListener()
			// {
			// public void actionPerformed(ActionEvent event)
			// {
			// boolean selected = ((AbstractButton)
			// event.getSource()).isSelected();
			// viewController.setEnabled(selected);
			// }
			// });
			// JButton button = new JButton("Go to objects");
			// Reduce the frequency at which terrain is regenerated.

			// JButton play = new JButton("Pause");
			// JButton pause = new JButton("Pause");
			// button.setAlignmentX(Component.LEFT_ALIGNMENT);
			// button.addActionListener(new ActionListener()
			// {
			// public void actionPerformed(ActionEvent event)
			// {
			// viewController.gotoScene();
			// }
			// });

			// play.addActionListener(new ActionListener() {
			// public void actionPerformed(ActionEvent e) {
			// if (e.getActionCommand().equals("Pause")) {
			// lastTime = System.currentTimeMillis();
			// animator.stop();
			// play.setText("Play");
			// } else {
			// lastTime = System.currentTimeMillis();
			// animator.start();
			// play.setText("Pause");
			// }
			//
			// }
			// });

			/*
			 * Box box = Box.createVerticalBox();
			 * box.setBorder(BorderFactory.createEmptyBorder(30, 30, 30,
			 * 30)); // top, // left, // bottom, // right //
			 * box.add(checkBox); box.add(Box.createVerticalStrut(5)); //
			 * box.add(button); box.add(play); // box.add(pause);
			 * 
			 * this.getLayerPanel().add(box, BorderLayout.SOUTH);
			 */
		}
	}

	// static ArrayList<gov.nasa.larcfm.Util.Position> lastIntruderPos;

	public static Iterable<?> createObjectsToTrack(
			gov.nasa.larcfm.ACCoRD.OwnshipState ownship,
			ArrayList<gov.nasa.larcfm.ACCoRD.TrafficState> traffic) {
		// if (lastIntruderPos == null || lastIntruderPos.size() !=
		// intruderPos.size()) {
		// lastIntruderPos = intruderPos;
		// }

		ArrayList<Object> objects = new ArrayList<Object>();
		Sector sector = Sector.fromDegrees(35, 45, -110, -100);
		// ArrayList<gov.nasa.larcfm.ACCoRD.TrafficState> pos = intruderPos;
		// LatLon randLocation1; // randLocation

		// Add the ownship.
		// randLocation1 = randomLocation(sector);
		Angle la = Angle
				.fromDegreesLatitude(ownship.getPosition().latitude());
		Angle lo = Angle.fromDegreesLongitude(ownship.getPosition()
				.longitude());

		// WWIcon icon = new
		// UserFacingIcon("gov/nasa/worldwindx/examples/images/compass3.png",
		// new Position(la,lo, 0));
		// icon.setSize(new Dimension(500, 500));
		// icon.setValue(AVKey.FEEDBACK_ENABLED, Boolean.TRUE);
		// objects.add(icon);

		// Add the ghost

		WWIcon icon1 = new UserFacingIcon(
				"gov/nasa/worldwindx/examples/images/blank.png",
				new Position(la, lo, 0));
		icon1.setSize(new Dimension(5, 5));
		icon1.setValue(AVKey.FEEDBACK_ENABLED, Boolean.TRUE);
		objects.add(icon1);
		rl.removeAllRenderables();
		for (int i = 0; i < traffic.size(); i++) {
			gov.nasa.larcfm.ACCoRD.TrafficState ac = traffic.get(i);
			String id = ac.getId();
			Position pa = new Position(Angle.fromDegreesLatitude(ac
					.getPosition().latitude()),
					Angle.fromDegreesLongitude(ac.getPosition()
							.longitude()), 0);
			Rotable icon2 = new Rotable(
					"gov/nasa/worldwindx/examples/images/ownship.png", pa);
			icon2.setSize(new Dimension(35, 35));
			icon2.setValue(AVKey.FEEDBACK_ENABLED, Boolean.TRUE);
			// LatLon p1 = new
			// LatLon(Angle.fromDegreesLatitude(lastIntruderPos.get(i).latitude()),
			// Angle.fromDegreesLongitude(lastIntruderPos.get(i).longitude()));
			// LatLon p2 = new
			// LatLon(Angle.fromDegreesLatitude(intruderPos.get(i).latitude()),
			// Angle.fromDegreesLongitude(intruderPos.get(i).longitude()));
			double owangle = Math.atan2(-ownship.getVelocity().x,
					ownship.getVelocity().y);
			double trangle = Math.atan2(-ac.getVelocity().x,
					ac.getVelocity().y)-owangle;
			icon2.heading = Angle.fromRadians(trangle); // ac.getVelocity().compassAngle());
			objects.add(icon2);

			rl.setName("Annotations (stand alone)");
			insertBeforeCompass(AppFrame.getWwd(), rl);
			GlobeAnnotation ga;
			ga = new GlobeAnnotation(id + "", pa);
			ga.setHeightInMeter(5e3);

			rl.addRenderable(ga);

		}

		// randLocation1 = randomLocation(sector);
		// Airspace airspace = new SphereAirspace(randLocation1, 50000d);
		// airspace.setAltitude(0d);
		// airspace.setTerrainConforming(true);
		// airspace.setAttributes(new BasicAirspaceAttributes(Material.GREEN,
		// 1d));
		// objects.add(airspace);

		// randLocation1 = randomLocation(sector);
		// ShapeAttributes attrs = new BasicShapeAttributes();
		// attrs.setInteriorMaterial(Material.BLUE);
		// attrs.setOutlineMaterial(new Material(WWUtil
		// .makeColorBrighter(Color.BLUE)));
		// attrs.setInteriorOpacity(0.5);
		// SurfaceCircle circle = new SurfaceCircle(attrs, randLocation1,
		// 50000d);
		// objects.add(circle);

		// lastIntruderPos = intruderPos;
		return objects;
	}

	protected static LatLon randomLocation(Sector sector) {
		return new LatLon(Angle.mix(Math.random(), sector.getMinLatitude(),
				sector.getMaxLatitude()), Angle.mix(Math.random(),
				sector.getMinLongitude(), sector.getMaxLongitude()));
	}

	// public static Annotation createHelpAnnotation(WorldWindow wwd)
	// {
	// String text = "The view tracks the ownship,"
	// + " Drag your ownship out of the window to track it.";
	// Rectangle viewport = ((Component) wwd).getBounds();
	// Point screenPoint = new Point(viewport.width / 2, viewport.height / 3);
	//
	// AnnotationAttributes attr = new AnnotationAttributes();
	// attr.setAdjustWidthToText(AVKey.SIZE_FIT_TEXT);
	// attr.setFont(Font.decode("Arial-Bold-16"));
	// attr.setTextAlign(AVKey.CENTER);
	// attr.setTextColor(Color.WHITE);
	// attr.setEffect(AVKey.TEXT_EFFECT_OUTLINE);
	// attr.setBackgroundColor(new Color(0, 0, 0, 127)); // 50% transparent
	// black
	// attr.setBorderColor(Color.LIGHT_GRAY);
	// attr.setLeader(AVKey.SHAPE_NONE);
	// attr.setCornerRadius(0);
	// attr.setSize(new Dimension(350, 0));
	//
	// return new ScreenAnnotation(text, screenPoint, attr);
	// }

	// **************************************************************//
	// ******************** View Controller ***********************//
	// **************************************************************//

	public static class ViewController {
		protected static final double SMOOTHING_FACTOR = 0.96;

		protected boolean enabled = true;
		protected WorldWindow wwd;
		protected ViewAnimator animator;
		protected Iterable<?> objectsToTrack;

		public ViewController(WorldWindow wwd) {
			this.wwd = wwd;
		}

		public boolean isEnabled() {
			return this.enabled;
		}

		public void setEnabled(boolean enabled) {
			this.enabled = enabled;

			if (this.animator != null) {
				this.animator.stop();
				this.animator = null;
			}
		}

		public Iterable<?> getObjectsToTrack() {
			return this.objectsToTrack;
		}

		public void setObjectsToTrack(Iterable<?> iterable) {
			this.objectsToTrack = iterable;
		}

		public boolean isSceneContained(View view) {
			ExtentVisibilitySupport vs = new ExtentVisibilitySupport();
			this.addExtents(vs);

			return vs.areExtentsContained(view);
		}

		public Vec4[] computeViewLookAtForScene(View view) {
			Globe globe = this.wwd.getModel().getGlobe();
			double ve = this.wwd.getSceneController()
					.getVerticalExaggeration();

			ExtentVisibilitySupport vs = new ExtentVisibilitySupport();
			this.addExtents(vs);

			return vs.computeViewLookAtContainingExtents(globe, ve, view);
		}

		public Position computePositionFromPoint(Vec4 point) {
			return this.wwd.getModel().getGlobe()
					.computePositionFromPoint(point);
		}

		public void gotoScene() {
			Vec4[] lookAtPoints = this.computeViewLookAtForScene(this.wwd
					.getView());
			if (lookAtPoints == null || lookAtPoints.length != 3)
				return;

			Position centerPos = this.wwd.getModel().getGlobe()
					.computePositionFromPoint(lookAtPoints[1]);
			double zoom = lookAtPoints[0].distanceTo3(lookAtPoints[1]);
			// System.out.println(zoom);
			this.wwd.getView().stopAnimations();
			this.wwd.getView().goTo(centerPos, 700);
		}

		public void sceneChanged() {
			OrbitView view = (OrbitView) this.wwd.getView();

			if (!this.isEnabled())
				return;

			if (this.isSceneContained(view))
				return;

			if (this.animator == null || !this.animator.hasNext()) {
				this.animator = new ViewAnimator(SMOOTHING_FACTOR, view,
						this);
				this.animator.start();
				view.stopAnimations();
				view.addAnimator(this.animator);
				view.firePropertyChange(AVKey.VIEW, null, view);
			}
		}

		protected void addExtents(ExtentVisibilitySupport vs) {
			// Compute screen extents for WWIcons which have feedback
			// information from their IconRenderer.
			Iterable<?> iterable = this.getObjectsToTrack();
			if (iterable == null)
				return;

			ArrayList<ExtentHolder> extentHolders = new ArrayList<ExtentHolder>();
			ArrayList<ExtentVisibilitySupport.ScreenExtent> screenExtents = new ArrayList<ExtentVisibilitySupport.ScreenExtent>();

			for (Object o : iterable) {
				if (o == null)
					continue;

				if (o instanceof ExtentHolder) {
					extentHolders.add((ExtentHolder) o);
				} else if (o instanceof AVList) {
					AVList avl = (AVList) o;

					Object b = avl.getValue(AVKey.FEEDBACK_ENABLED);
					if (b == null || !Boolean.TRUE.equals(b))
						continue;

					if (avl.getValue(AVKey.FEEDBACK_REFERENCE_POINT) != null) {
						screenExtents
								.add(new ExtentVisibilitySupport.ScreenExtent(
										(Vec4) avl.getValue(AVKey.FEEDBACK_REFERENCE_POINT),
										(Rectangle) avl
												.getValue(AVKey.FEEDBACK_SCREEN_BOUNDS)));
					}
				}
			}

			if (!extentHolders.isEmpty()) {
				Globe globe = this.wwd.getModel().getGlobe();
				double ve = this.wwd.getSceneController()
						.getVerticalExaggeration();
				vs.setExtents(ExtentVisibilitySupport
						.extentsFromExtentHolders(extentHolders, globe,
								ve));
			}

			if (!screenExtents.isEmpty()) {
				vs.setScreenExtents(screenExtents);
			}
		}
	}

	// **************************************************************//
	// ******************** View Animator *************************//
	// **************************************************************//

	public static class ViewAnimator extends BasicAnimator {
		protected static final double LOCATION_EPSILON = 1.0e-9;
		protected static final double ALTITUDE_EPSILON = 0.1;

		protected OrbitView view;
		protected ViewController viewController;
		protected boolean haveTargets;
		protected Position centerPosition;
		protected double zoom;

		public ViewAnimator(final double smoothing, OrbitView view,
				ViewController viewController) {
			super(new Interpolator() {
				public double nextInterpolant() {
					return 1d - smoothing;
				}
			});

			this.view = view;
			this.viewController = viewController;
		}

		public void stop() {
			super.stop();
			this.haveTargets = false;
		}

		protected void setImpl(double interpolant) {
			this.updateTargetValues();

			if (!this.haveTargets) {
				this.stop();
				return;
			}

			if (this.valuesMeetCriteria(this.centerPosition, this.zoom)) {
				this.view.setCenterPosition(this.centerPosition);
				this.view.setZoom(this.zoom);
				this.stop();
			} else {
				Position newCenterPos = Position.interpolateGreatCircle(
						interpolant, this.view.getCenterPosition(),
						this.centerPosition);
				double newZoom = WWMath.mix(interpolant,
						this.view.getZoom(), this.zoom);
				this.view.setCenterPosition(newCenterPos);
				this.view.setZoom(newZoom);
			}

			this.view.firePropertyChange(AVKey.VIEW, null, this);
		}

		protected void updateTargetValues() {
			if (this.viewController.isSceneContained(this.view))
				return;

			Vec4[] lookAtPoints = this.viewController
					.computeViewLookAtForScene(this.view);
			if (lookAtPoints == null || lookAtPoints.length != 3)
				return;

			this.centerPosition = this.viewController
					.computePositionFromPoint(lookAtPoints[1]);
			this.zoom = lookAtPoints[0].distanceTo3(lookAtPoints[1]);
			if (this.zoom < view.getZoom())
				this.zoom = view.getZoom();

			this.haveTargets = true;
		}

		protected boolean valuesMeetCriteria(Position centerPos, double zoom) {
			Angle cd = LatLon.greatCircleDistance(
					this.view.getCenterPosition(), centerPos);
			double ed = Math.abs(this.view.getCenterPosition()
					.getElevation() - centerPos.getElevation());
			double zd = Math.abs(this.view.getZoom() - zoom);

			return cd.degrees < LOCATION_EPSILON && ed < ALTITUDE_EPSILON
					&& zd < ALTITUDE_EPSILON;
		}
	}

	public static void main(String[] args) {

		ApplicationTemplate.start(
				"Demo of Air Traffic Conflict Prevention Bands Algorithms",
				AppFrame.class);
		MyPVSioWeb myPVSioWeb = new MyPVSioWeb("localhost:8082/");
		if (myPVSioWeb.connect()) {
			// myPVSioWeb.startPVSioDemo("TCAS/pvs", "top");
			myPVSioWeb.startPVSioDemo("DAIDALUS/demos/BBraun/pvs",
					"bbraun_space");
			java.util.Scanner scanner = new java.util.Scanner(System.in);
			String expr = "";
			while (expr.equals("quit;") == false) {
				expr = scanner.next();
				myPVSioWeb.evalExpr(expr);
			}
		} else {
			System.err.println("PVSio-web is not running. Please start PVSio-web first by executing ./start.sh from the pvsio-web folder.");
		}
	}
}
