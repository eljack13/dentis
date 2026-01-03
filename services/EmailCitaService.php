<?php

namespace app\services;

use app\models\Cita;
use Yii;

/**
 * Servicio para enviar emails de citas
 */
class EmailCitaService
{
    /**
     * Enviar confirmación de cita creada
     */
    public static function enviarConfirmacionCita(Cita $cita)
    {
        try {
            Yii::warning("🔔 Iniciando envío de confirmación de cita ID: {$cita->id}");

            if (!$cita->paciente) {
                Yii::error("❌ Cita {$cita->id}: Paciente no encontrado");
                return false;
            }

            if (!$cita->paciente->email) {
                Yii::error("❌ Cita {$cita->id}: Paciente {$cita->paciente->nombre} no tiene email");
                return false;
            }

            Yii::warning("📧 Enviando email a: {$cita->paciente->email}");
            Yii::warning("👤 Paciente: {$cita->paciente->nombre}");
            Yii::warning("🏥 Servicio: " . ($cita->servicio->nombre ?? 'N/A'));

            $html = self::generarHtmlConfirmacion($cita);

            $result = Yii::$app->mailer
                ->compose()
                ->setTo($cita->paciente->email)
                ->setFrom(['arturo.villa.rey@gmail.com' => 'Theeth CARE'])
                ->setSubject('Confirmación de tu cita | Theeth CARE')
                ->setHtmlBody($html)
                ->send();

            if ($result) {
                Yii::warning("✅ Email enviado exitosamente a: {$cita->paciente->email}");
                return true;
            }

            Yii::error("❌ Email no se envió (sin excepción)");
            return false;
        } catch (\Throwable $e) {
            Yii::error("❌ Error enviando email de cita {$cita->id}: " . $e->getMessage());
            Yii::error("Stack: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Generar HTML del email de confirmación
     */
    /**
     * Generar HTML del email de confirmación (ELEGANTE / PREMIUM)
     */
    private static function generarHtmlConfirmacion(Cita $cita)
    {
        $paciente = $cita->paciente;
        $servicio = $cita->servicio;

        $fecha = Yii::$app->formatter->asDate($cita->inicio, 'php:l, d \\d\\e F \\d\\e Y');
        $hora  = Yii::$app->formatter->asTime($cita->inicio, 'php:H:i');

        $nombrePaciente = htmlspecialchars($paciente->nombre ?? 'Paciente', ENT_QUOTES, 'UTF-8');
        $nombreServicio = htmlspecialchars($servicio->nombre ?? 'Servicio', ENT_QUOTES, 'UTF-8');

        $notasHtml = '';
        if (!empty($cita->notas)) {
            $notas = nl2br(htmlspecialchars($cita->notas, ENT_QUOTES, 'UTF-8'));
            $notasHtml = <<<HTML
                    <tr>
                    <td style="padding:14px 0 0 0;">
                        <div style="border:1px solid #e9eef5;border-radius:14px;padding:14px 14px;background:#ffffff;">
                        <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#6b7280;font-weight:800;margin:0 0 8px 0;">
                            Notas
                        </div>
                        <div style="font-size:14px;line-height:1.65;color:#111827;font-weight:600;">
                            {$notas}
                        </div>
                        </div>
                    </td>
                    </tr>
        HTML;
        }

        return <<<HTML
        <!doctype html>
        <html lang="es">
        <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Confirmación de cita</title>
        </head>
        <body style="margin:0;padding:0;background:#f4f6f9;">
        <div style="width:100%;padding:26px 12px;">
            <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #e9eef5;box-shadow:0 14px 44px rgba(17,24,39,.08);">

            <!-- Línea superior sutil (elegante, no llamativa) -->
            <div style="height:3px;background:#111827;"></div>

            <!-- Header -->
            <div style="padding:26px 26px 14px 26px;background:#ffffff;">
                <table role="presentation" style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="vertical-align:middle;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" style="min-width:40px;min-height:40px;">
                            <rect width="40" height="40" rx="8" fill="#f0f4f8"/>
                            <path d="M20 8c-2.5 0-4.5 2-4.5 4.5v10c0 3 1.5 4.5 4.5 4.5s4.5-1.5 4.5-4.5v-10c0-2.5-2-4.5-4.5-4.5zm0 18c-3.5 0-6 2.5-6 6v3h12v-3c0-3.5-2.5-6-6-6z" fill="#1e40af" stroke="#1e40af" stroke-width="0.5"/>
                        </svg>
                        <div style="font-size:15px;font-weight:900;letter-spacing:.02em;color:#111827;">
                        Theeth CARE
                        </div>
                    </div>
                    </td>
                    <td style="vertical-align:middle;text-align:right;">
                    <span style="display:inline-block;font-size:11px;letter-spacing:.12em;text-transform:uppercase;font-weight:900;color:#111827;background:#f3f4f6;border:1px solid #e5e7eb;padding:8px 10px;border-radius:999px;">
                        Confirmada
                    </span>
                    </td>
                </tr>
                </table>

                <div style="margin:16px 0 6px 0;font-size:22px;font-weight:950;letter-spacing:-.02em;color:#111827;line-height:1.25;">
                Confirmación de tu cita
                </div>
                <div style="margin:0;font-size:14px;color:#6b7280;line-height:1.7;">
                Hemos agendado tu cita correctamente. Te compartimos los detalles para que los tengas a la mano.
                </div>
            </div>

            <!-- Contenido -->
            <div style="padding:0 26px 18px 26px;">
                <div style="padding:14px 0 0 0;font-size:14px;color:#111827;line-height:1.7;">
                Hola <strong>{$nombrePaciente}</strong>,
                </div>
                <div style="margin:6px 0 16px 0;font-size:14px;color:#6b7280;line-height:1.7;">
                Gracias por agendar con nosotros. Si necesitas reprogramar o cancelar, contáctanos con anticipación.
                </div>

                <!-- Details (tabla para compatibilidad de email) -->
                <table role="presentation" style="width:100%;border-collapse:separate;border-spacing:0 12px;">
                <tr>
                    <td>
                    <div style="border:1px solid #e9eef5;border-radius:16px;background:#fbfcfe;overflow:hidden;">
                        <table role="presentation" style="width:100%;border-collapse:collapse;">
                        <tr>
                            <td style="padding:14px 14px;border-bottom:1px solid #e9eef5;">
                            <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#6b7280;font-weight:900;margin:0 0 6px 0;">
                                Fecha
                            </div>
                            <div style="font-size:15px;color:#111827;font-weight:900;line-height:1.35;">
                                {$fecha}
                            </div>
                            </td>
                            <td style="padding:14px 14px;border-bottom:1px solid #e9eef5;">
                            <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#6b7280;font-weight:900;margin:0 0 6px 0;">
                                Hora
                            </div>
                            <div style="font-size:15px;color:#111827;font-weight:900;line-height:1.35;">
                                {$hora} hrs
                            </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:14px 14px;">
                            <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#6b7280;font-weight:900;margin:0 0 6px 0;">
                                Servicio
                            </div>
                            <div style="font-size:15px;color:#111827;font-weight:900;line-height:1.35;">
                                {$nombreServicio}
                            </div>
                            </td>
                            <td style="padding:14px 14px;">
                            <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#6b7280;font-weight:900;margin:0 0 6px 0;">
                                Estado
                            </div>
                            <div style="font-size:15px;color:#111827;font-weight:900;line-height:1.35;">
                                Confirmada
                            </div>
                            </td>
                        </tr>
                        </table>
                    </div>
                    </td>
                </tr>

                {$notasHtml}
                </table>

                <!-- Contacto -->
                <div style="margin-top:16px;border-top:1px solid #eef2f7;padding-top:14px;">
                <table role="presentation" style="width:100%;border-collapse:collapse;">
                    <tr>
                    <td style="vertical-align:top;padding-right:12px;">
                        <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#6b7280;font-weight:900;margin:0 0 8px 0;">
                        Contacto
                        </div>
                        <div style="font-size:13px;line-height:1.7;color:#111827;">
                        Tel: <strong>+52 81 1044 1030</strong><br/>
                        Correo: <strong>·</strong>
                        </div>
                    </td>
                    <td style="vertical-align:top;padding-left:12px;">
                        <div style="font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:#6b7280;font-weight:900;margin:0 0 8px 0;">
                        Recomendación
                        </div>
                        <div style="font-size:13px;line-height:1.7;color:#111827;">
                        Llega 10 minutos antes para tu cita.
                        </div>
                    </td>
                    </tr>
                </table>
                </div>
            </div>

            <!-- Footer -->
            <div style="padding:16px 26px 20px 26px;background:#fbfcfe;border-top:1px solid #eef2f7;text-align:center;">
                <div style="font-size:12px;color:#6b7280;line-height:1.6;">
                © 2026 Theeth CARE. Todos los derechos reservados.<br/>
                Este es un mensaje automático, por favor no respondas a este correo.
                </div>
            </div>

            </div>
        </div>
        </body>
        </html>
        HTML;
    }
}
