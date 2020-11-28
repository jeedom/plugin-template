<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
//use QRcode;

require_once(dirname(__FILE__) . '/../../3rdparty/FPDF/fpdf.php');
require_once(dirname(__FILE__) . '/../../3rdparty/FPDI/autoload.php');
require_once(dirname(__FILE__) . '/../../3rdparty/phpqrcode/phpqrcode.php');

require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__  . '/../../../../core/php/utils.inc.php';


class ATTESTGEN {

    // constante pour les motifs
    const TRAVAIL = 'travail';
    const ACHATS = 'achats';
    const SANTE = 'sante';
    const FAMILLE = 'famille';
    const HANDICAP = 'handicap';
    const SPORT_ANIMAUX = 'sport_animaux';
    const CONVOCATION = 'convocation';
    const MISSIONS = 'missions';
    const ENFANTS = 'enfants';
    const CULTURE = 'culture';

    const certiFName = 'certificat_28_11_20.pdf';//'30-10-2020-attestation-de-deplacement-derogatoire.pdf'; //

    //public $aMemberVar = 'aMemberVar Member Variable';
    public $generate_attest = 'generate_attest';

    protected $idPos; // array avec les positions des identifiants
    protected $motPos; // array avec les position des cases ? cocher motif

    protected $url_qrcode; // addresse du Ppng du qrcode au besoin
    protected $url_pdf; // addresse du Ppng du qrcode au besoin
    protected $url_png; // addresse du Ppng du qrcode au besoin
    protected $certifNamePerso; // si on d?fini une url perso

    function __construct()
    {
    }

    /* ----------------------------------------  utilitaire de classe static ------------------------ */
    public static function convert_pdf_to_png($fileURL){
        $basePath= pathinfo($fileURL)['dirname'];
        $fileNAME = basename($fileURL);
        $pngName = basename($fileURL,  pathinfo($fileURL)['extension']).'png';
        $cmdIg ='convert -density 200 '.$fileURL.'[0] -fill "#FFFFFFFF" -opaque none -flatten -alpha flatten -alpha remove '.$basePath.'/'.$pngName;
        log::add('CovidAttest', 'debug', "╠════ ## Convert file $fileNAME to image $pngName by commande $cmdIg");
        shell_exec($cmdIg);
        if(is_file($basePath.'/'.$pngName)){
            return $basePath.'/'.$pngName;
        }else{
            return false;
        }

    }
    /* ----------------------------------------  Fonction d'instance  ------------------------ */
    // retourne l'url du png du QR code une fois le fichier cr??
    public function getQRCURL(){
        if (!isset($this->url_qrcode)){
            return false;
        }
        return $this->url_qrcode;
    }
    // retourne l'url du png du PDF une fois le fichier cr??
     public function getPNGURL(){
        if (!isset($this->url_png)){
            return false;
        }
        return $this->url_png;
    }
    //retourne l'URL du pdf une fois le fichier cr??
    public function getPDFURL(){
        if (!isset($this->url_pdf)){
            return false;
        }
        return $this->url_pdf;
    }

    // detruit le fichier PDF si cr??
    public function deletePDFFile(){
        if (!isset($this->url_pdf)){
            return false;
        }
        if(!file_exists($this->url_pdf)){
            return false;
        }

        return unlink($this->url_pdf);

    }
    // detruit le fichier QR code png cr??
    public function deleteQRFile(){
        if (!isset($this->url_qrcode)){
            return false;
        }
        if(!file_exists($this->url_qrcode)){
            return false;
        }

        return unlink($this->url_qrcode);

    }
    // d?truit les 2 fichiers
    public function deleteAllFiles(){
        return $this->deletePDFFile() && $this->deleteQRFile();
    }

    // changement du certif utilis?
    public function setCertif($name){
        
        if(is_file(realpath(dirname(__FILE__) . '/../../').'/3rdparty/Certificate/'.$name)){
            log::add('CovidAttest', 'debug', '╠════ ## change certif name to :'.$name);
            $this->certifNamePerso=$name;
            return true;
        }else{
            return false;
        }
    }
    public function generatePDFCurrentTime($name,$fname,$ddn,$lieu_ddn,$address,$zip,$ville, $motifs){
        $dateAttest=strftime("%d/%m/%G");
        $timeAttest=strftime("%Hh%M");
        return $this->generate_attest($name,$fname,$ddn,$lieu_ddn,$address,$zip,$ville, $motifs, $dateAttest, $timeAttest);
    }
    public function convertPDFtoPNG(){
        if (!isset($this->url_pdf))return false;
        $this->url_png=ATTESTGEN::convert_pdf_to_png($this->url_pdf);
    }
    
    function generate_attest($name,$fname,$ddn,$lieu_ddn,$address,$zip,$ville, $motifs, $dateAttest, $timeAttest, $secondPage=false, $subFolder='') {
        log::add('CovidAttest', 'debug', '║ ╔══════════════════════ Start Generating Attestation ════════════════════ ');
        // verification si le motif est bien un array
        if(!is_array($motifs)){
            if(is_string($motifs)){
                $motifs=array($motifs); // si c'est une string on le met dans un array pour le traiter ult?rieurement
            }else{
                log::add('CovidAttest', 'error', 'Error Motif provided is not an array or a string');
                return false;
            }
        }
         // choix du certif mis en place
         if(isset($this->certifNamePerso)){
            $cn = $this->certifNamePerso;
        }else{
            $cn =ATTESTGEN::certiFName;
        }

        $jsonFile=basename($cn,'.pdf').'.json';

        log::add('CovidAttest', 'debug', '║ ╠════ json file to be used :'.$jsonFile);
        $path=realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'.$jsonFile;
        
        // load du json pour les positions
        if(!is_file($path)){
            log::add('CovidAttest', 'error', 'Error positions definition not found ('.$path.') | try default');
            $path=realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/DEFAULT.json';
            if(!is_file($path)){
                log::add('CovidAttest', 'error', 'Error DEFAUTL positions definition not found ('.$path.')');
                return false;
            }
            
        }
        $stringPos = file_get_contents($path);
        $posDef = json_decode($stringPos, true);
		log::add('CovidAttest','debug', '║ ╠════ json def file file :'.$path);      


        // v?rificaiton existance du dossier
        $path=realpath(dirname(__FILE__). '/../../').'/EXPORT';
      if(!is_dir($path)){
            mkdir($path);
        }
      	// vérification de l'existance du sous dossier
        if($subFolder!='' and !is_numeric($subFolder)){
          $path.='/'.$subFolder;
        }
        if(!is_dir($path)){
            mkdir($path);
        }
      
        // g?n?ration du QR code
        $date_time=$dateAttest.' a '.$timeAttest;
        $qrcode="Cree le: ".$date_time.";\n Nom: ".$name.";\n Prenom: ".$fname.";\n Naissance: ".$ddn." a ".$lieu_ddn.";\n Adresse: ".$address." ".$zip." ".$ville.";\n Sortie: ".$date_time.";\n Motifs: ".implode (",", $motifs).';';

        $this->url_qrcode = $path.'/qrcode_attest'.urlencode(strftime("-%G-%m-%d_%H-%M")."_".self::remove_accents($fname."_".$name)).'.png';
        $qrcode= stripslashes($qrcode);
      try{
        $qrFile = QRcode::png($qrcode,$this->url_qrcode, 'M');
      } catch (Exception $e) {
            log::add('CovidAttest', 'error', 'Error creating PNG file ('.$e->getMessage().')');
        }
		log::add('CovidAttest','debug', '║ ╠════ QR code generated filepath :'.$this->url_qrcode);




        // g?n?ration du PDF
        try {
            $pdf = new FPDI();
            $pdf->addPage();
          	if(!is_file(realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'.$cn)){
              log::add('CovidAttest', 'error','certificate not found');
              return false;
            }

            $pageCount = $pdf->setSourceFile(realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'.$cn);
            $pageId = $pdf->importPage(1);
            $pdf->useTemplate($pageId);


        }
        catch (Exception $e) {
            log::add('CovidAttest', 'error', 'Error creating PDF file ('.$e->getMessage().')');
        }
      	log::add('CovidAttest','debug','║ ╠════ pdf source copies');
        // ecriture
        //$pdf->SetFont('Arial', '', '13');
        $pdf->SetTextColor(0,0,0);

        // NOM
        $pdf->SetFont('Arial', '', $posDef['NOM']['size']);
        $pdf->SetXY($posDef['NOM']["x"], $posDef['NOM']["y"]);
        $pdf->Write(0, utf8_decode($fname.' '.$name));

        //DDN
        $pdf->SetFont('Arial', '', $posDef['DDN']['size']);
        $pdf->SetXY($posDef['DDN']["x"], $posDef['DDN']["y"]);
        $pdf->Write(0, $ddn);

        //LIEU_DDN
        $pdf->SetFont('Arial', '', $posDef['LIEU_DDN']['size']);
        $pdf->SetXY($posDef['LIEU_DDN']["x"], $posDef['LIEU_DDN']["y"]);
        $pdf->Write(0, utf8_decode($lieu_ddn));

        //adresse
        // en plus petit
        //$pdf->SetFont('Arial', '', '10');
        $pdf->SetFont('Arial', '', $posDef['ADRESSE']['size']);
        $pdf->SetXY($posDef['ADRESSE']["x"], $posDef['ADRESSE']["y"]);
        $pdf->Write(0, utf8_decode($address.' '.$zip.' '.$ville));

        // pour la signature
        //ville
        $pdf->SetFont('Arial', '', $posDef['SIG_VILLE']['size']);
        $pdf->SetXY($posDef['SIG_VILLE']["x"], $posDef['SIG_VILLE']["y"]);
        $pdf->Write(0, utf8_decode($ville));

        // date
        $pdf->SetFont('Arial', '', $posDef['SIG_DATE']['size']);
        $pdf->SetXY($posDef['SIG_DATE']["x"], $posDef['SIG_DATE']["y"]);
        $pdf->Write(0, $dateAttest);
        
        //heure
        $pdf->SetFont('Arial', '', $posDef['SIG_HEURE']['size']);
        $pdf->SetXY($posDef['SIG_HEURE']["x"], $posDef['SIG_HEURE']["y"]);
        $pdf->Write(0, $timeAttest);


        ///// pour les motif

        $isOk = true;
      	
        foreach ($motifs as $motif){
          	log::add('CovidAttest','debug','║ ╠════ motif testé :'.$motif);
            switch ($motif) {
                case ATTESTGEN::TRAVAIL:
                    $pdf->SetFont('Arial', '', $posDef['TRAVAIL']['size']);
                    $pdf->SetXY($posDef['TRAVAIL']["x"], $posDef['TRAVAIL']["y"]);
                    break;
                case ATTESTGEN::ENFANTS:
                $pdf->SetFont('Arial', '', $posDef['ENFANT']['size']);
                    $pdf->SetXY($posDef['ENFANT']["x"], $posDef['ENFANT']["y"]);
                    break;
                case ATTESTGEN::SPORT_ANIMAUX:
                $pdf->SetFont('Arial', '', $posDef['LOISIR']['size']);
                    $pdf->SetXY($posDef['LOISIR']["x"], $posDef['LOISIR']["y"]);
                    break;
                case ATTESTGEN::ACHATS:
                $pdf->SetFont('Arial', '', $posDef['ACHAT']['size']);
                    $pdf->SetXY($posDef['ACHAT']["x"], $posDef['ACHAT']["y"]);
                    break;
                case ATTESTGEN::SANTE:
                $pdf->SetFont('Arial', '', $posDef['SANTE']['size']);
                    $pdf->SetXY($posDef['SANTE']["x"], $posDef['SANTE']["y"]);
                    break;
                case ATTESTGEN::FAMILLE:
                $pdf->SetFont('Arial', '', $posDef['FAMILLE']['size']);
                    $pdf->SetXY($posDef['FAMILLE']["x"], $posDef['FAMILLE']["y"]);
                    break;
                case ATTESTGEN::HANDICAP:
                $pdf->SetFont('Arial', '', $posDef['HANDI']['size']);
                    $pdf->SetXY($posDef['HANDI']["x"], $posDef['HANDI']["y"]);
                    break;
                case ATTESTGEN::CONVOCATION:
                $pdf->SetFont('Arial', '', $posDef['JUDIC']['size']);
                    $pdf->SetXY($posDef['JUDIC']["x"], $posDef['JUDIC']["y"]);
                    break;
                case ATTESTGEN::MISSIONS:
                $pdf->SetFont('Arial', '', $posDef['MIG']['size']);
                    $pdf->SetXY($posDef['MIG']["x"], $posDef['MIG']["y"]);
                    break;
                case ATTESTGEN::CULTURE:
                    $pdf->SetFont('Arial', '', $posDef['CULTURE']['size']);
                        $pdf->SetXY($posDef['CULTURE']["x"], $posDef['CULTURE']["y"]);
                        break;
                default:
		log::add('CovidAttest', 'debug', '║ ╠════ !! motif non trouvé <---------');
                    $isOk=false;
                    break;
            }
            if($isOk) $pdf->Write(0, 'X');
        }

        // le png
        $pdf->Image($this->url_qrcode,$posDef['QRcode']["x"], $posDef['QRcode']["y"],$posDef['QRcode']["size"],$posDef['QRcode']["size"],'PNG');

        if($secondPage){
            $pdf->addPage();
		    $pdf->Image($this->url_qrcode, 20, 20, 100, 100);
        }
        // enregistrement

        try {
            //attestation-2020-10-30_07-28_Benjamin Legendre
            $date_time=strftime("-%G-%m-%d_%H-%M");
            $this->url_pdf=$path."/attestation".urlencode($date_time."_".self::remove_accents($fname.'_'.$name).".pdf");
            $pdf->Output($this->url_pdf,'F');
            
        }
        catch (Exception $e) {
            log::add('CovidAttest', 'error', 'Error saving PDF file ('.$e->getMessage().')');
        }

        log::add('CovidAttest','debug','║ ╚═══════════════════════ Fin génération Attestation :'.$this->url_pdf);
        return $this->url_pdf;
    }
  
  public static function remove_accents($string) {
 
    $string=sanitizeAccent($string);
    $string = preg_replace('/\s/', '_', $string);

    return $string;
}

  
   
}
?>
