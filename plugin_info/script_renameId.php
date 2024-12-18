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

echo "Entrez le nouvel ID du plugin : ";
$newId = trim(fgets(STDIN));

echo "Vous avez entré '$newId'. Confirmez-vous cette modification ? (oui/non) : ";
$confirmation = trim(fgets(STDIN));

$directories = [
    __DIR__ . '/../core/class', 
    __DIR__ . '/../desktop',
    __DIR__ . '/../core/php',
    __DIR__ . '/../desktop/modal',
    __DIR__ . '/../desktop/php',
    __DIR__ . '/../desktop/js',
    __DIR__ ,
];


if (strtolower($confirmation) === 'oui' || strtolower($confirmation) === 'o') {
    processDirectories($directories, $newId);
    echo "L'ID du plugin a été remplacé et les fichiers ont été renommés avec succès.\n";
} else {
    echo "Modification annulée.\n";
}
