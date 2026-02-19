# Pourquoi ces technologies ?

Ce document explique les choix techniques du projet Toys Academy.

**Docker**  
Utilisé car c’est ce qui a été vu en cours. Permet de faire tourner l’ensemble du projet (frontend, backend, base, optimisation) de façon reproductible.

**Vue.js**  
Framework frontend vu en cours. On s’en sert pour l’interface (pages, composants, état).

**Tailwind CSS**  
Habitude de certains membres du groupe. Utilisé pour le style (classes utilitaires) sans écrire de CSS à la main.

**MariaDB**  
Base de données open-source. On privilégie l’open source pour ce projet.

**Java**  
La personne en charge de la partie optimisation préfère le faire en Java plutôt qu’en un autre langage, et c’est le langage vu en cours. Utilisé pour l’algorithme de composition des box.

**Architecture hexagonale light**  
On a demandé à l’IA si une architecture hexagonale complète était overkill pour ce projet ; la réponse était oui. On a demandé d’autres options et l’IA a proposé une architecture hexagonale “light”, qui nous a semblé être le bon compromis (structure claire sans trop de couches). Appliquée au backend PHP (Application, Port, Domain, Infrastructure).

**PHP Slim**  
Framework backend vu en cours. Utilisé pour l’API REST (routes, contrôleurs, injection des services).
