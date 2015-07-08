import gov.nasa.larcfm.ACCoRD.Daidalus;
import gov.nasa.larcfm.ACCoRD.KinematicBands;
import gov.nasa.larcfm.ACCoRD.OwnshipState;
import gov.nasa.larcfm.ACCoRD.TrafficState;

public class MingcongProject {

	static Daidalus daa = new Daidalus();

	// static DaidalusWalker walker;

	public static void main(String[] args) {
		String config_file = "/Users/cmunoz/src/DAIDALUS/Java/default_parameters.txt";
		String input_file = "/Users/cmunoz/src/DAIDALUS/tests/head_on_scenario_0.txt";

		// Here, have a way to read configuration file and an input file using
		// the graphical interface

		daa.loadParametersFromFile(config_file);
		daa.setAlertingTime(60);
		daa.setTimeDelay(5);
		daa.setCollisionAvoidanceBands(true);
		DaidalusWalker walker = new DaidalusWalker(input_file);

		while (!walker.atEnd()) {
			
			
			
			System.out.println("** Time: " + walker.currentTime());
			walker.readState(daa);

			OwnshipState own = daa.getOwnship();
			for (int i = 1; i < daa.numberOfAircraft(); ++i) {
				TrafficState ac = daa.getTraffic(i);
			}

			// Draw aircraft

			KinematicBands bands = daa.getKinematicBands();
			//daa.addPlanFromState("ownship", arg1, arg2, arg3);
//			walker.goToEnd();
//			System.out.println(bands.trackRegion(0).name());

			for (int i = 0; i < bands.trackLength(); ++i) {
				System.out.println(bands.track(i, "deg") + " "
						+ bands.trackRegion(i));

			}

			// Draw bands

		}

	}

}
