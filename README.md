# Covid Attest plugin pour Jeedom

<p align="center">
  <img width="100" src="/plugin_info/CovidAttest_icon.png">
</p>

Permet de générer une attestation dérogatoire au confinement en France. Génère un document PDF et/ou PNG de l'attestation ainsi qu'une image (png) du QRcode

*__!! l'auteur n'est pas responsable des amendes et sanctions que vous pourrez subir avec son utilisation !!__*

# |Configuration|
  
  1. activer le plugin
  
  ![equip_image](/img_readme/conf_1.png)  
  
  2. Installer les dépendance : imagemagik (convertir les pdf au format png)
  
  3. Configurations :  
  
  
  * bouton __Effacer__ : permet de supprimer tous les fichiers d'attestation présents pour tous les équipements créés, pour faire le ménage, particulièrement si la suppression auto est désactivée
  
  * __Selecteur__ : vous permet de choisir quel certificat doit être utilisé pour générer l'attestation
  
  * bouton __Upload New File__ : permet de télécharger un nouveau certificat au format pdf. Vous pouvez également charger un fichier de configuration au format json. Si vous chargez un fichier certificat pdf sans fichier de configuration un fichier par défaut sera créé.
  
  ![equip_image](/img_readme/conf_new.png) 
  
  * bouton __File Parameter__ : ouvre une fenêtre dans laquelle vous pourrez modifier la position de insert, ainsi que la taille du texte inséré.
  
  ![equip_image](/img_readme/conf_2.png) 
  
  
  * bouton __Test Params__ : Permet de générer une attestation test avec les paramètres sauvés
  
  * bouton __Share Configuration__ : permet de télécharger un zip avec le fichier de certificat plus la configuration sélectionnée. => Vous pouvez alors le partager sur le forum de Jeedom!
  
 # |Paramétrage|
 

 
 1/ créer un équipement par membre à notifier,
 
 2/ dans la configuration de l'équipement, renseigner les informations à faire figurer sur l'attestation
      
![equip_image](/img_readme/equipement.PNG)     
 
 * __Nom de l'équipement__ 
 * __Objet parent__ 
 * __Catégorie__ 
 Comme tout équipement classique
 
 * __Nom de l'utilisateur__ : Le nom à faire figurer sur l'attestation
 * __Prenom de l'utilisateur__ : le prénom à faire figurer sur l'attestation
 * __Date de Naissance__ : la date de naissance à faire figurer sur l'attestation
 * __Ville de naissance__ : la ville de naissance à faire figurer sur l'attestation
 
 * __Utiliser l'adresse de jeedom__ : permet d'utiliser l'adresse renseignée dans la configuration de Jeedom (Réglages->Système->Configuration->Information). Les champs suivants seront alors masqués
 * __Adresse__ : *masqué si utiliser l'adresse Jeedom est coché* l'adresse à faire figurer sur l'attestation
 * __Code postal__ : *masqué si utiliser l'adresse jeedom est coché* le code postal à faire figurer sur l'attestation
 * __Ville__ : *masqué si utiliser l'adresse Jeedom est coché* la ville à faire figurer sur l'attestation
 
 
 * __Commande d'envoi__ : commande qui permet d'envoyer les documents
 
    * __commande scénario__ : Si cochée, vous permet de choisir de déclencher un scénario. Les tags du scénario contiendrons les chemins des fichiers par les tags #pdfURL#, #pngURL#, #qrcURL#, selon les cases cochées pour l'envoi des fichiers ci-dessous, ainsi que #cmdID# et #cmdNAME# avec l'id et le nom de la commande et #eqID# et #eqNAME# pour le l'Id et le nom de l'équipement qui a envoyé la commande.
    
 ![equip_image](/img_readme/scenario_cmd.png)  
 
 
 * __Type Equipement__ : le type de l'équipement qui permet l'envoi des documents, qui peut être de 3 types : 
   * __Telegram__ : si il s'agit d'une commande du plugin Telegram (lunarok)
   * __mail__ : si il s'agit d'une commande mail du plugin officiel mail (testé configuration SMTP seulement)
   * __Pushover__ : si il s'agit d'une commande du plugin pushover (! n'acceptes pas les pdf -> choisir le format image)
   * __Custom__ : permet de genéré un comportement par défaut, prend alors 4 options : 
     * __Option de la commande__ : qui permet de construire la chaine comprenant les chemins des fichiers générés. Utilisez les tags #pdfRUL#, #pngURL# et #qrcURL# qui seront remplacé par les chemin relatifs aux fichiers générés
     * __destination__ : 4 choix choix : 
          * titre / message/ files (String): endroit de la commande type message ou sera inséré la chaine de caractère de l'option décrite ci-dessus, au format string avec les URL des fichiers
          * files (Array): option de la commande contiendra un array 'files' avec les url des fichiers
     
 * __Cases à cocher *Options*__ :
   * Envoi du PDF: si vous souhaitez recevoir le pdf
   * Envoi au format image: convertit le pdf de l'attestation en png pour l'envoi
   * Envoi du QRcode: si vous souhaitez recevoir l'image du QR code
   * Ajout de la seconde page: si vous souhaitez ajouter une seconde page dans l'attestation avec le QR code grand format (du type de l'attestation généré en ligne sur le site du gouvernement)
 
* __Désactiver Auto remove__ : si cochée, la suppression automatique après l'appel à la commande d'envoi des fichiers est désactivé. Pour supprimer les fichiers il faudra appeler la commande 'supprimer les fichiers' de l'équipement. Cette commande supprimera les fichiers référencés avec le nom et prénom figurant dans la configuration de l'équipement.
Peut être utile pour l'utiliser avec notification queue par exemple.


 # |Utilisation|
 
 ## envoi des documents

1. direct : utilisez simplement les commandes créées, qui correspondent chacune à un type de motif de dérogation.

2. Pour cocher plusieurs motifs : utiliser la commande envoi Multiple, de type message, avec dans la partie 'message' les motifs séparés par une virgule (',') ou point virgule (';'). Les motifs sont accessibles dans l'équipement par les commandes info nommées par motif (motif TRAVAIL par exemple).

## Spécifier la date ou l'heure :
Dans l'equipement, il y a 1 commande par type de motif, plus 2 info : __date d'attestation__ et __heure d'attestation__.
=> Si vous renseignez ces valeurs, avant d'envoyer la commande, elles seront utilisées pour générer l'attestation.
une fois utilisées, elle seront réinitialisées à 0.

**Note : si vous avez des problèmes avec la date, utilisez des quote ou double quote (' ou " ).**

Exemple :  ici on génère une attestation au *_1er décembre 1970_* à *_8h44_* avec les motifs *_enfants_* et *_travail_* de cochés 

![equip_image](/img_readme/scenario.PNG)  


## widget 

Il est celui par défaut, il reprend les commandes d'envoi des notifications.

![equip_image](/img_readme/widget.PNG) 

Utilise les librairies :
 * phpQRcode library : http://phpqrcode.sourceforge.net/docs/html/index.html
 *  Setasign / FPDF : https://github.com/Setasign/FPDF
 *  Setasign / FPDI : https://github.com/Setasign/FPDI

Avec les contributions de Naboleo, jjl87, Ludo, arnog23, benj29 et tous les autres qui ont fait avancer le schmilblic, essuyé les platres et passé le torchon,
