import gov.nasa.larcfm.ACCoRD.Daidalus;
import gov.nasa.larcfm.Util.Position;
import gov.nasa.larcfm.Util.SequenceReader;
import gov.nasa.larcfm.Util.Velocity;
import java.util.ArrayList;


public class DaidalusWalker {

  private SequenceReader sr;
  private ArrayList<Double> times;
  private int index;

  public DaidalusWalker(String filename) {
    sr = new SequenceReader(filename);
    index = 0;
    sr.setWindowSize(1);
    times = sr.sequenceKeys();
  }

  public double firsTime() {
    return times.get(0);
  }

  public double LastTime() {
    return times.size()-1;
  }

  public double currentTime() {
    if (0 <= index && index < times.size()) {
      return times.get(index);
    } else {
      return -1;
    }
  }

  public boolean atBeginning() {
    return index == 0;
  }

  public boolean atEnd() {
    return index == times.size()-1;
  }

  private void goTo(int i) {
    index = i;
    sr.setActive(currentTime());
  }
  
  public void goToBeginning() {
    goTo(0);
  }
  
  public void goToEnd() {
    goTo(times.size()-1);
  }
  
  public void goNext() {
    if (!atEnd()) {
      goTo(index+1);
    }
  }

  public void goPrev() {
    if (!atBeginning()) {
      goTo(index-1);
    }
  }

  public void goToTime(double t) {
    int i = 0;
    if (t >= firsTime()) {
      for (; i < times.size()-1; ++i) {
        if (t >= times.get(i) && t < times.get(i+1)) {
          break;
        }
      }
    }
    goTo(i);
  }
  
  public void readState(Daidalus daa) {
    daa.clearPlans();
    for (int ac = 0;ac < sr.size();++ac) {
      String ida = sr.getName(ac);
      if (sr.hasEntry(ida,currentTime())){
        Position sa = sr.getPosition(ac);
        Velocity va = sr. getVelocity(ac);
        daa.addPlanFromState(ida,sa,va,currentTime());
      }
    }
    goNext();
  }

}
