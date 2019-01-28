<?php
final class MediaForms extends MediasCommon {
	
	private $aElmtsAvailables				= array();
	private $sMediasUrl						= '';
	private $sMediaControlsTplName			= 'media.controls.tpl';
	private $sMediaArchivedControlsTplName	= 'media.archived.controls.tpl';
	private $sPreviewTplPatern				= 'preview.{__media_type__}.tpl';
	private $sBoxTplPatern					= 'box.{__media_type__}.tpl';
	private $sUpdatePageFormTplName				= 'page.form.update.tpl';
	private $sAddPageFormTplName					= 'page.form.add.tpl';
	
	public function __construct(array $aElmtsAvailables, $sMediasUrl) {
		parent::__construct();
		$this->aElmtsAvailables = $aElmtsAvailables;
		$this->sMediasUrl = $sMediasUrl;
	}
	
	public function getAddMediaForm() {
		$sForm = '';
		$sTranslationsForm = '';
		foreach($this->aElmtsAvailables as $aElmt) {
			$aForm = $this->getAddInputByElmt($aElmt);
			$sForm .= $aForm['form'];
			$sTranslationsForm .= $aForm['translationsForm'];
		}
		return array(
				'form'=>$sForm, 
				'translationsForm'=>$sTranslationsForm
			);
	}
	
	public function getUpdateMediaForm(array $aMediaProps) {
		$sForm = '';
		$sTranslationsForm = '';
		foreach($this->aElmtsAvailables as $aElmt) {
			$bCompleted = false;
			$sInputType = $aElmt['media_elmt_data_type'];
			$sInputName = $aElmt['media_elmt_type_name'];
			foreach($aMediaProps['elmts'] as $aMediaElmt) {
				if(strpos($aElmt['media_elmt_type_name'],$aMediaElmt['media_elmt_type_name']) === 0) {
					$bCompleted = true;
					if($sInputType === 'system') {
						continue;
					} elseif($sInputType === 'locale_text') {
						foreach(TranslationsMgr::getLangAvailableBySide(TranslationsMgr::TRANS_FRONT) as $sLang) {
							$sTmpForm = '';
							$sLangField = '_'.$sLang;
							if(isset($aMediaElmt[$aElmt['media_elmt_type_name'].$sLangField])) {
								$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag($sInputName));
								$sInputValue = $aMediaElmt[$aElmt['media_elmt_type_name'].$sLangField];
								$sTmpForm .= Toolz_Form::label($sInputName.$sLangField.$sTooltip, $sInputName.$sLangField, 'mediaLabel');
								$sTmpForm .= Toolz_Form::input('text', $sInputName.$sLangField, $sInputName.$sLangField, $sInputValue, 'form-control');
								if($sLang === DEFAULT_LANG) {
									$sForm .= $sTmpForm;
								} else {
									$sTranslationsForm .= $sTmpForm;
								}
							}
						}
					} else {
						if(!empty($aMediaElmt[$aElmt['media_elmt_type_name']])) {
							$sInputValue = $aMediaElmt[$aElmt['media_elmt_type_name']];
						} else {
							$sInputValue = '';
						}
						$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag($sInputName));
						$sForm .= Toolz_Form::label($sInputName.$sTooltip, $sInputName, 'mediaLabel');
						if($sInputType === 'textarea') {
							$sForm .= Toolz_Form::textarea($sInputName, $sInputName, html_entity_decode($sInputValue), '', '3');
						} else {
							$sForm .= Toolz_Form::input($sInputType, $sInputName, $sInputName, $sInputValue, 'form-control');
						}
						if($sInputType === 'file' && !empty($sInputValue)) {
							$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag('CURRENT_VALUE'));
							$sLabelValue = $sInputName.' ({__CURRENT_VALUE__}) '.$sTooltip;
							$sForm .= Toolz_Form::label($sLabelValue, $sInputName.'-current-value', 'mediaLabel');
							$sForm .= Toolz_Form::input('text', $sInputName.'-current-value', $sInputName.'-current-value', $sInputValue, 'form-control', 'readonly');
						}
					}
				}
			}
			if(!$bCompleted) {
				$aInputs = $this->getAddInputByElmt($aElmt);
				$sForm .= $aInputs['form'];
				$sTranslationsForm .= $aInputs['translationsForm'];
			}
		}
		return array(
				'form'=>$sForm, 
				'translationsForm'=>$sTranslationsForm
			);
	}
	
	public function getDedicatedPageForm($iMediaId, $iMediaTypeId) {
		$aMediaElmt = $this->oMediasModel->hasPage($iMediaId);
		if(!empty($aMediaElmt[0]) && !empty($aMediaElmt[0]['media_elmt'])) {
			$sPageName = $aMediaElmt[0]['media_elmt'];
		}
		$aReplace = array(
						'{__MEDIA_ID__}' => $iMediaId,
						'{__MEDIA_TYPE_ID__}' => $iMediaTypeId
					);
		if(!empty($sPageName)) {
			$sFormTplName = $this->sUpdatePageFormTplName;
			$sUrl = DEV ? SITE_URL_DEV : SITE_URL_PROD;
			$sUrl .= '/'.DEFAULT_LANG.'/'.$sPageName.  CacheMgr::DEFAULT_CACHE_EXT;
			$aReplace['{__MEDIA_PAGE_LINK__}'] = $sUrl;
		} else {
			$sFormTplName = $this->sAddPageFormTplName;
		}
		return str_replace(
						array_keys($aReplace), 
						array_values($aReplace), 
						file_get_contents($this->sTplPartsPath.$sFormTplName)
						);
	}
	
	private function getAddInputByElmt(array $aElmt) {
		$sInput = '';
		$sTranslationsInput = '';
		$sInputValue = '';
		$sInputName = $aElmt['media_elmt_type_name'];
		$sInputType = $aElmt['media_elmt_data_type'];
		$sTooltip = Toolz_Tpl::getToolTip(Toolz_Tpl::getToolTipTag($sInputName));
		if($sInputType === 'system') {
			//do nothing
		} elseif($sInputType === 'locale_text') {
			foreach(TranslationsMgr::getLangAvailableBySide(TranslationsMgr::TRANS_FRONT) as $sLang) {
				$sTmpInput = '';
				$sLangField = '_'.$sLang;
				$sTmpInput .= Toolz_Form::label($sInputName.$sLangField.$sTooltip, $sInputName.$sLangField, 'media-label');
				$sTmpInput .= Toolz_Form::input('text', $sInputName.$sLangField, $sInputName.$sLangField, $sInputValue, 'form-control');
				if($sLang === DEFAULT_LANG) {
					$sInput .= $sTmpInput;
				} else {
					$sTranslationsInput .= $sTmpInput;
				}
			}
		} else {
			$sInput .= Toolz_Form::label($sInputName.$sTooltip, $sInputName, 'media-label');
			if($sInputType === 'textarea') {
				$sInput .= Toolz_Form::textarea($sInputName, $sInputName, html_entity_decode($sInputValue), 'form-control', '3');
			} else {
				$sInput .= Toolz_Form::input($sInputType, $sInputName, $sInputName, $sInputValue, 'form-control');
			}
		}
		return array(
				'form'=>$sInput, 
				'translationsForm'=>$sTranslationsInput
			);
	}
	
	public function getPreview($sMediaTypeName, array $aMediaProps=array()) {
		$sPreviewTplName = str_replace('{__media_type__}', $sMediaTypeName, $this->sPreviewTplPatern);
		$sPreviewTplPath = $this->sTplPartsPath.$sPreviewTplName;
		$aReplace = array();
		$aReplace2Clean = array();
		foreach($this->aElmtsAvailables as $aElmt) {
			foreach($aMediaProps as $aMediaElmt) {
				if($aMediaElmt['media_elmt_type_name'] === 'url') {
					$sUrl = $aMediaElmt['url'];
				}
				if(!empty($aMediaElmt[$aElmt['media_elmt_type_name']])) {
					if($aMediaElmt['media_elmt_data_type'] === 'file') {
						$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = $aMediaElmt[$aElmt['media_elmt_type_name']];
					} else {
						$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = html_entity_decode($aMediaElmt[$aElmt['media_elmt_type_name']]);
					}
				} elseif(!empty($aMediaElmt[$aElmt['media_elmt_type_name'].'_'.DEFAULT_LANG])) {
					$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = $aMediaElmt[$aElmt['media_elmt_type_name'].'_'.DEFAULT_LANG];
				} else {
					$aReplace2Clean['{__'.$aElmt['media_elmt_type_name'].'__}'] = '';
				}
			}
		}
		$aReplace['{__PATH__}'] = $this->sMediasUrl;
		$sPreview =  str_replace(
					array_keys($aReplace), 
					array_values($aReplace), 
					file_get_contents($sPreviewTplPath)
				);
		if(!empty($sUrl)) {
			$sPreview = str_replace('{__src__}', $sUrl, $sPreview);
		}
		return str_replace(
						array_keys($aReplace2Clean), 
						array_values($aReplace2Clean), 
						$sPreview
					);
	}
	
	public function getBox($sMediaTypeName, array $aMediaProps, $bActive) {
		$sControlsTplName = $bActive ? $this->sMediaControlsTplName : $this->sMediaArchivedControlsTplName;
		$sControlsTpl = file_get_contents($this->sTplPartsPath.$sControlsTplName);
		$sBoxTplName = str_replace('{__media_type__}', $sMediaTypeName, $this->sBoxTplPatern);
		$sBoxTplPath = $this->sTplPartsPath.$sBoxTplName;
		$aReplace = array();
		$aReplace2Clean = array();
		$sControls = '';
		foreach($aMediaProps as $aMediaElmt) {
			if(!empty($aMediaElmt['media_id'])) {
				$sControls = str_replace('{__media_id__}', $aMediaElmt['media_id'], $sControlsTpl);
			} else {
				throw new GenericException('missing media id');
			}
			foreach($this->aElmtsAvailables as $aElmt) {
				$sThumbUrl = '';
				if($aMediaElmt['media_elmt_type_name'] === 'thumb') {
					if((int)$aMediaElmt['media_elmt_add_data'] <= 300 && (int)$aMediaElmt['media_elmt_add_data'] >= 150) {
						$sThumbUrl = $this->sMediasUrl.'thumb/'.$aMediaElmt['media_elmt_add_data'].'/';
						$aReplace['{__PATH__}'] = $sThumbUrl;
						continue;
					}
				}
				if($aMediaElmt['media_elmt_type_name'] === 'url') {
					$sUrl = $aMediaElmt['url'];
				}
				if(!empty($aMediaElmt[$aElmt['media_elmt_type_name']])) {
					if($aMediaElmt['media_elmt_data_type'] === 'file' || $aMediaElmt['media_elmt_data_type'] === 'system') {
						$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = $aMediaElmt[$aElmt['media_elmt_type_name']];
						$sUrl = $aMediaElmt[$aElmt['media_elmt_type_name']];
					} else {
						$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = html_entity_decode($aMediaElmt[$aElmt['media_elmt_type_name']]);
					}
				} elseif(!empty($aMediaElmt[$aElmt['media_elmt_type_name'].'_'.DEFAULT_LANG])) {
					$aReplace['{__'.$aElmt['media_elmt_type_name'].'__}'] = $aMediaElmt[$aElmt['media_elmt_type_name'].'_'.DEFAULT_LANG];
				} else {
					$aReplace2Clean['{__'.$aElmt['media_elmt_type_name'].'__}'] = '';
				}
			}
		}
		$aReplace2Clean['{__media_id__}'] = $aMediaElmt['media_id'];
		if(empty($aReplace['{__PATH__}'])) {
			$aReplace['{__PATH__}'] = $this->sMediasUrl;
		}
		$sPreview = str_replace(
						array_keys($aReplace), 
						array_values($aReplace), 
						file_get_contents($sBoxTplPath)
					);
		if(!empty($sUrl)) {
			$sPreview = str_replace('{__src__}', $sUrl, $sPreview);
		}
		// clean template
		$sPreview = str_replace(
						array_keys($aReplace2Clean), 
						array_values($aReplace2Clean), 
						$sPreview
					);
		return str_replace('{__CONTROLS__}', $sControls, $sPreview);
	}
}