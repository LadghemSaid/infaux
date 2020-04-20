# Infaux
site d'infos
1) git pull
2) composer install
3) npm install
4) modifier le .env => ligne 28 : 'DATABASE_URL=mysql://user:password@localhost:port(ex : 3306 Windows, 8888 Mac)/infaux'
5) php bin/console d:d:c
6) php bin/console d:s:u --force
7) php bin/console h:f:l
8) php bin/console c:c
9) npm run watch
10) php bin/console s:run


Linux

 ./mercure_lin/.mercure --jwt-key='mdptest' --addr=':3000' --debug  --cors-allowed-origins='http:/34.77.181.91' --publish-allowed-origins='http:/34.77.181.91:8000'


Windows

 export JWT_KEY='mdptest'; export ADDR=':3000'; export DEMO='0';  export ALLOW_ANONYMOUS='1'; export CORS_ALLOWED_ORIGINS=http://localhost:8000; export PUBLISH_ALLOWED_ORIGINS='http://localhost'; ./mercure_win/mercure.exe 


Generer un password 
bin/console security:encode-password 'mdptest'
