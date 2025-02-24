# Poisson soluble test

## Prérequis

- PHP 8.4
- PostgreSQL
- Dbeaver ou HeidiSQL ou psql

## Etape 1 : 

### Récuperer le repo git via :
    - ssh : git@github.com:RamdaneBoubziz/poisson_soluble.git
    - https : https://github.com/RamdaneBoubziz/poisson_soluble.git
    
## Etape 2 :

### Créer la base de donnée postgreSQL
    - Nom de la base par default : test_poisson_soluble_ramdane
    - Utilisateur par default : postgres
    - Mot de passe par default : root
    - Serveur : 127.0.0.1:5432

### Jouer les migrations présentes pour créer les tables
    - Lancer la commande : php bin/console sql-migrations:execute

## Etape 3 :

### Tester
    - Tester le décompte des lignes du csv : php bin/console app:count-receivers test.csv
    - Verifier en base l'enregistrement des lignes en succès dans destinataires
    - Lancer le serveur de symfony : symfony server:start
    - Tester l'alerteur : curl -X POST http://127.0.0.1:8000/alerter -H "Content-Type: application/json" -d '{"insee":"12345"}'
    - Verifier en base l'enregistrement des messages dans messenger_messages
    - Consommer les messages enregistré en base : php bin/console messenger:consume async.

## Développé par Ramdane Boubziz.
