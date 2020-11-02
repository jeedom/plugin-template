<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
//use QRcode;

require_once(dirname(__FILE__) . '/../../3rdparty/FPDF/fpdf.php');
require_once(dirname(__FILE__) . '/../../3rdparty/FPDI/autoload.php');
require_once(dirname(__FILE__) . '/../../3rdparty/phpqrcode/phpqrcode.php');

require_once __DIR__  . '/../../../../core/php/core.inc.php';


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

    const certiFName = 'certificate.301020.pdf';//'30-10-2020-attestation-de-deplacement-derogatoire.pdf'; //

    //public $aMemberVar = 'aMemberVar Member Variable';
    public $generate_attest = 'generate_attest';

    protected $idPos; // array avec les positions des identifiants
    protected $motPos; // array avec les position des cases ? cocher motif

    protected $url_qrcode; // addresse du Ppng du qrcode au besoin
    protected $url_pdf; // addresse du Ppng du qrcode au besoin
    protected $certifNamePerso; // si on d?fini une url perso

    function __construct()
    {
    }

    // retourne l'url du png du QR code une fois le fichier cr??
    public function getPNGURL(){
        if (!isset($this->url_qrcode)){
            return false;
        }
        return $this->url_qrcode;
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
        if(is_file(dirname(__FILE__) . '/Certificate/'.$name)){
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

    function generate_attest($name,$fname,$ddn,$lieu_ddn,$address,$zip,$ville, $motifs, $dateAttest, $timeAttest, $secondPage=false) {

        // verification si le motif est bien un array
        if(!is_array($motifs)){
            if(is_string($motifs)){
                $motifs=array($motifs); // si c'est une string on le met dans un array pour le traiter ult?rieurement
            }else{
                log::add('CovidAttest', 'error', 'Error Motif provided is not an array or a string');
                return false;
            }
        }
        $path=realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/position.json';
        
        // load du json pour les positions
        if(!is_file($path)){
            log::add('CovidAttest', 'error', 'Error positions definition not found ('.$path.')');
            return false;
        }
        $stringPos = file_get_contents($path);
        $json_pos = json_decode($stringPos, true);
		log::add('CovidAttest','debug', 'json def file file :'.$path);
        // choix du certif mis en place
        if(isset($this->certifNamePerso)){
            $cn = $this->certifNamePerso;
        }else{
            $cn =ATTESTGEN::certiFName;
        }

        // selection dans le json de la position du certificat de nom
        if (isset( $json_pos[$cn])){
            $posDef = $json_pos[$cn];

        }else{
            log::add('CovidAttest', 'error', 'certificate name not found in json');
            $posDef = $json_pos[ATTESTGEN::certiFName]; // par d?faut on utilise celui du certif par d?faut
        }
      


        // v?rificaiton existance du dossier
        $path=realpath(dirname(__FILE__). '/../../').'/EXPORT';
        if(!is_dir($path)){
            mkdir($path);
        }
        // g?n?ration du QR code
        $date_time=$dateAttest.' a '.$timeAttest;
        $qrcode="Cree le: ".$date_time.";\n Nom: ".$name.";\n Prenom: ".$fname.";\n Naissance: ".$ddn." a ".$lieu_ddn.";\n Adresse: ".$address." ".$zip." ".$ville.";\n Sortie: ".$date_time."\n Motifs: ".implode (",", $motifs);

        $this->url_qrcode = $path.'/qrcode_attest'.urlencode($fname).'.png';
        $qrcode= stripslashes($qrcode);
      try{
        $qrFile = QRcode::png($qrcode,$this->url_qrcode, 'M');
      } catch (Exception $e) {
            log::add('CovidAttest', 'error', 'Error creating PNG file ('.$e->getMessage().')');
        }
		log::add('CovidAttest','debug', 'QR code generated filepath :'.$this->url_qrcode);




        // g?n?ration du PDF
        try {
            $pdf = new FPDI();
            $pdf->addPage();
          	if(!is_file(realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'.$cn)){
              log::add('CovidAttest', 'error','certificate not found');
            }

            $pageCount = $pdf->setSourceFile(realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'.$cn);
            $pageId = $pdf->importPage(1);
            $pdf->useTemplate($pageId);


        }
        catch (Exception $e) {
            log::add('CovidAttest', 'error', 'Error creating PDF file ('.$e->getMessage().')');
        }
      	log::add('CovidAttest','debug','pdf source copies');
        // ecriture
        $pdf->SetFont('Arial', '', '13');
        $pdf->SetTextColor(0,0,0);

        // NOM
        $pdf->SetXY($posDef['NOM']["x"], $posDef['NOM']["y"]);
        $pdf->Write(0, $this->remove_accents($fname.' '.$name));

        //DDN
        $pdf->SetXY($posDef['DDN']["x"], $posDef['DDN']["y"]);
        $pdf->Write(0, $ddn);

        //LIEU_DDN
        $pdf->SetXY($posDef['LIEU_DDN']["x"], $posDef['LIEU_DDN']["y"]);
        $pdf->Write(0, $this->remove_accents($lieu_ddn));

        //adresse
        // en plus petit
        $pdf->SetFont('Arial', '', '10');
        $pdf->SetXY($posDef['ADRESSE']["x"], $posDef['ADRESSE']["y"]);
        $pdf->Write(0, $this->remove_accents($address.' '.$zip.' '.$ville));

        // pour la signature
        //ville
        $pdf->SetFont('Arial', '', '13');
        $pdf->SetXY($posDef['SIG_VILLE']["x"], $posDef['SIG_VILLE']["y"]);
        $pdf->Write(0, $this->remove_accents($ville));

        // date
        $pdf->SetXY($posDef['SIG_DATE']["x"], $posDef['SIG_DATE']["y"]);
        $pdf->Write(0, $dateAttest);
        //heure
        $pdf->SetXY($posDef['SIG_HEURE']["x"], $posDef['SIG_HEURE']["y"]);
        $pdf->Write(0, $timeAttest);


        ///// pour les motif

        $pdf->SetFont('Arial', '', $posDef['crossSize']);
        $isOk = true;
      	
        foreach ($motifs as $motif){
          	log::add('CovidAttest','debug','motif testé :'.$motif);
            switch ($motif) {
                case ATTESTGEN::TRAVAIL:
                    $pdf->SetXY($posDef['TRAVAIL']["x"], $posDef['TRAVAIL']["y"]);
                    break;
                case ATTESTGEN::ENFANTS:
                    $pdf->SetXY($posDef['ENFANT']["x"], $posDef['ENFANT']["y"]);
                    break;
                case ATTESTGEN::SPORT_ANIMAUX:
                    $pdf->SetXY($posDef['LOISIR']["x"], $posDef['LOISIR']["y"]);
                    break;
                case ATTESTGEN::ACHATS:
                    $pdf->SetXY($posDef['ACHAT']["x"], $posDef['ACHAT']["y"]);
                    break;
                case ATTESTGEN::SANTE:
                    $pdf->SetXY($posDef['SANTE']["x"], $posDef['SANTE']["y"]);
                    break;
                case ATTESTGEN::FAMILLE:
                    $pdf->SetXY($posDef['FAMILLE']["x"], $posDef['FAMILLE']["y"]);
                    break;
                case ATTESTGEN::HANDICAP:
                    $pdf->SetXY($posDef['HANDI']["x"], $posDef['HANDI']["y"]);
                    break;
                case ATTESTGEN::CONVOCATION:
                    $pdf->SetXY($posDef['JUDIC']["x"], $posDef['JUDIC']["y"]);
                    break;
                case ATTESTGEN::MISSIONS:
                    $pdf->SetXY($posDef['MIG']["x"], $posDef['MIG']["y"]);
                    break;
                default:
                    $isOk=false;
                    break;
            }
            if($isOk) $pdf->Write(0, 'X');
        }

        // le png
        $pdf->Image($this->url_qrcode,$posDef['QRcode']["x"], $posDef['QRcode']["y"],32,32,'PNG');

        if($secondPage){
            $pdf->addPage();
		    $pdf->Image($path.'/qrcode_attest'.urlencode($fname).'.png', 20, 20, 100, 100);
        }
        // enregistrement

        try {
            //attestation-2020-10-30_07-28_Benjamin Legendre
            $date_time=strftime("-%G-%m-%d_%H-%M");
            $this->url_pdf=$path."/attestation".urlencode($date_time."_".$fname.' '.$name.".pdf");
            $pdf->Output($this->url_pdf,'F');
        }
        catch (Exception $e) {
            log::add('CovidAttest', 'error', 'Error saving PDF file ('.$e->getMessage().')');
        }
        return $this->url_pdf;
    }
  
   private function remove_accents($string) {
    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );

    $string = strtr($string, $chars);

    return $string;
}
}
?>