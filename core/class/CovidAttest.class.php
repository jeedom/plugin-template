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
		if (exec(" dpkg --get-selections | grep -v deinstall | grep -E 'imagemagick' | wc -l") != 2) {
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
	$motifs=array(ATTESTGEN::TRAVAIL ,ATTESTGEN::ACHATS ,ATTESTGEN::SANTE ,ATTESTGEN::FAMILLE ,ATTESTGEN::HANDICAP ,ATTESTGEN::SPORT_ANIMAUX ,ATTESTGEN::CONVOCATION ,ATTESTGEN::MISSIONS ,ATTESTGEN::ENFANTS);
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

    }

 // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
    public function postSave() {      
       
        $dateAttest = $this->getCmd(null, 'dateAttest');
		if (!is_object($dateAttest)) {
			$dateAttest = new CovidAttestCmd();
			$dateAttest->setLogicalId('dateAttest');
			$dateAttest->setIsVisible(0);
			$dateAttest->setName(__('Date de l\'attestation', __FILE__));
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
			$heureAttest->setName(__('Heure de l\'attestation', __FILE__));
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

    public function createDirectPDF($motifs){
        log::add('CovidAttest','debug','╠════ createDirectPDF called for motif :'.$motifs);
		log::add('CovidAttest','debug','╠════ Id equipement :'.$this->getId());
      	$cmdDate = $this->getCmd(null, 'dateAttest');
      

        if(is_object($cmdDate)){
          $dateAttest= $cmdDate->execCmd();
        }
        if ($dateAttest=='') {
            $dateAttest=strftime("%d/%m/%G");
        }
      
     	$cmdTime = $this->getCmd(null, 'heureAttest');
		if(is_object($cmdTime)){
          $timeAttest= $cmdTime->execCmd();
        }
        
        if ($timeAttest=='') {
            $timeAttest=strftime("%Hh%M");
        }
        log::add('CovidAttest','debug', '╠════ date :'.$dateAttest.' / time : '.$timeAttest.' / motif : '.$motifs);
        $this->createPDF($dateAttest, $timeAttest, $motifs);
      	
      	// on remet à 0 les valerus de date et time
      if(is_object($cmdDate))$cmdDate->event('');
      if(is_object($cmdTime))$cmdTime->event('');
      
    }
  
    public function createPDF($dateAttest, $timeAttest, $motifs){
      
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
        $pngURL =$ag->getPNGURL();
        log::add('CovidAttest','debug', '╠════ png url :'.$pngURL);
        $sendCmd =$this->getConfiguration('sendCmd', '');

		if ($sendCmd === '') {
			log::add('CovidAttest', 'error', "Commande denvoi non configurée {$this->getHumanName()}.");
			return false;
		}
        log::add('CovidAttest','debug', '╠════ commande d\'envoi :'.$this->getHumanName());

        
      $sendPDF = $this->getConfiguration('option_sendPDF', '1');
	  $sendQRC = $this->getConfiguration('option_sendQRC', '1');
	  $sendPNG  = $this->getConfiguration('option_sendPNG', '1');

	  if($sendPNG)$pdfImageURL=ATTESTGEN::convert_pdf_to_png($pdfURL);
	  $sendPNG=($pdfImageURL!=false)?true:false;





      log::add('CovidAttest','debug','╠════ choix des fichiers à envoyer - pdf :'.$sendPDF.' | png : '.$sendPNG.' | QRcode : '.$sendQRC);
      // choix selon le type d'équipement:
      $typeCmd=$this->getConfiguration('option_typeEq', 'custom');
	    // pour le formattage des motif dans les notification, si c'est un motif multiple=> envoi un array
	  if (is_array($motifs)){
		  $motifStr =implode (',', $motifs);
	  }else{
		  $motifStr = $motifs;
	  }
	  
      switch ($typeCmd) {
          /// si telegram
          case 'telegram':
          		$str='file=';
          		if($sendPDF)$str.=$pdfURL;
				  if($sendQRC)$str.=(strlen($str)>6?',':'').$pngURL;
				  if($sendPNG)$str.=(strlen($str)>6?',':'').$pdfImageURL;
           		log::add('CovidAttest','debug','╠════ telegram : string envoyée :'.$str);
              	 $optionsSendCmd= array('title'=>$str,'message'=> 'Attestation Covid du '.$dateAttest.' a '.$timeAttest.' pour '.$motifStr);
              break;
          
          
          /// si c'est un mail
          case 'mail':
          		$filesA=array();
          		if($sendPDF)array_push($filesA,$pdfURL);
				  if($sendQRC)array_push($filesA,$pngURL);
				  if($sendPNG)array_push($filesA,$pdfImageURL);
				  log::add('CovidAttest','debug','╠════ MAIL : array  envoyée :'.implode(',', $filesA));
          		 $optionsSendCmd= array('files'=>$filesA,'title'=>'Attestation du '.$dateAttest.' a '.$timeAttest.' de '.$prenom.' pour '.$motifStr, 'message'=> " ");
            
			  break;
		/// si pushover
		
		case 'pushover':
			$filesA=array();
			if($sendPDF)array_push($filesA,$pdfURL);
			if($sendQRC)array_push($filesA,$pngURL);
			if($sendPNG)array_push($filesA,$pdfImageURL);
			
			log::add('CovidAttest','debug','╠════ PUSHOVER : array  envoyée :'.implode(',', $filesA));
			 $optionsSendCmd= array('files'=>$filesA,'title'=>'Attestation du '.$dateAttest.' a '.$timeAttest.' de '.$prenom.' pour '.$motifStr, 'message'=> y);
	  
		break;
		
          case "custom":
          			$optionsFormat=$this->getConfiguration('option_sendcmd', '');
                    if ($optionsFormat === '') {
                        log::add('CovidAttest', 'error', "Option de la commande d'envoi non configurée {$this->getHumanName()}.");
                        return false;
                    }
          			$optionsFormat=str_replace("#pdfURL#", $pdfURL, $optionsFormat);
					$optionsFormat=str_replace("#qrcURL#", $pngURL, $optionsFormat);
					$optionsFormat=str_replace("#pngURL#", $pngURL, $optionsFormat);
					
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
							$filesA=array();
								if($sendPDF)array_push($filesA,$pdfURL);
								if($sendQRC)array_push($filesA,$pngURL);
								if($sendPNG)array_push($filesA,$pdfImageURL);
								$optionsSendCmd= array('title'=>'', 'message'=> '', 'files'=>$filesA);
						break;

						case 'files_string':
							$optionsSendCmd= array('title'=>'', 'message'=> '', 'files'=>$optionsFormat);
						break;

						}
              break;
      }

		$cmd = cmd::byId(str_replace('#', '', $sendCmd));
		
        if (!is_object($cmd)) {
            log::add('CovidAttest', 'error', "Commande {$nextCmdId} non trouvée, vérifiez la configuration pour  {$this->getHumanName()}.");
         }else{
			log::add('CovidAttest','debug','╠════ envoi des fichiers par la commande :'.$cmd->getHumanName());
             $cmd ->execCmd($optionsSendCmd, $cache=0);
        }

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
         log::add('CovidAttest','debug', '╔═══════════════════════ execute CMD, logical id :'.$this->getLogicalId().'  options : '.print_r($_options));
         switch($this->getLogicalId()){
             case 'send_motif_TRAVAIL':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::TRAVAIL);
                 break;
             case 'send_motif_ACHATS':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::ACHATS);
                 break;
             case 'send_motif_SANTE':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::SANTE);
                 break;
             case 'send_motif_FAMILLE':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::FAMILLE);
                 break;
             case 'send_motif_HANDICAP':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::HANDICAP);
                 break;
             case 'send_motif_SPORT_ANIMAUX':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::SPORT_ANIMAUX);
                 break;
             case 'send_motif_CONVOCATION':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::CONVOCATION);
                 break;
             case 'send_motif_MISSIONS':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::MISSIONS);
                 break;
             case 'send_motif_ENFANTS':
                 $this->getEqLogic()->createDirectPDF(ATTESTGEN::ENFANTS);
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
             	$this->getEqLogic()->createDirectPDF($motifs);
             	break;
             Default:
                 log::add('CovidAttest','debug', '╠════ Deafault call');

		 }
		 log::add('CovidAttest','debug','╚═══════════════════════ Fin Commande');
			return true;
     }
  

    /*     * **********************Getteur Setteur*************************** */
}
