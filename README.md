# Covid Attest plugin pour Jeedom

![Logo_image](/plugin_info/CovidAttest_icon.png)

Permet de générer une attestation dérogatoire qu confinement en France. Génère un document PDF de l'attestation ainsi qu'une image (png) du QRcode reprenant les informations

*__!! n'a pas encore testé en controle !! l'auteur n'est pas responsable des amendes et sanctions que vous pourrez subir avec son utilisation !!__*

 
 
 # -------------|Paramétrage|-------------
 
 1/ activer le plug in
 
 2/ créer un équipement par membre à notifier,
 
 3/ dans la configuration de l'équipement, renseigner les informations à faire figurer sur l'attestation
      
![equip_image](/img_readme/equipement.PNG)     
 
 * __Nom de l'équipement__ 
 * __Objet parent__ 
 * __Catégorie__ 
 Comme tout équipement classique
 
 * __Nom de l'utilisateur__ : Le nom a faire figurer sur l'attestation
 * __Prenom de l'utilisateur__ : le prénom a faire figurer sur l'attestation
 * __Date de Naissance__ : la date de naissance a faire figurer sur l'attestation
 * __Ville de naissance__ : la ville de naissance a faire figurer sur l'attestation
 
 * __Utiliser l'adresse de jeedom__ : permet d'utiliser l'adresse renseignée dans la configuiration de jeedom (Réglages->Système->Configuration->Information). Les champs suivants seront alors masqués
 * __Adresse__ : *masqué si utiliser l'adresse jeedom est coché* l'adresse à faire figurer sur l'attestation
 * __Code postal__ : *masqué si utiliser l'adresse jeedom est coché* le code postal à faire figurer sur l'attestation
 * __Ville__ : *masqué si utiliser l'adresse jeedom est coché* la ville à faire figurer sur l'attestation
 
 
 * __Commande d'envoi__ : commande qui permet d'envoyer les documents
 
 * __Type Equipement__ : le type de l'équipement qui permet l'envoi des documents, qui peut être de 3 types : 
   * __Telegram__ : si il s s'agit d'une commande du plugin telegram (lunarok)
   * __mail__ : si il s'agit d'une commande mail du plugin officiel mail (testé configuration SMTP seulement)
   * __Custom__ : permet de genéré un comportement par défaut, prend alors 2 options : 
     * __Option de la commande__ : qui permet de construire la chaine comprenant les chemins des fichiers générés. Utilisez les tags #pdfRUL# et #qrcURL# qui seront remplacé par les chemin relatifs qux fichiers générés
     * __destination__ : deux choix : titre ou message : endroit de la commande type message ou sera inséré la chaine de caractère de l'option décrite ci-dessus.
     
 * __Cases à cocher *Options*__ :
   * Envoi du PDF: si vous souhaitez recevoir le pdf
   * Envoi du QRcode: si vous souhaitez recevoir l'image du QR code
   * Ajout de la seconde page: si vous souhaitez ajouter une seconde page dans l'attestation avec le QR code grand format (du type de l'attestation généré en ligne sur le site du gouvernement)
 



 # -------------|Utilisation|-------------
 
 ## envoi des documents

1. direct : utilisez simplement les commandes crées, qui correspondent chacune à un type de motif de dérogation.

2. Pour cocher plusieurs motifs : utiliser la commande envoi Multiple, de type message, avec dans la partie 'message' les motifs séparés par une virgule (',') ou point virgule (';'). Les motifs sont accessible dans l'équipement par les commande info nommé par motif (motif TRAVAIL par exemple).

## Spécifier la date ou l'heure :
Dans l'equipement, il y a 1 commande par type de motif, plus 2 info : date d'attestation et heure d'attestation.
=> Si vous renseignez ces valeurs, elle seront utilisées pour l'attestation.
une fois utilisé, elle seront réinitialisée à 0.

exemple : 
![equip_image](/img_readme/scenario.PNG)  


## widget 

Il est celui par défaut, il reprend les commandes d'envoi des notifications.

![equip_image](/img_readme/widget.PNG) 

Utilise les librairies :
 * phpQRcode library : http://phpqrcode.sourceforge.net/docs/html/index.html
 *  Setasign / FPDF : https://github.com/Setasign/FPDF
 *  Setasign / FPDI : https://github.com/Setasign/FPDI

Merci Naboleo, jjl87 et tout les autres qui ont fait avancer le schnmilblic, essuyé les platres et passé le torchon,
