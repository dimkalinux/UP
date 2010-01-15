<?php

// Make sure no one attempts to run this script "directly"
if (!defined('UP')) {
	exit;
}

define('OPENID_LOGIN_NO_ERROR', 0);
define('OPENID_LOGIN_CANCELED', 1);
define('OPENID_LOGIN_INVALID_ID', 1);
define('OPENID_LOGIN_ID_NOT_EXISTS', 2);
define('OPENID_LOGIN_FAILED', 3);
define('OPENID_LOGIN_ERROR_REDIRECT', 4);


require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/FileStore.php";
require_once "Auth/OpenID/SReg.php";
require_once "Auth/OpenID/PAPE.php";

global $pape_policy_uris;
$pape_policy_uris = array(
			  PAPE_AUTH_MULTI_FACTOR_PHYSICAL,
			  PAPE_AUTH_MULTI_FACTOR,
			  PAPE_AUTH_PHISHING_RESISTANT
			  );

function getStore() {
    $store_path = "/tmp/_php_consumer_test";

    if (!file_exists($store_path) && !mkdir($store_path)) {
        throw new Exception('Could not create the FileStore directory');
    }

    return new Auth_OpenID_FileStore($store_path);
}

function openid_login_error($errorID) {
    global $base_url;
    header("Location: {$base_url}login_openid.php?status=$errorID");
    exit();
}

function &getConsumer() {
    $store = getStore();
    $consumer =& new Auth_OpenID_Consumer($store);
    return $consumer;
}

function getScheme() {
    $scheme = 'http';
    if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') {
        $scheme .= 's';
    }
    return $scheme;
}

function getReturnTo() {
    return sprintf("%s://%s:%s%s/openid_finish_auth.php",
                   getScheme(), $_SERVER['SERVER_NAME'],
                   $_SERVER['SERVER_PORT'],
                   dirname($_SERVER['PHP_SELF']));
}

function getTrustRoot() {
    return sprintf("%s://%s:%s%s/",
                   getScheme(), $_SERVER['SERVER_NAME'],
                   $_SERVER['SERVER_PORT'],
                   dirname($_SERVER['PHP_SELF']));
}

?>
