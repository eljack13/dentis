<?php

/**
 * Script completo de testing para el sistema de citas
 * Uso: php test-all.php
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/web.php';
$app = new yii\web\Application($config);

echo "\n";
echo str_repeat("=", 70) . "\n";
echo "🧪 TEST COMPLETO DEL SISTEMA DE CITAS\n";
echo str_repeat("=", 70) . "\n\n";

// TEST 1: SMTP
echo "1️⃣ TEST SMTP\n";
echo str_repeat("-", 70) . "\n";
try {
    $result = Yii::$app->mailer
        ->compose()
        ->setTo('arturo.villa.rey@gmail.com')
        ->setFrom(['noreply@theethcare.com' => 'Theeth CARE'])
        ->setSubject('🧪 Test SMTP')
        ->setHtmlBody('<h2>✅ SMTP funciona correctamente</h2>')
        ->send();

    if ($result) {
        echo "✅ SMTP: OK - Email enviado\n\n";
    } else {
        echo "❌ SMTP: FALLO - Email no se envió\n\n";
    }
} catch (\Exception $e) {
    echo "❌ SMTP: ERROR - " . $e->getMessage() . "\n\n";
}

// TEST 2: CONFIRMACIÓN DE CITA
echo "2️⃣ TEST CONFIRMACIÓN DE CITA\n";
echo str_repeat("-", 70) . "\n";
try {
    $paciente = \app\models\Paciente::findOne(1);
    $servicio = \app\models\Servicio::findOne(1);

    if (!$paciente || !$servicio) {
        echo "❌ CONFIRMACIÓN: No hay paciente/servicio para prueba\n\n";
    } else {
        $cita = new \app\models\Cita();
        $cita->paciente_id = $paciente->id;
        $cita->servicio_id = $servicio->id;
        $cita->inicio = date('Y-m-d H:i:s', strtotime('+1 day 10:00'));
        $cita->fin = date('Y-m-d H:i:s', strtotime('+1 day 11:00'));
        $cita->estado = 'PENDIENTE';
        $cita->canal = 'WEB';  // Debe ser WEB o DENTISTA
        $cita->created_at = time();
        $cita->updated_at = time();

        if ($cita->save()) {
            echo "✅ CONFIRMACIÓN: Cita creada (ID: {$cita->id})\n";
            echo "   📧 Email: " . $paciente->email . "\n";
            echo "   👤 Paciente: " . $paciente->nombre . "\n\n";
        } else {
            echo "❌ CONFIRMACIÓN: Error al crear cita\n";
            foreach ($cita->errors as $field => $errors) {
                echo "   $field: " . implode(', ', $errors) . "\n";
            }
            echo "\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ CONFIRMACIÓN: ERROR - " . $e->getMessage() . "\n\n";
}

// TEST 3: RECORDATORIOS
echo "3️⃣ TEST RECORDATORIOS\n";
echo str_repeat("-", 70) . "\n";
try {
    $resultado = \app\services\ReminderCitaService::procesarRecordatorios();

    echo "✅ RECORDATORIOS: Proceso completado\n";
    echo "   📧 Recordatorios 24h: " . $resultado['recordatorios_24h'] . "\n";
    echo "   🔔 Recordatorios 2h: " . $resultado['recordatorios_2h'] . "\n";
    echo "   📊 Total: " . $resultado['total'] . "\n\n";
} catch (\Exception $e) {
    echo "❌ RECORDATORIOS: ERROR - " . $e->getMessage() . "\n\n";
}

// TEST 4: BASE DE DATOS
echo "4️⃣ TEST BASE DE DATOS\n";
echo str_repeat("-", 70) . "\n";
try {
    $citasCount = \app\models\Cita::find()->count();
    $pacientesCount = \app\models\Paciente::find()->count();
    $serviciosCount = \app\models\Servicio::find()->count();
    $notificacionesCount = \app\models\NotificacionQueue::find()->count();

    echo "✅ BASE DE DATOS: Conectado\n";
    echo "   📊 Citas: $citasCount\n";
    echo "   👥 Pacientes: $pacientesCount\n";
    echo "   🏥 Servicios: $serviciosCount\n";
    echo "   📧 Notificaciones: $notificacionesCount\n\n";
} catch (\Exception $e) {
    echo "❌ BASE DE DATOS: ERROR - " . $e->getMessage() . "\n\n";
}

// TEST 5: CRON COMMAND
echo "5️⃣ TEST COMANDO CRON\n";
echo str_repeat("-", 70) . "\n";
try {
    if (class_exists('app\commands\ReminderController')) {
        echo "✅ COMANDO: ReminderController existe\n";
        echo "   📋 Comando: php yii reminder/process\n";
        echo "   ⏰ Frecuencia recomendada: */5 * * * *\n\n";
    } else {
        echo "❌ COMANDO: No encontrado\n\n";
    }
} catch (\Exception $e) {
    echo "❌ COMANDO: ERROR - " . $e->getMessage() . "\n\n";
}

// TEST 6: SERVICIOS
echo "6️⃣ TEST SERVICIOS\n";
echo str_repeat("-", 70) . "\n";
try {
    if (class_exists('app\services\EmailCitaService')) {
        echo "✅ SERVICIO: EmailCitaService existe\n";
    } else {
        echo "❌ SERVICIO: EmailCitaService no existe\n";
    }

    if (class_exists('app\services\ReminderCitaService')) {
        echo "✅ SERVICIO: ReminderCitaService existe\n";
    } else {
        echo "❌ SERVICIO: ReminderCitaService no existe\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "❌ SERVICIOS: ERROR - " . $e->getMessage() . "\n\n";
}

// RESUMEN FINAL
echo str_repeat("=", 70) . "\n";
echo "✅ TEST COMPLETADO\n";
echo str_repeat("=", 70) . "\n";
echo "\n📋 PRÓXIMOS PASOS:\n";
echo "   1. Configura el CRON: bash setup-cron.sh (Linux/Mac)\n";
echo "   2. O configura Task Scheduler: setup-cron.bat (Windows)\n";
echo "   3. Crea citas de prueba desde la web\n";
echo "   4. Revisa los logs: tail -f runtime/logs/app.log\n";
echo "   5. Verifica los emails en tu bandeja\n\n";
echo "📚 Documentación:\n";
echo "   - IMPLEMENTACION_COMPLETA.md\n";
echo "   - RECORDATORIOS_CITAS.md\n";
echo "   - CONFIRMACION_CITA_EMAIL.md\n\n";

echo "🎉 ¡Sistema listo!\n\n";
