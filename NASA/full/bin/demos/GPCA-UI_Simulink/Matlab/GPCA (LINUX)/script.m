GOutputEvents = [0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0];
GSetConVal = '0';
GAlrmMsg = 31;
GVTBIIncrement = 5;
GDoseRateIncrement = 2;
GProgrammedDoseRate = 0;
GProgrammedVTBI = 0;
NL = 10; %Newline character
GMsgConStrArray = { 'Welcome to the Generic Patient Controlled Analgesic Infusion Pump. ' %1
                    'Are you sure you want to turn off the pump? ' %2
                    'Performing the Power On Self Test ...  '  %3
                    ['ALARM! The pump failed the Power On Self Test.' NL 'Please return the pump to the administrator.']  %4
                    'Performing the Administration Set Check ....  '  %5
                    ['ALARM! The pump failed the administration set check. This could be due to the tubing set being connected incorrectly, or an improper infusion configuration.' NL 'Please check the administration set and clear the alarm to continue. ']  %6
                    ['Administration set check succeeded.' NL 'The pump is now being primed ...']  %7
                    ['ALARM! The pump prime process failed.' NL 'Please clear the error condition and re-prime the pump. ']  %8
                    'Checking the drug in the reservoir against the drug library ... '  %9
                    ['ALARM! The drug in the reservoir does not match the patient prescription and/or the drug library. '  NL 'Please ensure that the correct drug has been loaded in the reservoir.'] %10
                    'Checking the drug dose unit against the drug library ... '  %11
                    ['ALARM! The dose unit does not correspond to the specified drug.' NL 'Please check the prescription and specify an appropriate dose unit. ' ] %12
                    'Checking the programmed drug concentration against the drug library ... '  %13
                    ['ALARM! The programmed drug concentration is outside the hard limits defined in the Drug Library.' NL 'Please change the programmed drug concentration before starting the infusion. ']  %14
                    ['WARNING! The programmed drug concentration is outside the soft limits defined in the Drug Library. It is advised that you change the programmed concentration before starting the infusion.' NL 'To continue with the current programmed value, press Continue. ']  %15
                    ' '  %16
                    ['The input VTBI does not match what the drug library recommends.' NL 'Please press Continue to select the input value, or press Change VTBI to enter another value.']  %17
                    'Please use the up and down buttons to adjust the volume of fluid to be infused. When done, press the Confirm button to set the new value. '  %18
                    'VTBI received. Checking it against the drug library ... '  %19
                    ['ALARM! The programmed VTBI is outside the hard limits defined in the Drug Library.' NL 'Please change the programmed VTBI before starting infusion. ']  %20
                    ''  %21
                    'Infusion rate received. Checking it against the drug library ... '  %22
                    'Please use the up and down buttons to adjust the value of infusion rate. When done, press the Confirm button to set the new value. '  %23
                    ['The input volume to be delivered is (VTBI) and the input infusion rate is (Infusion Rate).' NL 'Please press the Confirm button to start the infusion, or press the Cancel button to change infusion settings. ']  %24
                    ['ALARM! The programmed infusion rate is outside the hard limits defined in the Drug Library.' NL 'Please change the programmed infusion rate before starting infusion. ']  %25
                    'The programmed infusion is being delivered ... '  %26
                    ['The pump has successfully passed the Power On Self Test.' NL 'Please press the Continue button to proceed. ']  %27
                    '' %28                          
                    'The infusion has now been terminated. (Parameters of this infusion, including drug name, volume infused and infusion time, should be displayed)'  %29
                    ['Do you want stop and reprogram the paused infusion?' NL 'Press CONFIRM STOP to terminate the infusion.']  %30
                    'The current infusion has been paused.'  %31
                    'Are you sure you want to Pause the current infusion? '  %32
                    'Are you sure you want to Terminate the infusion (the pump will need to be re-programmed)? '  %33
                    '----Please press the Stop button to request stopping current infusion session, or press the Pause button to request pause the infusion. '  %34 %Not used any more
                    ['WARNING! The programmed infusion rate is outside the soft limits defined in the Drug Library.  It is advised that you change the programmed infusion rate before starting infusion.' NL 'To continue with the current programmed value, press Continue. ']  %35
                    ['WARNING! The programmed VTBI is outside the soft limits defined in the Drug Library.  It is advised that you change the programmed VTBI before starting infusion.' NL 'To continue with the current programmed value, press Continue. ']  %36                    
                    };            
                              
GAlrmMsgArray = {   'ALARM! The system battery will be depleted soon. Please connect the pump to AC power in order to continue operation.'  %1    
                    'ALARM! A serious error has been detected in the system real-time clock.  The ongoing infusion (if any) has been stopped automatically. Please power off the pump and clear the error.'  %2
                    'ALARM! A serious error has been detected in the system CPU. The ongoing infusion (if any) has been stopped automatically. Please power off the pump and clear the error.'  %3
                    'ALARM! The system memory has been corrupted. The ongoing infusion (if any) has been stopped automatically. Please power off the pump and clear the error.'  %4                    
                    'ALARM! The temperature of the pump is outside its operating range . The ongoing infusion (if any) has been stopped automatically. Please power off the pump and clear the error.' %5
                    'ALARM! The system watchdog has detected a serious problem with the delivery mechanism. The ongoing infusion (if any) has been stopped automatically. Please power off the pump and clear the error.'  %6                    
                    'ALARM! The drug reservoir door is open. The current infusion has been suspended. Please close the door and Clear the alarm to continue the infusion.'  %7
                    'ALARM! The drug reservoir is empty. The current infusion has been suspended. Please refill the drug reservoir and Clear the alarm to continue the infusion.'  %8
                    'ALARM! An Occlusion has been detected in the infusion set. The current infusion has been suspended. Please clear the occlusion and clear the alarm to continue.' %9                    
                    'ALARM! An OVERINFUSION error has been detected. The current infusion has been suspended. Please fix the error condition and clear the alarm to continue.'  %10
                    'ALARM! An UNDERINFUSION error has been detected. The current infusion has been suspended. Please fix the error condition and clear the alarm to continue.'  %11
                    'ALARM! The current infusion rate is lower than the KVO rate. The current infusion has been suspended. Please adjust the dose rate and clear the alarm to continue.'  %12
                    'ALARM! The programmed dose rate exceeds the capacity that the pump provides.  Please adjust the dose rate and clear the alarm to continue.'  %13
                    'ALARM! The current infusion session has been suspended too long.  Indicate whether you want to resume or stop infusion.'  %14
                    'ALARM! An air bubble has been detected in the infusion set. The current infusion has been suspended. Please ensure that there are no air bubbles in the tubing and Clear the alarm to continue.'  %15
                    'Warning! The infusion rate has a variance that exceeds safe limits. '  %16
                    'WARNING! The volume remaining in the drug reservoir is lower than a safe range. Please re-load the drug in the reservoir.'  %17                    
                    'WARNING! The current infusion session has been paused longer than expected.  Indicate whether you want to resume or stop infusion.'   %18                    
                    'WARNING! An error is detected when the system log tries to record an event.'  %19 
                    'WARNING! The power level of the system battery is detected as lower than a safe range. Please check the connectivity with the AC power or replace the battery.'  %20
                    'WARNING! The battery cannot be charged. Please check and replace the battery system in time.' %21             
                    'ALARM! The current power voltage is outside the safe range. The infusion has been forced to terminate. Please clear the condition before resume the infusion or start another infusion.' %22                    
                    'WARNING! The pump is overheated. Please check the pump and take remedial action.'  %23                    
                    'WARNING! The ambient temperature is outside the preset safe range, which might affect the pump�s functioning.'  %24
                    'WARNING! The ambient humidity is outside the preset safe range, which might affect the pump�s functioning.'  %25
                    'WARNING! The ambient air pressure is outside the preset safe range, which might affect the pump�s functioning.'  %26
                    'Problems found in the pump have prevented the infusion from being started. Please Check whether the reservoir is empty, or the reservoir door is open, and then start the infusion again.' %27
                    'Bolus request is granted. The bolus dose is being currently administered.'  %28
                    'Bolus request is denied because another bolus dose is currently being administered,or the request violates the drug library restriction.'  %29
                    'The bolus dose has been delivered. Infusion is now being done at programmed dose rate.'  %30
                    '   ' %31
                    'Reserved for infusion status display' %32
                    };
