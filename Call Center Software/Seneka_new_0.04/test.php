<?php
define('INCLUDE_CHECK',true);

require_once("includes/functions_twilio_subaccount.php");
include_once("includes/config_twilio.php");
require_once("includes/config_mongo.php"); // establishes connection to database or set $error
require_once("includes/config_mandrill.php");
require_once("includes/functions_validateforms.php");

/*
static $regions = array(
    'Africa' => DateTimeZone::AFRICA,
    'America' => DateTimeZone::AMERICA,
    'Antarctica' => DateTimeZone::ANTARCTICA,
    'Aisa' => DateTimeZone::ASIA,
    'Atlantic' => DateTimeZone::ATLANTIC,
    'Europe' => DateTimeZone::EUROPE,
    'Indian' => DateTimeZone::INDIAN,
    'Pacific' => DateTimeZone::PACIFIC
);
foreach ($regions as $name => $mask) {
    $tzlist[] = DateTimeZone::listIdentifiers($mask);
}
print_r($tzlist);

$timezone_identifiers = DateTimeZone::listIdentifiers();
print_r($timezone_identifiers);
*/

$friendly = "abc";
//$sid = "123";
//$token = "456";
$account = create_twilio_subaccount($friendly, $sid, $token);
echo $account->type;
echo "fgesgewafw";


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

$timezone_array2 = generate_timezone_list();
print_r($timezone_array2);
?>