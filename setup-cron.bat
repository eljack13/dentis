@echo off
REM Script para configurar recordatorios en Windows con Task Scheduler
REM Ejecuta: setup-cron.bat
REM Este script crea una tarea que se ejecuta cada 5 minutos

REM Verificar permisos de administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ❌ Este script requiere permisos de ADMINISTRADOR
    echo Por favor, ejecuta Command Prompt como Administrador
    pause
    exit /b 1
)

setlocal enabledelayedexpansion

REM Definir rutas
set PROJECT_PATH=C:\laragon\www\dentis
set PHP_PATH=C:\xampp\php\php.exe
REM O usar la ruta de Laragon:
REM set PHP_PATH=C:\laragon\bin\php\php-8.3.29-nts-Win32-vs16-x64\php.exe

REM Nombre de la tarea
set TASK_NAME=DentisCitaReminders

echo.
echo =========================================
echo Configurando Task Scheduler para Reminders
echo =========================================
echo.

REM Crear la tarea (se ejecuta cada 5 minutos)
schtasks /create /tn %TASK_NAME% /tr "cmd /c cd /d %PROJECT_PATH% && %PHP_PATH% yii reminder/process" /sc minute /mo 5 /f

if %errorLevel% equ 0 (
    echo.
    echo ✅ Tarea creada exitosamente!
    echo.
    echo 📋 Detalles:
    echo    Nombre: %TASK_NAME%
    echo    Frecuencia: Cada 5 minutos
    echo    Comando: %PHP_PATH% yii reminder/process
    echo.
    echo Para ver/editar la tarea:
    echo    1. Abre Task Scheduler (Programador de tareas)
    echo    2. Busca: %TASK_NAME%
    echo.
) else (
    echo.
    echo ❌ Error creando la tarea
    echo.
)

pause
