<?php
include_once "header.php";

// This is used to submit new markers for review.
// Markers won't appear on the map until they are approved.

$owner_name = mysqli_real_escape_string($GLOBALS['cxn'], parseInput($_POST['owner_name']));
$owner_email = mysqli_real_escape_string($GLOBALS['cxn'], parseInput($_POST['owner_email']));
$title = mysqli_real_escape_string($GLOBALS['cxn'], parseInput($_POST['title']));
$type = mysqli_real_escape_string($GLOBALS['cxn'], parseInput($_POST['type']));
$address = mysqli_real_escape_string($GLOBALS['cxn'], parseInput($_POST['address']));
$uri = mysqli_real_escape_string($GLOBALS['cxn'], parseInput($_POST['uri']));
$description = mysqli_real_escape_string($GLOBALS['cxn'], parseInput($_POST['description']));

// validate fields
if(empty($title) || empty($type) || empty($address) || empty($uri) || empty($description) || empty($owner_name) || empty($owner_email)) {
  echo "All fields are required - please try again.";
  exit;

} else {



  // if startup genome mode enabled, post new data to API
  if($sg_enabled) {

    try {
      @$r = $http->doPost("/organization", $_POST);
      $response = json_decode($r, 1);
      if ($response['response'] == 'success') {
        include_once("startupgenome_get.php");
        echo "success";
        exit;
      }
    } catch (Exception $e) {
      echo "<pre>";
      print_r($e);
    }


  // normal mode enabled, save new data to local db
  } else {

    // insert into db, wait for approval
    $insert = mysqli_query($GLOBALS['cxn'], "INSERT INTO places (approved, title, type, address, uri, description, owner_name, owner_email) VALUES (null, '$title', '$type', '$address', '$uri', '$description', '$owner_name', '$owner_email')") or die(mysqli_error($GLOBALS['cxn']));

    // geocode new submission
    $hide_geocode_output = true;
    include "geocode.php";

    echo "success";
    exit;

  }


}


?>
