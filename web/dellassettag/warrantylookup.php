<?php
// This page redirects to Dell's warranty lookup page, based on the dell service tag custom field.
// Dell has an official API to access this information, but you need to be a partner to get an API key,
// or request one through your partner (most of whom don't know what an API key even is...)
// Anyways, this URL seems to work fine to get to the information.

require_once('../../includes.php');

if(!isset($_REQUEST['id'])) {
  die("Asset ID number not specified");
}

$id = $_REQUEST['id'];
$asset = get_api_data('/api/v1/hardware/'.$id);
if(!$asset) { die("Unable to load asset from Snipe-IT API"); }
$servicetag = $asset->custom_fields->{'Dell Service Tag'}->value;
if(!$servicetag) { die("Asset does not have a Dell Service Tag"); }

$url = "http://www.dell.com/support/home/ca/en/cabsdt1/product-support/servicetag/" . strtolower($servicetag) . "/warranty";

header('Location: '.$url);
exit;
