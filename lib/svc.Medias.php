<?php
final class Medias extends CoreCommon {
	
	private $oMedias = NULL;
	
	public function __construct() {
		parent::__construct();
		$this->oMedias = new MediasMgr();
	}
	
	public function getDashboard() {
		$sDashboard = Dashboard::getDashboard('{__ACTIVE_MEDIAS__}', $this->oMedias->getMediasStats());
		return $this->oTplMgr->buildSimpleCacheTpl(
												$sDashboard, 
												ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'medias.xml'
											);
	}
	
	public function getHomePage() {
		$sTplPath = ModulesMgr::getFilePath(__CLASS__, 'backContentsTpl').'home.tpl';
		$oConfig = new Config(__CLASS__);
		$sConfInterface = $oConfig->getConfInterface(__CLASS__, __METHOD__);
		unset($oConfig);
		$sContent = str_replace(
						array(
							'{__MEDIA_TYPES_LIST__}',
							'{__SEARCH_MEDIA_FORM__}',
							'{__MEDIAS_STATS__}',
							'{__CONFIG__}'
						), 
						array(
							Toolz_Form::optionsList('', $this->oMedias->getMediaTypesList()),
							$this->oMedias->getSearchMediaForm(),
							$this->oMedias->getMediasStats(),
							$sConfInterface
						), 
						file_get_contents($sTplPath)
					);
		return array(
				'content' => $this->oTplMgr->buildSimpleCacheTpl(
															$sContent, 
															ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'medias.xml'
														),
				'sPage'	=> 'medias_home'
			);
	}
	
	public function getMediasManagerPage() {
		if(UserRequest::getRequest('media_active') !== false) {
			UserRequest::setParams('media_active', 0);
		}
		if(UserRequest::getParams('media_type_id') === false) {
			if(UserRequest::getRequest('media_type_id') !== false) {
				$iMediaTypeId = (int)UserRequest::getRequest('media_type_id');
			} else {
				$iMediaTypeId = 1;
			}
			UserRequest::setParams('media_type_id', $iMediaTypeId);
		}
		if(UserRequest::getRequest('keyword') !== false) {
			UserRequest::setParams('sKeyword', UserRequest::getRequest('keyword'));
			return $this->searchMedia();
		}
		if(UserRequest::getParams('media_active') === false) {
			UserRequest::setParams('media_active', 1);
		}
		$sContent = $this->oTplMgr->buildSimpleCacheTpl(
													$this->oMedias->getMediasManager(
														(int)UserRequest::getParams('media_type_id'), 
														(int)UserRequest::getParams('media_active')
													), 
													ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'medias.xml'
												);
		return array(
				'content' => $sContent,
				'sPage'	=> 'medias_manager'
			);
	}
	
	public function searchMedia() {
		$sContent = $this->oTplMgr->buildSimpleCacheTpl(
													$this->oMedias->searchMedia(
														UserRequest::getParams('sKeyword'), 
														(int)UserRequest::getParams('media_type_id')
													), 
													ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'medias.xml'
												);
		return array(
				'content' => $sContent,
				'sPage'	=> 'medias_manager'
			);
	}
	
	public function getAddMediaPage() {
		$sContent = $this->oTplMgr->buildSimpleCacheTpl(
													$this->oMedias->getAddMediaForm(UserRequest::getParams('media_type_id')), 
													ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'medias.xml'
												);
		return array(
				'content' => $sContent,
				'sPage'	=> 'medias_add'
			);
	}
	
	public function addMedia() {
		$sContent = $this->oTplMgr->buildSimpleCacheTpl(
													$this->oMedias->addMedia(UserRequest::getParams()), 
													ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'medias.xml'
												);
		return array(
				'content' => $sContent,
				'sPage'	=> 'medias_update'
			);
	}
	
	public function getUpdateMediaPage() {
		if(!UserRequest::getParams('media_id')) {
			UserRequest::setParams('media_id', 1);
		}
		$sContent = $this->oTplMgr->buildSimpleCacheTpl(
													$this->oMedias->getUpdateMediaForm(UserRequest::getParams('media_id')), 
													ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'medias.xml'
												);
		return array(
				'content' => $sContent,
				'sPage'	=> 'medias_update'
			);
	}
	
	public function updateMedia() {
		$sContent = $this->oTplMgr->buildSimpleCacheTpl(
													$this->oMedias->updateMedia(UserRequest::getParams()), 
													ModulesMgr::getFilePath(__CLASS__, 'backLocales', $this->oLang->LOCALE).'medias.xml'
												);
		return array(
				'content' => $sContent,
				'sPage'	=> 'medias_update'
			);
	}
	
	public function deleteMedia() {
		$this->oMedias->deleteMedia(UserRequest::getParams('media_id'));
		return $this->getMediasManagerPage();
	}
	
	public function deleteAllArchivedMedia() {
		$this->oMedias->deleteAllArchivedMedia(UserRequest::getParams('media_type_id'));
		return $this->getMediasManagerPage();
	}
	
	public function restoreMedia() {
		$this->oMedias->restoreMedia(UserRequest::getParams('media_id'));
		return $this->getMediasManagerPage();
	}
	
	public function generateThumbnails() {
		$this->oMedias->generateThumbnails(UserRequest::getParams('sMaxSize'));
		return $this->getMediasManagerPage();
	}
	
	public function generateAllThumbnails() {
		UserRequest::startBenchmark('generateAllThumbnails');
		$this->oMedias->generateAllThumbnails();
		return $this->getMediasManagerPage();
	}
	
	public function deleteThumbnails() {
		$this->oMedias->deleteThumbnails(UserRequest::getParams('sSize'));
		return $this->getMediasManagerPage();
	}
	
	public function createDedicatedPage() {
		$this->oMedias->createDedicatedPage(
										UserRequest::getParams('media_id'),
										UserRequest::getParams('dedicatedPageName')
									);
		return $this->getUpdateMediaPage();
	}
	
	public function updateDedicatedPage() {
		$this->oMedias->updateDedicatedPage(
										UserRequest::getParams('media_id'),
										UserRequest::getParams('dedicatedPageName'),
										UserRequest::getParams('mediaCurrentPageName')
									);
		return $this->getUpdateMediaPage();
	}
	
	public function deleteDedicatedPage() {
		$this->oMedias->deleteDedicatedPage(
										UserRequest::getParams('media_id'),
										UserRequest::getParams('dedicatedPageName')
									);
		return $this->getUpdateMediaPage();
	}
}