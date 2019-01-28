<?php
abstract class MediasCommon {
	
	protected $aConf					= array();
	protected $aMediaTypesAvailable		= array();
	protected $aMediaTypesList			= array();
	protected $aMediaTypesSelectList	= array();
	protected $oMediasModel				= NULL;
	protected $sMediasManagerTplName	= 'mediaManager.tpl';
	protected $sAddMediaFormTplName		= 'addMediaForm.tpl';
	protected $sUpdateMediaFormTplName	= 'updateMediaForm.tpl';
	protected $sUploadPath				= '';
	protected $sUploadUrl				= '';
	protected $sMediaType2TransPatern	= '{__{__media_type__}_label__}';
	protected $sMediasDirectory			= 'medias';
	protected $sTplPartsPath			= '';
	private $sSearchFormTplName				= 'search.media.tpl';
	private $sSearchFormTpl					= '';
	
	public function __construct() {
		$oConfig = new Config(MediasMgr::$sModuleName);
		$this->aConf = $oConfig->getGlobalConf();
		unset($oConfig);
		$this->oMediasModel				= new MediasModel($this->aConf['NB_MEDIA_PER_PAGE']);
		$this->aMediaTypesAvailable		= $this->oMediasModel->getMediaTypes();
		$this->aMediaTypesList			= $this->getMediaTypesList();
		$this->aMediaTypesSelectList	= $this->aMediaTypesList;
		$this->sUploadPath				= ModulesMgr::getFilePath(MediasMgr::$sModuleName, 'data');
		$this->sUploadPath				.= $this->sMediasDirectory.'/';
		$this->sUploadUrl				= WEB_PATH.'Core/modules/Medias/data/'.$this->sMediasDirectory.'/';
		$this->sTplPartsPath			= ModulesMgr::getFilePath(MediasMgr::$sModuleName, 'backPartsTpl');
		$this->sSearchFormTpl			= file_get_contents($this->sTplPartsPath.$this->sSearchFormTplName);
	}
	
	public function getMediaTypesList() {
		$aOptions = $this->oMediasModel->getMediaTypes();
		reset($aOptions);
		return $aOptions;
	}
	
	public function getSearchMediaForm($sMediaTypeName='') {
		return str_replace(
					'{__MEDIA_TYPES_LIST__}', 
					Toolz_Form::optionsList($sMediaTypeName, $this->getMediaTypesList()),
					$this->sSearchFormTpl
				);
	}
}