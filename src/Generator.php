<?php

class Generator {
    private $pdo;
    private $outputDir;
    
    public function __construct($dbConfig) {
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset=utf8mb4";
        $this->pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
    
    public function setOutputDir($dir) {
        $this->outputDir = $dir;
    }
    
    public function generateAll() {
        $tables = $this->pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            $this->generateEntity($table);
            // Ajouter ici les autres générations (Repository, etc.)
        }
    }
    
    private function generateEntity($table) {
        // Votre logique existante de génération d'entité
        $className = $this->convertToPascalCase($table);
        $columns = $this->pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        
        $code = "<?php\n\n// Votre entité générée pour $table\n\nclass $className {\n";
        
        foreach ($columns as $col) {
            $code .= "    private \${$col['Field']};\n";
        }
        
        $code .= "}\n";
        
        file_put_contents("{$this->outputDir}/Entity/{$className}.php", $code);
    }
    
    private function convertToPascalCase($str) {
        return str_replace('_', '', ucwords($str, '_'));
    }
}