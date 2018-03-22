<?php
require_once('config.php');


// Map arrays to make it easier to find an object by one of it's properties
// By default it will decode HTML entities in the property, since Snipe-IT encodes most properties
function lookup_objects($array, $property, $decode = TRUE) {
  $new = array();
  foreach($array as $a) {
    // The ENT_QUOTES const makes it decode both types of quotes, which seems to be what Snipe-IT does
    $new[html_entity_decode($a->$property, ENT_QUOTES)] = $a;
  }
  return $new;
}

// Get data from an API URL
function get_api_data($url) {
  $headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer '.accessToken
  );

  $ch = curl_init(baseurl.$url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $data = curl_exec($ch);
  curl_close($ch);
  $dataobject = json_decode($data);
  return $dataobject;
}

// Get just the rows element from an API call
function get_api_data_rows($url) {
  $dataobject = get_api_data($url);
  return $dataobject->rows;
}

// Update specified fields for a person
function update_user($userid, $fields) {
  $headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer '.accessToken
  );

  $ch = curl_init(baseurl.'/api/v1/users/'.$userid);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function get_ad_user($upn) {
  $ldapconn = ldap_connect(ldapserver) or die ("Could not connect to LDAP server");
  ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
  ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

  $ldapbind = ldap_bind($ldapconn, ldapuser, ldappassword) or die ("Could not bind to LDAP server");
  $result = ldap_search($ldapconn, ldapsearchtree, "(userPrincipalName=$upn)") or die ("Could not perform LDAP search".ldap_error($ldapconn));

  $entries = ldap_get_entries($ldapconn, $result);

  if($entries["count"] == 0) return false;

  return $entries[0];

}
