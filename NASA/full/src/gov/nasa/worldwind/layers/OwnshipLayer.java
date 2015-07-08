/*
 * Copyright (C) 2012 United States Government as represented by the Administrator of the
 * National Aeronautics and Space Administration.
 * All Rights Reserved.
 */
package gov.nasa.worldwind.layers;

import com.jogamp.opengl.util.texture.*;
import gov.nasa.worldwind.View;
import gov.nasa.worldwind.avlist.AVKey;
import gov.nasa.worldwind.exception.WWRuntimeException;
import gov.nasa.worldwind.geom.*;
import gov.nasa.worldwind.pick.*;
import gov.nasa.worldwind.render.*;
import gov.nasa.worldwind.util.*;
import gov.nasa.worldwind.view.orbit.OrbitView;

import javax.media.opengl.*;
import java.awt.*;
import java.io.*;

/**
 * @author tag
 * @version $Id: OwnshipLayer.java 1953 2014-04-21 15:43:35Z tgaskins $
 */
public class OwnshipLayer extends AbstractLayer
{
    protected String iconFilePath = "images/ownship.png"; // TODO: make configurable
    protected double ownshipToViewportScale = 0.05; // TODO: make configurable
    protected double iconScale = 0.5;
    protected int borderWidth = 20; // TODO: make configurable
    protected String position = AVKey.NORTHWEST; // TODO: make configurable
    protected String resizeBehavior = AVKey.RESIZE_SHRINK_ONLY2;
    protected int iconWidth;
    protected int iconHeight;
    protected Vec4 locationCenter = null;
    protected Vec4 locationOffset = null;
    protected boolean showTilt = true;
    protected PickSupport pickSupport = new PickSupport();

    // Draw it as ordered with an eye distance of 0 so that it shows up in front of most other things.
    protected OrderedIcon orderedImage = new OrderedIcon();

    protected class OrderedIcon implements OrderedRenderable
    {
        public double getDistanceFromEye()
        {
            return 0;
        }

        public void pick(DrawContext dc, Point pickPoint)
        {
            OwnshipLayer.this.draw(dc);
        }

        public void render(DrawContext dc)
        {
            OwnshipLayer.this.draw(dc);
        }
    }

    public OwnshipLayer()
    {
        this.setOpacity(0.8); // TODO: make configurable
        this.setPickEnabled(false);  // Default to no picking
    }

    public OwnshipLayer(String iconFilePath)
    {
        this.setIconFilePath(iconFilePath);
        this.setOpacity(0.8); // TODO: make configurable
        this.setPickEnabled(false); // Default to no picking
    }

    /**
     * Returns the layer's current icon file path.
     *
     * @return the icon file path
     */
    public String getIconFilePath()
    {
        return iconFilePath;
    }

    /**
     * Sets the ownship icon's image location. The layer first searches for this location in the current Java classpath.
     * If not found then the specified path is assumed to refer to the local file system. found there then the
     *
     * @param iconFilePath the path to the icon's image file
     */
    public void setIconFilePath(String iconFilePath)
    {
        if (iconFilePath == null)
        {
            String message = Logging.getMessage("nullValue.IconFilePath");
            Logging.logger().severe(message);
            throw new IllegalArgumentException(message);
        }
        this.iconFilePath = iconFilePath;
    }

    /**
     * Returns the layer's ownship-to-viewport scale factor.
     *
     * @return the ownship-to-viewport scale factor
     */
    public double getOwnshipToViewportScale()
    {
        return ownshipToViewportScale;
    }

    /**
     * Sets the scale factor applied to the viewport size to determine the displayed size of the ownship icon. This
     * scale factor is used only when the layer's resize behavior is AVKey.RESIZE_STRETCH2 or AVKey.RESIZE_SHRINK_ONLY2.
     * The icon's width is adjusted to occupy the proportion of the viewport's width indicated by this factor. The
     * icon's height is adjusted to maintain the ownship image's native aspect ratio.
     *
     * @param ownshipToViewportScale the ownship to viewport scale factor
     */
    public void setOwnshipToViewportScale(double ownshipToViewportScale)
    {
        this.ownshipToViewportScale = ownshipToViewportScale;
    }

    /**
     * Returns the icon scale factor. See {@link #setIconScale(double)} for a description of the scale factor.
     *
     * @return the current icon scale
     */
    public double getIconScale()
    {
        return iconScale;
    }

    /**
     * Sets the scale factor defining the displayed size of the ownship icon relative to the icon's width and height in
     * its image file. Values greater than 1 magify the image, values less than one minify it. If the layer's resize
     * behavior is other than AVKey.RESIZE_KEEP_FIXED_SIZE2, the icon's displayed sized is further affected by the value
     * specified by {@link #setOwnshipToViewportScale(double)} and the current viewport size.
     * <p/>
     * The default icon scale is 0.5.
     *
     * @param iconScale the icon scale factor
     */
    public void setIconScale(double iconScale)
    {
        this.iconScale = iconScale;
    }

    /**
     * Returns the ownship icon's resize behavior.
     *
     * @return the icon's resize behavior
     */
    public String getResizeBehavior()
    {
        return resizeBehavior;
    }

    /**
     * Sets the behavior the layer uses to size the ownship icon when the viewport size changes, typically when the
     * World Wind window is resized. If the value is AVKey.RESIZE_KEEP_FIXED_SIZE2, the icon size is kept to the size
     * specified in its image file scaled by the layer's current icon scale. If the value is AVKey.RESIZE_STRETCH2, the
     * icon is resized to have a constant size relative to the current viewport size. If the viewport shrinks the icon
     * size decreases; if it expands then the icon file enlarges. The relative size is determined by the current
     * ownship-to-viewport scale and by the icon's image file size scaled by the current icon scale. If the value is
     * AVKey.RESIZE_SHRINK_ONLY2 (the default), icon sizing behaves as for AVKey.RESIZE_STRETCH2 but the icon will not
     * grow larger than the size specified in its image file scaled by the current icon scale.
     *
     * @param resizeBehavior the desired resize behavior
     */
    public void setResizeBehavior(String resizeBehavior)
    {
        this.resizeBehavior = resizeBehavior;
    }

    public int getBorderWidth()
    {
        return borderWidth;
    }

    /**
     * Sets the ownship icon offset from the viewport border.
     *
     * @param borderWidth the number of pixels to offset the ownship icon from the borders indicated by {@link
     *                    #setPosition(String)}.
     */
    public void setBorderWidth(int borderWidth)
    {
        this.borderWidth = borderWidth;
    }

    /**
     * Returns the current relative ownship icon position.
     *
     * @return the current ownship position
     */
    public String getPosition()
    {
        return position;
    }

    /**
     * Sets the relative viewport location to display the ownship icon. Can be one of AVKey.NORTHEAST (the default),
     * AVKey.NORTHWEST, AVKey.SOUTHEAST, or AVKey.SOUTHWEST. These indicate the corner of the viewport to place the
     * icon.
     *
     * @param position the desired ownship position
     */
    public void setPosition(String position)
    {
        if (position == null)
        {
            String message = Logging.getMessage("nullValue.OwnshipPositionIsNull");
            Logging.logger().severe(message);
            throw new IllegalArgumentException(message);
        }
        this.position = position;
    }

    /**
     * Returns the current ownship image location.
     *
     * @return the current location center. May be null.
     */
    public Vec4 getLocationCenter()
    {
        return locationCenter;
    }

    /**
     * Specifies the screen location of the ownship image, relative to the image's center. May be null. If this value is
     * non-null, it overrides the position specified by {@link #setPosition(String)}. The location is specified in
     * pixels. The origin is the window's lower left corner. Positive X values are to the right of the origin, positive
     * Y values are upwards from the origin. The final image location will be affected by the currently specified
     * location offset if a non-null location offset has been specified (see {@link
     * #setLocationOffset(gov.nasa.worldwind.geom.Vec4)}).
     *
     * @param locationCenter the location center. May be null.
     *
     * @see #setPosition(String)
     * @see #setLocationOffset(gov.nasa.worldwind.geom.Vec4)
     */
    public void setLocationCenter(Vec4 locationCenter)
    {
        this.locationCenter = locationCenter;
    }

    /**
     * Returns the current location offset. See #setLocationOffset for a description of the offset and its values.
     *
     * @return the location offset. Will be null if no offset has been specified.
     */
    public Vec4 getLocationOffset()
    {
        return locationOffset;
    }

    /**
     * Specifies a placement offset from the ownship' position on the screen.
     *
     * @param locationOffset the number of pixels to shift the ownship image from its specified screen position. A
     *                       positive X value shifts the image to the right. A positive Y value shifts the image up. If
     *                       null, no offset is applied. The default offset is null.
     *
     * @see #setLocationCenter(gov.nasa.worldwind.geom.Vec4)
     * @see #setPosition(String)
     */
    public void setLocationOffset(Vec4 locationOffset)
    {
        this.locationOffset = locationOffset;
    }

    protected void doRender(DrawContext dc)
    {
        dc.addOrderedRenderable(this.orderedImage);
    }

    protected void doPick(DrawContext dc, Point pickPoint)
    {
        dc.addOrderedRenderable(this.orderedImage);
    }

    public boolean isShowTilt()
    {
        return showTilt;
    }

    public void setShowTilt(boolean showTilt)
    {
        this.showTilt = showTilt;
    }

    protected void draw(DrawContext dc)
    {
        if (this.getIconFilePath() == null)
            return;

        GL2 gl = dc.getGL().getGL2(); // GL initialization checks for GL2 compatibility.
        OGLStackHandler ogsh = new OGLStackHandler();

        try
        {
            gl.glDisable(GL.GL_DEPTH_TEST);

            Texture iconTexture = dc.getTextureCache().getTexture(this.getIconFilePath());
            if (iconTexture == null)
            {
                this.initializeTexture(dc);
                iconTexture = dc.getTextureCache().getTexture(this.getIconFilePath());
                if (iconTexture == null)
                {
                    String msg = Logging.getMessage("generic.ImageReadFailed");
                    Logging.logger().finer(msg);
                    return;
                }
            }

            // Need to assign the width and height here to address the case in which the texture was already
            // loaded into the cache by another layer or a previous instance of this one.
            this.iconWidth = iconTexture.getWidth();
            this.iconHeight = iconTexture.getHeight();

            double width = this.getScaledIconWidth();
            double height = this.getScaledIconHeight();

            // Load a parallel projection with xy dimensions (viewportWidth, viewportHeight)
            // into the GL projection matrix.
            java.awt.Rectangle viewport = dc.getView().getViewport();
            ogsh.pushProjectionIdentity(gl);
            double maxwh = width > height ? width : height;
            if (maxwh == 0)
                maxwh = 1;
            gl.glOrtho(0d, viewport.width, 0d, viewport.height, -0.6 * maxwh, 0.6 * maxwh);

            ogsh.pushModelviewIdentity(gl);
            double scale = this.computeScale(viewport);
            Vec4 locationSW = this.computeLocation(viewport, scale);
            double heading = this.computeHeading(dc.getView());
            double pitch = this.computePitch(dc.getView());

            gl.glTranslated(locationSW.x, locationSW.y, locationSW.z);
            gl.glScaled(scale, scale, 1);

            if (!dc.isPickingMode())
            {
                gl.glTranslated(width / 2, height / 2, 0);
                if (this.showTilt) // formula contributed by Ty Hayden
//                    gl.glRotated(70d * (pitch / 90.0), 1d, 0d, 0d);
//                gl.glRotated(heading, 0d, 0d, 1d); // get icon rotate
                gl.glTranslated(-width / 2, -height / 2, 0);

                gl.glEnable(GL.GL_TEXTURE_2D);
                iconTexture.bind(gl);

                gl.glColor4d(1d, 1d, 1d, this.getOpacity());
                gl.glEnable(GL.GL_BLEND);
                gl.glBlendFunc(GL.GL_SRC_ALPHA, GL.GL_ONE_MINUS_SRC_ALPHA);
                TextureCoords texCoords = iconTexture.getImageTexCoords();
                gl.glScaled(width, height, 1d);
                dc.drawUnitQuad(texCoords);
            }
            else
            {
                // Picking
                this.pickSupport.clearPickList();
                this.pickSupport.beginPicking(dc);
                try
                {
                    // Add a picked object for the ownship to the list of pickable objects.
                    Color color = dc.getUniquePickColor();
                    PickedObject po = new PickedObject(color.getRGB(), this, null, false);
                    this.pickSupport.addPickableObject(po);

                    if (dc.getPickPoint() != null)
                    {
                        // If the pick point is not null, compute the pick point 'heading' relative to the ownship
                        // center and set the picked heading on our picked object. The pick point is null if a pick
                        // rectangle is specified but a pick point is not.
                        Vec4 center = new Vec4(locationSW.x + width * scale / 2, locationSW.y + height * scale / 2,
                            0);
                        double px = dc.getPickPoint().x - center.x;
                        double py = viewport.getHeight() - dc.getPickPoint().y - center.y;
                        Angle pickHeading = Angle.fromRadians(Math.atan2(px, py));
                        pickHeading = pickHeading.degrees >= 0 ? pickHeading : pickHeading.addDegrees(360);
                        po.setValue("Heading", pickHeading);
                    }

                    // Draw the ownship in the unique pick color.
                    gl.glColor3ub((byte) color.getRed(), (byte) color.getGreen(), (byte) color.getBlue());
                    gl.glScaled(width, height, 1d);
                    dc.drawUnitQuad();
                }
                finally
                {
                    // Done picking
                    this.pickSupport.endPicking(dc);
                    this.pickSupport.resolvePick(dc, dc.getPickPoint(), this);
                }
            }
        }
        finally
        {
            dc.restoreDefaultDepthTesting();
            dc.restoreDefaultCurrentColor();

            if (!dc.isPickingMode())
            {
                gl.glBindTexture(GL.GL_TEXTURE_2D, 0);
                gl.glDisable(GL.GL_TEXTURE_2D); // restore to default texture state
                dc.restoreDefaultBlending();
            }

            ogsh.pop(gl);
        }
        
        
        
        
        
        
        
//        System.out.print(this.getIconFilePath());
//        
//        
//        
//        
//        
//        GL2 gl2 = dc.getGL().getGL2(); // GL initialization checks for GL2 compatibility.
//        OGLStackHandler ogsh2 = new OGLStackHandler();
//
//        try
//        {
//            gl2.glDisable(GL.GL_DEPTH_TEST);
//
//            Texture iconTexture = dc.getTextureCache().getTexture(this.getIconFilePath());
//            if (iconTexture == null)
//            {
//                this.initializeTexture(dc);
//                iconTexture = dc.getTextureCache().getTexture(this.getIconFilePath());
//                if (iconTexture == null)
//                {
//                    String msg = Logging.getMessage("generic.ImageReadFailed");
//                    Logging.logger().finer(msg);
//                    return;
//                }
//            }
//
//            // Need to assign the width and height here to address the case in which the texture was already
//            // loaded into the cache by another layer or a previous instance of this one.
//            this.iconWidth = iconTexture.getWidth();
//            this.iconHeight = iconTexture.getHeight();
//
//            double width = this.getScaledIconWidth();
//            double height = this.getScaledIconHeight();
//
//            // Load a parallel projection with xy dimensions (viewportWidth, viewportHeight)
//            // into the GL projection matrix.
//            java.awt.Rectangle viewport = dc.getView().getViewport();
//            ogsh2.pushProjectionIdentity(gl2);
//            double maxwh = width > height ? width : height;
//            if (maxwh == 0)
//                maxwh = 1;
//            gl2.glOrtho(0d, viewport.width, 0d, viewport.height, -0.6 * maxwh, 0.6 * maxwh);
//
//            ogsh2.pushModelviewIdentity(gl2);
//            double scale = this.computeScale(viewport);
//            Vec4 locationSW = this.computeLocation(viewport, scale);
//            double heading = this.computeHeading(dc.getView());
//            double pitch = this.computePitch(dc.getView());
//
//            gl2.glTranslated(locationSW.x, locationSW.y, locationSW.z);
//            gl2.glScaled(scale, scale, 1);
//
//            if (!dc.isPickingMode())
//            {
//                gl2.glTranslated(width / 2, height / 2, 0);
//                if (this.showTilt) // formula contributed by Ty Hayden
//                gl2.glTranslated(-width / 2, -height / 2, 0);
//
//                gl2.glEnable(GL.GL_TEXTURE_2D);
//                iconTexture.bind(gl2);
//
//                gl2.glColor4d(1d, 1d, 1d, this.getOpacity());
//                gl2.glEnable(GL.GL_BLEND);
//                gl2.glBlendFunc(GL.GL_SRC_ALPHA, GL.GL_ONE_MINUS_SRC_ALPHA);
//                TextureCoords texCoords = iconTexture.getImageTexCoords();
//                gl2.glScaled(width, height, 1d);
//                dc.drawUnitQuad(texCoords);
//            }
//            else
//            {
//                // Picking
//                this.pickSupport.clearPickList();
//                this.pickSupport.beginPicking(dc);
//                try
//                {
//                    // Add a picked object for the ownship to the list of pickable objects.
//                    Color color = dc.getUniquePickColor();
//                    PickedObject po = new PickedObject(color.getRGB(), this, null, false);
//                    this.pickSupport.addPickableObject(po);
//
//                    if (dc.getPickPoint() != null)
//                    {
//                        // If the pick point is not null, compute the pick point 'heading' relative to the ownship
//                        // center and set the picked heading on our picked object. The pick point is null if a pick
//                        // rectangle is specified but a pick point is not.
//                        Vec4 center = new Vec4(locationSW.x + width * scale / 2, locationSW.y + height * scale / 2,
//                            0);
//                        double px = dc.getPickPoint().x - center.x;
//                        double py = viewport.getHeight() - dc.getPickPoint().y - center.y;
//                        Angle pickHeading = Angle.fromRadians(Math.atan2(px, py));
//                        pickHeading = pickHeading.degrees >= 0 ? pickHeading : pickHeading.addDegrees(360);
//                        po.setValue("Heading", pickHeading);
//                    }
//
//                    // Draw the ownship in the unique pick color.
//                    gl2.glColor3ub((byte) color.getRed(), (byte) color.getGreen(), (byte) color.getBlue());
//                    gl2.glScaled(width, height, 1d);
//                    dc.drawUnitQuad();
//                }
//                finally
//                {
//                    // Done picking
//                    this.pickSupport.endPicking(dc);
//                    this.pickSupport.resolvePick(dc, dc.getPickPoint(), this);
//                }
//            }
//        }
//        finally
//        {
//            dc.restoreDefaultDepthTesting();
//            dc.restoreDefaultCurrentColor();
//
//            if (!dc.isPickingMode())
//            {
//                gl2.glBindTexture(GL.GL_TEXTURE_2D, 0);
//                gl2.glDisable(GL.GL_TEXTURE_2D); // restore to default texture state
//                dc.restoreDefaultBlending();
//            }
//
//            ogsh2.pop(gl2);
//        }
//        
        
        
        
        
        
        
        
        
    }

    protected double computeScale(java.awt.Rectangle viewport)
    {
        if (this.resizeBehavior.equals(AVKey.RESIZE_SHRINK_ONLY2))
        {
            return Math.min(1d, (this.ownshipToViewportScale) * viewport.width / this.getScaledIconWidth());
        }
        else if (this.resizeBehavior.equals(AVKey.RESIZE_STRETCH2))
        {
            return (this.ownshipToViewportScale) * viewport.width / this.getScaledIconWidth();
        }
        else if (this.resizeBehavior.equals(AVKey.RESIZE_KEEP_FIXED_SIZE2))
        {
            return 1d;
        }
        else
        {
            return 1d;
        }
    }

    protected double getScaledIconWidth()
    {
        return this.iconWidth * this.iconScale;
    }

    protected double getScaledIconHeight()
    {
        return this.iconHeight * this.iconScale;
    }

    protected Vec4 computeLocation(java.awt.Rectangle viewport, double scale)
    {
        double width = this.getScaledIconWidth();
        double height = this.getScaledIconHeight();

        double scaledWidth = scale * width;
        double scaledHeight = scale * height;

        double x;
        double y;

        if (this.locationCenter != null)
        {
            x = this.locationCenter.x - scaledWidth / 2;
            y = this.locationCenter.y - scaledHeight / 2;
        }
//        else if (this.position.equals(AVKey.CENTER))
//        {
//        			x = viewport.getWidth()/2 - scaledWidth/2 ;
//        			y = viewport.getHeight()/2 - scaledHeight/2 ;
//        }
//        
        else if (this.position.equals(AVKey.NORTHEAST))
        {
            x = viewport.getWidth() - scaledWidth - this.borderWidth;
            y = viewport.getHeight() - scaledHeight - this.borderWidth;
        }
        else if (this.position.equals(AVKey.SOUTHEAST))
        {
            x = viewport.getWidth() - scaledWidth - this.borderWidth;
            y = 0d + this.borderWidth;
        }
        else if (this.position.equals(AVKey.NORTHWEST))
        {
//            x = 0d + this.borderWidth;
//            y = viewport.getHeight() - scaledHeight - this.borderWidth;
				        	x = viewport.getWidth()/2 - scaledWidth/2 ;
				    			y = viewport.getHeight()/2 - scaledHeight/2 ;
        }
        else if (this.position.equals(AVKey.SOUTHWEST))
        {
            x = 0d + this.borderWidth;
            y = 0d + this.borderWidth;
        }
        else // use North East as default
        {
            x = viewport.getWidth() - scaledWidth - this.borderWidth;
            y = viewport.getHeight() - scaledHeight - this.borderWidth;
        }

        if (this.locationOffset != null)
        {
            x += this.locationOffset.x;
            y += this.locationOffset.y;
        }

        return new Vec4(x, y, 0);
    }

    protected double computeHeading(View view)
    {
        if (view == null)
            return 0.0;

        return view.getHeading().getDegrees();
    }

    protected double computePitch(View view)
    {
        if (view == null)
            return 0.0;

        if (!(view instanceof OrbitView))
            return 0.0;

        OrbitView orbitView = (OrbitView) view;
        return orbitView.getPitch().getDegrees();
    }

    protected void initializeTexture(DrawContext dc)
    {
        Texture iconTexture = dc.getTextureCache().getTexture(this.getIconFilePath());
        if (iconTexture != null)
            return;

        GL gl = dc.getGL();

        try
        {
            InputStream iconStream = this.getClass().getResourceAsStream("/" + this.getIconFilePath());
            if (iconStream == null)
            {
                File iconFile = new File(this.iconFilePath);
                if (iconFile.exists())
                {
                    iconStream = new FileInputStream(iconFile);
                }
            }

            TextureData textureData = OGLUtil.newTextureData(gl.getGLProfile(), iconStream, false);
            iconTexture = TextureIO.newTexture(textureData);
            iconTexture.bind(gl);
            this.iconWidth = iconTexture.getWidth();
            this.iconHeight = iconTexture.getHeight();
            dc.getTextureCache().put(this.getIconFilePath(), iconTexture);
        }
        catch (IOException e)
        {
            String msg = Logging.getMessage("layers.IOExceptionDuringInitialization");
            Logging.logger().severe(msg);
            throw new WWRuntimeException(msg, e);
        }

        gl.glTexParameteri(GL.GL_TEXTURE_2D, GL.GL_TEXTURE_MIN_FILTER, GL.GL_LINEAR);//_MIPMAP_LINEAR);
        gl.glTexParameteri(GL.GL_TEXTURE_2D, GL.GL_TEXTURE_MAG_FILTER, GL.GL_LINEAR);
        gl.glTexParameteri(GL.GL_TEXTURE_2D, GL.GL_TEXTURE_WRAP_S, GL.GL_CLAMP_TO_EDGE);
        gl.glTexParameteri(GL.GL_TEXTURE_2D, GL.GL_TEXTURE_WRAP_T, GL.GL_CLAMP_TO_EDGE);
        // Enable texture anisotropy, improves "tilted" ownship quality.
        int[] maxAnisotropy = new int[1];
        gl.glGetIntegerv(GL.GL_MAX_TEXTURE_MAX_ANISOTROPY_EXT, maxAnisotropy, 0);
        gl.glTexParameteri(GL.GL_TEXTURE_2D, GL.GL_TEXTURE_MAX_ANISOTROPY_EXT, maxAnisotropy[0]);
    }

    @Override
    public String toString()
    {
        return Logging.getMessage("layers.OwnshipLayer.Name");
    }
}
