<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Illaronov Anton (ya_m0ps) <ya_m0ps@mail.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


include_once($_SERVER['DOCUMENT_ROOT']."/local/modules/citfact.seopage/global/constants.php");
 
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;

use Bitrix\Main;
use Bitrix\Iblock;
Main\Loader::includeModule('iblock');

Loc::loadMessages(__FILE__);

class citfact_seopage extends CModule
{
    /**
     * @var string
     */
    public $MODULE_ID = 'citfact.seopage';

    /**
     * @var string
     */
    public $MODULE_VERSION;

    /**
     * @var string
     */
    public $MODULE_VERSION_DATE;

    /**
     * @var string
     */
    public $MODULE_NAME;

    /**
     * @var string
     */
    public $MODULE_DESCRIPTION;

    /**
     * @var string
     */
    public $PARTNER_NAME;

    /**
     * @var string
     */
    public $PARTNER_URI;

    /**
     * @var Bitrix\Main\DB\ConnectionPool
     */
    private $connection;

    /**
     * Construct object
     */
    public function __construct()
    {
        $this->MODULE_NAME = Loc::getMessage('USERVARS_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('USERVARS_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('PARTNER_URI');
        $this->MODULE_PATH = $this->getModulePath();

        $arModuleVersion = array();
        include $this->MODULE_PATH . '/install/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->connection = Application::getConnection();
    }

    /**
     * Return path module
     *
     * @return string
     */
    protected function getModulePath()
    {
        $modulePath = explode('/', __FILE__);
        $modulePath = array_slice($modulePath, 0, array_search($this->MODULE_ID, $modulePath) + 1);

        return join('/', $modulePath);
    }

    /**
     * Return components path for install
     *
     * @param bool $absolute
     * @return string
     */
    protected function getComponentsPath($absolute = true)
    {
        $documentRoot = getenv('DOCUMENT_ROOT');
        if (strpos($this->MODULE_PATH, 'local/modules') !== false) {
            $componentsPath = '/local/components';
        } else {
            $componentsPath = '/bitrix/components';
        }

        if ($absolute) {
            $componentsPath = sprintf('%s%s', $documentRoot, $componentsPath);
        }

        return $componentsPath;
    }

    /**
     * Install module
     *
     * @return void
     */
    public function doInstall()
    {
        RegisterModule($this->MODULE_ID);

        $this->installFiles();
        $this->installDB();
        $this->installIblock();
        $this->installEvents();
    }

    /**
     * Remove module
     *
     * @return void
     */
    public function doUninstall()
    {
        $this->unInstallDB();
        $this->unInstallFiles();
        $this->unInstallEvents();

        UnRegisterModule($this->MODULE_ID);
    }

    /**
     * Add tables to the database
     *
     * @return bool
     */
    public function installDB()
    {
        $sqlBatch = file_get_contents($this->MODULE_PATH . '/install/db/install.sql');
        $sqlBatchErrors = $this->connection->executeSqlBatch($sqlBatch);
        if (sizeof($sqlBatchErrors) > 0) {
            return false;
        }
        return true;
    }

    /**
     * Remove tables from the database
     *
     * @return bool
     */
    public function unInstallDB()
    {
        $sqlBatch = file_get_contents($this->MODULE_PATH . '/install/db/uninstall.sql');
        $sqlBatchErrors = $this->connection->executeSqlBatch($sqlBatch);
        if (sizeof($sqlBatchErrors) > 0) {
            return false;
        }

        return true;
    }
	
	/**
     * Add tables to the infoblock
     *
     * @return bool
     */
    public function installIblock()
    {
		
		$db_iblock_type = CIBlockType::GetList(array(), array('=ID'=>CODE_IBLOCK_TYPE));
		if(!$ar_iblock_type = $db_iblock_type->Fetch())
		{
			global $DB;
			$obBlocktype = new CIBlockType;
			$DB->StartTransaction();
			$res = $obBlocktype->Add(
				array(
					'ID'=>CODE_IBLOCK_TYPE,
					'SECTIONS'=>'Y',
					'IN_RSS'=>'N',
					'SORT'=>500,
					'LANG'=>Array(
						'ru'=>Array(
							'NAME'=>'Сервисы',
							'SECTION_NAME'=>'Разделы',
							'ELEMENT_NAME'=>'Страницы'),
						'en'=>Array(
							'NAME'=>'Servies',
							'SECTION_NAME'=>'Sections',
							'ELEMENT_NAME'=>'Pages'),
					)
				)
			);
			if(!$res)
			{
			   $DB->Rollback();
			   echo 'Error: '.$obBlocktype->LAST_ERROR.'<br>';
			}
			else
			   $DB->Commit();
		}

		$idBlock = 0;
		$res = CIBlock::GetList(Array(),Array('TYPE'=>CODE_IBLOCK_TYPE,'SITE_ID'=>SITE_ID,'ACTIVE'=>'Y',"CODE"=>CODE_IBLOCK_CODE), true);
		if(!$ar_res = $res->Fetch())
		{
			$ib = new CIBlock;
			$arFields = Array(
				"ACTIVE" => 'Y',
				"NAME" => 'СЕО страницы',
				"CODE" => CODE_IBLOCK_CODE,
				"LIST_PAGE_URL" => "",
				"DETAIL_PAGE_URL" => "",
				"IBLOCK_TYPE_ID" => CODE_IBLOCK_TYPE,
				"SITE_ID" => Array(SITE_ID),
				"SORT" => '500',
				"GROUP_ID" => Array("2"=>"R")
			);
			$idBlock = $ib->Add($arFields);
		}else{
			$idBlock = $ar_res['ID'];
		}

		$PropID = 0;
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$idBlock, "CODE"=>CODE_PROP_IBLOCK));
		if(!$prop_fields = $properties->GetNext())
		{
			$arFieldsProps = Array(
				"NAME" => "URL копия",
				"ACTIVE" => "Y",
				"SORT" => "500",
				"CODE" => CODE_PROP_IBLOCK,
				"PROPERTY_TYPE" => "S",
				"IS_REQUIRED"=>'Y',
				"IBLOCK_ID" => $idBlock,
			);
			$ibp = new CIBlockProperty;
			$PropID = $ibp->Add($arFieldsProps);
			
		}else{
			$PropID = $prop_fields['ID'];
		}

		global $DB;
		$DB->PrepareFields("b_citfact_uservars_group");
		$arFieldsDB = array(
			'NAME'=>"'СЕО Страницы'",
			'CODE'=>"'".CODE_GROUP_USER_VARS."'",
		);

		$IDGroup = $DB->Insert("b_citfact_uservars_group", $arFieldsDB, $err_mess.__LINE__);
		$IDGroup = intval($IDGroup);
		$arFieldsDB = array(
			'GROUP_ID'=>"'".$IDGroup."'",
			'NAME'=>"'ID свойства в SEO'",
			'CODE'=>"'".CODE_PROPS_SEO_BLOCK_USER_VARS."'",
			'VALUE'=>"'".$PropID."'",
			'DESCRIPTION'=>"'Символьный код свойства в инфоблоке SEO Страниц'",
		);
		$DB->Insert("b_citfact_uservars", $arFieldsDB, $err_mess.__LINE__);
		$arFieldsDB = array(
			'GROUP_ID'=>"'".$IDGroup."'",
			'NAME'=>"'Инфоблок SEO'",
			'CODE'=>"'".CODE_ID_SEO_BLOCK_USER_VARS."'",
			'VALUE'=>"'".$idBlock."'",
			'DESCRIPTION'=>"'Инфоблок SEO'",
		);
		$DB->Insert("b_citfact_uservars", $arFieldsDB, $err_mess.__LINE__);
		$arFieldsDB = array(
			'GROUP_ID'=>"'".$IDGroup."'",
			'NAME'=>"'Редирект'",
			'CODE'=>"'".CODE_IS_REDIRECT_SEO."'",
			'VALUE'=>"'N'",
			'DESCRIPTION'=>"'Если установлен данный параметр, будет работать редирект из обычной страницы на сео-страницу'",
		);
		$DB->Insert("b_citfact_uservars", $arFieldsDB, $err_mess.__LINE__);

        return true;
    }

    /**
     * Add post events
     *
     * @return bool
     */
    public function installEvents()
    {
        return true;
    }


    /**
     * Delete post events
     *
     * @return bool
     */
    public function unInstallEvents()
    {
        return true;
    }

    /**
     * Copy files module
     *
     * @return bool
     */
    public function installFiles()
    {
        CopyDirFiles($this->MODULE_PATH . '/install/root', getenv('DOCUMENT_ROOT') . '/', true, true);

        return true;
    }

    /**
     * Remove files module
     *
     * @return bool
     */
    public function unInstallFiles()
    {
        DeleteDirFiles($this->MODULE_PATH . '/install/root', getenv('DOCUMENT_ROOT') . '/');

        return true;
    }
}
