# Infaux
site d'infos
1) git pull
2) composer install
3) npm install
4) modifier le .env => ligne 28 : 'DATABASE_URL=mysql://user:password@localhost:port(ex : 3306 Windows, 8888 Mac)/infaux'
5) ajouter au .env :
                   MERCURE_PUBLISH_URL=http://localhost:3000/.well-known/mercure
                   MERCURE_JWT_KEY=test
                   MERCURE_JWT_TOKEN=abcde

6) php bin/console d:d:d
7) php bin/console d:d:c
8) php bin/console d:s:u --force
9) php bin/console h:f:l
10) php bin/console c:c
11) npm run watch
12) php bin/console s:run


Linux

 ./mercure_lin/mercure --jwt-key='mdptest' --addr=':3000' --debug  --cors-allowed-origins='http://34.77.181.91' --publish-allowed-origins='http://34.77.181.91'


Windows

 export JWT_KEY='mdptest'; export ADDR=':3000'; export DEMO='0';  export ALLOW_ANONYMOUS='0'; export CORS_ALLOWED_ORIGINS=http://localhost:8000; export PUBLISH_ALLOWED_ORIGINS='http://localhost'; ./mercure_win/mercure.exe 


Generer un password 
bin/console security:encode-password 'mdptest'

 - sudo systemctl restart mercure.service
 - sudo systemctl stop mercure.service
 - sudo systemctl start mercure.service

 /var/www/mercure_lin/mercure --jwt-key='mdptest' --addr=':3000' --debug  --cors-allowed-origins='https://www.infaux.ga/,http://localhost:8000/,https://infaux.ga/'  acme_hosts='https://s-website.ga/.well-known/mercure/' use_forwarded_headers="true"  DEMO=1 ALLOW_ANONYMOUS=1 
 
 Conf actuelle
 /var/www/mercure_lin/mercure --jwt-key='mdptest' --addr=':3000' --debug  --cors-allowed-origins='*'  acme_hosts='https://s-website.ga/.well-known/mercure/' use_forwarded_headers="true"  
