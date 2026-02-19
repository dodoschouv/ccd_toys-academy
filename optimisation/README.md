# Optimisation — Toys Academy

Service de **composition des box** : à partir des articles, abonnés et du poids max par box, calcule une attribution optimale (score de préférences) et renvoie un CSV utilisé par le backend.

## Stack

- Java 17
- Maven
- Serveur HTTP : `com.sun.net.httpserver` (sans framework web)

## Structure

```
optimisation/
├── src/main/java/com/example/
│   ├── Main.java            # Point d’entrée : serveur HTTP + logique de composition
│   ├── Article.java
│   ├── Abonne.java
│   ├── Box.java
│   ├── Categorie.java
│   ├── Etat.java
│   └── TrancheAge.java
├── 01_exemple/              # CSV d’exemple
├── 02_pb_simples/           # Problèmes simples (pb1..pb4 + solutions)
├── 03_pb_complexes/         # Problèmes complexes (generate_pb.py)
├── pom.xml
└── Dockerfile
```

## API HTTP

Le service écoute sur le **port 80**.

| Méthode | Route | Rôle |
|--------|--------|------|
| GET | `/api/health` | Health check → `{"status":"ok","service":"optimization"}` |
| POST | `/api/compute` | Corps : CSV entrée. Réponse : CSV (score + lignes de composition). `Content-Type: text/plain; charset=utf-8` |

## Format CSV entrée (backend → optimisation)

Trois sections séparées par des lignes vides :

1. **articles** — Lignes : `id;designation;category;age_range;state;price;weight`
2. **abonnes** — Lignes : `id;prenom;child_age_range;preferences` (preferences = catégories séparées par des virgules)
3. **parametres** — Une ligne : `max_weight_per_box` (grammes)

## Format CSV sortie (optimisation → backend)

- **Ligne 1** : score total (entier).
- **Lignes suivantes** : `prenom;article_id;category;age_range;state` (une ligne par article attribué).

## Lancer en local

```bash
cd optimisation
mvn -q package -DskipTests
java -jar target/optimisation-1.0-SNAPSHOT.jar
```

Test : `GET http://localhost/api/health`

## Mode fichier (sans serveur)

```bash
java -jar target/optimisation-1.0-SNAPSHOT.jar exemple.csv sortie.csv
```

## Lien avec le backend

Le backend PHP envoie le CSV en **POST** vers `{OPTIMISATION_URL}/api/compute`, parse la réponse et crée les box en base. En Docker : `OPTIMISATION_URL=http://optimisation:80`.
