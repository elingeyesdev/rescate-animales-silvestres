<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el envío de correos y verifica la configuración de Brevo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== VERIFICANDO CONFIGURACIÓN DE CORREO ===');
        $this->line('');
        
        // Mostrar configuración
        $this->line('MAIL_MAILER: ' . config('mail.default'));
        $this->line('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->line('MAIL_PORT: ' . config('mail.mailers.smtp.port'));
        $this->line('MAIL_USERNAME: ' . config('mail.mailers.smtp.username'));
        $this->line('MAIL_PASSWORD: ' . (config('mail.mailers.smtp.password') ? '***CONFIGURADO***' : '❌ NO CONFIGURADO'));
        $this->line('MAIL_ENCRYPTION: ' . (config('mail.mailers.smtp.encryption') ?: '❌ NO CONFIGURADO (debe ser tls)'));
        $this->line('MAIL_FROM_ADDRESS: ' . config('mail.from.address'));
        $this->line('MAIL_FROM_NAME: ' . config('mail.from.name'));
        $this->line('');
        
        // Verificar problemas comunes
        $issues = [];
        if (config('mail.default') !== 'smtp') {
            $issues[] = 'MAIL_MAILER no está configurado como "smtp"';
        }
        if (empty(config('mail.mailers.smtp.host'))) {
            $issues[] = 'MAIL_HOST no está configurado';
        }
        if (empty(config('mail.mailers.smtp.username'))) {
            $issues[] = 'MAIL_USERNAME no está configurado';
        }
        if (empty(config('mail.mailers.smtp.password'))) {
            $issues[] = 'MAIL_PASSWORD no está configurado';
        }
        if (empty(config('mail.mailers.smtp.encryption'))) {
            $issues[] = 'MAIL_ENCRYPTION no está configurado (debe ser "tls" para puerto 587)';
        }
        
        if (!empty($issues)) {
            $this->error('⚠️  PROBLEMAS ENCONTRADOS:');
            foreach ($issues as $issue) {
                $this->error('  - ' . $issue);
            }
            $this->line('');
        } else {
            $this->info('✅ Configuración básica parece correcta');
            $this->line('');
        }
        
        // Probar envío
        $testEmail = $this->argument('email') ?: config('mail.from.address');
        
        $this->info('=== PROBANDO ENVÍO DE CORREO ===');
        $this->line('Enviando a: ' . $testEmail);
        $this->line('');
        
        try {
            Mail::raw('Este es un correo de prueba desde Laravel con Brevo - ' . now()->format('d-m-Y H:i:s'), function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Brevo - ' . now()->format('H:i:s'));
            });
            
            $this->info('✅ Correo enviado sin errores en Laravel');
            $this->line('');
            $this->line('📋 PRÓXIMOS PASOS:');
            $this->line('1. Ve a Brevo → Sending → Email Logs');
            $this->line('2. Debe aparecer el correo en menos de 1 minuto');
            $this->line('3. Si aparece: el problema es de entrega (revisa spam)');
            $this->line('4. Si NO aparece: el problema es de conexión SMTP');
            $this->line('');
            $this->line('Revisa también: storage/logs/laravel.log para más detalles');
            
        } catch (\Exception $e) {
            $this->error('❌ ERROR al enviar correo:');
            $this->error('Mensaje: ' . $e->getMessage());
            $this->error('Archivo: ' . $e->getFile() . ':' . $e->getLine());
            $this->line('');
            $this->line('Stack trace completo guardado en storage/logs/laravel.log');
            
            Log::error('Error en test:email', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
        
        return 0;
    }
}
