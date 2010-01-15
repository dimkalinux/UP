<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/openid.inc.php';

session_start();

// Render a default page if we got a submission without an openid value.
if (empty($_GET['openid_identifier'])) {
    openid_login_error(OPENID_LOGIN_ID_NOT_EXISTS);
}

$openid = $_GET['openid_identifier'];
$consumer = getConsumer();

// Begin the OpenID authentication process.
$auth_request = $consumer->begin($openid);

// No auth request means we can't begin OpenID.
if (!$auth_request) {
    openid_login_error(OPENID_LOGIN_INVALID_ID);
}

$sreg_request = Auth_OpenID_SRegRequest::build(array('nickname', 'email'));
if ($sreg_request) {
    $auth_request->addExtension($sreg_request);
}

/*
$policy_uris = $_GET['policies'];
$pape_request = new Auth_OpenID_PAPE_Request($policy_uris);
if ($pape_request) {
    $auth_request->addExtension($pape_request);
}
*/
// Redirect the user to the OpenID server for authentication.
// Store the token for this authentication so we can verify the
// response.

// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
// form to send a POST request to the server.
if ($auth_request->shouldSendRedirect()) {
    $redirect_url = $auth_request->redirectURL(getTrustRoot(), getReturnTo());

    // If the redirect URL can't be built, display an error
    // message.
    if (Auth_OpenID::isFailure($redirect_url)) {
        openid_login_error(OPENID_LOGIN_ERROR_REDIRECT);
    } else {
        header("Location: ".$redirect_url);
    }
} else {
    // Generate form markup and render it.
    $form_id = 'openid_message';
    $form_html = $auth_request->htmlMarkup(getTrustRoot(), getReturnTo(), FALSE, array('id' => $form_id));

    // Display an error if the form markup couldn't be generated;
    // otherwise, render the HTML.
    if (Auth_OpenID::isFailure($form_html)) {
        openid_login_error(OPENID_LOGIN_ERROR_REDIRECT);
    } else {
        print $form_html;
    }
}

?>
