package gov.nasa.worldwindx.examples;


import java.util.ArrayList;

import gov.nasa.larcfm.ACCoRD.Daidalus;
import gov.nasa.larcfm.ACCoRD.KinematicBands;
import gov.nasa.larcfm.ACCoRD.OwnshipState;
import gov.nasa.larcfm.ACCoRD.TrafficState;
import gov.nasa.larcfm.Util.Position;
import gov.nasa.worldwind.layers.CompassLayer;

public class MingcongProject {

	static Daidalus daa = new Daidalus();
	double time;
	ArrayList<KinematicBands> bandInfo = new ArrayList<KinematicBands>();
	ArrayList<ArrayList<TrafficState>> trafficArray = new ArrayList<ArrayList<TrafficState>>();
	ArrayList<OwnshipState> ownArray = new ArrayList<OwnshipState>();
	public static String config_file, input_file;
	
			
	public MingcongProject(double time){
		this.time = time;
		config_file = "/Users/mzhang7/Documents/workspace/full/src/default_parameters.txt";
//		String input_file = "/Users/mzhang7/Documents/workspace/full/src/multi_converge_scenario_0.txt";
	input_file = "/Users/mzhang7/Documents/workspace/full/src/test.txt";

		// Here, have a way to read configuration file and an input file using
		// the graphical interface
		daa.loadParametersFromFile(config_file);
		daa.setAlertingTime(60);
		daa.setTimeDelay(5);
		daa.setCollisionAvoidanceBands(true);
		DaidalusWalker walker = new DaidalusWalker(input_file);

		while (!walker.atEnd()) {
			//System.out.println(walker.currentTime()+"____________");

			if (walker.currentTime() >= time) {

				walker.readState(daa);
				bandInfo.add(daa.getKinematicBands());
				
				ArrayList<TrafficState> intruders = new ArrayList<TrafficState>();
				for (int i = 1; i < daa.numberOfAircraft(); ++i) {
					intruders.add(daa.getTraffic(i));
				}
				trafficArray.add(intruders);
				ownArray.add(daa.getOwnship());			
			}
			else{walker.goNext();}
			//OwnshipState own = daa.getOwnship();
			

			// Draw aircraft
			
/*			KinematicBands bands = daa.getKinematicBands();

			for (int i = 0; i < bands.trackLength(); ++i) {
				System.out.println(bands.track(i, "deg") + " "
						+ bands.trackRegion(i));

			}*/

			// Draw bands

		}
	
	}
	
/*	public double getUpperBound(){
		return upperBound;
	}
	public double getLowerBound(){
		return lowerBound;
	}
	public double getLatitude(){
		return La;
	}
	public double getLongitude(){
		return Lo;
	}
	public String getBandName(){
		return band;
	}*/
	public ArrayList<KinematicBands> getBandInfo(){
		return bandInfo;
	}
	public ArrayList<ArrayList<TrafficState>> getTrafficArray(){
		return trafficArray;
	}
	public ArrayList<OwnshipState> getOwnArray(){
		return ownArray;
	}

}
