# Template de plugin pour Jeedom

Ce "template de plugin" sert de base à la réalisation de plugins pour **Jeedom**.

La documentation générale relative à la conception de plugin est consultable [ici](https://doc.jeedom.com/fr_FR/dev/). Dans le détail :   
* [Utilisation du template de plugin](https://doc.jeedom.com/fr_FR/dev/plugin_template) : Le template de plugin est une base de plugin pour Jeedom qui doit être adaptée avec l'id de votre plugin et à laquelle il suffit d'ajouter vos propres fonctions. 
* [Fichier info.json](https://doc.jeedom.com/fr_FR/dev/structure_info_json) : Intégré depuis la version 3.0 de Jeedom, le fichier **info.json** est obligatoire pour le bon fonctionnement des plugins et leur bon déploiement sur le Market Jeedom.
* [Icône du plugin](https://doc.jeedom.com/fr_FR/dev/Icone_de_plugin) : Afin de pouvoir être publié sur le Market Jeedom, tout plugin doit disposer d’une icône. Attention à ne pas utiliser le même code couleur que les icônes des plugins Jeedom officiels.
* [Widget du plugin](https://doc.jeedom.com/fr_FR/dev/widget_plugin) : Présentation des différentes manières d'inclure des widgets personnalisés au plugin.
* [Documentation du plugin](https://doc.jeedom.com/fr_FR/dev/documentation_plugin) : Présentation de la mise en place d'une documentation car un bon plugin n'est rien sans documentation adéquate.
* [Publication du plugin](https://doc.jeedom.com/fr_FR/dev/publication_plugin) : Description des pré-requis indispensables à la publication du plugin.

---
Si vous créez une branch nommée prettier, le robot workflows fera une passe complete sur le code pour que le code soit le plus uniforme possible.
---
test workflow en cours... for commit

---
Nouvel assistant pour un gain de temps à la création de votre plugin
---
Sur votre terminal, vous pouvez vous rendre dans plugin_info, et executer : php helperConfiguration.php
Un assistant en CLI vous posera quelques questions, (l'id du plugin, la catégorie, si démon, etcc), puis s'occupera de renommer tous les fichiers et le reste.
Voila, votre plugin est basiquement prêt pour coder votre travail.


