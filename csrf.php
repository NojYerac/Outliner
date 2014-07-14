<?php

function get_csrf_token() {
    $csrf_token = md5(openssl_random_pseudo_bytes(16));
	$_SESSION['csrf_token'] = $csrf_token;
	if (!isset($_SESSION['csrf_array'])) {$_SESSION['csrf_array'] = array();}
	$expires = new DateTime();
	$expires->modify('+2hours');
	$_SESSION['csrf_array'][] = array('csrf_token' => $csrf_token, 'expires' => $expires);
	return $csrf_token;
}

function check_csrf_token($csrf_token) {
	$result = false;
    if (isset($_SESSION['csrf_token'])) {
        if ($csrf_token == $_SESSION['csrf_token']) {
            unset($_SESSION['csrf_token']);
            $result = true;
        }
	}
	if (isset($_SESSION['csrf_array'])) {
		foreach ($_SESSION['csrf_array'] as $key => $value) {
			if ($value['expires'] < (new DateTime())) {
				unset($_SESSION['csrf_array'][$key]);
				$result = false;
			} elseif ($csrf_token == $value['csrf_token']) {
				unset($_SESSION['csrf_array'][$key]);
				$result = true;
			}
		}
	}
    return $result;
}
?>
