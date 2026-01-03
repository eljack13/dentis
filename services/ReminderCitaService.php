<?php

namespace app\services;

use app\models\Cita;
use app\models\NotificacionQueue;
use Yii;
use yii\db\Expression;

/**
 * Servicio para enviar recordatorios de citas
 */
class ReminderCitaService
{
    const REMINDER_24H = 'REMINDER_24H';
    const REMINDER_2H = 'REMINDER_2H';
    const REMINDER_UPDATE = 'REMINDER_UPDATE';

    /**
     * Procesar recordatorios pendientes
     * Se ejecuta cada X minutos via CRON
     */
    public static function procesarRecordatorios()
    {
        $count24h = 0;
        $count2h = 0;

        // 1. Recordatorios de 24 horas
        $ahora = time();
        $en24h = $ahora + (24 * 3600); // +24 horas
        $en24hMenos5m = $en24h - (5 * 60); // -5 minutos (rango)

        $citas24h = Cita::find()
            ->where(['estado' => 'PENDIENTE'])
            ->andWhere(['>=', 'inicio', date('Y-m-d H:i:s', $en24hMenos5m)])
            ->andWhere(['<=', 'inicio', date('Y-m-d H:i:s', $en24h)])
            ->all();

        foreach ($citas24h as $cita) {
            if (self::enviarRecordatorio($cita, self::REMINDER_24H)) {
                $count24h++;
            }
        }

        // 2. Recordatorios de 2 horas
        $en2h = $ahora + (2 * 3600);
        $en2hMenos5m = $en2h - (5 * 60);

        $citas2h = Cita::find()
            ->where(['estado' => 'PENDIENTE'])
            ->andWhere(['>=', 'inicio', date('Y-m-d H:i:s', $en2hMenos5m)])
            ->andWhere(['<=', 'inicio', date('Y-m-d H:i:s', $en2h)])
            ->all();

        foreach ($citas2h as $cita) {
            if (self::enviarRecordatorio($cita, self::REMINDER_2H)) {
                $count2h++;
            }
        }

        return [
            'recordatorios_24h' => $count24h,
            'recordatorios_2h' => $count2h,
            'total' => $count24h + $count2h,
        ];
    }

    /**
     * Enviar recordatorio de cita
     */
    public static function enviarRecordatorio(Cita $cita, $tipo = self::REMINDER_24H)
    {
        // Validar que paciente tenga email
        if (!$cita->paciente || !$cita->paciente->email) {
            return false;
        }

        // Validar que no se haya enviado ya
        $existe = NotificacionQueue::findOne([
            'cita_id' => $cita->id,
            'tipo' => $tipo,
            'enviado' => 1,
        ]);

        if ($existe) {
            return false; // Ya se envió
        }

        try {
            $html = self::generarHtmlRecordatorio($cita, $tipo);
            $asunto = self::generarAsunto($tipo);

            $result = Yii::$app->mailer
                ->compose()
                ->setTo($cita->paciente->email)
                ->setFrom(['arturo.villa.rey@gmail.com' => 'Theeth CARE'])
                ->setSubject($asunto)
                ->setHtmlBody($html)
                ->send();

            if ($result) {
                // Registrar en queue
                $notif = new NotificacionQueue();
                $notif->cita_id = $cita->id;
                $notif->paciente_id = $cita->paciente_id;
                $notif->tipo = $tipo;
                $notif->enviado = 1;
                $notif->fecha_envio = new Expression('NOW()');
                $notif->save(false);

                Yii::info("✅ Recordatorio $tipo enviado para cita {$cita->id}");
                return true;
            }
        } catch (\Exception $e) {
            Yii::error("❌ Error enviando recordatorio: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Enviar notificación de actualización de cita
     */
    public static function enviarNotificacionActualizacion(Cita $citaAnterior, Cita $citaNueva)
    {
        if (!$citaNueva->paciente || !$citaNueva->paciente->email) {
            return false;
        }

        try {
            $html = self::generarHtmlActualizacion($citaAnterior, $citaNueva);

            $result = Yii::$app->mailer
                ->compose()
                ->setTo($citaNueva->paciente->email)
                ->setFrom(['arturo.villa.rey@gmail.com' => 'Theeth CARE'])
                ->setSubject('Actualización de tu cita - Theeth CARE')
                ->setHtmlBody($html)
                ->send();

            if ($result) {
                Yii::info("✅ Notificación de actualización enviada para cita {$citaNueva->id}");
                return true;
            }
        } catch (\Exception $e) {
            Yii::error("❌ Error enviando notificación de actualización: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Generar asunto del email según tipo
     */
    private static function generarAsunto($tipo)
    {
        $asuntos = [
            self::REMINDER_24H => '⏰ Recordatorio: Tu cita es mañana',
            self::REMINDER_2H => '🔔 Recordatorio: Tu cita es en 2 horas',
            self::REMINDER_UPDATE => '✏️ Tu cita ha sido actualizada',
        ];

        return $asuntos[$tipo] ?? 'Recordatorio de Theeth CARE';
    }

    /**
     * Generar HTML del recordatorio
     */
    private static function generarHtmlRecordatorio(Cita $cita, $tipo)
    {
        $paciente = $cita->paciente;
        $servicio = $cita->servicio;

        $fecha = Yii::$app->formatter->asDate($cita->inicio, 'php:d \\d\\e F \\d\\e Y');
        $hora = Yii::$app->formatter->asTime($cita->inicio, 'php:H:i');

        $nombrePaciente = htmlspecialchars($paciente->nombre ?? 'Paciente', ENT_QUOTES, 'UTF-8');
        $nombreServicio = htmlspecialchars($servicio->nombre ?? 'Servicio', ENT_QUOTES, 'UTF-8');

        $mensaje = match ($tipo) {
            self::REMINDER_24H => '⏰ Te recordamos que tu cita es <strong>mañana a las ' . $hora . '</strong>',
            self::REMINDER_2H => '🔔 Tu cita es en <strong>2 horas</strong>. ¿No olvides confirmarte!',
            default => 'Recordatorio de tu cita'
        };

        $icono = match ($tipo) {
            self::REMINDER_24H => '⏰',
            self::REMINDER_2H => '🔔',
            default => '📋'
        };

        $color = match ($tipo) {
            self::REMINDER_24H => '#3b82f6',
            self::REMINDER_2H => '#f59e0b',
            default => '#6366f1'
        };

        return <<<HTML
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Recordatorio de cita</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<div style="width:100%;padding:26px 12px;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e9eef5;box-shadow:0 14px 44px rgba(17,24,39,.08);">
        
        <!-- HEADER CON ÍCONO -->
        <div style="background:linear-gradient(135deg, $color 0%, rgba(0,0,0,.05) 100%);padding:40px;text-align:center;border-bottom:3px solid $color;">
            <div style="font-size:48px;margin-bottom:12px;">$icono</div>
            <h1 style="margin:0;font-size:28px;color:#1f2937;font-weight:700;">Recordatorio de cita</h1>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div style="padding:40px;">
            <p style="margin:0 0 20px 0;font-size:16px;color:#1f2937;line-height:1.6;">
                ¡Hola <strong>$nombrePaciente</strong>!
            </p>

            <div style="background:#f0f9ff;border-left:4px solid $color;padding:16px;margin:20px 0;border-radius:8px;">
                <p style="margin:0;font-size:16px;color:#1f2937;">
                    $mensaje
                </p>
            </div>

            <!-- DETALLES -->
            <div style="background:#f9fafb;padding:20px;border-radius:12px;margin:20px 0;">
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:12px 0;border-bottom:1px solid #e5e7eb;color:#6b7280;font-size:14px;width:120px;">
                            <strong>📅 Fecha:</strong>
                        </td>
                        <td style="padding:12px 0;padding-left:16px;border-bottom:1px solid #e5e7eb;color:#1f2937;font-size:14px;font-weight:600;">
                            $fecha
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 0;border-bottom:1px solid #e5e7eb;color:#6b7280;font-size:14px;">
                            <strong>🕐 Hora:</strong>
                        </td>
                        <td style="padding:12px 0;padding-left:16px;border-bottom:1px solid #e5e7eb;color:#1f2937;font-size:14px;font-weight:600;">
                            $hora
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 0;color:#6b7280;font-size:14px;">
                            <strong>🏥 Servicio:</strong>
                        </td>
                        <td style="padding:12px 0;padding-left:16px;color:#1f2937;font-size:14px;font-weight:600;">
                            $nombreServicio
                        </td>
                    </tr>
                </table>
            </div>

            <!-- LLAMADA A ACCIÓN -->
            <div style="text-align:center;margin:30px 0;">
                <a href="https://localhost/dentis/web/cita/index" style="display:inline-block;background:$color;color:#ffffff;padding:14px 32px;border-radius:10px;text-decoration:none;font-weight:600;font-size:16px;transition:background 0.3s;">
                    Ver mis citas
                </a>
            </div>

            <!-- CONTACTO -->
            <div style="background:#f0f9ff;padding:20px;border-radius:12px;margin:20px 0;text-align:center;">
                <p style="margin:0;font-size:14px;color:#1f2937;">
                    ¿Necesitas cambiar tu cita? Contáctanos:
                </p>
                <p style="margin:8px 0 0 0;font-size:14px;">
                    <strong>☎️ +1 (234) 567-8900</strong><br>
                    <strong>📧 contacto@theethcare.com</strong>
                </p>
            </div>
        </div>

        <!-- FOOTER -->
        <div style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
            <p style="margin:0;font-size:12px;color:#6b7280;">
                © 2026 Theeth CARE. Todos los derechos reservados.
            </p>
        </div>
    </div>
</div>
</body>
</html>
HTML;
    }

    /**
     * Generar HTML de notificación de actualización
     */
    private static function generarHtmlActualizacion(Cita $anterior, Cita $nueva)
    {
        $paciente = $nueva->paciente;
        $servicio = $nueva->servicio;

        $fechaNueva = Yii::$app->formatter->asDate($nueva->inicio, 'php:d \\d\\e F \\d\\e Y');
        $horaNueva = Yii::$app->formatter->asTime($nueva->inicio, 'php:H:i');

        $fechaAnterior = Yii::$app->formatter->asDate($anterior->inicio, 'php:d \\d\\e F \\d\\e Y');
        $horaAnterior = Yii::$app->formatter->asTime($anterior->inicio, 'php:H:i');

        $nombrePaciente = htmlspecialchars($paciente->nombre ?? 'Paciente', ENT_QUOTES, 'UTF-8');
        $nombreServicio = htmlspecialchars($servicio->nombre ?? 'Servicio', ENT_QUOTES, 'UTF-8');

        $cambio = '';
        if ($anterior->inicio !== $nueva->inicio) {
            $cambio = "Fecha/Hora: <s>$fechaAnterior a las $horaAnterior</s> → <strong>$fechaNueva a las $horaNueva</strong>";
        }
        if ($anterior->servicio_id !== $nueva->servicio_id) {
            $cambio .= "<br>Servicio: Cambió a <strong>$nombreServicio</strong>";
        }

        return <<<HTML
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Cita actualizada</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
<div style="width:100%;padding:26px 12px;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e9eef5;box-shadow:0 14px 44px rgba(17,24,39,.08);">
        
        <div style="background:linear-gradient(135deg, #8b5cf6 0%, rgba(0,0,0,.05) 100%);padding:40px;text-align:center;border-bottom:3px solid #8b5cf6;">
            <div style="font-size:48px;margin-bottom:12px;">✏️</div>
            <h1 style="margin:0;font-size:28px;color:#1f2937;font-weight:700;">Tu cita ha sido actualizada</h1>
        </div>

        <div style="padding:40px;">
            <p style="margin:0 0 20px 0;font-size:16px;color:#1f2937;line-height:1.6;">
                ¡Hola <strong>$nombrePaciente</strong>!
            </p>

            <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;margin:20px 0;border-radius:8px;">
                <p style="margin:0;font-size:16px;color:#1f2937;line-height:1.6;">
                    Tu cita ha sido modificada. Aquí están los cambios:
                </p>
            </div>

            <div style="background:#f0fdf4;padding:20px;border-radius:12px;margin:20px 0;border-left:4px solid #22c55e;">
                <p style="margin:0;font-size:14px;color:#1f2937;line-height:1.8;">
                    $cambio
                </p>
            </div>

            <div style="background:#f9fafb;padding:20px;border-radius:12px;margin:20px 0;">
                <h3 style="margin:0 0 12px 0;color:#1f2937;font-size:14px;">📋 Detalles actuales:</h3>
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:8px 0;color:#6b7280;font-size:13px;width:100px;"><strong>📅 Fecha:</strong></td>
                        <td style="padding:8px 0;padding-left:16px;color:#1f2937;font-size:13px;font-weight:600;">$fechaNueva</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0;color:#6b7280;font-size:13px;"><strong>🕐 Hora:</strong></td>
                        <td style="padding:8px 0;padding-left:16px;color:#1f2937;font-size:13px;font-weight:600;">$horaNueva</td>
                    </tr>
                </table>
            </div>

            <p style="margin:20px 0;font-size:14px;color:#6b7280;text-align:center;">
                Si tienes dudas, contacta con nosotros.
            </p>
        </div>

        <div style="background:#f9fafb;padding:20px;text-align:center;border-top:1px solid #e5e7eb;">
            <p style="margin:0;font-size:12px;color:#6b7280;">
                © 2026 Theeth CARE. Todos los derechos reservados.
            </p>
        </div>
    </div>
</div>
</body>
</html>
HTML;
    }
}
