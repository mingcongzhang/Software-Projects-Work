package gov.nasa.worldwindx.examples;

/*1. Compute a great circle arc (GCA) from the origin to the destination.
2. Break up the arc into smaller pieces (lots of little GCAs) - there is a method in WW to do it. 
3. Make a thread to spawn new animators to fly between each little GCA's origin and destination points.
4. For each time the thread spawns a new animator, first compute new heading and pitch from start point of little GCA to the end point of the same GCA and set the camera to those values.*/

import gov.nasa.worldwind.animation.*;
import gov.nasa.worldwind.geom.*;
import gov.nasa.worldwind.globes.Earth;
import gov.nasa.worldwind.globes.Globe;
import gov.nasa.worldwind.util.PropertyAccessor;
import gov.nasa.worldwind.view.*;
import gov.nasa.worldwind.view.orbit.*;
import gov.nasa.worldwind.tracks.TrackPoint;

public class FirstPersonTrackAnimator extends CompoundAnimator
{
    BasicOrbitView orbitView;

    TrackPoint[] track;
    Vec4[] speed;
    int index = 0;

    public FirstPersonTrackAnimator(OrbitView orbitView, Interpolator interpolator,
                                   Animator[] animators,
                                   TrackPoint[] track, Vec4[] speed)
    {
        super(interpolator);
        this.animators = animators;
        this.orbitView = (BasicOrbitView) orbitView;
        if (interpolator == null)
        {
            this.interpolator = new ScheduledInterpolator(10000);
        }
        this.track = track;
        this.speed = speed;
    }


    protected void setImpl(double interpolant)
    {
        boolean allStopped = true;
        for (Animator a : animators)
        {
            if (a != null)
            {
                if (a.hasNext())
                {
                    allStopped = false;
                    a.set(interpolant);
                }
            }
        }
        if (allStopped && index >= track.length)
        {
            this.stop();
        }
        else if (allStopped)
        {
            index++;
            
            long timeToMove = getTimeToMove(orbitView.getEyePosition(), track[index].getPosition());

            interpolator = new ScheduledInterpolator(timeToMove);
            animators = createAnimators(orbitView, timeToMove, track[index].getPosition(), speed[index]);
        }
    }

    private static Animator[] createAnimators(OrbitView orbitView, long timeToMove,
                                              Position centerPos,
                                              Vec4 speed)
    {
        Vec4 upStart = Vec4.UNIT_Y;
        Vec4 forwardStart = Vec4.UNIT_NEGATIVE_Z;

        Earth earth = new Earth();
        Vec4 start = earth.computePointFromPosition(centerPos);
        Vec4 end = start.add3(speed);
        Position viewingAt =  earth.computePositionFromPoint(end);

        Angle heading = Vec4.axisAngle(upStart, speed, new Vec4[1]).addDegrees(45.0);
        Angle pitch = Vec4.axisAngle(forwardStart, speed, new Vec4[1]).subtractDegrees(45.0);
        centerPos = new Position(new LatLon(centerPos.getLatitude(), centerPos.getLongitude()), centerPos.getElevation() + 100);

        PositionAnimator centerAnimator = new PositionAnimator(
            new ScheduledInterpolator(timeToMove),
                orbitView.getEyePosition(), centerPos,
                ViewPropertyAccessor.createEyePositionAccessor(orbitView));

        AngleAnimator headingAnimator = new AngleAnimator(
                new ScheduledInterpolator(timeToMove),
                orbitView.getHeading(), heading,
                ViewPropertyAccessor.createHeadingAccessor(orbitView));

        AngleAnimator pitchAnimator = new AngleAnimator(
                new ScheduledInterpolator(timeToMove),
                orbitView.getPitch(), pitch,
                ViewPropertyAccessor.createPitchAccessor(orbitView));

        Animator[] animators = new Animator[2];
        animators[0] = centerAnimator;
        animators[1] = headingAnimator;
        //animators[2] = pitchAnimator;

        return animators;
    }

    public static FirstPersonTrackAnimator createFollowTrackViewAnimator(
        OrbitView orbitView,
        TrackPoint[] track,
        Vec4[] speed)
    {
        long timeToMove = getTimeToMove(orbitView.getEyePosition(), track[0].getPosition());
        Animator[] animators = createAnimators(orbitView, timeToMove, track[0].getPosition(), speed[0]);

        FirstPersonTrackAnimator panAnimator = new FirstPersonTrackAnimator(orbitView,
            new ScheduledInterpolator(timeToMove), animators,
            track, speed);

        return(panAnimator);
    }

    private static long getTimeToMove(Position beginCenterPos, Position endCenterPos)
    {
        final long MIN_LENGTH_MILLIS = 4000;
        final long MAX_LENGTH_MILLIS = 16000;
        return AnimationSupport.getScaledTimeMillisecs(
            beginCenterPos, endCenterPos,
            MIN_LENGTH_MILLIS, MAX_LENGTH_MILLIS);

    }

}