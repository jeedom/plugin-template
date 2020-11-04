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

  /*
   public static function cron() {
     		log::add('CovidAttest', 'debug', 'Launch CRON'); 
   		$path = realpath(dirname(__FILE__). '/../../').'/EXPORT';
   		$files = glob($path.'/*'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file)){
           	log::add('CovidAttest', 'debug', 'CRON could delete : '.$file); 
          }
            //unlink($file); // delete file
        }

      }
     */

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
			$dateAttest->setIsVisible(1);
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
			$heureAttest->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
			$motifType->setIsVisible(1);
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
    
  		log::add('CovidAttest', 'debug', 'remove My Files Call pour '.$prenom.' '.$nom); 
   		$path = realpath(dirname(__FILE__). '/../../').'/EXPORT';
   		$files = glob($path.'/*'); 
    
    	$patternPDF='/attestation-[0-9-_]{15,18}_'.urlencode($prenom).'.'.urlencode($nom).'/'; 
    	$patternQRCode='/qrcode_attest-[0-9-_]{15,18}_'.urlencode($prenom).'.'.urlencode($nom).'/'; 
    	log::add('CovidAttest', 'debug', 'delette pattern pdf :'.$patternPDF.'  |  Qr :'.$patternQRCode);
        foreach($files as $file){ // iterate files
          if(is_file($file)){
           	log::add('CovidAttest', 'debug', 'Check for delete : '.basename ($file).'  |  verif pdf pattern : '.preg_match($patternPDF,basename ($file)).'  | match qr pattern : '.preg_match($patternQRCode,basename ($file))); 
            
            if(preg_match($patternPDF,basename ($file)) | preg_match($patternQRCode,basename ($file))){
              	log::add('CovidAttest', 'debug', 'Remove file  : '.basename ($file)); 
              unlink($file);
            }
            
          }
            //unlink($file); // delete file
        }
  }

    public function createDirectPDF($motifs){
        log::add('CovidAttest','debug','|-----------------------> createDirectPDF called for motif :'.$motifs);
      
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
        log::add('CovidAttest','debug', 'date :'.$dateAttest.' / time : '.$timeAttest.' / motif : '.$motifs);
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

        log::add('CovidAttest','debug', "Cree le: ".$dateAttest.";\n Nom: ".$nom.";\n Prenom: ".$prenom.";\n Naissance: ".$date_naissance." a ".$lieu_naissance.";\n Adresse: ".$adresse." ".$code_postal." ".$ville.";\n Sortie: ".$dateAttest."\n Motifs: ".$motifs);
		// récupération de l'option seconde page
      	$secondpage=$this->getConfiguration('option_addpage', '');
      log::add('CovidAttest', 'debug', 'ajout de la seconde page :'.$secondpage);
      
        // creation de l'instance
        $ag=new ATTESTGEN();
        $pdfURL = $ag->generate_attest($nom, $prenom, $date_naissance,$lieu_naissance,$adresse,$code_postal,$ville, $motifs, $dateAttest, $timeAttest, $secondpage);
        log::add('CovidAttest','debug', 'pdf url :'.$pdfURL);
        $pngURL =$ag->getPNGURL();
        log::add('CovidAttest','debug', 'png url :'.$pngURL);
        $sendCmd =$this->getConfiguration('sendCmd', '');

		if ($sendCmd === '') {
			log::add('CovidAttest', 'error', "Commande denvoi non configurée {$this->getHumanName()}.");
			return false;
		}
        log::add('CovidAttest','debug', 'commande d\'envoi :'.$this->getHumanName());

        
      $sendPDF = $this->getConfiguration('option_sendPDF', '1');
      $sendQRC = $this->getConfiguration('option_sendQRC', '1');
      log::add('CovidAttest','debug',' choix des fichiers à envoyer - pdf :'.$sendPDF.' | png : '.$sendQRC);
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
           		log::add('CovidAttest','debug','telegram : string envoyée :'.$str);
              	 $optionsSendCmd= array('title'=>$str,'message'=> 'Attestation Covid du '.$dateAttest.' a '.$timeAttest.' pour '.$motifStr);
              break;
          
          
          /// si c'est un mail
          case 'mail':
          		$filesA=array();
          		if($sendPDF)array_push($filesA,$pdfURL);
          		if($sendQRC)array_push($filesA,$pngURL);

          		 $optionsSendCmd= array('files'=>$filesA,'title'=>'Attestation du '.$dateAttest.' a '.$timeAttest.' de '.$prenom.' pour '.$motifStr, 'message'=> " ");
            
              break;
          case "custom":
          			$optionsFormat=$this->getConfiguration('option_sendcmd', '');
                    if ($optionsFormat === '') {
                        log::add('CovidAttest', 'error', "Option de la commande d'envoi non configurée {$this->getHumanName()}.");
                        return false;
                    }
          			$optionsFormat=str_replace("#pdfURL#", $pdfURL, $optionsFormat);
                    $optionsFormat=str_replace("#qrcURL#", $pngURL, $optionsFormat);
                    $optionEmplacement=$this->getConfiguration('option_conf', 'titre');
          			
                    log::add('CovidAttest','debug', 'Option emplacement :'.$optionEmplacement.' options :'.$optionsFormat);
                    if($optionEmplacement=='title'){
                        $optionsSendCmd= array('title'=>$optionsFormat, 'message'=> '');
                    }else{
                        $optionsSendCmd= array('title'=>'', 'message'=> $optionsFormat);
                    }
              break;
      }

        
      
      

        $cmd = cmd::byId(str_replace('#', '', $sendCmd));
        if (!is_object($cmd)) {
            log::add('CovidAttest', 'error', "Commande {$nextCmdId} non trouvée, vérifiez la configuration pour  {$this->getHumanName()}.");
         }else{
             $cmd ->execCmd($optionsSendCmd, $cache=0);
             log::add('CovidAttest','debug','envoi des fichiers');
        }

        // suppressiond es fichiers
      	$deactivate_autoremove = $this->getConfiguration('auto_remove', '1');
      	log::add('CovidAttest','debug','suppression auto des fichiers statut :'.$deactivate_autoremove);
      	if($deactivate_autoremove==0){
          $successDelete=$ag->deleteAllFiles();
          log::add('CovidAttest','debug','Suppression des fichiers : '.($successDelete?'ok':'echoue'));
        }
        
        

    }
  
  
 
  
   
}

class CovidAttestCmd extends cmd {
   
  // Exécution d'une commande
     public function execute($_options = array()) {
         log::add('CovidAttest','debug', 'execute CMD, logical id :'.$this->getLogicalId().'  options : '.print_r($_options));
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
             	$motifsStr=str_replace(' ', '',$_options['message']);
             	$motifs=explode (';',$motifsStr);
             	
             	log::add('CovidAttest', 'debug', 'motifs multiples : '.implode('#',$motifs));
             	$this->getEqLogic()->createDirectPDF($motifs);
             	break;
             Default:
                 log::add('CovidAttest','debug', 'Deafault call');

         }
			return true;
     }
  

    /*     * **********************Getteur Setteur*************************** */
}