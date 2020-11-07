<?php

try {
/* * ***************************Includes********************************* */
    require_once __DIR__  . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    
    
    
    ajax::init();
    require_once __DIR__  . '/../class/AttestGen.class.php';
   
    
    $valid_extensions = array('pdf'); // valid extensions
    $path = realpath(dirname(__FILE__). '/../../').'/3rdparty/Certificate/'; // upload directory

    if( $_FILES['file'])
    {
        
        $img = $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];
        
        log::add('CovidAttest', 'debug', 'request upload for :'.$img);
        //test si exist edéjà
        log::add('CovidAttest', 'debug', 'test exit file :'.$path.$img.' | '.file_exists ($path.strtolower($img)));
        if(file_exists ($path.strtolower($img))){
            //ajax::success('File Already loaded');
            ajax::error('File Already loaded', 666);
        }else{
            // get uploaded file's extension
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
            // can upload same image using rand function
            $final_image = $img;
            // check's valid format
            if(in_array($ext, $valid_extensions)) 
            { 
                $pathFinal= $path.strtolower($final_image); 
                if(move_uploaded_file($tmp,$pathFinal)) 
                {
                    log::add('CovidAttest', 'debug', ' upload file :'.$img);

                    // copie du json par défaut pour les positions
                    log::add('CovidAttest', 'debug', ' copy json provided'.array_key_exists('json_file',$_FILES));

                    if(array_key_exists('json_file',$_FILES)){
                        $imgJ = $_FILES['json_file']['name'];
                        $tmpJ = $_FILES['json_file']['tmp_name'];
                        log::add('CovidAttest', 'debug', 'request upload for json conf :'.$imgJ);

                        $pathjson=$path.basename($pathFinal,'.pdf').'.json';
                        if(move_uploaded_file($tmpJ,$pathjson)) {
                            log::add('CovidAttest', 'debug', ' upload json file :'.$pathjson.' from '.$imgJ);
                            ajax::success('ok');

                        }else{
                            log::add('CovidAttest', 'error', ' Unable to download json file :'.$pathjson .' from '.$imgJ);
                            ajax::error('Unable to find download json pos file', 666);
                        }


                        
                    }else{// on copie le json par défaut
                        $jsonDefaultPath = $path.'DEFAULT.json';
                        if(!is_file($jsonDefaultPath)){
                            log::add('CovidAttest', 'error', ' Unable to find default json pos file :'.$jsonDefaultPath );
                            ajax::error('Unable to find default json pos file', 666);
                        }
                        $jsonTargetName=$path.basename($pathFinal, '.pdf').'.json';
    
                        log::add('CovidAttest', 'debug', ' copy json default  :'.$jsonDefaultPath.' || to :'.$jsonTargetName);
    
                        if(copy($jsonDefaultPath, $jsonTargetName)){
                            ajax::success('ok');
                        }else{
                            log::add('CovidAttest', 'error', ' Unable to copy default json pos file :'.$jsonDefaultPath );
                            ajax::error('Unable to copy default json pos file', 666);
                        }
                    }

                   

                }else 
                {
                    log::add('CovidAttest', 'debug', 'fail move uploaded file ');
                    //ajax::success('fail move uploaded file ');
                    ajax::error('fail move uploaded file ', 666);
                } 
            } 
            else 
            {
                log::add('CovidAttest', 'debug', 'fail  upload wrong filetype :'.$ext.' found and need :'.implode(', ',$valid_extensions));
                //ajax::success('fail  upload wrong filetype :'.$ext.' found and need :'.implode(', ',$valid_extensions));
                ajax::error(' wrong filetype :'.$ext.' loaded and required :'.implode(', ',$valid_extensions), 666);
            }
        }
    }
    

} catch (Exception $e) {
	ajax::error(displayExeption($e), $e->getCode());
}































?>