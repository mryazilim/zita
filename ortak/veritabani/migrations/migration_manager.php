<?php
/**
 * Zita Projesi - Migration Manager
 * 
 * Bu sınıf migration'ları yönetir.
 * 
 * @author Zita Projesi
 * @version v25.1.0.0
 * @date 2025-01-13
 */

require_once __DIR__ . '/../../kutuphaneler/veritabani_sinifi.php';

class MigrationManager {
    private $db;
    private $migrations_dir;
    
    public function __construct() {
        $this->db = new VeritabaniSinifi();
        $this->migrations_dir = __DIR__;
        $this->createMigrationsTable();
    }
    
    /**
     * Migration tablosunu oluştur
     */
    private function createMigrationsTable() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration_name VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_migration_name (migration_name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
    
    /**
     * Tüm migration'ları çalıştır
     */
    public function runAll() {
        echo "🚀 Tüm migration'lar çalıştırılıyor...\n\n";
        
        $migrations = $this->getAvailableMigrations();
        $executed = 0;
        
        foreach ($migrations as $migration) {
            if ($this->isMigrationExecuted($migration)) {
                echo "⏭️  Migration zaten çalıştırılmış: {$migration}\n";
                continue;
            }
            
            echo "🔄 Migration çalıştırılıyor: {$migration}\n";
            
            if ($this->runMigration($migration)) {
                $this->markMigrationAsExecuted($migration);
                $executed++;
                echo "✅ Migration başarıyla çalıştırıldı: {$migration}\n\n";
            } else {
                echo "❌ Migration başarısız: {$migration}\n";
                break;
            }
        }
        
        echo "🎉 Migration işlemi tamamlandı! {$executed} migration çalıştırıldı.\n";
    }
    
    /**
     * Belirli bir migration'ı çalıştır
     */
    public function runMigration($migrationName) {
        $migrationFile = $this->migrations_dir . "/{$migrationName}.php";
        
        if (!file_exists($migrationFile)) {
            echo "❌ Migration dosyası bulunamadı: {$migrationFile}\n";
            return false;
        }
        
        require_once $migrationFile;
        $className = $this->getMigrationClassName($migrationName);
        
        if (!class_exists($className)) {
            echo "❌ Migration sınıfı bulunamadı: {$className}\n";
            return false;
        }
        
        $migration = new $className();
        return $migration->up();
    }
    
    /**
     * Belirli bir migration'ı geri al
     */
    public function rollbackMigration($migrationName) {
        $migrationFile = $this->migrations_dir . "/{$migrationName}.php";
        
        if (!file_exists($migrationFile)) {
            echo "❌ Migration dosyası bulunamadı: {$migrationFile}\n";
            return false;
        }
        
        require_once $migrationFile;
        $className = $this->getMigrationClassName($migrationName);
        
        if (!class_exists($className)) {
            echo "❌ Migration sınıfı bulunamadı: {$className}\n";
            return false;
        }
        
        $migration = new $className();
        $result = $migration->down();
        
        if ($result) {
            $this->markMigrationAsNotExecuted($migrationName);
        }
        
        return $result;
    }
    
    /**
     * Mevcut migration'ları listele
     */
    public function listMigrations() {
        echo "📋 Mevcut Migration'lar:\n\n";
        
        $migrations = $this->getAvailableMigrations();
        
        foreach ($migrations as $migration) {
            $status = $this->isMigrationExecuted($migration) ? "✅ Çalıştırılmış" : "⏳ Bekliyor";
            echo "   {$migration}: {$status}\n";
        }
    }
    
    /**
     * Migration durumunu sıfırla
     */
    public function resetMigrations() {
        echo "🔄 Migration durumu sıfırlanıyor...\n";
        
        $this->db->query("DELETE FROM migrations");
        echo "✅ Migration durumu sıfırlandı\n";
    }
    
    /**
     * Mevcut migration dosyalarını getir
     */
    private function getAvailableMigrations() {
        $files = glob($this->migrations_dir . "/migration_*.php");
        $migrations = [];
        
        foreach ($files as $file) {
            $filename = basename($file, '.php');
            // Migration manager'ı hariç tut
            if ($filename !== 'migration_manager') {
                $migrations[] = $filename;
            }
        }
        
        sort($migrations);
        return $migrations;
    }
    
    /**
     * Migration'ın çalıştırılıp çalıştırılmadığını kontrol et
     */
    private function isMigrationExecuted($migrationName) {
        $result = $this->db->get_row(
            "SELECT id FROM migrations WHERE migration_name = :name",
            ['name' => $migrationName]
        );
        
        return $result !== false;
    }
    
    /**
     * Migration'ı çalıştırılmış olarak işaretle
     */
    private function markMigrationAsExecuted($migrationName) {
        $this->db->insert('migrations', [
            'migration_name' => $migrationName
        ]);
    }
    
    /**
     * Migration'ı çalıştırılmamış olarak işaretle
     */
    private function markMigrationAsNotExecuted($migrationName) {
        $this->db->delete(
            'migrations',
            'migration_name = :name',
            ['name' => $migrationName]
        );
    }
    
    /**
     * Migration sınıf adını oluştur
     */
    private function getMigrationClassName($migrationName) {
        $parts = explode('_', $migrationName);
        $className = '';
        
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        return $className;
    }
}

// Komut satırından çalıştırılıyorsa
if (php_sapi_name() === 'cli') {
    $manager = new MigrationManager();
    
    $command = $argv[1] ?? 'run';
    
    switch ($command) {
        case 'run':
            $manager->runAll();
            break;
        case 'list':
            $manager->listMigrations();
            break;
        case 'reset':
            $manager->resetMigrations();
            break;
        case 'rollback':
            $migrationName = $argv[2] ?? null;
            if ($migrationName) {
                $manager->rollbackMigration($migrationName);
            } else {
                echo "❌ Migration adı belirtilmedi\n";
            }
            break;
        default:
            echo "Kullanım: php migration_manager.php [run|list|reset|rollback]\n";
    }
}
?>
