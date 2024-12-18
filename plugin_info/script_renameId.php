<?php

function replacePluginIdInFiles($directory, $newId) {
    $oldId = 'template';
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    
    foreach ($files as $file) {
        if ($file->isFile()) {

            $fileName = $file->getFilename();
            if ($fileName === 'script_renameId.php') {
                continue;
            }

            $filePath = $file->getRealPath();
            $fileContents = file_get_contents($filePath);

            $lines = explode(PHP_EOL, $fileContents);
            foreach ($lines as &$line) {
                if (strpos($line, "include_file('core', 'plugin.template', 'js');") === false) {
                    $line = str_replace($oldId, $newId, $line);
                }
            }
            $fileContents = implode(PHP_EOL, $lines);
            
            file_put_contents($filePath, $fileContents);
        }
    }
}

function renameFiles($directory, $newId) {
    $oldId = 'template';
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    
    foreach ($files as $file) {
        if ($file->isFile()) {
            $filePath = $file->getRealPath();
            $fileName = $file->getFilename();
            
            if (strpos($fileName, $oldId) !== false) {
                $newFileName = str_replace($oldId, $newId, $fileName);
                $newFilePath = $file->getPath() . DIRECTORY_SEPARATOR . $newFileName;
                rename($filePath, $newFilePath);
            }
        }
    }
}

function processDirectories($directories, $newId) {
    foreach ($directories as $directory) {
        replacePluginIdInFiles($directory, $newId);
        renameFiles($directory, $newId);
    }
}

$pathInfoJson = __DIR__ . '/info.json';
$jsonContent = file_get_contents($pathInfoJson);
$dataJson  = json_decode($jsonContent, true);


echo "Quel est le nom de votre plugin ? ";
$namePlugin = trim(fgets(STDIN));


if ($namePlugin !== '') {
    $dataJson['name'] = $namePlugin;
    echo "Plugin renommé.\n";
} 

echo "Votre plugin possede t-il un démon ? (oui/non) : ";
$demonResponse = trim(fgets(STDIN));

if (strtolower($demonResponse) === 'oui' || strtolower($demonResponse) === 'o') {

    $dataJson['hasOwnDeamon'] = true; 
    echo "La prise en compte du démon est activée.\n";
} 


echo "Votre plugin possede t-il des dépendances ? (oui/non) : ";
$dependancyResponse = trim(fgets(STDIN));



if (strtolower($dependancyResponse) === 'oui' || strtolower($dependancyResponse) === 'o') {

    $dataJson['hasDependency'] = true; 
    echo "La prise en compte des dépendances est activée.\n";
} 


echo "Quel est l'ID du plugin : ";
$newId = trim(fgets(STDIN));

echo "Vous avez entré '$newId'. Confirmez-vous cette modification ? (oui/non) : ";
$confirmation = trim(fgets(STDIN));


if (strtolower($confirmation) === 'oui' || strtolower($confirmation) === 'o') {

    $dataJson['id'] = $newId; 

    $directories = [
        __DIR__ . '/../core/class', 
        __DIR__ . '/../desktop',
        __DIR__ . '/../core/php',
        __DIR__ . '/../desktop/modal',
        __DIR__ . '/../desktop/php',
        __DIR__ . '/../desktop/js',
        __DIR__ ,
    ];
     
    processDirectories($directories, $newId);
    echo "L'ID du plugin a été remplacé et les fichiers ont été renommés avec succès.\n";
} else {
    echo "Modification annulée.\n";
}

$newJsonContent = json_encode($dataJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
file_put_contents($pathInfoJson, $newJsonContent);
