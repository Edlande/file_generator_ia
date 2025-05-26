#!/usr/bin/env php
<?php

require __DIR__.'/../src/EnvParser.php';
require __DIR__.'/../src/Generator.php';

try {
    // 1. Trouver le .env du projet principal
    $envPath = EnvParser::findProjectEnv(__DIR__);
    echo "Fichier .env trouvé : $envPath\n";

    // 2. Extraire la configuration de la base de données
    $dbConfig = EnvParser::extractDbConfig($envPath);
    
    // 3. Initialiser le générateur
    $generator = new Generator($dbConfig);
    
    // 4. Définir le répertoire de sortie (src/ du projet principal)
    $projectDir = dirname($envPath);
    $generator->setOutputDir("$projectDir/src");
    
    // 5. Lancer la génération
    echo "Début de la génération des entités...\n";
    $generator->generateAll();
    echo "Génération terminée avec succès!\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}