<?php
final class MediasMgr extends MediasCommon {

	//en attendant de trouver plus propre
	const SRC_URL						= '{##WEB_PATH##}Core/modules/Medias/data/medias/';
	const SUCCESS_ADD_MEDIA				= 'SUCCESS_ADD_MEDIA';
	const ERROR_CAN_NOT_ADD_MEDIA		= 'ERROR_CAN_NOT_ADD_MEDIA';
	const SUCCESS_UPDATE_MEDIA			= 'SUCCESS_UPDATE_MEDIA';
	const ERROR_CAN_NOT_UPDATE_MEDIA	= 'ERROR_CAN_NOT_UPDATE_MEDIA';
	const SUCCESS_ARCHIVE_MEDIA			= 'SUCCESS_ARCHIVE_MEDIA';
	const ERROR_CAN_NOT_ARCHIVE_MEDIA	= 'ERROR_CAN_NOT_ARCHIVE_MEDIA';
	const SUCCESS_DELETE_MEDIA			= 'SUCCESS_DELETE_MEDIA';
	const ERROR_CAN_NOT_DELETE_MEDIA	= 'ERROR_CAN_NOT_DELETE_MEDIA';
	const SUCCESS_RESTORE_MEDIA			= 'SUCCESS_RESTORE_MEDIA';
	const ERROR_CAN_NOT_RESTORE_MEDIA	= 'ERROR_CAN_NOT_RESTORE_MEDIA';
	const ERROR_CAN_NOT_RESIZE_IMG		= 'ERROR_CAN_NOT_RESIZE_IMG';
	const ERROR_PAGE_NOT_EXISTS			= 'ERROR_PAGE_NOT_EXISTS';
	const INVALID_FILE                  = 'INVALID_FILE';
	const INVALID_URL                   = 'INVALID_URL';
	const NO_MEDIA_FOUND                = 'NO_MEDIA_FOUND';
	const MEDIA_FOUND                   = 'MEDIA_FOUND';
	const THUMB_ELMT_ID                 = 7;
	public static $sModuleName			= 'Medias';
	private $sKeyword					= '';
	private $sPageTplPatern				= 'page.{__media_type__}.tpl';
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getMediasUrl($sMediaTypeName) {
		return $this->sUploadUrl.$sMediaTypeName.'/';
	}
	
	public function getMediasPath($sMediaTypeName) {
		return $this->sUploadPath.$sMediaTypeName.'/';
	}
	
	public function getMedias($iTypeId, $sLang='') {
		return $this->oMediasModel->getAllMedias($iTypeId, $sLang);
	}
	
	public function getMediasStats() {
		$aMediaStats = array();
		$aStatsRaw = $this->oMediasModel->getNbMediasByType();
		foreach($aStatsRaw as $aMedia) {
			foreach($this->aMediaTypesList as $sMediaTypeId=>$sMediaTypeName) {
				if(trim($aMedia['media_type_name']) === $sMediaTypeName) {
					$iMediaTypeId = (int)$sMediaTypeId;
					break;
				}
			}
			$sUrl = WEB_PATH.SessionLang::getLang().'/medias/manager.html?media_type_id='.$iMediaTypeId;
			$sMediaStats = Toolz_Tpl::getA($sUrl, implode(': ', $aMedia));
			$aMediaStats[] = $sMediaStats;
		}
		return implode(' - ', $aMediaStats);
	}
	
	private function getActivePage($iNbPages) {
		if(UserRequest::getRequest('page_number') !== false) {
			if((int)UserRequest::getRequest('page_number') > $iNbPages 
			|| (int)UserRequest::getRequest('page_number') < 1) {
				$iActivePage = 1;
			} else {
				$iActivePage = (int)UserRequest::getRequest('page_number');
			}
		} else {
			$iActivePage = 1;
		}
		return $iActivePage;
	}
	
	private function getQueryString($iMediaActive, $bIsSearch, $iMediaTypeId) {
		$sQueryString = WEB_PATH.SessionLang::getLang().'/medias/manager.html?';
		if($iMediaActive !== 1) {
			$sQueryString .= 'media_active=0&';
		}
		if(!empty($bIsSearch)) {
			$sQueryString .= 'keyword='.$this->sKeyword;
		}
		$sQueryString .= '&media_type_id='.$iMediaTypeId.'&page_number=';
		return $sQueryString;
	}
	
	public function searchMedia($sKeyword, $iMediaTypeId, $sLang='') {
		$aMedias = $this->oMediasModel->searchMedia($sKeyword, $iMediaTypeId, $sLang);
		if(empty($aMedias)) {
			$sMsg = SessionCore::getLangObject()->getMsg('medias', self::NO_MEDIA_FOUND);
			UserRequest::$oAlertBoxMgr->danger = $sMsg.' ('.$sKeyword.')';
		} else {
			if(isset($aMedias['elmts'])) {
				$aMedias = array($aMedias);
			}
			$sMsg = SessionCore::getLangObject()->getMsg('medias', self::MEDIA_FOUND);
			$this->sKeyword = $sKeyword;
			UserRequest::$oAlertBoxMgr->success = $sMsg.' '.count($aMedias).' ('.$sKeyword.')';
		}
		return $this->getMediasManager($iMediaTypeId, 1, $aMedias);
	}
	
	public function getMediasManager($iMediaTypeId, $iMediaActive=1, $aMedias=array()) {
		$bIsSearch = !empty($aMedias);
		if(!isset($this->aMediaTypesList[$iMediaTypeId])) {
			$iMediaTypeId = 1;
		}
		$sTitle = (bool)$iMediaActive ? '{__MEDIA_MANAGER__}' : '{__ARCHIVED_MEDIA_MANAGER__}';
		$sMediaManagerTpl = ModulesMgr::getFilePath(self::$sModuleName, 'backContentsTpl');
		$sMediaManagerTpl .= $this->sMediasManagerTplName;
		//gestion de la pagination
		$iOffset = 0;
		$iNbMedia = 0;
		$aNbMedias = $this->oMediasModel->getNbMediasByType($iMediaActive);
		foreach($aNbMedias as $aMediaType) {
			if($aMediaType['media_type_name'] === $this->aMediaTypesList[$iMediaTypeId]) {
				$iNbMedia = $aMediaType['media_types'];
				break;
			}
		}
		if($bIsSearch) {
			$iNbPages = ceil(count($aMedias) / $this->aConf['NB_MEDIA_PER_PAGE']);
		} else {
			$iNbPages = ceil($iNbMedia / $this->aConf['NB_MEDIA_PER_PAGE']);
		}
		$iActivePage = $this->getActivePage($iNbPages);
		if($iActivePage > 1) {
			$iOffset = (($iActivePage-1) * $this->aConf['NB_MEDIA_PER_PAGE']);
		}
		if(empty($aMedias)) {
			$aMedias = $this->oMediasModel->getAllMedias($iMediaTypeId, DEFAULT_LANG, $iMediaActive, $iOffset);
		} else {
			$aMediasTmp = array();
			$iMediaStart = ($iActivePage-1) * $this->aConf['NB_MEDIA_PER_PAGE'];
			$iMediaStop = $iMediaStart + $this->aConf['NB_MEDIA_PER_PAGE'];
			
			foreach($aMedias as $iKey=>$aValue) {
				if($iKey >= $iMediaStart && $iKey < $iMediaStop) {
					$aMediasTmp[] = $aValue;
				}
			}
			$aMedias = $aMediasTmp;
		}
		$sQueryString = $this->getQueryString($iMediaActive, $bIsSearch, $iMediaTypeId);
		$sMediasBoxes = '';
		$oMediaOperations = new MediasOperations();
		if(!empty($aMedias)) {
			$aElmts = $this->oMediasModel->getElmtsByMediaType($iMediaTypeId);
			$oMediaForms = new MediaForms(
                                        $aElmts, 
                                        $this->getMediasUrl($this->aMediaTypesList[$iMediaTypeId])
                                );
			if(!isset($aMedias[0]['media']) || !is_array($aMedias[0]['media'])) {
				$aMedias = array($aMedias);
			}
			foreach($aMedias as $aMedia) {
				$sMediasBoxes .= $oMediaForms->getBox(
												$this->aMediaTypesList[$iMediaTypeId], 
												$aMedia['elmts'], 
												$iMediaActive
											);
			}
		} else {
			$sMediasBoxes = '{__NO_MEDIA__}';
		}
		return str_replace(
						array(
							'{__MEDIA_MANAGER_TITLE__}',
							'{__MEDIA_TYPE__}',
							'{__MEDIA_TYPES_LIST__}',
							'{__MEDIA_MENU_CONTENTS__}',
							'{__CONFIRM_DELETE_MEDIA__}',
							'{__MEDIAS_BOXES__}',
							'{__PAGING__}'
						), 
						array(
							$sTitle,
							$this->getLabel2Trans($this->aMediaTypesList[$iMediaTypeId]),
							Toolz_Form::optionsList($this->aMediaTypesList[$iMediaTypeId], $this->aMediaTypesList),
							$oMediaOperations->getOpsMenu($this->aMediaTypesList[$iMediaTypeId]),
							$iMediaActive ? '{__CONFIRM_ARCHIVE_MEDIA__}' : '{__CONFIRM_DELETE_MEDIA__}',
							$sMediasBoxes,
							Toolz_Tpl::getPaging($iNbPages, $iActivePage, $sQueryString)
						), 
						file_get_contents($sMediaManagerTpl)
			);
	}
    
    public function checkMedia($sMediaTypeName, array $aMediaProps, $sExt='src') {
        $bIsValid = true;
        //check file extension
        if(isset($aMediaProps[$sExt])) {
            $aExtAllowed = $this->oMediasModel->getExtensionsByElmtTypeName($sMediaTypeName);
            $oFileInfos = new SplFileInfo($aMediaProps[$sExt]);
            if(!in_array(strtolower($oFileInfos->getExtension()), $aExtAllowed) || ($sExt !== 'src' && $sExt !== $oFileInfos->getExtension())) {
				$sMsg = SessionCore::getLangObject()->getMsg('medias', self::INVALID_FILE).' '.$aMediaProps[$sExt];
                UserRequest::$oAlertBoxMgr->danger = $sMsg;
                $bIsValid= false;
            }
            unset($oFileInfos, $aExtAllowed);
        }
        //check url
        if(!empty($aMediaProps['url'])) {
            if(!Toolz_Checker::checkUrl($aMediaProps['url'])) {
				$sMsg = SessionCore::getLangObject()->getMsg('medias', self::INVALID_URL).' '.$aMediaProps['url'];
                UserRequest::$oAlertBoxMgr->danger = $sMsg;
                $bIsValid = false;
            }
        }
        return $bIsValid;
    }
	
	private function formatElmts2Save(array $aElmtsProps, $iMediaId) {
		try {
			$aElmts = array();
			$aElmtsFormated = array();
			foreach($this->oMediasModel->getElmtsByMediaType($aElmtsProps['media_type_id']) as $aValues) {
				$aElmts[$aValues['media_elmt_type_name']] = array(
																'media_elmt_type_id'=>$aValues['media_elmt_type_id'],
																'media_elmt_data_type'=>$aValues['media_elmt_data_type']
															);
			}
			foreach($aElmtsProps as $sKey=>$sValue) {
				if(!empty($aElmts[$sKey]) && !empty($sValue)) {
					// cas des iframe
					if($sKey === 'iframe') {
						$sValue = html_entity_decode($sValue, ENT_HTML5);
					}
					$aElmtFormated = array(
										'media_id' => $iMediaId,
										'media_elmt_type_id' => $aElmts[$sKey]['media_elmt_type_id'], 
										'media_elmt_add_data' => '', 
										'media_elmt' => $sValue
									);
				// que dans le cas d'un update
				} elseif(strpos($sKey, '-current-value') !== false) {
					$sFileKey = str_replace('-current-value', '', $sKey);
					$sFinalValue = '';
					foreach($aElmtsProps as $sTmpKey=>$sTmpValue) {
						if($sTmpKey === $sFileKey) {
							if(empty($sTmpValue) && !empty($sValue)) {
								$sFinalValue = $sValue;
							} else {
								$sMediaTypeName = $this->aMediaTypesList[$aElmtsProps['media_type_id']];
								$sFilePath = $this->sUploadPath.$sMediaTypeName.'/'.$sValue;
								if(file_exists($sFilePath)) {
									unlink($sFilePath);
								}
							}
						}
					}
					if(!empty($sFinalValue)) {
						$aElmtFormated = array(
											'media_id' => $iMediaId,
											'media_elmt_type_id' => $aElmts[$sFileKey]['media_elmt_type_id'], 
											'media_elmt_add_data' => '', 
											'media_elmt' => $sFinalValue
										);
					}
				} elseif(strpos($sKey, '_') !== false) {
					$aKey = explode('_', $sKey);
					$aLangs = TranslationsMgr::getLangAvailableBySide(TranslationsMgr::TRANS_FRONT);
					$sLang = array_pop($aKey);
					$sElmtName = implode('_', $aKey);
					if(isset($aElmts[$sElmtName]) && in_array($sLang, $aLangs)) {
						$aElmtFormated = array(
											'media_id' => $iMediaId,
											'media_elmt_type_id' => $aElmts[$sElmtName]['media_elmt_type_id'], 
											'media_elmt_add_data' => $sLang, 
											'media_elmt' => $sValue
										);
					}
				} else {
					continue;
				}
				if(isset($aElmtFormated) && is_array($aElmtFormated)) {
					$aElmtsFormated[] = $aElmtFormated;
					unset($aElmtFormated);
				}
			}
		} catch(Exception $e) {
			return false;
		}
		return $aElmtsFormated;
	}
	
	private function uploadFiles(&$aMediaProps) {
		try {
			if(($aFiles = UserRequest::getFiles()) === false) {
				return true;
			}
			foreach($aFiles as $sElmtType=>$aFile) {
				$sFileTmp = $aFile['tmp_name'];
				$aMediaProps[$sElmtType] = '';
				if (!empty($sFileTmp)) { 
					$sFileErrorMsg = $aFile['error'];
					$sMediaTypeName = $this->aMediaTypesList[$aMediaProps['media_type_id']];
					if($this->checkMedia($sMediaTypeName, array($sElmtType=>$aFile['name']), $sElmtType)) {
						$aFileTmp = explode('.', $aFile['name']);
						$sExt = array_pop($aFileTmp);
						$sFilename = implode('.', $aFileTmp);
						unset($aFileTmp);
						if(file_exists($this->getMediasPath($sMediaTypeName).$aFile['name'])) {
							$aFile['name'] = $sFilename.'_'.time().'.'.$sExt;
						}
						if(!move_uploaded_file($sFileTmp, $this->getMediasPath($sMediaTypeName).$aFile['name'])){
							UserRequest::$oAlertBoxMgr->danger = $sFileErrorMsg;
							continue;
						}
						if($sMediaTypeName === 'img') {
							$bResize = Toolz_Img::resize(
											$this->getMediasPath($sMediaTypeName).$aFile['name'], 
											(int)$this->aConf['IMG_MAX_SIZE']
										);
							if(!$bResize) {
								UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', self::ERROR_CAN_NOT_RESIZE_IMG);
							}
						}
						$aMediaProps[$sElmtType] = $aFile['name'];
					} else {
						return false;
					}
				}
			}
		} catch(Exception $e) {
			if(DEV) {
				debug($e->getMessage());
				die($e->getTraceAsString());
			}
			return false;
		}
		return true;
	}
	
	private function getExtensionsAllowedHtmlBloc($sMediaTypeName) {
		$aExtensionsAllowed = $this->oMediasModel->getExtensionsByElmtTypeName($sMediaTypeName);
		if(empty($aExtensionsAllowed)) {
			return '';
		} else {
			return Toolz_Tpl::getP(
				'{__EXTENSIONS_ALLOWED__}'.
				implode(', ', $aExtensionsAllowed)
			);
		}
	}
	
	public function getAddMediaForm($iMediaTypeId) {
		if(!$iMediaTypeId) {
			$aMediaTypes = $this->oMediasModel->getMediaTypes();			
			reset($aMediaTypes);
			$sMediaType = key($aMediaTypes);
			$iMediaTypeId = (int)$sMediaType;
		}
		$sAddMediaFormTplPath = ModulesMgr::getFilePath(MediasMgr::$sModuleName, 'backContentsTpl');
		$sAddMediaFormTplPath.= $this->sAddMediaFormTplName;
		$aElmts = $this->oMediasModel->getElmtsByMediaType($iMediaTypeId);
		$oMediaForms = new MediaForms($aElmts, $this->getMediasUrl($this->aMediaTypesList[$iMediaTypeId]));
		$aForm = $oMediaForms->getAddMediaForm();
		return str_replace(
						array(
							'{__MEDIA_TYPE__}',
							'{__EXTENSIONS_ALLOWED_BLOC__}',
							'{__MEDIA_TYPES_LIST__}',
							'{__CONTENTS_FORM__}',
							'{__TRANSLATIONS_FORM__}',
							'{__PREVIEW__}'
						), 
						array(
							$this->getLabel2Trans($this->aMediaTypesList[$iMediaTypeId]),
							$this->getExtensionsAllowedHtmlBloc($this->aMediaTypesList[$iMediaTypeId]),
							Toolz_Form::optionsList($this->aMediaTypesList[$iMediaTypeId], $this->aMediaTypesList),
							$aForm['form'],
							$aForm['translationsForm'],
							$oMediaForms->getPreview($this->aMediaTypesList[$iMediaTypeId])
						), 
						file_get_contents($sAddMediaFormTplPath)
					);
	}
	
	/*
	 * Ajout d'un média par le formulaire dédié par type de média
	 */
	public function addMedia(array $aMediaProps) {
		try {
			Toolz_Checker::checkParams(array(
									'required' => array(
													'media_type_id',
													'media_download_allowed'
												),
									'data'	=> $aMediaProps
								));
			if($this->uploadFiles($aMediaProps)) {
				$iMediaId = $this->oMediasModel->addMedia($aMediaProps);
				$aElmts = $this->formatElmts2Save($aMediaProps, $iMediaId);
				foreach($aElmts as $aElmt) {
					$this->oMediasModel->addMediaElmt($aElmt);
				}
				UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('medias', self::SUCCESS_ADD_MEDIA);
				return $this->getUpdateMediaForm($iMediaId);
			} else {
				return $this->getAddMediaForm($aMediaProps['media_type_id']);
			}
		} catch (Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', self::ERROR_CAN_NOT_ADD_MEDIA);
		}
	}
	
	public function getUpdateMediaForm($iMediaId) {
		$aMediaProps = $this->oMediasModel->getMediasByIds(array($iMediaId));
		$sMediaTypeId = $aMediaProps['media']['media_type_id'];
		$sMediaTypeName = $aMediaProps['media']['media_type_name'];
		$aElmts = $this->oMediasModel->getElmtsByMediaType($sMediaTypeId);
		$oMediaForms = new MediaForms($aElmts, $this->getMediasUrl($sMediaTypeName));
		$sUpdateMediaFormTplPath = ModulesMgr::getFilePath(MediasMgr::$sModuleName, 'backContentsTpl');
		$sUpdateMediaFormTplPath.= $this->sUpdateMediaFormTplName;
		$bDownloadAllowed = (int)$aMediaProps['media']['media_download_allowed'] === 1;
		$aForms = $oMediaForms->getUpdateMediaForm($aMediaProps);
		return str_replace(
						array(
							'{__MEDIA_TYPE__}',
							'{__EXTENSIONS_ALLOWED_BLOC__}',
							'{__MEDIA_TYPE_ID__}',
							'{__CONTENTS_FORM__}',
							'{__TRANSLATIONS_FORM__}',
							'{__CHECKED_NO__}',
							'{__CHECKED_YES__}',
							'{__MEDIA_ID__}',
							'{__DEDICATED_PAGE_FORM__}',
							'{__PREVIEW__}'
						), 
						array(
							$this->getLabel2Trans($this->aMediaTypesList[$sMediaTypeId]),
							$this->getExtensionsAllowedHtmlBloc($this->aMediaTypesList[$sMediaTypeId]),
							$sMediaTypeId,
							$aForms['form'],
							$aForms['translationsForm'],
							$bDownloadAllowed ? '' : 'checked',
							$bDownloadAllowed ? 'checked' : '',
							$iMediaId,
							$oMediaForms->getDedicatedPageForm($iMediaId, $sMediaTypeId),
							$oMediaForms->getPreview($sMediaTypeName, $aMediaProps['elmts'])
						), 
						file_get_contents($sUpdateMediaFormTplPath)
				);
	}
	
	public function updateMedia($aMediaProps) {
		try {
			Toolz_Checker::checkParams(array(
									'required'	=> array(
													'media_id', 
													'media_type_id',
													'media_download_allowed'
												),
									'data'	=> $aMediaProps
								));
			$sMediaTypeName =  $this->oMediasModel->getMediaTypeById($aMediaProps['media_type_id']);
			if(!$this->checkMedia($sMediaTypeName, $aMediaProps)) {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', self::ERROR_CAN_NOT_UPDATE_MEDIA);
			} else {
				$this->oMediasModel->updateMedia($aMediaProps);
				/* TODO
				// Virer les fichiers liés au média
				*/
				$aMediaElmt = $this->oMediasModel->hasPage($aMediaProps['media_id']);
				$this->oMediasModel->deleteMediaElmtsByMediaId($aMediaProps['media_id']);
				$this->uploadFiles($aMediaProps);
				$aElmts = $this->formatElmts2Save($aMediaProps, $aMediaProps['media_id']);
				foreach($aElmts as $aElmt) {
					$this->oMediasModel->addMediaElmt($aElmt);
				}
				// pages dédiées
				if(!empty($aMediaElmt[0]) && !empty($aMediaElmt[0]['media_elmt'])) {
					$sPageName = $aMediaElmt[0]['media_elmt'];
					foreach(TranslationsMgr::getLangAvailableBySide(TranslationsMgr::TRANS_FRONT) as $sLang) {
						$sCachePath = CACHE_PATH.$sPageName.'_'.$sLang.CacheMgr::DEFAULT_CACHE_EXT;
						file_put_contents(
									$sCachePath, 
									$this->buildDedicatedPage($aMediaProps['media_id'], $sLang)
								);
					}
					$aElmt = array(
								'media_id' => $aMediaProps['media_id'],
								'media_elmt_type_id' => 14, 
								'media_elmt_add_data' => '', 
								'media_elmt' => $sPageName
						);
					$this->oMediasModel->addMediaElmt($aElmt);
				}
				UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('medias', self::SUCCESS_UPDATE_MEDIA);
			}
			return $this->getUpdateMediaForm($aMediaProps['media_id']);
		} catch (Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', self::ERROR_CAN_NOT_UPDATE_MEDIA);
			return false;
		}
	}
	
	public function deleteMedia($iMediaId) {
		//suppression du ou des fichiers
		$aMediaTypes = $this->oMediasModel->getMediaTypes();
		$aMediaProps = $this->oMediasModel->getMediasByIds(array($iMediaId));
		$iActive = $aMediaProps['media']['media_active'];
		if($iActive === 0) {
			foreach($aMediaProps['elmts'] as $aElmt) {
				if($aElmt['media_elmt_data_type'] === 'file' || $aElmt['media_elmt_data_type'] === 'system') {
					if(!isset($aMediaTypes[$aMediaProps['media']['media_type_id']])) {
						throw new GenericException('unknow type id '.$aMediaProps['media']['media_type_id']);
					} else {
						$sMediaDirName = $aMediaTypes[$aMediaProps['media']['media_type_id']];
					}
					$sFileName = $aElmt['media_elmt'];
					$sFilePath = $this->sUploadPath.$sMediaDirName.DIRECTORY_SEPARATOR.$sFileName;
					if(file_exists($sFilePath)) {
						unlink($sFilePath);
					}
				}
			}
		}
		if($this->oMediasModel->deleteMedia($iMediaId, $iActive)) {
			$sTransTag = $iActive ? self::SUCCESS_ARCHIVE_MEDIA : self::SUCCESS_DELETE_MEDIA;
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('medias', $sTransTag);
			return true;
		} else {
			$sTransTag = $iActive ? self::ERROR_CAN_NOT_ARCHIVE_MEDIA : self::ERROR_CAN_NOT_DELETE_MEDIA;
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', $sTransTag);
			return false;
		}
	}
	
	public function deleteAllArchivedMedia($iMediaTypeId) {
		foreach($this->oMediasModel->getArchivedMediaIds($iMediaTypeId) as $iMediaId) {
			$this->deleteMedia($iMediaId);
		}
		return true;
	}
	
	public function restoreMedia($iMediaId) {
		if($this->oMediasModel->restoreMedia($iMediaId)) {
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('medias', self::SUCCESS_RESTORE_MEDIA);
			return true;
		} else {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', self::ERROR_CAN_NOT_RESTORE_MEDIA);
			return false;
		}
	}
	
	public function getElmtAvailables($iMediaTypeId) {
		$sElmtsAvailables = $this->oMediasModel->getElmtsByMediaType($iMediaTypeId);
		return explode(',', $sElmtsAvailables);
	}
	
	public function generateThumbnails($iMaxSize) {
		$oMediaOperations = new MediasOperations();
		return $oMediaOperations->generateThumbnails($iMaxSize);
	}
	
	public function generateAllThumbnails() {
		$oMediaOperations = new MediasOperations();
		return $oMediaOperations->generateAllThumbnails();
	}
	
	public function deleteThumbnails($iSize) {
		$oMediaOperations = new MediasOperations();
		return $oMediaOperations->deleteThumbnails($iSize);
	}
	
	private function getLabel2Trans($sMediaType) {
		return str_replace('{__media_type__}', $sMediaType, $this->sMediaType2TransPatern);
	}
	
	public function getMedias2Tpl() {
		// remplace les placeholder des templates pour les caches et autres pages dynamiques !!!!
		// exemple '{__MEDIA_3__}'
	}
	
	private function checkDedicatedPageName($sPageName) {
		$oPagesListMgr = new PagesListMgr();
		$aPagesList = $oPagesListMgr->getPagesList();
		if(isset($aPagesList[$sPageName])) {
			$sMsg = SessionCore::getLangObject()->getMsg('pages_configuration', PageConfig::PAGE_ALREADY_EXISTS);
			$sMsg .= ' ('.$sPageName.')';
			UserRequest::$oAlertBoxMgr->danger = $sMsg;
			return false;
		}
		return true;
	}
	
	private function getPageContents(array $aMedia, $iMediaTypeId, $sPageTplName, $sLang) {
		$aReplace = array();
		$aReplace2Clean = array();
		$aElmts = $this->oMediasModel->getElmtsByMediaType($iMediaTypeId);
		$sMediaUrl = $this->getMediasUrl($this->aMediaTypesList[$iMediaTypeId]);
		foreach($aElmts as $aElmt) {
			foreach($aMedia as $aMediaElmt) {
				if($aMediaElmt['media_elmt_type_name'] === 'url') {
					$sUrl = $aMediaElmt['url'];
				}
				if(!empty($aMediaElmt[$aElmt['media_elmt_type_name']])) {
					if($aMediaElmt['media_elmt_data_type'] === 'file') {
						$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = $sMediaUrl.$aMediaElmt[$aElmt['media_elmt_type_name']];
					} else {
						$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = html_entity_decode($aMediaElmt[$aElmt['media_elmt_type_name']]);
					}
				} elseif(!empty($aMediaElmt[$aElmt['media_elmt_type_name'].'_'.$sLang])) {
					$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = $aMediaElmt[$aElmt['media_elmt_type_name'].'_'.$sLang];
				} else {
					$aReplace2Clean['{__'.$aElmt['media_elmt_type_name'].'__}'] = '';
				}
			}
		}
		$sContents = str_replace(
						array_keys($aReplace), 
						array_values($aReplace), 
						file_get_contents($this->sTplPartsPath.$sPageTplName)
					);
		if(!empty($sUrl)) {
			$aReplace2Clean['{__src__}'] = $sUrl;
		}
		return str_replace(
						array_keys($aReplace2Clean), 
						array_values($aReplace2Clean), 
						$sContents
					);
	}
	
	public function buildDedicatedPage($iMediaId, $sLang) {
		$aMedia = $this->oMediasModel->getMediasByIds(array($iMediaId));
		if(!empty($aMedia)) {
			$sMediaTypeName = $aMedia['media']['media_type_name'];
			$iMediaTypeId = (int)$aMedia['media']['media_type_id'];
		}
		$sPageTplName = str_replace('{__media_type__}', $sMediaTypeName, $this->sPageTplPatern);
		return $this->getPageContents($aMedia['elmts'], $iMediaTypeId, $sPageTplName, $sLang);
	}
	
	public function createDedicatedPage($iMediaId, $sPageName) {
		if(empty($sPageName)) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', PageConfig::ERROR_PAGE_NAME_EMPTY);
			return false;
		}
		if(!$this->checkDedicatedPageName($sPageName)) {
			return false;
		}
		$oPagesListMgr = new PagesListMgr();
		foreach(TranslationsMgr::getLangAvailableBySide(TranslationsMgr::TRANS_FRONT) as $sLang) {
			$sContents = $this->buildDedicatedPage($iMediaId, $sLang);
			$oPagesListMgr->createDedicatedPage($sPageName, $sContents, $sLang);
		}
		$oPagesListMgr->addPage($sPageName);
		unset($oPagesListMgr);
		$aElmt = array(
					'media_id' => $iMediaId,
					'media_elmt_type_id' => 14, 
					'media_elmt_add_data' => '', 
					'media_elmt' => $sPageName
			);
		$this->oMediasModel->addMediaElmt($aElmt);
		return true;
	}
	
	public function updateDedicatedPage($iMediaId, $sPageName, $sCurrentPageName) {
		if(empty($sPageName)) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', PageConfig::ERROR_PAGE_NAME_EMPTY);
			return false;
		}
		if(!$this->checkDedicatedPageName($iMediaId, $sPageName)) {
			return false;
		}
		$oPagesListMgr = new PagesListMgr();
		$aPagesList = $oPagesListMgr->getPagesList();
		$sCurrentPageName = basename($sCurrentPageName, CacheMgr::DEFAULT_CACHE_EXT);
		if(!isset($aPagesList[trim($sCurrentPageName)])) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('pages_configuration', PageConfig::ERROR_PAGE_NOT_EXISTS);
			return false;
		}
		// suupression pure et simple
		$this->deleteDedicatedPage($iMediaId, $sCurrentPageName, false);
		//et on recrée
		$this->createDedicatedPage($iMediaId, $sPageName);
		$oPageConfig = new PageConfig($sCurrentPageName);
		$oPageConfig->updatePage($sCurrentPageName, $sPageName);
		unset($oPageConfig);
		return true;
	}
	
	public function deleteDedicatedPage($iMediaId, $sPageName, $bWithConf=true) {
		try {
			$this->oMediasModel->deleteMediaElmtByElmtTypeIdAndMediaId($iMediaId, 14);
			$oCacheMgr = new CacheMgrFront(SessionCore::getLangObject());
			$oCacheMgr->deleteCache($sPageName);
			$oPagesListMgr = new PagesListMgr();
			$oPagesListMgr->removePage($sPageName);
			unset($oPagesListMgr, $oCacheMgr);
			if($bWithConf) {
				$oPageConfig = new PageConfig();
				$oPageConfig->deletePage($sPageName);
				unset($oPageConfig);
			}
			return true;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
}