<?php
final class MediasOperations extends MediasCommon {
	
	private $sOpsMenuTplPatern				= 'ops.{__media_type__}.menu.tpl';
	private $sEditThumbnailsFormTplName		= 'edit.thumbnails.form.tpl';
	const SUCCESS_GENERATE_THUMBNAILS		= 'SUCCESS_GENERATE_THUMBNAILS';
	const ERROR_GENERATE_THUMBNAILS			= 'ERROR_GENERATE_THUMBNAILS';
	const SUCCESS_DELETE_THUMBNAILS			= 'SUCCESS_DELETE_THUMBNAILS';
	const ERROR_DELETE_THUMBNAILS			= 'ERROR_DELETE_THUMBNAILS';
	const NUMBER_THUMBNAILS_GENERATED		= 'NUMBER_THUMBNAILS_GENERATED';
	
	public function __construct() {
		parent::__construct();
	}
	
	public function getOpsMenu($sMediaTypeName) {
		$sOpsMenuTplName = str_replace('{__media_type__}', $sMediaTypeName, $this->sOpsMenuTplPatern);
		$sOpsMenuTpl = file_get_contents($this->sTplPartsPath.$sOpsMenuTplName);
		// Factory 
		switch($sMediaTypeName) {
			case 'img' :
				$sThumbnails = '';
				$aThumbnails = $this->getThumbnailsSizesAvailable();
				if(!empty($aThumbnails)) {
					$aNbMediasByType = $this->oMediasModel->getNbMediasByType();
					$iNbImg = 0;
					foreach($aNbMediasByType as $aMedia) {
						if($aMedia['media_type_name'] === 'img') {
							$iNbImg = (int)$aMedia['media_types'];
						}
					}
					$sEditFormTplPath = ModulesMgr::getFilePath(MediasMgr::$sModuleName, 'backPartsTpl');
					$sEditFormTplPath .= $this->sEditThumbnailsFormTplName;
					$sEditThumbnailsFormTpl = file_get_contents($sEditFormTplPath);
					foreach($aThumbnails as $sSize=>$sNbThumb) {
						$sCompletion = $sNbThumb.' / '.$iNbImg;
						$sThumbnails .= str_replace(
												array(
													'{__COMPLETE_CLASS__}',
													'{__THUMBNAILS_SIZE__}',
													'{__COMPLETION_PERCENT__}'
												), 
												array(
													$sNbThumb === $iNbImg ? 'text-success' : 'text-danger',
													$sSize,
													$sCompletion
												), 
												$sEditThumbnailsFormTpl
											);
					}
				}
				$sOpsMenuTpl = str_replace('{__THUMBNAILS_AVAILABLE__}', $sThumbnails, $sOpsMenuTpl);
				break;
			case 'audio':
				
				break;
			case 'video':
				
				break;
		}
		return str_replace(
					'{__SEARCH_FORM__}', 
					$this->getSearchMediaForm($sMediaTypeName), 
					$sOpsMenuTpl
				);
	}
	
	public function generateThumbnails($iSize) {
		try{
			//benchmark
			UserRequest::startBenchmark('generateThumbnails');
			//initialisation
			$iNbThumbnailsGenerate = 0;
			$sSourcePath = $this->sUploadPath.'img/';
			$sThumbnailsPath = $this->sUploadPath.'img/thumb/';
			if(!file_exists($sThumbnailsPath) || !is_dir($sThumbnailsPath)) {
				mkdir($sThumbnailsPath);
			}
			$sThumbnailsPath .= $iSize.DIRECTORY_SEPARATOR;
			//création sinon du dossier pour cette taille dans /médias/img/thumb/ ou purge si le dossier est déjà existant
			if(!file_exists($sThumbnailsPath) || !is_dir($sThumbnailsPath)) {
				mkdir($sThumbnailsPath);
			} else {
				// purge des fichiers
				Toolz_FileSystem::purgeDir($sThumbnailsPath);
				//unlink($sThumbnailsPath);
				// suppression en base de données
				$this->oMediasModel->deleteThumbnails($iSize);
			}
			//même nom de fichier pour les vignettes ex: /médias/img/thumb/300x300/test_img.jpg
			$aMedias = $this->oMediasModel->getAllMedias(1, DEFAULT_LANG, 1, 0, false);
			if(isset($aMedias['media'])) {
				$aMedias = array($aMedias);
			} elseif(empty($aMedias)) {
				return array();
			}
			$bSuccess = true;
			foreach($aMedias as $aMedia) {
				foreach($aMedia['elmts'] as $aElmts) {
					if(isset($aElmts['src'])) {
						try {
							$bResize = Toolz_Img::resize(
											$sSourcePath.$aElmts['src'], 
											(int)$iSize, 
											'', 
											$sThumbnailsPath.$aElmts['src'],
											(int)$this->aConf['IMG_THUMB_QUALITY']
										);
						} catch (Toolz_Img_Exception $e) {
							UserRequest::$oAlertBoxMgr->danger = $e->getMessage();
							$bSuccess = false;
							continue;
						}
						if(isset($bResize) && $bResize === true) {
							// insertion en base de données
							$aElmtInfos = array(
											'media_id'				=> $aMedia['media']['media_id'],
											'media_elmt_type_id'	=> MediasMgr::THUMB_ELMT_ID, 
											'media_elmt_add_data'	=> $iSize, 
											'media_elmt'			=> $aElmts['src']
										);
							$this->oMediasModel->addMediaElmt($aElmtInfos);
						}
					}
				}
				$iNbThumbnailsGenerate++;
			}
			if($bSuccess) {
				UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('medias', self::SUCCESS_GENERATE_THUMBNAILS).' ('.$iSize.'px)';
				$sMsg = SessionCore::getLangObject()->getMsg('medias', self::NUMBER_THUMBNAILS_GENERATED).$iNbThumbnailsGenerate;
				$sMsg .= ' ('.UserRequest::stopBenchmark('generateThumbnails', false).'s)';
				UserRequest::$oAlertBoxMgr->success = $sMsg;
				return $iNbThumbnailsGenerate;
			} else {
				UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', self::ERROR_GENERATE_THUMBNAILS).' ('.$iSize.'px)';
				return false;
			}
		} catch(Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', self::ERROR_GENERATE_THUMBNAILS).' ('.$iSize.'px)';
		}
	}
	
	public function generateAllThumbnails() {
		try {
			$iNbThumbnailsGenerate = 0;
			$aThumbnails = $this->getThumbnailsSizesAvailable();
			foreach($aThumbnails as $sSize=>$sNbThumb) {
				if(($iNbThumbnails = $this->generateThumbnails($sSize)) !== false) {
					$iNbThumbnailsGenerate += $iNbThumbnails;
				}
			}
			$sMsg = SessionCore::getLangObject()->getMsg('medias', self::NUMBER_THUMBNAILS_GENERATED).$iNbThumbnailsGenerate;
			$sMsg .= ' ('.UserRequest::stopBenchmark('generateAllThumbnails').'s)';
			UserRequest::$oAlertBoxMgr->success = $sMsg;
		} catch (Exception $e) {
			echo $e->getMessage();
			echo $e->getTraceAsString();
			return false;
		}
		return true;
	}
	
	public function deleteThumbnails($iSize) {
		try {
			$sThumbnailsPath = $this->sUploadPath.'img/thumb/'.$iSize.DIRECTORY_SEPARATOR;
			// purge des fichiers
			Toolz_FileSystem::purgeDir($sThumbnailsPath);
			//unlink($sThumbnailsPath);
			// suppression en base de données
			$this->oMediasModel->deleteThumbnails($iSize);
			UserRequest::$oAlertBoxMgr->success = SessionCore::getLangObject()->getMsg('medias', self::SUCCESS_DELETE_THUMBNAILS).' ('.$iSize.'px)';
			return true;
		} catch(Exception $e) {
			UserRequest::$oAlertBoxMgr->danger = SessionCore::getLangObject()->getMsg('medias', self::ERROR_DELETE_THUMBNAILS).' ('.$iSize.'px)';
		}
	}
	
	public function getThumbnailsSizesAvailable() {
		$aThumbnailsAvailable = $this->oMediasModel->getThumbnailsAvailables();
		$aReturn = array();
		foreach($aThumbnailsAvailable as $aMediaElmt) {
			$aMediaElmt['media_elmt_add_data'] = trim($aMediaElmt['media_elmt_add_data']);
			if(!isset($aReturn[$aMediaElmt['media_elmt_add_data']])) {
				$aReturn[$aMediaElmt['media_elmt_add_data']] = 1;
			} else {
				$aReturn[$aMediaElmt['media_elmt_add_data']]++;
			}
		}
		ksort($aReturn);
		return $aReturn;
	}
	
	public function buildCache(&$sCache) {
		return true;
	}
}