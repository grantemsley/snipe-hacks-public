<?php
// This page prints labels using Dymo's web service.
// Requires that the computer you are using has Dymo's label software installed and
// the web service running (which it is by default).

require_once('../../includes.php');

if(!isset($_REQUEST['id'])) {
  die("Asset ID number not specified");
}

$id = $_REQUEST['id'];

$asset = get_api_data('/api/v1/hardware/'.$id);

if(!$asset) { die("Unable to load asset from Snipe-IT API"); }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>Label Printer</title> 
<link rel="stylesheet" type="text/css" href="label.css" />
<script src="DYMO.Label.Framework.latest.js" type="text/javascript" charset="UTF-8"></script>
<script src = "printlabel.js" type="text/javascript" charset="UTF-8"> </script>
</head>

<input type="hidden" id="qrcode" name="qrcode" value="<?=baseurl.'/hardware/'.$asset->id?>"/>
<input type="hidden" id="assetnumber" name="assetnumber" value="<?=$asset->asset_tag?>"/>
<input type="hidden" id="date" name="date" value="<?=$asset->purchase_date->date?>"/>

<div class="content">
    <div id="labelImageDiv">
        <img id="labelImage" src="" alt="If image is not showing, ensure DYMO Label Web Service is running"/>
    </div>

    <div class="printControls">
        <div id="printersDiv">
            <label for="printersSelect">Printer:</label>
            <select id="printersSelect"></select>
        </div>

        <div id="printDiv">
            <button id="printButton">Print</button>
        </div>
    </div>
</div>

</body> 
 
</html> 
