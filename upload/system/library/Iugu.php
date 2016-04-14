<?php

$baseDirectory = dirname(__FILE__);

require( $baseDirectory . "/iugu/Iugu/Backward_Compatibility.php" );

require( $baseDirectory . "/iugu/Iugu/Base.php" );
require( $baseDirectory . "/iugu/Iugu/SearchResult.php" );
require( $baseDirectory . "/iugu/Iugu/Object.php" );
require( $baseDirectory . "/iugu/Iugu/Utilities.php" );

require( $baseDirectory . "/iugu/Iugu/APIRequest.php" );
require( $baseDirectory . "/iugu/Iugu/APIResource.php" );
require( $baseDirectory . "/iugu/Iugu/APIChildResource.php" );

require( $baseDirectory . "/iugu/Iugu/Customer.php" );
require( $baseDirectory . "/iugu/Iugu/PaymentMethod.php" );
require( $baseDirectory . "/iugu/Iugu/PaymentToken.php" );
require( $baseDirectory . "/iugu/Iugu/Charge.php" );
require( $baseDirectory . "/iugu/Iugu/Invoice.php" );
require( $baseDirectory . "/iugu/Iugu/Subscription.php" );

require( $baseDirectory . "/iugu/Iugu/Factory.php" );
