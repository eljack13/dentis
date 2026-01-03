#!/bin/bash

# Script para configurar CRON en Linux/Mac
# Uso: bash setup-cron.sh

# Ruta del proyecto (CAMBIAR SEGÚN TU INSTALACIÓN)
PROJECT_PATH="/var/www/html/dentis"

# O en Windows (Laragon):
# PROJECT_PATH="C:\\laragon\\www\\dentis"

# Agregar cron job que se ejecute cada 5 minutos
# (5 * * * * = cada 5 minutos)

# Para Linux/Mac:
CRON_COMMAND="*/5 * * * * cd $PROJECT_PATH && php yii reminder/process >> /var/log/dentis-reminders.log 2>&1"

# Agregar a crontab
(crontab -l 2>/dev/null; echo "$CRON_COMMAND") | crontab -

echo "✅ CRON configurado para ejecutarse cada 5 minutos"
echo "📋 Comando: $CRON_COMMAND"
echo ""
echo "Para ver los logs:"
echo "  tail -f /var/log/dentis-reminders.log"
