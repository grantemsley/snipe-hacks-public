<?php
require_once('includes.php');

// This script syncs with LDAP, then goes through all Snipe-IT users, finds the LDAP 
// synced ones, and updates additional fields that the built in LDAP sync doesn't cover.
//
// Fields synced:
//  - LDAP department to Snipe-IT Department (list)
//  - LDAP streetaddress to Snipe-IT Location (matched by it's Address, not name) (list)
//  - LDAP company to Snipe-IT Company (list)
//  - LDAP title to Snipe-IT Title (text)
//  - LDAP telephonenumber to Snipe-IT Phone (text)

$debug = FALSE;

// Run the built in LDAP sync
if ($debug) {
  system('/usr/bin/php /opt/snipe-it/artisan snipeit:ldap-sync --summary');
} else {
  system('/usr/bin/php /opt/snipe-it/artisan snipeit:ldap-sync');
}

// Get companies list
$companies = get_api_data_rows('/api/v1/companies?limit=5000');
$company_names = lookup_objects($companies, 'name');
if ($debug) foreach($companies as $company) { print "Loaded company " . $company->name . "\n"; }

// Get departments list
$departments = get_api_data_rows('/api/v1/departments?limit=5000');
$department_names = lookup_objects($departments, 'name');
if ($debug) foreach($departments as $department) { print "Loaded department " . $department->name . "\n"; }

// Get locations list
$locations = get_api_data_rows('/api/v1/locations?limit=5000');
$location_addresses = lookup_objects($locations, 'address');
if ($debug) foreach($locations as $location) { print "Loaded location " . $location->name . "\n"; }

// Get all the users
$users = get_api_data_rows('/api/v1/users?limit=5000');
if ($debug) print "Loaded " . count($users) . " users\n";

foreach($users as $user) {
  // Skip users who are not from AD
  if($user->notes != "Imported from LDAP") {
    if ($debug) print "Skipping user {$user->name}, note is not 'Imported from LDAP'\n";
    continue;
  } 

  // Get their AD user, move on if they are not found
  $aduser = get_ad_user($user->username);
  if(!$aduser) {
    if ($debug) print "Skipping user {$user->name}, user was not found in AD\n";
    continue;
  }

  $fields = array();

  // Update their department
  if(!array_key_exists('department', $aduser)) {
    if ($debug) print "User {$user->name} does not have an AD department set\n";
  } else {
    // If they don't have a department set, or the department name does not match their AD department, change it
    if(!$user->department || ($user->department->name != $aduser['department'][0])) {
      if(!array_key_exists($aduser['department'][0], $department_names)) {
        print "Unable to find department {$aduser['department'][0]} for {$user->name}\n";
      } else {
        $fields['department_id'] = $department_names[$aduser['department'][0]]->id;
      }
    }
  }

  // Update their location by streetaddress
  if(!array_key_exists('streetaddress', $aduser)) {
    if ($debug) print "User {$user->name} does not have an AD streetAddress set\n";
  } else {
    if(!array_key_exists($aduser['streetaddress'][0], $location_addresses)) {
      print "Unable to find location address '{$aduser['streetaddress'][0]}' for {$user->name}\n";
    } else {
      $location_id = $location_addresses[$aduser['streetaddress'][0]]->id;
      if(!$user->location || $user->location->id != $location_id) {
        $fields['location_id'] = $location_id;
      }
    }
  }

  // Update their company
  if(!array_key_exists('company', $aduser)) {
    if ($debug) print "User {$user->name} does not have an AD company set\n";
  } else {
    if(!array_key_exists($aduser['company'][0], $company_names)) {
      print "Unable to find company name '{$aduser['company'][0]}' for {$user->name} \n";
    } else {
      $company_id = $company_names[$aduser['company'][0]]->id;
      if(!$user->company || $user->company->id != $company_id) {
        $fields['company_id'] = $company_id;
      }
    }
  }

  // Update their title
  if(array_key_exists('title', $aduser)) {
    if($user->jobtitle != $aduser['title'][0]) {
      $fields['jobtitle'] = $aduser['title'][0];
    }
  }

  // Update their phone number
  if(array_key_exists('telephonenumber', $aduser)) {
    if($user->phone != $aduser['telephonenumber'][0]) {
      $fields['phone'] = $aduser['telephonenumber'][0];
    }
  }

  //print_r($user);
  //print_r($fields);


  // Update the user if there are any fields set to update
  if($fields) {
    //print "Updating user {$user->name}\n";
    update_user($user->id, $fields);
  }

}

?>
