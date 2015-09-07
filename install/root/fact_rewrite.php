<?
include_once("bitrix/php_interface/dbconn.php");
include_once("local/modules/citfact.seopage/global/classes.php");

$link = trim(urldecode($_SERVER['REQUEST_URI']));

if (citfactSeopage::isSeoRedirect($DBHost, $DBLogin, $DBPassword, $DBName)){
	if($resNormalPage = citfactSeopage::getNormalPage($link, $DBHost, $DBLogin, $DBPassword, $DBName)){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".$resNormalPage);
		exit();
	}
}

if ($arRes = citfactSeopage::getSeoPage($link, $DBHost, $DBLogin, $DBPassword, $DBName)){
	global $seoUrls; 
	$_SERVER['REQUEST_URI'] = $arRes['VALUE'];
	$parseUri = parse_url($arRes['VALUE']);
	$_SERVER['QUERY_STRING'] = $parseUri['query']; 
	parse_str($parseUri['query'], $_GET); 
	$seoUrls = $arRes['ID'];
}

include_once("bitrix/urlrewrite.php");