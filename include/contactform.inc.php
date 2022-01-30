<?php
defined('PHPCAPTCHA_PATH') or die('Hacking attempt!');

$conf['template'] = 'contactform';
include(PHPCAPTCHA_PATH.'include/common.inc.php');

add_event_handler('contact_form_check', 'check_phpcaptcha', EVENT_HANDLER_PRIORITY_NEUTRAL, 2);


global $template;
$template->set_prefilter('contactform', 'prefilter_phpcaptcha');

function prefilter_phpcaptcha($content)
{
	$search = '</textarea></td>';
	return str_replace($search, $search."\n{\$captcha.parsed_content}", $content);
}


function check_phpcaptcha($action, $comment)
{
	global $phpcaptcha_config, $page;
	
	
	$captchasession = PhpCaptchaConfig::getChallengeFromFile($_POST['captcha_hash']);
	
	$sessionvalue = $captchasession;
	$postvalue = $_POST['captcha_code'];
	if($phpcaptcha_config['strictlowercase']==true) {
		
		$sessionvalue = strtolower($captchasession);
		$postvalue = strtolower($_POST['captcha_code']);
		
	}
	
	if (strcmp($postvalue,$sessionvalue) != 0)
	{
		$page['errors'][] = l10n('Invalid Captcha');
		return 'reject';
	}
	
	return $action;
}