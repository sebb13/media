<?php
final class MediasModel {
	
	private $oPdo								= NULL;
	private $sMediasTableName					= 't_medias';
	private $sMediaElmtsTableName				= 't_media_elmts';
	private $sMediaTypesTableName				= 'tr_media_types';
	private $sMediaElmtTypesTableName			= 'tr_media_elmt_types';
	private $sMediaElmtDataTypesTableName		= 'tr_media_elmt_data_types';
	private $sMediaExtensions					= 'tr_medias_extensions';
	private $sTjElmtTypesAvailablesTableName	= 'tj_elmt_type_availables';
	private $sTjMediasTypesExtensions			= 'tj_medias_types_extensions';
    private $aMediaTypes                        = array();
    private $aExtentionsByElmtType              = array();
	private $aMediasFormated					= array();
	private $aNbMediasByType					= array();
	private $iNbMediaPerPage					= 25;
	
	public function __construct($iNbMediaPerPage=10) {
		$this->oPdo = SPDO::getInstance();
		$this->iNbMediaPerPage = (int)$iNbMediaPerPage;
	}
	
	public function searchMedia($sKeyword, $iMediaTypeId, $sLang='') {
		if(empty($sLang)) {
			$sWhereComplete = '';
		} else {
			$sWhereComplete = ' AND media_elmts.media_elmt_add_data = :media_elmt_add_data';
		}
		$sQuery = 'SELECT
						media_elmts.media_id,
						medias.media_type_id
					FROM '.$this->sMediaElmtsTableName.' media_elmts 
					LEFT JOIN '.$this->sMediasTableName.' medias
						ON media_elmts.media_id = medias.media_id
					WHERE (media_elmts.media_elmt like CONCAT(\'%\', :keyword, \'%\') 
					OR media_elmts.media_id like CONCAT(\'%\', :keyword, \'%\')) 
					AND medias.media_type_id = :media_type_id
					AND medias.media_active=1
					'.$sWhereComplete;
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':media_type_id', $iMediaTypeId, PDO::PARAM_INT);
		$oQuery->bindParam(':keyword', $sKeyword, PDO::PARAM_STR);
		if(!empty($sLang)) {
			$oQuery->bindParam(':media_elmt_add_data', $sLang, PDO::PARAM_STR);
		}
		$oQuery->execute();
		$aMediasIds = $oQuery->fetchAll(PDO::FETCH_COLUMN, 0);
		if(empty($aMediasIds)) {
			return array();
		}
		return $this->getMediasByIds($aMediasIds, $sLang);
	}
	
	public function getMediasByIds(array $aMediasIds, $sLang='') {
		$aParams = $aClause = array();
		foreach($aMediasIds as $sKey=>$iId){
			$aClause[] = ':id'.$sKey;
			$aParams[':id'.$sKey] = $iId;
		}
		$sQuery = 'SELECT
						medias.media_id,
						medias.media_type_id,
						medias.media_tags,
						medias.media_date_add,
						medias.media_download_allowed,
						medias.media_active,
						mediaTypes.media_type_name
					FROM '.$this->sMediasTableName.' medias 
					LEFT JOIN '.$this->sMediaTypesTableName.' mediaTypes
						ON medias.media_type_id = mediaTypes.media_type_id
					WHERE media_id in ('.implode(',', $aClause).')
					AND media_active=1';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->execute($aParams);
		$aMedias = $oQuery->fetchAll(PDO::FETCH_ASSOC);
		if(count($aMedias) === 0) {
			return false;
		} else {
			return $this->attachElmts($aMedias, $sLang);
		}
	}
	
	public function getAllMedias($iMediaTypeId, $sLang='', $iActive=1, $iOffset=0, $bLimit=true) {
		try {
			$sQuery = 'SELECT
							medias.media_id,
							medias.media_type_id,
							medias.media_tags,
							medias.media_date_add,
							medias.media_download_allowed,
							mediaTypes.media_type_name
						FROM '.$this->sMediasTableName.' medias
						LEFT JOIN '.$this->sMediaTypesTableName.' mediaTypes
							ON medias.media_type_id = mediaTypes.media_type_id
						WHERE medias.media_type_id = :media_type_id
						AND media_active = :media_active 
						ORDER BY medias.media_date_add DESC ';
			if($bLimit) {
				$sQuery .= 'LIMIT '.$this->iNbMediaPerPage.' OFFSET :offset'; 
			}
			$oQuery = $this->oPdo->prepare($sQuery);
			$oQuery->bindParam(':media_type_id', $iMediaTypeId, PDO::PARAM_INT);
			$oQuery->bindParam(':media_active', $iActive, PDO::PARAM_INT);
			if($bLimit) {
				$oQuery->bindParam(':offset', $iOffset, PDO::PARAM_INT);
			}
			$oQuery->execute();
			$aMedias = $oQuery->fetchAll(PDO::FETCH_ASSOC);
			if(count($aMedias) === 0) {
				return false;
			} else {
				return $this->attachElmts($aMedias, $sLang);
			}
		} catch (Exception $e) {
			echo $e;
		}
	}
	
	private function attachElmts(array $aMedias, $sLang='') {
		$iKey = -1;
		$this->aMediasFormated = array();
		foreach($aMedias as $aMedia) {
			$this->aMediasFormated[++$iKey] = array('media'=>$aMedia, 'elmts'=>array());
			$aElmts = $this->getMediaElmts($aMedia['media_id'], $sLang);
			if(empty($aElmts)) {
				continue;
			}
			$aElmtAllowed = array();
			foreach($this->getElmtsByMediaType($aMedia['media_type_id']) as $aValues) {
				$aElmtAllowed[$aValues['media_elmt_type_name']] = array(
																'media_elmt_type_id'=>$aValues['media_elmt_type_id'],
																'media_elmt_data_type'=>$aValues['media_elmt_data_type']
															);
			}
			foreach($aElmts as $aElmt) {
				
				if($aElmt['media_elmt_data_type'] === 'locale_text') {
					$sKey = $aElmt['media_elmt_type_name'].'_'.$aElmt['media_elmt_add_data'];
					$aElmtFormated = array(
										$sKey => $aElmt['media_elmt'],
										'media_elmt' => $aElmt['media_elmt'], 
										'media_elmt_type_id' => $aElmtAllowed[$aElmt['media_elmt_type_name']]['media_elmt_type_id'], 
										'media_elmt_type_name' => $aElmt['media_elmt_type_name'],
										'media_elmt_add_data' => $aElmt['media_elmt_add_data'],
										'media_elmt_data_type' => $aElmt['media_elmt_data_type'],
										'media_id' => $aMedia['media_id']
									);
				} elseif(isset($aElmtAllowed[$aElmt['media_elmt_type_name']])) {
					$aElmtFormated = array(
										$aElmt['media_elmt_type_name'] => $aElmt['media_elmt'],
										'media_elmt' => $aElmt['media_elmt'], 
										'media_elmt_type_id' => $aElmtAllowed[$aElmt['media_elmt_type_name']]['media_elmt_type_id'], 
										'media_elmt_type_name' => $aElmt['media_elmt_type_name'],
										'media_elmt_add_data' => $aElmt['media_elmt_add_data'],
										'media_elmt_data_type' => $aElmt['media_elmt_data_type'],
										'media_id' => $aMedia['media_id']
									);
				} else {
					continue;
				}
				$this->aMediasFormated[$iKey]['elmts'][] = $aElmtFormated;
			}
		}
		return count($this->aMediasFormated) === 1 ? $this->aMediasFormated[0] : $this->aMediasFormated;
	}
	
	public function getMediaElmts($iMediaId) {
		$sQuery = 'SELECT
						media_elmts.media_elmt,
						media_elmts.media_elmt_add_data, 
						media_elmts.media_elmt_type_id,
						media_elmt_types.media_elmt_type_name, 
						trdt.media_elmt_data_type
					FROM '.$this->sMediaElmtsTableName.' media_elmts 
					LEFT JOIN '.$this->sMediaElmtTypesTableName.' media_elmt_types 
						ON media_elmt_types.media_elmt_type_id = media_elmts.media_elmt_type_id
					LEFT JOIN '.$this->sMediaElmtDataTypesTableName.' trdt 
						ON trdt.media_elmt_data_type_id = media_elmt_types.media_elmt_data_type_id
					WHERE media_elmts.media_id = :media_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':media_id', $iMediaId, PDO::PARAM_INT);
		$oQuery->execute();
		return $oQuery->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function hasPage($iMediaId) {
		$sQuery = 'SELECT
						media_elmts.media_elmt
					FROM '.$this->sMediaElmtsTableName.' media_elmts 
					WHERE media_elmts.media_id = :media_id
					AND media_elmts.media_elmt_type_id = 14';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':media_id', $iMediaId, PDO::PARAM_INT);
		$oQuery->execute();
		return $oQuery->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function addMedia(array $aMediaProps) {
		try {
			Toolz_Checker::checkParams(array(
									'required'	=> array(
													'media_type_id',
													'media_download_allowed'
												),
									'data'	=> $aMediaProps
								));
			if(empty($aMediaProps['media_tags'])) {
				$aMediaProps['media_tags'] = '';
			}
			$sQuery = 'INSERT INTO '.$this->sMediasTableName.' (
						media_type_id,
						media_download_allowed,
						media_tags
					) VALUES (
						:media_type_id, 
						:media_download_allowed, 
						:media_tags
					)';
			$oQuery = $this->oPdo->prepare($sQuery);
			$oQuery->bindParam(':media_type_id', $aMediaProps['media_type_id'], PDO::PARAM_INT);
			$oQuery->bindParam(':media_download_allowed', $aMediaProps['media_download_allowed'], PDO::PARAM_STR);
			$oQuery->bindParam(':media_tags', $aMediaProps['media_tags'], PDO::PARAM_INT);
			$oQuery->execute();
			return $this->oPdo->lastInsertId();
		} catch (Exception $e) {
			throw new GenericException($e->getMessage());
		}
	}
	
	public function addMediaElmt(array $aElmtInfos) {
		try {
			Toolz_Checker::checkParams(array(
									'required'	=> array(
													'media_id',
													'media_elmt_type_id', 
													'media_elmt_add_data', 
													'media_elmt', 
												),
									'data'	=> $aElmtInfos
								));
			$sQuery = 'INSERT INTO '.$this->sMediaElmtsTableName.' (
							media_id,
							media_elmt_type_id,
							media_elmt_add_data,
							media_elmt
						) VALUES (
							:media_id,
							:media_elmt_type_id,
							:media_elmt_add_data,
							:media_elmt
						)';
			$oQuery = $this->oPdo->prepare($sQuery);
			$oQuery->bindParam(':media_id', $aElmtInfos['media_id'], PDO::PARAM_INT);
			$oQuery->bindParam(':media_elmt_type_id', $aElmtInfos['media_elmt_type_id'], PDO::PARAM_INT);
			$oQuery->bindParam(':media_elmt_add_data', $aElmtInfos['media_elmt_add_data'], PDO::PARAM_STR);
			$oQuery->bindParam(':media_elmt', $aElmtInfos['media_elmt'], PDO::PARAM_STR);
			return $oQuery->execute();
		} catch (Exception $e) {
			throw new GenericException($e->getMessage());
		}
	}
	
	public function updateMedia(array $aMediaProps) {
		try {
			$sQuery = 'UPDATE '.$this->sMediasTableName.' SET 
							media_download_allowed = :media_download_allowed
						WHERE media_id = :media_id';
			$oQuery = $this->oPdo->prepare($sQuery);
			$oQuery->bindParam(':media_download_allowed', $aMediaProps['media_download_allowed'], PDO::PARAM_INT);
			$oQuery->bindParam(':media_id', $aMediaProps['media_id'], PDO::PARAM_INT);
			return $oQuery->execute();
		} catch (Exception $e) {
			throw new GenericException($e->getMessage());
		}
	}
	
	public function deleteMedia($iMediaId, $bIsActive=true) {
		if($bIsActive) {
			return $this->archiveMedia($iMediaId);
		} else {
			try {
				$this->oPdo->beginTransaction();
				// le média
				$sQuery = 'DELETE FROM '.$this->sMediasTableName.' 
						WHERE media_id = :media_id';
				$oQuery = $this->oPdo->prepare($sQuery);
				$oQuery->bindParam(':media_id', $iMediaId, PDO::PARAM_INT);
				$oQuery->execute();
				// ses infos
				if(!$this->deleteMediaElmtsByMediaId($iMediaId)) {
					throw new GenericException('Unable to delete media elmts');
				}
				return $this->oPdo->commit();
			} catch (PDOException $e) {
				$this->oPdo->rollBack();
				return false;
			} catch (Exception $e) {
				return false;
			}
		}
	}
	
	private function archiveMedia($iMediaId, $iMediaActive=0) {
		try {
			$sQuery = 'UPDATE '.$this->sMediasTableName.' SET 
							media_active = :media_active
						WHERE media_id = :media_id';
			$oQuery = $this->oPdo->prepare($sQuery);
			$oQuery->bindParam(':media_active', $iMediaActive, PDO::PARAM_INT);
			$oQuery->bindParam(':media_id', $iMediaId, PDO::PARAM_INT);
			return $oQuery->execute();
		} catch (Exception $e) {
			throw new GenericException($e->getMessage());
		}
	}
	
	public function restoreMedia($iMediaId) {
		return $this->archiveMedia($iMediaId, 1);
	}
	
	public function getMediaTypes() {
        if(empty($this->aMediaTypes)) {
            $sQuery = 'SELECT
                            media_type_id, 
                            media_type_name
                        FROM '.$this->sMediaTypesTableName;
            $oQuery = $this->oPdo->query($sQuery);
            $oQuery->execute();
			foreach($oQuery->fetchAll(PDO::FETCH_ASSOC) as $aMediaType) {
				$this->aMediaTypes[$aMediaType['media_type_id']] = $aMediaType['media_type_name'];
			}
        }
        return $this->aMediaTypes;
	}
	
	public function getMediaTypeById($iMediaTypeId) {
        if(empty($this->aMediaTypes)) {
				$this->getMediaTypes();
        }
        return isset($this->aMediaTypes[$iMediaTypeId]) ? $this->aMediaTypes[$iMediaTypeId] : false;
	}
	
	public function deleteMediaElmtsByMediaId($iMediaId) {
		$sQuery = 'DELETE FROM '.$this->sMediaElmtsTableName.' 
					WHERE media_id = :media_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':media_id', $iMediaId, PDO::PARAM_INT);
		return $oQuery->execute();
	}
	
	public function deleteMediaElmtByElmtId($iElmtId) {
		$sQuery = 'DELETE FROM '.$this->sMediaElmtsTableName.' 
					WHERE media_elmts_id = :media_elmts_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':media_elmts_id', $iElmtId, PDO::PARAM_INT);
		return $oQuery->execute();
	}
	
	public function deleteMediaElmtByElmtTypeIdAndMediaId($iMediaId, $iElmtTypeId) {
		$sQuery = 'DELETE FROM '.$this->sMediaElmtsTableName.' 
					WHERE media_elmt_type_id = :media_elmt_type_id
					AND media_id = :media_id';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':media_elmt_type_id', $iElmtTypeId, PDO::PARAM_INT);
		$oQuery->bindParam(':media_id', $iMediaId, PDO::PARAM_INT);
		return $oQuery->execute();
	}
	
	public function getElmtsByMediaType($iMediaTypeId) {
		$sQuery = 'SELECT 
						tre.media_elmt_type_id,
						tre.media_elmt_type_name,
						trdt.media_elmt_data_type
					FROM '.$this->sMediaElmtTypesTableName.' tre
					LEFT JOIN '.$this->sTjElmtTypesAvailablesTableName.' tja
						ON tja.media_elmt_type_id = tre.media_elmt_type_id
					LEFT JOIN '.$this->sMediaElmtDataTypesTableName.' trdt 
						ON trdt.media_elmt_data_type_id = tre.media_elmt_data_type_id
					WHERE tja.media_type_id = :media_type_id
					ORDER BY tre.media_elmt_data_type_id, tre.media_elmt_type_name';
		$oQuery = $this->oPdo->prepare($sQuery);
		$oQuery->bindParam(':media_type_id', $iMediaTypeId, PDO::PARAM_INT);
		$oQuery->execute();
		return $oQuery->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function getNbMediasByType($iActive=1) {
			$sQuery = 'SELECT mediatypes.media_type_name, COUNT(medias.media_type_id) as media_types
						FROM t_medias medias
						LEFT JOIN tr_media_types mediatypes
							ON mediatypes.media_type_id = medias.media_type_id
						WHERE medias.media_active=:media_active
						GROUP BY medias.media_type_id';
			$oQuery = $this->oPdo->prepare($sQuery);
			$oQuery->bindParam(':media_active', $iActive, PDO::PARAM_INT);
			$oQuery->execute();
			$this->aNbMediasByType = $oQuery->fetchAll(PDO::FETCH_ASSOC);
		return $this->aNbMediasByType;
	}
	
	public function getExtensionsByElmtTypeName($sType='') {
        if(empty($this->aExtentionsByElmtType)) {
            $sQuery = 'SELECT mediaType.media_type_name, ext.media_extension
                        FROM '.$this->sTjMediasTypesExtensions.' tjExt
                        LEFT JOIN '.$this->sMediaTypesTableName.' mediaType
                            ON mediaType.media_type_id = tjExt.media_type_id
                        LEFT JOIN '.$this->sMediaExtensions.' ext
                            ON ext.media_extension_id = tjExt.media_extension_id';
            $oQuery = $this->oPdo->query($sQuery);
            $oQuery->execute();
            $this->aExtentionsByElmtType = $oQuery->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_GROUP);
        }
        if(!empty($sType)) {
            if(!empty($this->aExtentionsByElmtType[$sType])) {
                return $this->aExtentionsByElmtType[$sType];
            } else {
                return false;
            }
        } else {
            return $this->aExtentionsByElmtType;
        }
	}
	
	public function getThumbnailsAvailables() {
		try {
			$sQuery = 'SELECT 
						mediaElmts.media_elmts_id, 
						mediaElmts.media_elmt_add_data, 
						mediaElmts.media_elmt,
						mediaElmtType.media_elmt_type_name
					FROM '.$this->sMediaElmtsTableName.' mediaElmts
					LEFT JOIN '.$this->sMediaElmtTypesTableName.' mediaElmtType
						ON mediaElmts.media_elmt_type_id = mediaElmtType.media_elmt_type_id
					WHERE mediaElmtType.media_elmt_type_name = :media_elmt_type_name';
			$oQuery = $this->oPdo->prepare($sQuery);
			$sElmtType = 'thumb';
			$oQuery->bindParam(':media_elmt_type_name', $sElmtType, PDO::PARAM_STR);
			$oQuery->execute();
			return $oQuery->fetchAll(PDO::FETCH_ASSOC);
		} catch(Exception $e) {
			throw new GenericException($e->getMessage());
		}
	}
	
	public function deleteThumbnails($iSize) {
		try {
			$aMediaElmts = $this->getThumbnailsAvailables();
			foreach($aMediaElmts as $aMediaElmt) {
				if($aMediaElmt['media_elmt_add_data'] === (string)$iSize) {
					$this->deleteMediaElmtByElmtId($aMediaElmt['media_elmts_id']);
				}
			}
			return true;
		} catch(Exception $e) {
			throw new GenericException($e->getMessage());
		}
	}
	
	public function getArchivedMediaIds($iMediaTypeId, $iAtive=0) {
		$sQuery = 'SELECT 
						media_id
					FROM '.$this->sMediasTableName.' 
					WHERE media_active = :media_active 
					AND media_type_id = :media_type_id';
			$oQuery = $this->oPdo->prepare($sQuery);
			$oQuery->bindParam(':media_active', $iAtive, PDO::PARAM_INT);
			$oQuery->bindParam(':media_type_id', $iMediaTypeId, PDO::PARAM_INT);
			$oQuery->execute();
			return $oQuery->fetchAll(PDO::FETCH_COLUMN);
	}
}