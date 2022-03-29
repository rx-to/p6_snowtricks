# Projet n°6 – Snowtricks

## Description du projet
Snowtricks est un site communautaire dédié au snowboard développé en **Symfony 5.4**.
Sur ce site, vous serez en mesure de :
- vous inscrire / vous connecter,
- réinitialiser votre mot de passe si vous l'avez oublié,
- ajouter / éditer et supprimer vos figures de snowboard,
- intéragir avec les autres membres en postant des commentaires sous une figure.


## Installation du projet
Vous pouvez installer le projet sur votre serveur local.

### Base de données 
Dans le fichier `.env`, renseignez dans la variable `DATABASE_URL` les identifiants de la base de données péalablement installée sur votre serveur à l'aide du fichier `P8_08_base_de_donnees.sql` présent dans le livrable.

### Serveur SMTP (pour l'envoi d'emails)
Toujours dans le fichier `.env`, renseignez cette fois-ci vos identifiants SMTP (fournis par votre hébergeur ou utilisez un site comme [Mailtrap](https://mailtrap.io/)) dans la variable `MAILER_DSN`.

## Accéder au site internet
Vous pouvez également accéder directement au projet hébergé en [cliquant ici](https://snowtricks.siocnarf.fr/).

## Utilisation de l'espace communautaire
Afin de pouvoir ajouter des figures ou intéragir avec les autres membres, il vous suffit de créer un compte et de vous y connecter.
