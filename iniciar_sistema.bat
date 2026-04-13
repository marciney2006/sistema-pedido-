@echo off
echo ========================================
echo   SISTEMA DE PEDIDOS DE LANCHES
echo ========================================
echo.
echo Este script ajuda a configurar e acessar o sistema
echo.
echo Opcoes:
echo [1] Abrir XAMPP Control Panel
echo [2] Abrir phpMyAdmin
echo [3] Abrir Sistema (navegador)
echo [4] Abrir Painel Admin
echo [5] Verificar Instalacao
echo [6] Abrir pasta do projeto
echo [7] Sair
echo.
set /p choice="Escolha uma opcao (1-7): "

if "%choice%"=="1" goto xampp
if "%choice%"=="2" goto phpmyadmin
if "%choice%"=="3" goto sistema
if "%choice%"=="4" goto admin
if "%choice%"=="5" goto verificar
if "%choice%"=="6" goto pasta
if "%choice%"=="7" goto sair

echo Opcao invalida!
pause
goto menu

:xampp
echo Abrindo XAMPP Control Panel...
start "" "C:\xampp\xampp-control.exe"
goto menu

:phpmyadmin
echo Abrindo phpMyAdmin...
start http://localhost/phpmyadmin/
goto menu

:sistema
echo Abrindo sistema no navegador...
start http://localhost/sistema-lanches/
goto menu

:admin
echo Abrindo painel administrativo...
start http://localhost/sistema-lanches/admin/dashboard.php
goto menu

:verificar
echo Abrindo verificacao de instalacao...
start http://localhost/sistema-lanches/check_install.php
goto menu

:pasta
echo Abrindo pasta do projeto...
explorer "C:\xampp\htdocs\sistema-lanches"
goto menu

:sair
echo Ate logo!
exit

:menu
echo.
goto start