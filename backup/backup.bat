@echo off
REM Definir a data no formato DD-MM-YYYY
set x=%DATE:~0,2%-%DATE:~3,2%-%DATE:~6,4%
set date=%x%

REM Definir o nome do arquivo de backup
set BACKUP_DIR=C:\Users\eduardo\backup
set BACKUP_FILE=%BACKUP_DIR%\Trabalho_Vendas_%date%.backup

REM Criar o diretório de backup caso não exista
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

REM Exibir o nome do arquivo de backup
echo Backup file name is: %BACKUP_FILE%

REM Definir a senha do PostgreSQL
SET PGPASSWORD=12345

REM Executar o pg_dump para criar o backup
pg_dump -h localhost -p 5432 -U postgres -F c -b -v -f "%BACKUP_FILE%" venda_db

pause
