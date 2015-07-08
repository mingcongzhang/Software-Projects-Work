/*
 * SatPropertyPanel.java
 * =====================================================================
 *   This file is part of JSatTrak.
 *
 *   Copyright 2007-2013 Shawn E. Gano
 *   
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *   
 *       http://www.apache.org/licenses/LICENSE-2.0
 *   
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 * =====================================================================
 *
 * Created on August 3, 2007, 11:29 PM
 *
 * // 13 June 2009 -- change name in table from Longitude of the Asc Node [deg] to RAAN (checked out in STK)
 */

package jsattrak.gui;

import javax.swing.table.DefaultTableModel;
import jsattrak.objects.AbstractSatellite;
import name.gano.astro.AstroConst;
import name.gano.astro.coordinates.CoordinateConversion;

/**
 *
 * @author  sgano
 */
public class SatPropertyPanel extends javax.swing.JPanel implements java.io.Serializable
{
    
    // satellite prop object used to derive all the properties
    AbstractSatellite satProp;
    
    // table model
    DefaultTableModel propTableModel;
    
    /** Creates new form SatPropertyPanel -- bean */
    public SatPropertyPanel()
    {
        initComponents();
        
//        // will not work correctly
//        satProp = new SatelliteTleSGP4("Test","1","2");
//
//        // get table model
//        propTableModel = (DefaultTableModel)satPropertyTable.getModel();
        
    }
    
    // normal constructor - pass in a SatelliteProps object
    public SatPropertyPanel(AbstractSatellite satProp)
    {
        initComponents();
        this.satProp = satProp;
        
        // get table model
        propTableModel = (DefaultTableModel)satPropertyTable.getModel(); 
        
        // set renderer
         // set cell renderer
        try
        {
            
            // everything is a string in this table
            satPropertyTable.setDefaultRenderer(Class.forName( "java.lang.String" ),new BoldFontCellRenderer());
            
//        TableColumnModel tcm = satPropertyTable.getColumnModel();
//        TableColumn tc = tcm.getColumn(1);
//        tc.setCellRenderer( new BoldFontCellRenderer() );
//
//        System.out.println("here!");
            
        }
        catch(Exception e)
        {
            e.printStackTrace();
        }
        
        
    }
    
    /** This method is called from within the constructor to
     * initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is
     * always regenerated by the Form Editor.
     */
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        jScrollPane1 = new javax.swing.JScrollPane();
        satPropertyTable = new javax.swing.JTable();
        jLabel1 = new javax.swing.JLabel();
        cartComboBox = new javax.swing.JComboBox();

        satPropertyTable.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {
                {"TLE Age [days]", null},
                {"Latitude [deg]", null},
                {"Longitude [deg]", null},
                {"Altitude [m]", null},
                {"Position: J2000", null},
                {"x [m]", null},
                {"y [m]", null},
                {"z [m]", null},
                {"Velocity: J2000", null},
                {"dx/dt [m/s]", null},
                {"dy/dt [m/s]", null},
                {"dz/dt [m/s]", null},
                {"Keplarian Elem. (Osc / J2000)", null},
                {"Semimajor axis (a) [m]", null},
                {"Eccentricity (e) ", null},
                {"Inclination (i) [deg]", null},
                {"RAAN [deg]", null},
                {"Argument of pericenter [deg]", null},
                {"Mean anomaly [M] [deg]", null}
            },
            new String [] {
                "Property", "Value"
            }
        ) {
            Class[] types = new Class [] {
                java.lang.String.class, java.lang.String.class
            };
            boolean[] canEdit = new boolean [] {
                false, true
            };

            public Class getColumnClass(int columnIndex) {
                return types [columnIndex];
            }

            public boolean isCellEditable(int rowIndex, int columnIndex) {
                return canEdit [columnIndex];
            }
        });
        jScrollPane1.setViewportView(satPropertyTable);

        jLabel1.setText("Cartesian Coord. Sys:");

        cartComboBox.setModel(new javax.swing.DefaultComboBoxModel(new String[] { "J2000.0", "True Equator Mean Equinox (TEME) of Date", "Mean of Date (MOD)", "True of Date (TOD)" }));
        cartComboBox.addItemListener(new java.awt.event.ItemListener() {
            public void itemStateChanged(java.awt.event.ItemEvent evt) {
                cartComboBoxItemStateChanged(evt);
            }
        });

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(this);
        this.setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addComponent(jLabel1)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(cartComboBox, 0, 136, Short.MAX_VALUE)
                .addContainerGap())
            .addComponent(jScrollPane1, javax.swing.GroupLayout.DEFAULT_SIZE, 256, Short.MAX_VALUE)
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                .addComponent(jScrollPane1, javax.swing.GroupLayout.DEFAULT_SIZE, 347, Short.MAX_VALUE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabel1)
                    .addComponent(cartComboBox, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)))
        );
    }// </editor-fold>//GEN-END:initComponents

    private void cartComboBoxItemStateChanged(java.awt.event.ItemEvent evt)//GEN-FIRST:event_cartComboBoxItemStateChanged
    {//GEN-HEADEREND:event_cartComboBoxItemStateChanged
        updateProperties();
        // set titles in table correctly
        switch(cartComboBox.getSelectedIndex())
        {
            case 0: // J2000.0
                propTableModel.setValueAt("Position: J2000", 4, 0); // , row, col
                propTableModel.setValueAt("Velocity: J2000", 8, 0); // , row, col
                break;
            case 1: // TEME
                propTableModel.setValueAt("Position: TEME", 4, 0); // , row, col
                propTableModel.setValueAt("Velocity: TEME", 8, 0); // , row, col
                break;
            case 2: // MOD
                propTableModel.setValueAt("Position: MOD", 4, 0); // , row, col
                propTableModel.setValueAt("Velocity: MOD", 8, 0); // , row, col
                break;
            case 3: // TOD
                propTableModel.setValueAt("Position: TOD", 4, 0); // , row, col
                propTableModel.setValueAt("Velocity: TOD", 8, 0); // , row, col
                break;
            default: // J2000.0
                propTableModel.setValueAt("Position: J2000", 4, 0); // , row, col
                propTableModel.setValueAt("Velocity: J2000", 8, 0); // , row, col
                break;
        }
}//GEN-LAST:event_cartComboBoxItemStateChanged
    
    
    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JComboBox cartComboBox;
    private javax.swing.JLabel jLabel1;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JTable satPropertyTable;
    // End of variables declaration//GEN-END:variables
    
    // function used to update all the properties in the table
    public void updateProperties()
    {
        // get needed properties that are arrays
        double[] pos = new double[3], vel = new double[3];
        getSelectedPosVel(pos, vel); // satProp.getJ2000Position();
                
        // set new values in the table .setValueAt(object, row, column)
        propTableModel.setValueAt(""+satProp.getTleAgeDays(),0,1);  // TLE AGE
        
        propTableModel.setValueAt(""+satProp.getLatitude()*180.0/Math.PI,1,1); // lat
        propTableModel.setValueAt(""+satProp.getLongitude()*180.0/Math.PI,2,1); // long
        propTableModel.setValueAt(""+satProp.getAltitude(),3,1); // alt
        
        propTableModel.setValueAt(""+pos[0],5,1); // x
        propTableModel.setValueAt(""+pos[1],6,1); // y
        propTableModel.setValueAt(""+pos[2],7,1); // z
        
        propTableModel.setValueAt(""+vel[0],9,1); // dx
        propTableModel.setValueAt(""+vel[1],10,1); // dy
        propTableModel.setValueAt(""+vel[2],11,1); // dz
        
        // try for keplarian elements -- might be singular
        try
        {
            double[] kep = satProp.getKeplarianElements();
                    
            propTableModel.setValueAt(""+kep[0],13,1);
            propTableModel.setValueAt(""+kep[1],14,1);
            propTableModel.setValueAt(""+kep[2]*180.0/Math.PI,15,1);
            propTableModel.setValueAt(""+kep[3]*180.0/Math.PI,16,1);
            propTableModel.setValueAt(""+kep[4]*180.0/Math.PI,17,1);
            propTableModel.setValueAt(""+kep[5]*180.0/Math.PI,18,1);
            
        }
        catch(Exception e)
        {
            propTableModel.setValueAt("-",13,1);
            propTableModel.setValueAt("-",14,1);
            propTableModel.setValueAt("-",15,1);
            propTableModel.setValueAt("-",16,1);
            propTableModel.setValueAt("-",17,1);
            propTableModel.setValueAt("-",18,1);
        } // Keplarian Elements
        
        
    } // updateProperties

    private void getSelectedPosVel(double[] pos, double[] vel)
    {
        switch(cartComboBox.getSelectedIndex())
        {
            case 0: // J2000.0
                copyArray(satProp.getJ2000Position(), pos);
                copyArray(satProp.getJ2000Velocity(), vel);
                break;
            case 1: // TEME
                copyArray(satProp.getTEMEPos(), pos);
                copyArray(satProp.getTEMEVelocity(), vel);
                break;
            case 2: // MOD
                // get J2k first
                double[] j2kpos = satProp.getJ2000Position();
                double[] j2kvel = satProp.getJ2000Velocity();
                double[] temp1 = new double[3];
                double[] temp2 = new double[3];
                // find transform
                CoordinateConversion.j2000toMOD(satProp.getCurrentJulDate() - AstroConst.JDminusMJD,j2kpos,temp1,j2kvel,temp2);
                // copy results back
                copyArray(temp1, pos);
                copyArray(temp2, vel);
                break;
            case 3: // TOD
                // get J2k first
                j2kpos = satProp.getJ2000Position();
                j2kvel = satProp.getJ2000Velocity();
                temp1 = new double[3];
                temp2 = new double[3];
                // find transform
                CoordinateConversion.j2000toTOD(satProp.getCurrentJulDate() - AstroConst.JDminusMJD,j2kpos,temp1,j2kvel,temp2);
                // copy results back
                copyArray(temp1, pos);
                copyArray(temp2, vel);
                break;
            default: // J2000.0
                copyArray(satProp.getJ2000Position(), pos);
                copyArray(satProp.getJ2000Velocity(), vel);
                break;
        }
    } // getSelectedPosVel

    // copies a1 -> a2
    private void copyArray(double[] a1, double[] a2)
    {
        for(int i=0; i<a1.length; i++)
        {
            a2[i] = a1[i];
        }
    } // copyArray
    
    public String getSatName()
    {
        // return sat name for the sat Properties
        return satProp.getName();
    }
    
    
} // SatPropertyPanel
