<?php
require_once __DIR__  . '/../../../../core/php/core.inc.php';
require_once __DIR__  . '/../../../../core/php/utils.inc.php';

class ATTESTJSON {
	
	public static function extract_json_from_filename($fname){
		$path=realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'.basename($fname, '.pdf').'.json';
        
        // load du json pour les positions
        if(!is_file($path)){
            log::add('CovidAttest', 'error', 'Error positions definition not found ('.$path.')');
            return;
        }
        
		$stringPos = file_get_contents($path);
        $posDef = json_decode($stringPos, true);
		log::add('CovidAttest','debug', '╠════ json def file file :'.$path);
		
        return $posDef;
    }
    
    public static function save_json($datas){
        

        $datas=str_replace('&','#',$datas);
        log::add('CovidAttest', 'debug', '╔═══════════════════════ save json :'.$datas);

        $pattern='/([^\#\=]+)\=([^\#\=]+)/';

        preg_match_all($pattern,'#'.$datas.'#', $matches);
        
        log::add('CovidAttest', 'debug', '╠════ match count:'.count($matches[1]));
        $newValue=array();
        for($i=0; $i<count($matches[1]); $i++){
            //log::add('CovidAttest', 'debug', ' match :'.$i.' - '.$matches[1][$i].' : '.$matches[2][$i]);
            $newValue[$matches[1][$i]]=$matches[2][$i];

        }
        // nload du json
        $path=realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'.basename($newValue['filename'], '.pdf').'.json';
        if(!is_file($path)){
            log::add('CovidAttest', 'error', 'Error Saving : positions definition not found ('.$path.')');
            return 'error json file not found';
        }
        log::add('CovidAttest', 'debug', '╠════ Json file found ('.$path.')');
        $stringPos = file_get_contents($path);
        $posDef = json_decode($stringPos, true);

        foreach($posDef as $case_name => $props){
            foreach($props as $prop_name => $prop_value){
                if(array_key_exists($case_name.'_'.$prop_name, $newValue)){
                    log::add('CovidAttest', 'debug', '╠════ new prop value :'.$case_name.'_'.$prop_name.' | '.$newValue[$case_name.'_'.$prop_name]);
                    $posDef[$case_name][$prop_name]=$newValue[$case_name.'_'.$prop_name];
                }else{
                    log::add('CovidAttest', 'debug', '╠════ new prop value NOT FOUND :'.$case_name.'_'.$prop_name);
                }
                
            }
        }

        // saving json
        $jsonencoded=json_encode($posDef);
        
        $fp = fopen($path, 'w');
        fwrite($fp, $jsonencoded);
        fclose($fp);
        log::add('CovidAttest', 'debug', '╚═══════════════════════ json SAVED :'.$path);
    }

    public static function share_conf_file($filename){
        log::add('CovidAttest', 'debug', '╔═══════════════════════ receive share file '.$filename);
        $pathCertif=realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/';
        if(!is_dir($pathCertif)){
            log::add('CovidAttest', 'error', ' Certificate path not found');
            return false;
        }
        
        $pathExport=realpath(dirname(__FILE__). '/../../').'/EXPORT/';
        if(!is_dir($pathExport)){
                mkdir($pathExport);
        }
        $zipName=$pathExport.'TEST/';//
        // vérification de l'existance du sous dossier
        if(!is_dir($zipName)){
            mkdir($zipName);
        }
        $zipName.='CovidAttest_CONF.zip';
        $certifPath=$pathCertif.$filename;
        $jsonPath=$pathCertif.basename($filename, '.pdf').'.json';

        if(!is_file($certifPath)){
            log::add('CovidAttest', 'error', ' Impossible de trouver le certificat :'.$certifPath);
            return false;
        }
        if(!is_file($jsonPath)){
            log::add('CovidAttest', 'error', ' Impossible de trouver le fichier de configuration :'.$jsonPath);
            return false;
        }


        log::add('CovidAttest','debug','╠════ création du ZIP :'.$zipName);
        $zip = new ZipArchive();
        if(is_file($zipName)){
            if ($zip->open($zipName, ZipArchive::OVERWRITE)!==TRUE) {
                log::add('CovidAttest', 'error', ' Impossible de créer le zip :'.$zipName);
                return false;
            }

        }else{
            if ($zip->open($zipName, ZipArchive::CREATE)!==TRUE) {
                log::add('CovidAttest', 'error', ' Impossible de créer le zip :'.$zipName);
                return false;
            }
        }
        
        $zip->addFile($certifPath, basename($certifPath));
        $zip->addFile($jsonPath, basename($jsonPath));
        $zip->close();
        $zipURL ='/plugins/CovidAttest/EXPORT/TEST/'.basename($zipName);
        log::add('CovidAttest', 'debug', '╠════ Zip créé '.$zipName);
        log::add('CovidAttest', 'debug', '╠════ URL '.$zipURL);
        log::add('CovidAttest', 'debug', '╚═══════════════════════ Fin Export ZIP');
        return  $zipURL;
    }
	
}
?>