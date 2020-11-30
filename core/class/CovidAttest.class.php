<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__  . '/AttestGen.class.php';

class CovidAttest extends eqLogic {
	
  
  const SUBFOLDER="AG_";



/* --------------------------  Méthode static pour dépendances -------------------------------------- */
  public static function dependancy_info() {
	$return = array();
	$return['log'] = 'CovidAttest';
	$return['progress_file'] = '/tmp/dependancy_covidattest_in_progress';
	$return['state'] = 'ok';
	if (exec('which imagemagick | wc -l') == 0) {
		if (exec(" dpkg --get-selections | grep -v deinstall | grep -E 'imagemagick' | wc -l") == 0) {
			$return['state'] = 'nok';
		} 
	} 
	return $return;
}

public static function dependancy_install() {
	log::remove(__CLASS__ . '_update');
	return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder('CovidAttest') . '/dependance', 'log' => log::getPathToLog(__CLASS__ . '_update'));
}





  /* --------------------------  Méthode static pour gestion des fichiers généraux -------------------------------------- */
  public static function DELETE_ALL(){
    	$path=realpath(dirname(__FILE__). '/../../').'/EXPORT';
    	log::add('CovidAttest', 'debug', '╔═══════════════════════ CALL REMOVE ALL FILES to '.$path);
    	 if(!is_dir($path)){
            log::add('CovidAttest', 'debug', '╠════ Path  not found');
           return true;
        }
    	$success = self::delTree($path);
		log::add('CovidAttest', 'debug', '╠════ delete folder :'.$path.' | success :'.$success);
		log::add('CovidAttest', 'debug', '╚═══════════════════════ End Files Remove ');
  }
  public static function delTree($dir) { 
   $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file) { 
		log::add('CovidAttest', 'debug', '╠════ delete folder/file :'.$file.' from :'.$dir);
      (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file"); 
	}
    return rmdir($dir); 
  } 


// génération d'une attestation de test pour la section configuration
  public static function generate_test($filename){
	log::add('CovidAttest', 'debug', '╔═══════════════════════ Generating Test File');
	$path=realpath(dirname(__FILE__). '/../../').'/EXPORT/TEST/'; 
	if(is_dir($path)){
		log::add('CovidAttest', 'debug', '╠════ clear test path :'.$path);
		$success = self::delTree($path);
	}
	$ag = new ATTESTGEN();
	$ag->setCertif($filename);

	$name='DUPONT';
	$fname='Camille';
	$ddn='01/01/1970';
	$lieu_ddn='Bruère-Allichamps';
	$address='01 rue de la mouette';
	$zip='00 001';
	$ville='Saint-Amand-Montrond';
	$motifs=array(ATTESTGEN::TRAVAIL ,ATTESTGEN::ACHATS ,ATTESTGEN::SANTE ,ATTESTGEN::FAMILLE ,ATTESTGEN::HANDICAP ,ATTESTGEN::SPORT_ANIMAUX ,ATTESTGEN::CONVOCATION ,ATTESTGEN::MISSIONS ,ATTESTGEN::ENFANTS,ATTESTGEN::CULTURE);
	$dateAttest='12/12/2012';
	$timeAttest='12h12';
	$testPATH = $ag->generate_attest($name,$fname,$ddn,$lieu_ddn,$address,$zip,$ville, $motifs, $dateAttest, $timeAttest, $secondPage=false, 'TEST');
	$testURL ='/plugins/CovidAttest/EXPORT/TEST/'.basename($testPATH);
	log::add('CovidAttest', 'debug', '╠════ Test créé '.$testPATH);
	log::add('CovidAttest', 'debug', '╠════ URL '.$testURL);
	log::add('CovidAttest', 'debug', '╚═══════════════════════ generation test file');
	return $testURL;
	
}


    /*     * *********************Méthodes d'instance************************* */

 // Fonction exécutée automatiquement avant la création de l'équipement
    public function preInsert() {

    }

 // Fonction exécutée automatiquement après la création de l'équipement
    public function postInsert() {

    }

 // Fonction exécutée automatiquement avant la mise à jour de l'équipement
    public function preUpdate() {

    }

 // Fonction exécutée automatiquement après la mise à jour de l'équipement
    public function postUpdate() {

    }
  
  

 // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
    public function preSave() {
      	$testAttest = $this->getCmd(null, 'motif_TRAVAIL');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::TRAVAIL);
			$testAttest->save();
		}
      
      $testAttest = $this->getCmd(null, 'motif_ACHATS');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::ACHATS);
			$testAttest->save();
		}
      
      $testAttest = $this->getCmd(null, 'motif_SANTE');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::SANTE);
			$testAttest->save();
		}
      
      $testAttest = $this->getCmd(null, 'motif_FAMILLE');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::FAMILLE);
			$testAttest->save();
		}
      
      $testAttest = $this->getCmd(null, 'motif_HANDICAP');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::HANDICAP);
			$testAttest->save();
		}
      
      $testAttest = $this->getCmd(null, 'motif_SPORT_ANIMAUX');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::SPORT_ANIMAUX);
			$testAttest->save();
		}
      
      $testAttest = $this->getCmd(null, 'motif_CONVOCATION');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::CONVOCATION);
			$testAttest->save();
		}
      
      $testAttest = $this->getCmd(null, 'motif_MISSIONS');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::MISSIONS);
			$testAttest->save();
		}
      
      $testAttest = $this->getCmd(null, 'motif_ENFANTS');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::ENFANTS);
			$testAttest->save();
		}
	$testAttest = $this->getCmd(null, 'motif_CULTURE');
		if (is_object($testAttest)) {
			$testAttest->event(ATTESTGEN::CULTURE);
			$testAttest->save();
		}

    }

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
    public function postSave() {      
       
        $dateAttest = $this->getCmd(null, 'dateAttest');
		if (!is_object($dateAttest)) {
			$dateAttest = new CovidAttestCmd();
			$dateAttest->setLogicalId('dateAttest');
			$dateAttest->setIsVisible(0);
			$dateAttest->setName(__('Date attestation', __FILE__));
		}
        $dateAttest->setType('info');
		$dateAttest->setSubType('string');
		$dateAttest->setEqLogic_id($this->getId());
		$dateAttest->save();

        $heureAttest = $this->getCmd(null, 'heureAttest');

		if (!is_object($heureAttest)) {
			$heureAttest = new CovidAttestCmd();
			$heureAttest->setLogicalId('heureAttest');
			$heureAttest->setIsVisible(0);
			$heureAttest->setName(__('Heure attestation', __FILE__));
		}
        $heureAttest->setType('info');
		$heureAttest->setSubType('string');
		$heureAttest->setEqLogic_id($this->getId());
		$heureAttest->save();

        // les info pour les motifs
        $motif = $this->getCmd(null, 'send_motif_TRAVAIL');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_TRAVAIL');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif TRAVAIL', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

        $motif = $this->getCmd(null, 'send_motif_ACHATS');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_ACHATS');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif ACHATS', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

        $motif = $this->getCmd(null, 'send_motif_SANTE');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_SANTE');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif SANTE', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

        $motif = $this->getCmd(null, 'send_motif_FAMILLE');
        if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_FAMILLE');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif FAMILLE', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

        $motif = $this->getCmd(null, 'send_motif_HANDICAP');
        if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_HANDICAP');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif HANDICAP', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

        $motif = $this->getCmd(null, 'send_motif_SPORT_ANIMAUX');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_SPORT_ANIMAUX');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif SPORT_ANIMAUX', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

        $motif = $this->getCmd(null, 'send_motif_CONVOCATION');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_CONVOCATION');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif CONVOCATION', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

        $motif = $this->getCmd(null, 'send_motif_MISSIONS');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_MISSIONS');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif MISSIONS', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

        $motif = $this->getCmd(null, 'send_motif_ENFANTS');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_ENFANTS');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif ENFANTS', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();

		$motif = $this->getCmd(null, 'send_motif_CULTURE');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_CULTURE');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif CULTURE', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();
      
      $motif = $this->getCmd(null, 'send_motif_MULTI');
		if (!is_object($motif)) {
			$motif = new CovidAttestCmd();
			$motif->setLogicalId('send_motif_MULTI');
			$motif->setIsVisible(1);
			$motif->setName(__('Envoi motif MULTIPLES', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('message');
		$motif->setEqLogic_id($this->getId());
		$motif->save();
      
      $motifType = $this->getCmd(null, 'motif_TRAVAIL');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_TRAVAIL');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif TRAVAIL', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $motifType = $this->getCmd(null, 'motif_ACHATS');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_ACHATS');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif ACHATS', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $motifType = $this->getCmd(null, 'motif_SANTE');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_SANTE');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif SANTE', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $motifType = $this->getCmd(null, 'motif_FAMILLE');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_FAMILLE');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif FAMILLE', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $motifType = $this->getCmd(null, 'motif_HANDICAP');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_HANDICAP');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif HANDICAP', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $motifType = $this->getCmd(null, 'motif_SPORT_ANIMAUX');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_SPORT_ANIMAUX');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif SPORT_ANIMAUX', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $motifType = $this->getCmd(null, 'motif_CONVOCATION');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_CONVOCATION');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif CONVOCATION', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $motifType = $this->getCmd(null, 'motif_MISSIONS');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_MISSIONS');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif MISSIONS', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $motifType = $this->getCmd(null, 'motif_ENFANTS');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_ENFANTS');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif ENFANTS', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();

		$motifType = $this->getCmd(null, 'motif_CULTURE');
		if (!is_object($motifType)) {
			$motifType = new CovidAttestCmd();
			$motifType->setLogicalId('motif_CULTURE');
			$motifType->setIsVisible(0);
			$motifType->setName(__('motif CULTURE', __FILE__));
		}
        $motifType->setType('info');
		$motifType->setSubType('string');
      	$motifType->setIsVisible(0);
		$motifType->setEqLogic_id($this->getId());
		$motifType->save();
      
      $removeCmd = $this->getCmd(null, 'remove_file');
		if (!is_object($removeCmd)) {
			$removeCmd = new CovidAttestCmd();
			$removeCmd->setLogicalId('remove_file');
			$removeCmd->setIsVisible(1);
			$removeCmd->setName(__('Supprimer les fichiers', __FILE__));
		}
        $removeCmd->setType('action');
		$removeCmd->setSubType('other');
		$removeCmd->setEqLogic_id($this->getId());
		$removeCmd->save();
      
      
      //
    }

 // Fonction exécutée automatiquement avant la suppression de l'équipement
    public function preRemove() {

    }

 // Fonction exécutée automatiquement après la suppression de l'équipement
    public function postRemove() {

    }
  
  public function removeMyFiles(){
    	$nom=( $this->getConfiguration('user_name', ''));
        $prenom=($this->getConfiguration('user_firstname', ''));
    
		log::add('CovidAttest', 'debug', '╔═══════════════════════ remove My Files Call pour '.$prenom.' '.$nom); 
		
		$path = realpath(dirname(__FILE__). '/../../').'/EXPORT/'.self::SUBFOLDER.$this->getID();
		log::add('CovidAttest', 'debug', '╠════ path à supprimer '.$path); 

		$success = self::delTree($path);
		log::add('CovidAttest', 'debug', '╠════ delete folder :'.$path.' | success :'.$success);
		log::add('CovidAttest', 'debug', '╚═══════════════════════ End Files Remove pour '.$prenom.' '.$nom);
		
  }

    public function createDirectPDF($motifs, $cmdId, $cmdName){
        log::add('CovidAttest','debug','╠════ createDirectPDF called for motif :'.$motifs);
		log::add('CovidAttest','debug','╠════ Id equipement :'.$this->getId());


      	$cmdDate = $this->getCmd(null, 'dateAttest');
        if(is_object($cmdDate)){
		  $dateAttest= $cmdDate->execCmd();
		  $dateAttest=preg_replace("/[\'\"]/", "", $dateAttest);
        }
        if ($dateAttest=='') {
            $dateAttest=strftime("%d/%m/%G");
        }
      
     	$cmdTime = $this->getCmd(null, 'heureAttest');
		if(is_object($cmdTime)){
		  $timeAttest= $cmdTime->execCmd();
		  $timeAttest=preg_replace("/[\'\"]/", "", $timeAttest);
        }
        
        if ($timeAttest=='') {
            $timeAttest=strftime("%Hh%M");
        }
        log::add('CovidAttest','debug', '╠════ date :'.$dateAttest.' / time : '.$timeAttest.' / motif : '.$motifs);
        $this->createPDF($dateAttest, $timeAttest, $motifs,$cmdId, $cmdName);
      	
      	// on remet à 0 les valerus de date et time
      if(is_object($cmdDate))$cmdDate->event('');
      if(is_object($cmdTime))$cmdTime->event('');
      
    }
  
    public function createPDF($dateAttest, $timeAttest, $motifs,$cmdId, $cmdName){
      
      	$nom=( $this->getConfiguration('user_name', ''));
        $prenom=( $this->getConfiguration('user_firstname', ''));
      
        $date_naissance=$this->getConfiguration('user_ddn', '');
        $lieu_naissance=$this->getConfiguration('user_btown', '');
      
      // addresse
      if($this->getConfiguration('use_jeeadd', '')){
        $adresse = config::byKey('info::address');
        $code_postal = config::byKey('info::postalCode');
        $ville = config::byKey('info::city');
      }else{
        
        $adresse=$this->getConfiguration('user_adress', '');
        $code_postal=$this->getConfiguration('user_zip', '');
        $ville=$this->getConfiguration('user_ctown', '');
      }

        log::add('CovidAttest','debug', "╠════ Cree le: ".$dateAttest.";\n Nom: ".$nom.";\n Prenom: ".$prenom.";\n Naissance: ".$date_naissance." a ".$lieu_naissance.";\n Adresse: ".$adresse." ".$code_postal." ".$ville.";\n Sortie: ".$dateAttest."\n Motifs: ".$motifs);
		// récupération de l'option seconde page
      	$secondpage=$this->getConfiguration('option_addpage', '');
	  	log::add('CovidAttest', 'debug', '╠════ ajout de la seconde page :'.$secondpage);
		  
		  // creation de l'instance
		  $ag=new ATTESTGEN();

		  // récupération du fichier défini en configuration
		$certifFile=config::byKey('certificate_name', 'CovidAttest', 'none');
		
		if (!is_null($certifFile) && $certifFile!= 'none'){
			log::add('CovidAttest', 'debug', '╠════ Certificat utilisé :'.$certifFile);
			$ag->setCertif($certifFile);
		}else{
			log::add('CovidAttest', 'debug', '╠════ Certificat utilisé : DEFAULT');
		}

        
        $pdfURL = $ag->generate_attest($nom, $prenom, $date_naissance,$lieu_naissance,$adresse,$code_postal,$ville, $motifs, $dateAttest, $timeAttest, $secondpage, self::SUBFOLDER.$this->getId());
        log::add('CovidAttest','debug', '╠════ pdf url :'.$pdfURL);
        $qrcURL =$ag->getQRCURL();
        log::add('CovidAttest','debug', '╠════ png url :'.$qrcURL);
       

        
      $sendPDF = $this->getConfiguration('option_sendPDF', '1');
	  $sendQRC = $this->getConfiguration('option_sendQRC', '1');
	  $sendPNG  = $this->getConfiguration('option_sendPNG', '1');

	  if($sendPNG){
		  $pdfImageURL=ATTESTGEN::convert_pdf_to_png($pdfURL);
	  		 $sendPNG=($pdfImageURL!=false)?true:false;
	  }
	  log::add('CovidAttest','debug','╠════ choix des fichiers à envoyer - pdf :'.$sendPDF.' | png : '.$sendPNG.' | QRcode : '.$sendQRC);
	  // creation de l'array des fichiers à envoyer
		$filesA=array();
		if($sendPDF)$filesA['#pdfURL#'] = $pdfURL;
		if($sendQRC)$filesA['#qrcURL#'] = $qrcURL;
		if($sendPNG)$filesA['#pngURL#'] = $pdfImageURL;
		log::add('CovidAttest','debug','╠════ url des fichiers à envoyer - pdf :'.implode(', ',$filesA));


		// motif string 
		$motifStr = 'Attestation Covid du '.$dateAttest.' a '.$timeAttest.' pour ';
		 if (is_array($motifs)){
		  $motifStr .=implode (',', $motifs);
		  }else{
			  $motifStr .= $motifs;
		  }
	    
	    	$motifStr=str_replace("_"," ",$motifStr);
		log::add('CovidAttest','debug','╠════ String motifs :'.$motifStr);

		$useScenarioCMD = $this->getConfiguration('use_scenar', '0');

		if($useScenarioCMD){
			$this->sendFilesByScenario($ag,$filesA,$cmdId, $cmdName);
		}else{
			$this->sendFileByCMD($ag,$filesA, $motifStr);
		}

	}

public function sendFilesByScenario($ag,$files,$cmdId, $cmdName){
	$scenarioID=$this->getConfiguration('scenarCMD', '');
	if ($scenarioID === '') {
			log::add('CovidAttest', 'error', "Scenario denvoi non configurée {$this->getHumanName()}.");
			return false;
		}
		$scenario=scenario::byId($scenarioID);
		if(is_null($scenario)){
			log::add('CovidAttest', 'error', "Scenario denvoi non trouvé $scenarioID.");
			return false;
		}
		log::add('CovidAttest','debug', '╠════ commande d\'envoi :'.$scenario->getHumanName());
		
		$files["#cmdID#"]=$cmdId;
		$files["#cmdNAME#"]=$cmdName;
		$files["#eqID#"]=$this->getId();
		$files["#eqNAME#"]=$this->getHumanName();
		log::add('CovidAttest','debug', '╠════ tags  :'.implode(',', $files));
		$scenario->setTags($files);
		$scenario->launch();

		// suppression des fichiers
		$this->autoDeleteFiles($ag);
}


 public function sendFileByCMD($ag, $files, $motifStr){     
      // choix selon le type d'équipement:
      $typeCmd=$this->getConfiguration('option_typeEq', 'custom');

	    // pour le formattage des motif dans les notification, si c'est un motif multiple=> envoi un array
	 
	  
      switch ($typeCmd) {
          /// si telegram
          case 'telegram':
          		$str='file='.implode(',',$files);
           		log::add('CovidAttest','debug','╠════ telegram : string envoyée :'.$str);
              	 $optionsSendCmd= array('title'=>$str,'message'=> $motifStr);
              break;
          
          
          /// si c'est un mail
          case 'mail':
				  log::add('CovidAttest','debug','╠════ MAIL : array  envoyée :'.implode(',', $files));
          		 $optionsSendCmd= array('files'=>$files,'title'=>$motifStr, 'message'=> " ");
            
			  break;
	/// si pushover

	case 'pushover':
		$files=explode(',', implode(',', $files));
		log::add('CovidAttest','debug','╠════ PUSHOVER : array  envoyée :'.implode(',', $files));
		 $optionsSendCmd= array('files'=>$files,'title'=>$motifStr, 'message'=> 'Attestation :');

	break;
		
          case "custom":
          			$optionsFormat=$this->getConfiguration('option_sendcmd', '');
                    if ($optionsFormat === '') {
                        log::add('CovidAttest', 'error', "Option de la commande d'envoi non configurée {$this->getHumanName()}.");
                        return false;
                    }
          			if(array_key_exists('pdfURL', $files))$optionsFormat=str_replace("#pdfURL#", $files['#pdfURL#'], $optionsFormat);
					if(array_key_exists('qrcURL', $files))$optionsFormat=str_replace("#qrcURL#", $files['#qrcURL#'], $optionsFormat);
					if(array_key_exists('pngURL', $files))$optionsFormat=str_replace("#pngURL#", $files['#pngURL#'], $optionsFormat);
					
                    $optionEmplacement=$this->getConfiguration('option_conf', 'titre');
          			
					log::add('CovidAttest','debug', '╠════ Option emplacement :'.$optionEmplacement.' options :'.$optionsFormat);
					
					switch ($optionEmplacement) {
						/// si telegram
						case 'title':
							$optionsSendCmd= array('title'=>$optionsFormat, 'message'=> '');
						break;

						case 'message':
							$optionsSendCmd= array('title'=>'', 'message'=> $optionsFormat);
						break;

						case 'files_array':
								$optionsSendCmd= array('title'=>'', 'message'=> '', 'files'=>$files);
						break;

						case 'files_string':
							$optionsSendCmd= array('title'=>'', 'message'=> '', 'files'=>$optionsFormat);
						break;

						}
              break;
      }

	   $sendCmd =$this->getConfiguration('sendCmd', '');

		if ($sendCmd === '') {
			log::add('CovidAttest', 'error', "Commande denvoi non configurée {$this->getHumanName()}.");
			return false;
		}
        log::add('CovidAttest','debug', '╠════ commande d\'envoi :'.$sendCmd);

		$cmd = cmd::byId(str_replace('#', '', $sendCmd));
		
        if (!is_object($cmd)) {
            log::add('CovidAttest', 'error', "Commande {$sendCmd} non trouvée, vérifiez la configuration pour  {$this->getHumanName()}.");
         }else{
			log::add('CovidAttest','debug','╠════ envoi des fichiers par la commande :'.$cmd->getHumanName());
             $cmd ->execCmd($optionsSendCmd, $cache=0);
        }
		$this->autoDeleteFiles($ag);
	}

public function autoDeleteFiles($ag){

        // suppressiond es fichiers
      	$deactivate_autoremove = $this->getConfiguration('auto_remove', '1');
      	log::add('CovidAttest','debug','╠════ suppression auto des fichiers statut :'.$deactivate_autoremove);
      	if($deactivate_autoremove==0){
          $successDelete=$ag->deleteAllFiles();
          log::add('CovidAttest','debug','╠════ Suppression des fichiers : '.($successDelete?'ok':'echoue'));
        }     

    }
  
  
 
  
   
}

class CovidAttestCmd extends cmd {
   
  // Exécution d'une commande
     public function execute($_options = array()) {
         log::add('CovidAttest','debug', "╔═══════════════════════ execute CMD : ".$this->getId()." | ".$this->getHumanName().", logical id : ".$this->getLogicalId() ."options : ".print_r($_options));
		 $cmdId=$this->getId();
		 $cmdName=$this->getHumanName();
		 switch($this->getLogicalId()){
             case 'send_motif_TRAVAIL':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::TRAVAIL, $cmdId, $cmdName);
                 break;
             case 'send_motif_ACHATS':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::ACHATS, $cmdId, $cmdName);
                 break;
             case 'send_motif_SANTE':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::SANTE, $cmdId, $cmdName);
                 break;
             case 'send_motif_FAMILLE':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::FAMILLE, $cmdId, $cmdName);
                 break;
             case 'send_motif_HANDICAP':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::HANDICAP, $cmdId, $cmdName);
                 break;
             case 'send_motif_SPORT_ANIMAUX':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::SPORT_ANIMAUX, $cmdId, $cmdName);
                 break;
             case 'send_motif_CONVOCATION':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::CONVOCATION, $cmdId, $cmdName);
                 break;
             case 'send_motif_MISSIONS':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::MISSIONS, $cmdId, $cmdName);
                 break;
             case 'send_motif_ENFANTS':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::ENFANTS, $cmdId, $cmdName);
				 break;
			case 'send_motif_CULTURE':
				$this->getEqLogic()->createDirectPDF(ATTESTGEN::CULTURE, $cmdId, $cmdName);
				break;
           case 'remove_file':
             	$this->getEqLogic()->removeMyFiles();
             	break;
           case 'send_motif_MULTI':
             	if(!isset($_options['message']) | empty ($_options['message'])){
                	 log::add('CovidAttest','error', 'Aucun motif de défini pour un envoi mutliple, veuillez selectionner parmi les commandes info disponibles');
                }
             	$motifsStr=str_replace(',', ';',$_options['message']);
             	$motifsStr=str_replace(' ', '',$motifsStr);
             	$motifs=explode (';',$motifsStr);
             	
             	log::add('CovidAttest', 'debug', '╠════ motifs multiples : '.implode('#',$motifs));
             	$this->getEqLogic()->createDirectPDF($motifs, $cmdId, $cmdName);
             	break;
             Default:
                 log::add('CovidAttest','debug', '╠════ Deafault call');

		 }
		 log::add('CovidAttest','debug','╚═══════════════════════ Fin Commande');
			return true;
     }
  

    /*     * **********************Getteur Setteur*************************** */
}
