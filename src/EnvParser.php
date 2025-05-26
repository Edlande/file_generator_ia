<?php

class EnvParser
{
    public static function findProjectEnv(string $startDir): string
    {
        $currentDir = realpath($startDir);
        
        while ($currentDir !== '/') {
            $envPath = $currentDir . '/.env';
            if (file_exists($envPath)) {
                return $envPath;
            }
            $currentDir = dirname($currentDir);
        }
        
        throw new RuntimeException("Aucun fichier .env trouvé dans les répertoires parents");
    }

    public static function extractDbConfig(string $envPath): array
    {
        $content = file_get_contents($envPath);
        
        // Supporte DATABASE_URL=... et DATABASE_URL="..."
        if (!preg_match('/DATABASE_URL=(?:"([^"]+)"|([^"\s]+))/', $content, $matches)) {
            throw new RuntimeException("Variable DATABASE_URL introuvable dans le .env");
        }
        
        $url = $matches[1] ?? $matches[2];
        $parsedUrl = parse_url($url);
        
        if ($parsedUrl === false) {
            throw new RuntimeException("Format de DATABASE_URL invalide");
        }
        
        return [
            'driver' => 'mysql',
            'host' => $parsedUrl['host'] ?? 'localhost',
            'port' => $parsedUrl['port'] ?? 3306,
            'database' => isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : '',
            'username' => $parsedUrl['user'] ?? '',
            'password' => $parsedUrl['pass'] ?? '',
            'charset' => 'utf8mb4'
        ];
    }
}