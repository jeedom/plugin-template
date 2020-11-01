<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
//use QRcode;

require_once(dirname(__FILE__).'/../../3rdparty/FPDF/fpdf.php');
require_once(dirname(__FILE__).'/../../3rdparty/FPDI/autoload.php');
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
    protected $motPos; // array avec les position des cases à cocher motif

    protected $url_qrcode; // addresse du Ppng du qrcode au besoin
    protected $url_pdf; // addresse du Ppng du qrcode au besoin
    protected $certifNamePerso; // si on défini une url perso

    function __construct()
    {
    }

    // retourne l'url du png du QR code une fois le fichier créé
    public function getPNGURL(){
        if (!isset($this->url_qrcode)){
            return false;
        }
        return $this->url_qrcode;
    }
    //retourne l'URL du pdf une fois le fichier créé
    public function getPDFURL(){
        if (!isset($this->url_pdf)){
            return false;
        }
        return $this->url_pdf;
    }

    // detruit le fichier PDF si créé
    public function deletePDFFile(){
        if (!isset($this->url_pdf)){
            return false;
        }
        if(!file_exists($this->url_pdf)){
            return false;
        }

        return unlink($this->url_pdf);

    }
    // detruit le fichier QR code png créé
    public function deleteQRFile(){
        if (!isset($this->url_qrcode)){
            return false;
        }
        if(!file_exists($this->url_qrcode)){
            return false;
        }

        return unlink($this->url_qrcode);

    }
    // détruit les 2 fichiers
    public function deleteAllFiles(){
        return $this->deletePDFFile() && $this->deleteQRFile();
    }

    // changement du certif utilisé
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
                $motifs=array($motifs); // si c'est une string on le met dans un array pour le traiter ultérieurement
            }else{
                log::add('CovidAttest', 'error', 'Error Motif provided is not an array or a string');
                return false;
            }
        }
        $path=realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/position.json';
        log::add('CovidAttest','debug', 'file :'.$path);
        // load du json pour les positions
        if(!is_file($path)){
            log::add('CovidAttest', 'error', 'Error positions definition not found ('.$path.')');
            return false;
        }
        $stringPos = file_get_contents($path);
        $json_pos = json_decode($stringPos, true);

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
            $posDef = $json_pos[ATTESTGEN::certiFName]; // par défaut on utilise celui du certif par défaut
        }


        // vérificaiton existance du dossier
        $path=realpath(dirname(__FILE__). '/../../').'/EXPORT';
        if(!is_dir($path)){
            mkdir($path);
        }
        // génération du QR code
        $date_time=$dateAttest.' a '.$timeAttest;
        $qrcode="Cree le: ".$date_time.";\n Nom: ".$name.";\n Prenom: ".$fname.";\n Naissance: ".$ddn." a ".$lieu_ddn.";\n Adresse: ".$address." ".$zip." ".$ville.";\n Sortie: ".$date_time."\n Motifs: ".implode (",", $motifs);

        $this->url_qrcode = $path.'/qrcode_attest'.$fname.'.png';
        $qrcode= stripslashes($qrcode);
        $qrFile = QRcode::png($qrcode,$this->url_qrcode, 'M');





        // génération du PDF
        try {
            $pdf = new FPDI();
            $pdf->addPage();

            $pageCount = $pdf->setSourceFile(realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'.$cn);
            $pageId = $pdf->importPage(1);
            $pdf->useTemplate($pageId);


        }
        catch (Exception $e) {
            log::add('CovidAttest', 'error', 'Error creating PDF file ('.$e->getMessage().')');
        }
        // ecriture
        $pdf->SetFont('Arial', '', '13');
        $pdf->SetTextColor(0,0,0);

        // NOM
        $pdf->SetXY($posDef['NOM']["x"], $posDef['NOM']["y"]);
        $pdf->Write(0, $fname.' '.$name);

        //DDN
        $pdf->SetXY($posDef['DDN']["x"], $posDef['DDN']["y"]);
        $pdf->Write(0, $ddn);

        //LIEU_DDN
        $pdf->SetXY($posDef['LIEU_DDN']["x"], $posDef['LIEU_DDN']["y"]);
        $pdf->Write(0, $lieu_ddn);

        //adresse
        // en plus petit
        $pdf->SetFont('Arial', '', '10');
        $pdf->SetXY($posDef['ADRESSE']["x"], $posDef['ADRESSE']["y"]);
        $pdf->Write(0, $address.' '.$zip.' '.$ville);

        // pour la signature
        //ville
        $pdf->SetFont('Arial', '', '13');
        $pdf->SetXY($posDef['SIG_VILLE']["x"], $posDef['SIG_VILLE']["y"]);
        $pdf->Write(0, $ville);

        // date
        $pdf->SetXY($posDef['SIG_DATE']["x"], $posDef['SIG_DATE']["y"]);
        $pdf->Write(0, $dateAttest);
        //heure
        $pdf->SetXY($posDef['SIG_HEURE']["x"], $posDef['SIG_HEURE']["y"]);
        $pdf->Write(0, $timeAttest);


        ///// pour les motif

        $pdf->SetFont('Arial', '', $posDef['QRcode']['crossSize']);
        $isOk = true;
        foreach ($motifs as $motif){
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
		    $pdf->Image($path.'/qrcode_attest'.$fname.'.png', 20, 20, 100, 100);
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
}
?>