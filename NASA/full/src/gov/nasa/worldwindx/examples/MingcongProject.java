package gov.nasa.worldwindx.examples;


import java.util.ArrayList;

import gov.nasa.larcfm.ACCoRD.Daidalus;
import gov.nasa.larcfm.ACCoRD.KinematicBands;
import gov.nasa.larcfm.ACCoRD.OwnshipState;
import gov.nasa.larcfm.ACCoRD.TrafficState;
import gov.nasa.larcfm.Util.Position;

public class MingcongProject {

	static Daidalus daa = new Daidalus();
	double time;
	KinematicBands bandInfo;
	ArrayList<Position> intruderPos = new ArrayList<Position>();
	Position ownPos;
	
	public MingcongProject(double time){
		this.time = time;
		String config_file = "/Users/mzhang7/Documents/workspace/full/src/default_parameters.txt";
		String input_file = "/Users/mzhang7/Documents/workspace/full/src/head_on_scenario_0.txt";

		// Here, have a way to read configuration file and an input file using
		// the graphical interface

		daa.loadParametersFromFile(config_file);
		daa.setAlertingTime(60);
		daa.setTimeDelay(5);
		daa.setCollisionAvoidanceBands(true);
		DaidalusWalker walker = new DaidalusWalker(input_file);

		while (!walker.atEnd()) {
	
			//System.out.println("** Time: " + walker.currentTime());
			walker.readState(daa);

			if(Math.abs(walker.currentTime()-this.time)<1.0){
				KinematicBands bands = daa.getKinematicBands();
//				for (int i = 0; i < bands.trackLength(); ++i) {
//					bandInfo.add(bands);
//					//System.out.println(bands.track(i, "deg") + " "+ bands.trackRegion(i));
//
//				}
				bandInfo = bands;
				for (int i = 1; i < daa.numberOfAircraft(); ++i) {
					intruderPos.add(daa.getTraffic(i).getPosition());
				}
				this.ownPos = daa.getOwnship().getPosition();
				break;
			}
			
			
			
			//OwnshipState own = daa.getOwnship();
			

			// Draw aircraft
			/*
			KinematicBands bands = daa.getKinematicBands();

			for (int i = 0; i < bands.trackLength(); ++i) {
				System.out.println(bands.track(i, "deg") + " "
						+ bands.trackRegion(i));

			}
*/
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
	public KinematicBands getBandInfo(){
		return bandInfo;
	}
	public ArrayList<Position> getIntruderPos(){
		return intruderPos;
	}
	public Position getOwnPosition(){
		return ownPos;
	}

}
