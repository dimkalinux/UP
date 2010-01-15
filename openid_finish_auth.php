<?php

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';
require UP_ROOT.'include/openid.inc.php';

session_start();

function escape($thing) {
    return htmlentities($thing);
}


$consumer = getConsumer();
$return_to = getReturnTo();
$response = $consumer->complete($return_to);

// Check the response status.
if ($response->status == Auth_OpenID_CANCEL) {
    openid_login_error(OPENID_LOGIN_CANCELED);
} else if ($response->status == Auth_OpenID_FAILURE) {
    openid_login_error(OPENID_LOGIN_FAILED);
} else if ($response->status == Auth_OpenID_SUCCESS) {
    $openid = $response->getDisplayIdentifier();
    $esc_identity = escape($openid);

    $success = sprintf('You have successfully verified ' .
                       '<a href="%s">%s</a> as your identity.',
                       $esc_identity, $esc_identity);

    if ($response->endpoint->canonicalID) {
        $escaped_canonicalID = escape($response->endpoint->canonicalID);
        $success .= '  (XRI CanonicalID: '.$escaped_canonicalID.') ';
    }

    $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
    $sreg = $sreg_resp->contents();

    if (@$sreg['email']) {
        $success .= "  You also returned '".escape($sreg['email']).
            "' as your email.";
    }

    if (@$sreg['nickname']) {
        $success .= "  Your nickname is '".escape($sreg['nickname']).
            "'.";
    }

    if (@$sreg['fullname']) {
        $success .= "  Your fullname is '".escape($sreg['fullname']).
            "'.";
    }
}

if (isset($msg)) {
    echo $msg;
}

if (isset($success)) {
    echo $success;
}

?>
