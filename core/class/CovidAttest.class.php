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
			$motif->setName(__('send_motif_ENFANTS MISSIONS', __FILE__));
		}
        $motif->setType('action');
		$motif->setSubType('other');
		$motif->setEqLogic_id($this->getId());
		$motif->save();


    }

 // Fonction exécutée automatiquement avant la suppression de l'équipement
    public function preRemove() {

    }

 // Fonction exécutée automatiquement après la suppression de l'équipement
    public function postRemove() {

    }

    public function createDirectPDF($motifs){
        log::add('CovidAttest','debug','createDirectPDF called for motif :'.$motifs);
      
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
        $nom=$this->getConfiguration('user_name', '');
        $prenom=$this->getConfiguration('user_firstname', '');
        $date_naissance=$this->getConfiguration('user_ddn', '');
        $lieu_naissance=$this->getConfiguration('user_btown', '');
        $adresse=$this->getConfiguration('user_adress', '');
        $code_postal=$this->getConfiguration('user_zip', '');
        $ville=$this->getConfiguration('user_ctown', '');

        log::add('CovidAttest','debug', "Cree le: ".$dateAttest.";\n Nom: ".$nom.";\n Prenom: ".$prenom.";\n Naissance: ".$date_naissance." a ".$lieu_naissance.";\n Adresse: ".$adresse." ".$code_postal." ".$ville.";\n Sortie: ".$dateAttest."\n Motifs: ".$motifs);

        // creation de l'instance
        $ag=new ATTESTGEN();
        $pdfURL = $ag->generate_attest($nom, $prenom, $date_naissance,$lieu_naissance,$adresse,$code_postal,$ville, $motifs, $dateAttest, $timeAttest);
        log::add('CovidAttest','debug', 'pdf url :'.$pdfURL);
        $pngURL =$ag->getPNGURL();
        log::add('CovidAttest','debug', 'png url :'.$pngURL);
        $sendCmd =$this->getConfiguration('sendCmd', '');

		if ($sendCmd === '') {
			log::add('CovidAttest', 'error', "Commande denvoi non configurée {$this->getHumanName()}.");
			return false;
		}
        log::add('CovidAttest','debug', 'commande d\'envoir :'.$this->getHumanName());

        $optionsFormat=$this->getConfiguration('option_sendcmd', '');
        if ($optionsFormat === '') {
			log::add('CovidAttest', 'error', "Option de la commande d'envoie non configuré {$this->getHumanName()}.");
			return false;
		}

        $optionsFormat=str_replace("#files#", $pdfURL.",".$pngURL, $optionsFormat);
        log::add('CovidAttest','debug', 'Option de la commande d\'envoi :'.$optionsFormat);
        $optionsSendCmd= array('title'=>$optionsFormat, 'message'=> 'Attestation Covid du '.$dateAttest.' a '.$timeAttest.' pour '.$motifs);

        $cmd = cmd::byId(str_replace('#', '', $sendCmd));
        if (!is_object($cmd)) {
            log::add('CovidAttest', 'error', "Commande {$nextCmdId} non trouvée, vérifiez la configuration pour la file {$this->getHumanName()}.");
         }else{
             $cmd ->execCmd($optionsSendCmd, $cache=0);
             log::add('CovidAttest','debug','envoi des fichier');
        }

        // suppressiond es fichiers
        $successDelete=$ag->deleteAllFiles();
        log::add('CovidAttest','debug','Suppression des fichier : '.($successDelete?'ok':'echoue'));

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
             Default:
                 log::add('CovidAttest','debug', 'Deafault call');

         }

     }

    /*     * **********************Getteur Setteur*************************** */
}