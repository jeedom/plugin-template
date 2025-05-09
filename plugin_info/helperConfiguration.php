<?php


$scriptFileName = basename(__FILE__);


function replacePluginIdInFiles($directory, $newId) {
    global $scriptFileName;
    $oldId = 'template';
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    
    foreach ($files as $file) {
        if ($file->isFile()) {

            $fileName = $file->getFilename();
            if ($fileName === $scriptFileName) {
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
    global $scriptFileName;
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

echo "Choisissez la catégorie de votre plugin :\n";
echo "1. Securité\n";
echo "2. Protocole Domotique\n";
echo "3. Passereille Domotique\n";
echo "4. Programmation\n";
echo "5. Organisation\n";
echo "6. Météo\n";
echo "7. Communication\n";
echo "8. Objets connectés\n";
echo "9. Multimédia\n";
echo "10. Confort\n";
echo "11. Monitoring\n";
echo "12. Santé\n";
echo "13. Nature\n";
echo "14. Automatisme\n";
echo "15. Energie\n";
echo "16. Autre\n";

$categoryChoice = trim(fgets(STDIN));

switch ($categoryChoice) {
    case '1':
        $dataJson['category'] = 'security';
        break;
    case '2':
        $dataJson['category'] = 'automation protocol';
        break;
    case '3':
        $dataJson['category'] = 'home automation protocol';
        break;
    case '4':
        $dataJson['category'] = 'programming';
        break;
    case '5':
        $dataJson['category'] = 'organization';
        break;
    case '6':
        $dataJson['category'] = 'weather';
        break;
    case '7':
        $dataJson['category'] = 'communication';
        break;
    case '8':
        $dataJson['category'] = 'devicecommunication';
        break;
    case '9':
        $dataJson['category'] = 'multimedia';
        break;
    case '10':
        $dataJson['category'] = 'wellness';
        break;
    case '11':
        $dataJson['category'] = 'monitoring';
        break;
    case '12':
        $dataJson['category'] = 'health';
        break;
    case '13':
        $dataJson['category'] = 'nature';
        break;
    case '14':
        $dataJson['category'] = 'automatisation';
        break;
    case '15':
        $dataJson['category'] = 'energy';
        break;
    case '16':
        $dataJson['category'] = 'other';
        break;
    default:
        echo "Choix invalide. La catégorie par défaut 'programming' sera utilisée.\n";
        $data['category'] = 'programming';
        break;
}

// echo "Vous avez choisir la catégorie '$categoryChoice'.\n";

echo "Votre plugin possede t-il un démon ? (oui/non) : ";
$demonResponse = trim(fgets(STDIN));

if (strtolower($demonResponse) === 'oui' || strtolower($demonResponse) === 'o') {

    $dataJson['hasOwnDeamon'] = true; 
    echo "La prise en compte du démon est activée.\n";
} else {
    echo "Vous avez entré '$demonResponse'. Confirmez-vous cette modification ? (oui/non) : ";
    $confirmationDemon = trim(fgets(STDIN));

    if (strtolower($confirmationDemon) === 'oui' || strtolower($confirmationDemon) === 'o') {
        echo "Suppression du repertoire démon.\n";
        $demonDirectory = __DIR__ . '/../resources';
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($demonDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isFile()) {
                unlink($file->getRealPath());
            } elseif ($file->isDir()) {
                rmdir($file->getRealPath());
            }
        }
        rmdir($demonDirectory);
    }
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

    $dataJson['changelog_beta'] = str_replace('template', $newId, $dataJson['changelog_beta']);
    $dataJson['changelog'] = str_replace('template', $newId, $dataJson['changelog']);
    $dataJson['documentation_beta'] = str_replace('template', $newId, $dataJson['documentation_beta']);
    $dataJson['documentation'] = str_replace('template', $newId, $dataJson['documentation']);

    $directories = [
        __DIR__ . '/../core/class', 
        __DIR__ . '/../desktop',
        __DIR__ . '/../core/php',
        __DIR__ . '/../desktop/modal',
        __DIR__ . '/../desktop/php',
        __DIR__ . '/../desktop/js',
        __DIR__ . '/../core/ajax', 
        __DIR__ ,
    ];
     
    processDirectories($directories, $newId);
    echo "L'ID du plugin a été remplacé et les fichiers ont été renommés avec succès.\n";
} else {
    echo "Modification annulée.\n";
}

$newJsonContent = json_encode($dataJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
file_put_contents($pathInfoJson, $newJsonContent);
