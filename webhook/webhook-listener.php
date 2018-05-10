<?php

// imports Parse PHP SDK
require "parse-php-sdk/autoload.php";

// Import file
require_once("functions.php");

use Parse\ParseClient;
use Parse\ParseQuery;
use Parse\ParseObject;

ParseClient::initialize( 'dVVty0n8MrhMhTusZHskFKJADY2HmG17KWW2TpQ9', 'Ifrp7J1Mji2ZPUObnid0AZ5i46zaLdyebHe3zSnO', 'WJ6MQPvVPjxkbyeLOMxgYj70AgIN181kFZYqtO2T' );
ParseClient::setServerURL('https://parseapi.back4app.com', '/');

// retrieve the request's body and parse it as JSON
$input = @file_get_contents("php://input");

// array of key-value pairs
$event_json = json_decode($input);

// validate webhook id
if (!isset($event_json->id))
{	
	echo 'ID NULL';
    http_response_code(200);
    exit();
}

// validate webhook event type
if ($event_json->event_type == 'PAYMENT.SALE.COMPLETED') {

	// get order by ppSaleId
	$query = new ParseQuery("Order");
	$query->equalTo("parentPayment", $event_json->resource->parent_payment);
	$object = $query->first();
	// update order paymentStage
	$object->set("paymentStage", 2);
	$object->save();

	echo 'PAYMENT.SALE.COMPLETED received';
	http_response_code(200);
    exit();

} elseif ($event_json->event_type == 'CUSTOMER.DISPUTE.CREATED') {

	$newDispute = new ParseObject("Dispute");

	// Check webhook response and set every available field
	$newDispute->set("disputeId", $event_json->resource->dispute_id);
	$newDispute->set("saleId", $event_json->resource->dispute_id);
	$newDispute->set("transactionId", $event_json->resource->disputed_transactions[0]->seller_transaction_id);
	$newDispute->set("sellerName", $event_json->resource->disputed_transactions[0]->seller->name);
	$newDispute->set("reason", $event_json->resource->reason);
	$newDispute->set("status", $event_json->resource->status);	
	$newDispute->set("outcome", "Aún sin resolución");
	$newDispute->save();

	// Get more information of dispute
  	$disputeId = $event_json->resource->dispute_id;
  	$access_token = get_access_token();
  	// Update dispute with rest of fields
	$disputeDetails = get_dispute_details($access_token, $disputeId);
	$newDispute->set("buyerMail", $disputeDetails->disputed_transactions[0]->buyer->email);
	$newDispute->set("buyerComment", $disputeDetails->messages[0]->content);
  	$newDispute->save();

  	echo 'New object created with objectId: ' . $newDispute->getObjectId();
  	http_response_code(200);
	exit();

} elseif ($event_json->event_type == 'CUSTOMER.DISPUTE.UPDATED') {

	// get dispute by disputeId
	$disputeQuery = new ParseQuery("Dispute");
	$disputeQuery->equalTo("disputeId", $event_json->resource->dispute_id);
	$updateObj = $disputeQuery->first();
	
	// update dispute details
	$updateObj->set("status", $event_json->resource->status);
	$updateObj->save();

	echo 'CUSTOMER.DISPUTE.UPDATED received';
	http_response_code(200);
    exit();

} elseif ($event_json->event_type == 'CUSTOMER.DISPUTE.RESOLVED') {

	// get dispute by disputeId
	$disputeQuery = new ParseQuery("Dispute");
	$disputeQuery->equalTo("disputeId", $event_json->resource->dispute_id);
	$updateObj = $disputeQuery->first();
	
	// update dispute details
	$updateObj->set("status", $event_json->resource->status);
	$updateObj->set("outcome", $event_json->resource->dispute_outcome->outcome_code);
	$updateObj->save();

	echo 'CUSTOMER.DISPUTE.RESOLVED received';
	http_response_code(200);
    exit();

} else {
	echo 'Other event';
	http_response_code(200);
    exit();
}

?>