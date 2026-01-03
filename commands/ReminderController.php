<?php

namespace app\commands;

use app\services\ReminderCitaService;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Comando para procesar recordatorios de citas
 * Uso: php yii reminder/process
 */
class ReminderController extends Controller
{
    public $defaultAction = 'process';

    /**
     * Procesar y enviar recordatorios pendientes
     */
    public function actionProcess()
    {
        $this->stdout("🔔 Iniciando proceso de recordatorios...\n");
        $this->stdout(str_repeat('=', 60) . "\n");

        try {
            $resultado = ReminderCitaService::procesarRecordatorios();

            $this->stdout("✅ Proceso completado:\n");
            $this->stdout("   📧 Recordatorios 24h: {$resultado['recordatorios_24h']}\n");
            $this->stdout("   🔔 Recordatorios 2h: {$resultado['recordatorios_2h']}\n");
            $this->stdout("   📊 Total enviados: {$resultado['total']}\n");
            $this->stdout(str_repeat('=', 60) . "\n");

            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("❌ Error: " . $e->getMessage() . "\n");
            Yii::error("Error en ReminderController: " . $e->getMessage());
            return ExitCode::DATAERR;
        }
    }

    public function actionHelp()
    {
        $this->stdout("Comandos disponibles:\n");
        $this->stdout("  php yii reminder/process - Procesar recordatorios de citas\n");
        return ExitCode::OK;
    }
}
