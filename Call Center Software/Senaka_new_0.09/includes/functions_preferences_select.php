<?php

//  Timezone suggestions from PHP CookBook By Adam Trachtenberg
//  and David Sklar Table 3-5 zoneinfo zones

$timezone_array2 = array(
	'America/Halifax'      => 'Atlantic with Daylight Savings Time (ADT)',
	'America/Puerto_Rico'  => 'Atlantic Standard Time (AST)',
	'EST5EDT'              => 'Eastern with Daylight Savings Time (EDT)',
	'EST'                  => 'Eastern Standard Time (EST)',
	'CST6CDT'              => 'Central with Daylight Savings Time (CDT)',
	'Canada/Saskatchewan'  => 'Central Standard Time (CST)',
	'MST7MDT'              => 'Mountain with Daylight Savings Time (MDT)',
	'MST'                  => 'Mountain Standard Time (MST)',
	'PST8PDT'              => 'Pacific with Daylight Savings Time (PDT)',
	'America/Dawson_Creek' => 'Pacific Standard Time (PST)',
	'America/Anchorage'    => 'Alaska with Daylight Savings Time (AKDT)',
	'Etc/GMT+9'            => 'Alaska Standard Time (AKST)',
	'US/Aleutian'          => 'Hawaii-Aleutian with Daylight Savings Time (HADT)',
	'Pacific/Honolulu'     => 'Hawaii-Aleutian Standard Time (HAST)'
);

/*
function generate_timezone_list()
{
	static $regions = array(
		DateTimeZone::AFRICA,
		DateTimeZone::AMERICA,
		DateTimeZone::ANTARCTICA,
		DateTimeZone::ASIA,
		DateTimeZone::ATLANTIC,
		DateTimeZone::AUSTRALIA,
		DateTimeZone::EUROPE,
		DateTimeZone::INDIAN,
		DateTimeZone::PACIFIC,
	);

    $timezones = array();
    foreach( $regions as $region )
    {
        $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
    }

    $timezone_offsets = array();
    foreach( $timezones as $timezone )
    {
// senaka temp delete later
date_default_timezone_set('America/New_York');
        $tz = new DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
    }

    // sort timezone by offset
    asort($timezone_offsets);

    $timezone_list = array();
    foreach( $timezone_offsets as $timezone => $offset )
    {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate( 'H:i', abs($offset) );

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

        $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
    }

    return $timezone_list;
}
*/

//$timezone_array2 = generate_timezone_list();


$day_array = array( "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");

$time_array = array(
		"0000"=>"00:00 am (midnight)",
		"0015"=>"00:15 am",
		"0030"=>"00:30 am",
		"0045"=>"00:45 am",
		"0100"=>"01:00 am",
		"0115"=>"01:15 am",
		"0130"=>"01:30 am",
		"0145"=>"01:45 am",
		"0200"=>"02:00 am",
		"0215"=>"02:15 am",
		"0230"=>"02:30 am",
		"0245"=>"02:45 am",
		"0300"=>"03:00 am",
		"0315"=>"03:15 am",
		"0330"=>"03:30 am",
		"0345"=>"03:45 am",
		"0400"=>"04:00 am",
		"0415"=>"04:15 am",
		"0430"=>"04:30 am",
		"0445"=>"04:45 am",
		"0500"=>"05:00 am",
		"0515"=>"05:15 am",
		"0530"=>"05:30 am",
		"0545"=>"05:45 am",
		"0600"=>"06:00 am",
		"0615"=>"06:15 am",
		"0630"=>"06:30 am",
		"0645"=>"06:45 am",
		"0700"=>"07:00 am",
		"0715"=>"07:15 am",
		"0730"=>"07:30 am",
		"0745"=>"07:45 am",
		"0800"=>"08:00 am",
		"0815"=>"08:15 am",
		"0830"=>"08:30 am",
		"0845"=>"08:45 am",
		"0900"=>"09:00 am",
		"0915"=>"09:15 am",
		"0930"=>"09:30 am",
		"0945"=>"09:45 am",
		"1000"=>"10:00 am",
		"1015"=>"10:15 am",
		"1030"=>"10:30 am",
		"1045"=>"10:45 am",
		"1100"=>"11:00 am",
		"1115"=>"11:15 am",
		"1130"=>"11:30 am",
		"1145"=>"11:45 am",
		"1200"=>"12:00 pm (noon)",
		"1215"=>"12:15 pm",
		"1230"=>"12:30 pm",
		"1245"=>"12:45 pm",
		"1300"=>"01:00 pm",
		"1315"=>"01:15 pm",
		"1330"=>"01:30 pm",
		"1345"=>"01:45 pm",
		"1400"=>"02:00 pm",
		"1415"=>"02:15 pm",
		"1430"=>"02:30 pm",
		"1445"=>"02:45 pm",
		"1500"=>"03:00 pm",
		"1515"=>"03:15 pm",
		"1530"=>"03:30 pm",
		"1545"=>"03:45 pm",
		"1600"=>"04:00 pm",
		"1615"=>"04:15 pm",
		"1630"=>"04:30 pm",
		"1645"=>"04:45 pm",
		"1700"=>"05:00 pm",
		"1715"=>"05:15 pm",
		"1730"=>"05:30 pm",
		"1745"=>"05:45 pm",
		"1800"=>"06:00 pm",
		"1815"=>"06:15 pm",
		"1830"=>"06:30 pm",
		"1845"=>"06:45 pm",
		"1900"=>"07:00 pm",
		"1915"=>"07:15 pm",
		"1930"=>"07:30 pm",
		"1945"=>"07:45 pm",
		"2000"=>"08:00 pm",
		"2015"=>"08:15 pm",
		"2030"=>"08:30 pm",
		"2045"=>"08:45 pm",
		"2100"=>"09:00 pm",
		"2115"=>"09:15 pm",
		"2130"=>"09:30 pm",
		"2145"=>"09:45 pm",
		"2200"=>"10:00 pm",
		"2215"=>"10:15 pm",
		"2230"=>"10:30 pm",
		"2245"=>"10:45 pm",
		"2300"=>"11:00 pm",
		"2315"=>"11:15 pm",
		"2330"=>"11:30 pm",
		"2345"=>"11:45 pm");

 	$ringagent_array = array(
			"1"=>"1",
			"2"=>"2",
			"3"=>"3",
			"4"=>"4",
			"5"=>"5",
			"6"=>"6",
			"7"=>"7",
			"8"=>"8",
			"9"=>"9",
			"10"=>"10"
	);


 	$queuesize_array = array(
			"0"=>"Unlimited/Any",
			"5"=>"5",
			"10"=>"10",
			"15"=>"15",
			"20"=>"20",
			"25"=>"25",
			"30"=>"30",
			"40"=>"40",
			"50"=>"50"
	);
?>
