<?php
/**
 * ==============================================================================
 * TRABALHO PRÁTICO - BANCO DE DADOS 2 (IFMG CAMPUS OURO PRETO)
 * CURSO: Análise e Desenvolvimento de Sistemas (ADS)
 * CENÁRIO: Congresso Acadêmico de ADS (CONADS 2026)
 * ARQUIVO: php/config/database.php
 * OBJETIVO: Gerenciar conexão transparente com MongoDB Atlas e Redis.
 * ==============================================================================
 */

// Inicia a sessão PHP para armazenar mensagens e estados
if (ob_get_level() === 0) {
    ob_start();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Função utilitária para carregar variáveis de ambiente de um arquivo .env
 */
if (!function_exists('loadEnvConfig')) {
    function loadEnvConfig($path) {
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv("{$name}={$value}");
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}

// 1. Carrega configurações do arquivo .env (se existir)
loadEnvConfig(__DIR__ . '/../.env');

// 2. Carrega configurações sobrescritas via interface web (env_config.json)
$jsonConfigPath = __DIR__ . '/env_config.json';
if (file_exists($jsonConfigPath)) {
    $jsonConfig = json_decode(file_get_contents($jsonConfigPath), true);
    if (is_array($jsonConfig)) {
        foreach ($jsonConfig as $key => $val) {
            putenv("{$key}={$val}");
            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
        }
    }
}

// Define constantes padrão de fallback
if (!defined('MONGO_HOST')) define('MONGO_HOST', getenv('MONGO_HOST') ?: '127.0.0.1');
if (!defined('MONGO_PORT')) define('MONGO_PORT', getenv('MONGO_PORT') ?: '27017');
if (!defined('MONGO_DB_NAME')) define('MONGO_DB_NAME', getenv('MONGO_DB_NAME') ?: 'congresso_ads');

if (!defined('REDIS_HOST')) define('REDIS_HOST', getenv('REDIS_HOST') ?: '127.0.0.1');
if (!defined('REDIS_PORT')) define('REDIS_PORT', getenv('REDIS_PORT') ?: '6379');

// Tenta incluir o autoloader do Composer
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

/**
 * Classe utilitária para gerenciar conexões MongoDB (Local/Atlas) e Redis (Local/Cloud).
 */
class Database {
    private static $mongoClient = null;
    private static $mongoDb = null;
    private static $redisClient = null;

    /**
     * Retorna o objeto da base de dados no MongoDB (funciona tanto com MongoDB Local quanto Mongo Atlas).
     */
    public static function getMongoDb() {
        if (self::$mongoDb !== null) {
            return self::$mongoDb;
        }

        try {
            if (!class_exists('MongoDB\Client')) {
                return null;
            }

            // Tenta obter a URI personalizada do MongoDB Atlas ou local
            $uri = getenv('MONGO_URI');
            if (empty($uri)) {
                $host = getenv('MONGO_HOST') ?: MONGO_HOST;
                $port = getenv('MONGO_PORT') ?: MONGO_PORT;
                $user = getenv('MONGO_USER');
                $pass = getenv('MONGO_PASS');
                if (!empty($user) && !empty($pass)) {
                    $uri = "mongodb://{$user}:{$pass}@{$host}:{$port}";
                } else {
                    $uri = "mongodb://{$host}:{$port}";
                }
            }

            $dbName = getenv('MONGO_DB_NAME') ?: MONGO_DB_NAME;

            // Opções com timeout curto para resiliência de conexão em conexões cloud (Mongo Atlas)
            $uriOptions = [
                'serverSelectionTimeoutMS' => 5000,
                'connectTimeoutMS' => 5000
            ];

            self::$mongoClient = new MongoDB\Client($uri, [], $uriOptions);
            self::$mongoDb = self::$mongoClient->selectDatabase($dbName);

            // Executa comando ping para confirmar que o cluster/servidor respondeu
            self::$mongoDb->command(['ping' => 1]);

            return self::$mongoDb;
        } catch (\Exception $e) {
            self::$mongoClient = null;
            self::$mongoDb = null;
            return null;
        } catch (\Throwable $t) {
            self::$mongoClient = null;
            self::$mongoDb = null;
            return null;
        }
    }

    /**
     * Retorna a conexão com o servidor Redis (Local, Cloud ou Upstash).
     */
    public static function getRedis() {
        if (self::$redisClient !== null) {
            return self::$redisClient;
        }

        try {
            $redisUri = getenv('REDIS_URI');
            $host     = getenv('REDIS_HOST') ?: REDIS_HOST;
            $port     = (int)(getenv('REDIS_PORT') ?: REDIS_PORT);
            $password = getenv('REDIS_PASSWORD');
            $scheme   = getenv('REDIS_SCHEME') ?: 'tcp';

            // Opção 1: Usando Predis
            if (class_exists('Predis\Client')) {
                if (!empty($redisUri)) {
                    self::$redisClient = new Predis\Client($redisUri, ['timeout' => 3.0]);
                } else {
                    $parameters = [
                        'scheme'   => $scheme,
                        'host'     => $host,
                        'port'     => $port,
                        'timeout'  => 3.0
                    ];
                    if (!empty($password)) {
                        $parameters['password'] = $password;
                    }
                    self::$redisClient = new Predis\Client($parameters);
                }

                self::$redisClient->ping();
                return self::$redisClient;
            }

            // Opção 2: Usando a extensão phpredis nativa
            if (class_exists('Redis')) {
                $redis = new Redis();
                $redis->connect($host, $port, 3.0);
                if (!empty($password)) {
                    $redis->auth($password);
                }
                $redis->ping();
                self::$redisClient = $redis;
                return self::$redisClient;
            }

            return null;
        } catch (\Exception $e) {
            self::$redisClient = null;
            return null;
        } catch (\Throwable $t) {
            self::$redisClient = null;
            return null;
        }
    }

    /**
     * Diagnóstico completo do MongoDB (detecta Mongo Atlas vs Local, coleções e latência)
     */
    public static function getMongoStatus() {
        $uri = getenv('MONGO_URI');
        if (empty($uri)) {
            $uri = "mongodb://" . (getenv('MONGO_HOST') ?: MONGO_HOST) . ":" . (getenv('MONGO_PORT') ?: MONGO_PORT);
        }
        
        $isAtlas = (strpos($uri, 'mongodb+srv://') !== false || strpos($uri, '.mongodb.net') !== false);
        $maskedUri = preg_replace('/:(.*?)@/', ':****@', $uri);

        $start = microtime(true);
        $db = self::getMongoDb();
        $latency = round((microtime(true) - $start) * 1000, 2);

        if ($db !== null) {
            try {
                $collections = [];
                $cursor = $db->listCollectionNames();
                foreach ($cursor as $name) {
                    $collections[] = $name;
                }

                return [
                    'status' => true,
                    'type' => $isAtlas ? 'MongoDB Atlas (Cloud Nuvem ☁️)' : 'MongoDB Local / Servidor 🖥️',
                    'is_atlas' => $isAtlas,
                    'database' => getenv('MONGO_DB_NAME') ?: MONGO_DB_NAME,
                    'uri_masked' => $maskedUri,
                    'latency_ms' => $latency,
                    'collections_count' => count($collections),
                    'collections' => $collections,
                    'error' => null
                ];
            } catch (\Exception $e) {
                return [
                    'status' => false,
                    'type' => $isAtlas ? 'MongoDB Atlas (Cloud Nuvem ☁️)' : 'MongoDB Local 🖥️',
                    'is_atlas' => $isAtlas,
                    'database' => getenv('MONGO_DB_NAME') ?: MONGO_DB_NAME,
                    'uri_masked' => $maskedUri,
                    'latency_ms' => null,
                    'collections_count' => 0,
                    'collections' => [],
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'status' => false,
            'type' => $isAtlas ? 'MongoDB Atlas (Cloud Nuvem ☁️)' : 'MongoDB Local 🖥️',
            'is_atlas' => $isAtlas,
            'database' => getenv('MONGO_DB_NAME') ?: MONGO_DB_NAME,
            'uri_masked' => $maskedUri,
            'latency_ms' => null,
            'collections_count' => 0,
            'collections' => [],
            'error' => 'Não foi possível conectar. Verifique a URI/Credenciais do MongoDB.'
        ];
    }

    /**
     * Diagnóstico completo do Redis (detecta Redis Cloud vs Local, quantidade de chaves e latência)
     */
    public static function getRedisStatus() {
        $redisUri = getenv('REDIS_URI');
        $host     = getenv('REDIS_HOST') ?: REDIS_HOST;
        $port     = getenv('REDIS_PORT') ?: REDIS_PORT;
        $isCloud  = (!empty($redisUri) && strpos($redisUri, '127.0.0.1') === false && strpos($redisUri, 'localhost') === false) 
                    || ($host !== '127.0.0.1' && $host !== 'localhost');

        $start = microtime(true);
        $redis = self::getRedis();
        $latency = round((microtime(true) - $start) * 1000, 2);

        if ($redis !== null) {
            try {
                $keysCount = 0;
                if (method_exists($redis, 'dbsize')) {
                    $keysCount = $redis->dbsize();
                } elseif (method_exists($redis, 'keys')) {
                    $keys = $redis->keys('*');
                    $keysCount = is_array($keys) ? count($keys) : 0;
                }

                return [
                    'status' => true,
                    'type' => $isCloud ? 'Redis Cloud / Remote ⚡' : 'Redis Local ⚡',
                    'is_cloud' => $isCloud,
                    'host' => $host,
                    'port' => $port,
                    'latency_ms' => $latency,
                    'keys_count' => $keysCount,
                    'error' => null
                ];
            } catch (\Exception $e) {
                return [
                    'status' => false,
                    'type' => $isCloud ? 'Redis Cloud / Remote ⚡' : 'Redis Local ⚡',
                    'is_cloud' => $isCloud,
                    'host' => $host,
                    'port' => $port,
                    'latency_ms' => null,
                    'keys_count' => 0,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'status' => false,
            'type' => $isCloud ? 'Redis Cloud / Remote ⚡' : 'Redis Local ⚡',
            'is_cloud' => $isCloud,
            'host' => $host,
            'port' => $port,
            'latency_ms' => null,
            'keys_count' => 0,
            'error' => 'Não foi possível conectar ao servidor Redis.'
        ];
    }

    /**
     * Salva as configurações de conexão no arquivo env_config.json
     */
    public static function saveConfig($newConfig) {
        $file = __DIR__ . '/env_config.json';
        $existing = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        if (!is_array($existing)) $existing = [];

        $merged = array_merge($existing, $newConfig);
        file_put_contents($file, json_encode($merged, JSON_PRETTY_PRINT));

        // Reseta instâncias estáticas para forçar reconexão com os novos dados
        self::$mongoClient = null;
        self::$mongoDb = null;
        self::$redisClient = null;
    }
}
