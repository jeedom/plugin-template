Covid Attest plugin pour Jeedom

Utilise les librairies :
 * phpQRcode library : http://phpqrcode.sourceforge.net/docs/html/index.html
 *  Setasign / FPDF : https://github.com/Setasign/FPDF
 *  Setasign / FPDI : https://github.com/Setasign/FPDI
 
 
 utilisation :
 1/ activer le plug in
 
 2/ créer un équipement par membre à notifier,
 
 3/ dans la configuration de l'équipement, renseigner les renseignements à faire figurer sur l'attestation
 
 4/ ajouter une commande d'envoi, de type message. Les fichiers seront passés en options dans la partie 'titre' du message, selon le template renseigné dans "Option de la commande". Utilisez les tag #pdfURL# (pour  le pdf de l'attestation) et #qrcURL# (pour le png du QRcode)  pour renseigner ou doit être mis l'url des fichiers.
 

 typiquement, pour une commande telegram (plugin lunarok) mettre : 
 
```file=#qrcURL#,#pdfURL#```

pour renvoyer le pdf et le qrcode.

Dans l'equipement, il y a 1 commande par type de motif, plus 2 info : date d'attestation et heure d'attestation.
=> Si vous renseigner ces valeurs, elle seront utilisées pour l'attestation.
une fois utilisé, elle seront réinitialisée à 0.


ToDo : 
* une commande general pour envoyer plusieurs motif d'un coup
* une option pour ajouter la seconde page avec le grand QRcode sur le pdf

