Covid Attest plugin pour Jeedom

Utilise les librairies :
 * phpQRcode library : http://phpqrcode.sourceforge.net/docs/html/index.html
 *  Setasign / FPDF : https://github.com/Setasign/FPDF
 *  Setasign / FPDI : https://github.com/Setasign/FPDI
 
 
 -------------|Paramétrage|-------------
 
 1/ activer le plug in
 
 2/ créer un équipement par membre à notifier,
 
 3/ dans la configuration de l'équipement, renseigner les renseignements à faire figurer sur l'attestation
      - la case à cocher "Utiliser l'adresse de jeedom" permet d'utiliser l'adresse renseigné dans la configuration de votre jeedom (merci naboleo)
      - la case à cocher "Ajout de la seconde page" permet d'ajouter une seconde page à l'attestation, avec le QRcode affiché en grand (merci jjl87)
  

 
 4/ ajouter une commande d'envoi, de type message. Les fichiers seront passés en options dans la partie 'titre' du message, selon le template renseigné dans "Option de la commande". Utilisez les tag #pdfURL# (pour  le pdf de l'attestation) et #qrcURL# (pour le png du QRcode)  pour renseigner ou doit être mis l'url des fichiers.
 l'option "destination" permet de préciser si ce template sera appliqué au titre de la commande type message ou au corps du message.
 

 typiquement, pour une commande telegram (plugin lunarok) mettre : 
 
```file=#qrcURL#,#pdfURL#```
et destination => titre.

pour renvoyer le pdf et le qrcode.

Dans l'equipement, il y a 1 commande par type de motif, plus 2 info : date d'attestation et heure d'attestation.
=> Si vous renseigner ces valeurs, elle seront utilisées pour l'attestation.
une fois utilisé, elle seront réinitialisée à 0.

 -------------|Utilisation|-------------

1/ direct : utilisez simplement les commandes crées, qui correspondent chacune à un type de motif de dérogation.

2/ Pour cocher plusieurs motifs : utiliser la commande envoi Multiple, de type message, avec dans la partie 'message' les motifs séparés par une virgule (',') ou point virgule (';'). Les motifs sont accessible dans l'équipement par les commande info nommé par motif (motif TRAVAIL par exemple).

ToDo : 
* une commande general pour envoyer plusieurs motif d'un coup
* paramètre général avec le nom du certificat à utiliser.

