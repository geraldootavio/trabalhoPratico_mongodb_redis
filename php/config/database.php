<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/config/database.php
 * OBJETIVO: Gerenciar a conexão simples com MongoDB e Redis com tratamento de erros.
 * ==============================================================================
 */

// Inicia a sessão PHP para armazenar mensagens do sistema (ex: "Inscrição realizada com sucesso!")
if (ob_get_level() === 0) {
    ob_start();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações de Conexão (Padrões para execução local)
define('MONGO_HOST', '127.0.0.1');
define('MONGO_PORT', '27017');
define('MONGO_DB_NAME', 'congresso_ads');

define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6379');

// Tenta incluir o autoloader do Composer (se instalado)
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

/**
 * Classe utilitária para conectar ao MongoDB e Redis de forma transparente.
 */
class Database {
    private static $mongoClient = null;
    private static $mongoDb = null;
    private static $redisClient = null;

    /**
     * Retorna a conexão com a base de dados do MongoDB.
     */
    public static function getMongoDb() {
        if (self::$mongoDb !== null) {
            return self::$mongoDb;
        }

        try {
            // Se a extensão MongoDB oficial ou a biblioteca do Composer estiver presente
            if (class_exists('MongoDB\Client')) {
                $uri = "mongodb://" . MONGO_HOST . ":" . MONGO_PORT;
                self::$mongoClient = new MongoDB\Client($uri);
                self::$mongoDb = self::$mongoClient->selectDatabase(MONGO_DB_NAME);
                return self::$mongoDb;
            } else {
                // Caso o driver oficial não esteja instalado no PHP local
                return null;
            }
        } catch (\Exception $e) {
            // Em caso de erro na conexão, retorna null para tratamento amigável na interface
            return null;
        }
    }

    /**
     * Retorna a conexão com o servidor Redis.
     */
    public static function getRedis() {
        if (self::$redisClient !== null) {
            return self::$redisClient;
        }

        try {
            // Opção 1: Usando Predis (biblioteca PHP pura que não precisa de extensão C)
            if (class_exists('Predis\Client')) {
                self::$redisClient = new Predis\Client([
                    'scheme' => 'tcp',
                    'host'   => REDIS_HOST,
                    'port'   => REDIS_PORT,
                    'timeout' => 1.5
                ]);
                // Testa conexão simples
                self::$redisClient->ping();
                return self::$redisClient;
            }
            
            // Opção 2: Usando a extensão phpredis nativa (se instalada)
            if (class_exists('Redis')) {
                $redis = new Redis();
                $redis->connect(REDIS_HOST, (int)REDIS_PORT, 1.5);
                self::$redisClient = $redis;
                return self::$redisClient;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
