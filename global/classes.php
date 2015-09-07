<?
include_once("constants.php");
class citfactSeopage
{
	static function getPropsSeoUserVar($DBHost, $DBLogin, $DBPassword, $DBName)
    {
		$_QUERY =
		"
		SELECT elements.ID, elements.GROUP_ID, elements.NAME, elements.CODE, elements.VALUE, elements.DESCRIPTION
		FROM b_citfact_uservars AS elements, b_citfact_uservars_group AS groups
		WHERE elements.group_id = groups.id AND groups.code = '".CODE_GROUP_USER_VARS."';
		";

		$mysqli = new mysqli($DBHost, $DBLogin, $DBPassword, $DBName);
		if ($mysqli->connect_errno) {
			//return "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			return false;
		}
		$res = mysqli_query($mysqli, $_QUERY);
		$result = array();
		while ($arRes = mysqli_fetch_assoc($res)){
			$result[] = $arRes;
		}
		return $result;
	}
	
	static function getSeoPage($link, $DBHost, $DBLogin, $DBPassword, $DBName)
    {
		$arResult = self::getPropsSeoUserVar($DBHost, $DBLogin, $DBPassword, $DBName);
		foreach ($arResult as $result) {
			switch($result['CODE']) {
				case CODE_ID_SEO_BLOCK_USER_VARS:
				$iblockId = $result['VALUE'];
				break;
				case CODE_PROPS_SEO_BLOCK_USER_VARS:
				$propertyId = $result['VALUE'];
				break;
			}
		}
		
		$_QUERY =
		"
		SELECT element.ID, element.CODE, element.IBLOCK_ID, element.SORT, property.VALUE
		FROM b_iblock_element_property AS property, b_iblock_element AS element
		WHERE property.iblock_property_id = ".$propertyId." AND element.id = property.iblock_element_id AND element.code='".$link."' AND element.active = 'Y' AND element.iblock_id = ".$iblockId."
		ORDER BY element.SORT , element.ID;
		";

		$mysqli = new mysqli($DBHost, $DBLogin, $DBPassword, $DBName);
		if ($mysqli->connect_errno) {
			//echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			return false;
		}
		$res = mysqli_query($mysqli, $_QUERY);
		if ($arRes = mysqli_fetch_assoc($res)) {
			return $arRes;
		}else{
			return false;
		}
	}

	static function isSeoRedirect($DBHost, $DBLogin, $DBPassword, $DBName)
    {
		$arResult = self::getPropsSeoUserVar($DBHost, $DBLogin, $DBPassword, $DBName);
		foreach ($arResult as $result) {
			switch($result['CODE']) {
				case CODE_IS_REDIRECT_SEO:
				$redirect = $result['VALUE'];
				break;
			}
		}
		if ($redirect == 'Y') {
			return true;
		}else{
			return false;
		}
	}
	
	static function getNormalPage($link, $DBHost, $DBLogin, $DBPassword, $DBName)
    {
		$arResult = self::getPropsSeoUserVar($DBHost, $DBLogin, $DBPassword, $DBName);
		foreach ($arResult as $result) {
			switch($result['CODE']) {
				case CODE_ID_SEO_BLOCK_USER_VARS:
				$iblockId = $result['VALUE'];
				break;
				case CODE_PROPS_SEO_BLOCK_USER_VARS:
				$propertyId = $result['VALUE'];
				break;
			}
		}
		
		$_QUERY =
		"
		SELECT element.ID, element.CODE, element.IBLOCK_ID, element.SORT, property.VALUE
		FROM b_iblock_element_property AS property, b_iblock_element AS element
		WHERE property.iblock_property_id = ".$propertyId." AND element.id = property.iblock_element_id AND property.value='".$link."' AND element.active = 'Y' AND element.iblock_id = ".$iblockId."
		ORDER BY element.SORT , element.ID;
		";

		$mysqli = new mysqli($DBHost, $DBLogin, $DBPassword, $DBName);
		if ($mysqli->connect_errno) {
			//echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			return false;
		}
		$res = mysqli_query($mysqli, $_QUERY);
		if ($arRes = mysqli_fetch_assoc($res)) {
			return $arRes['CODE'];
		}else{
			return false;
		}
	}
	
}
?>