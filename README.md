# Toys Academy — CCD 2026

Application web (gestion articles, abonnés). Optimisation des box prévue plus tard.

## Stack

- **Frontend** : Vue 3 + Vite + Tailwind CSS 4 (service dédié, nginx)
- **Backend** : PHP 8.5 Slim (API, service dédié)
- **Optimisation** : Python 3.12 + FastAPI
- **Base de données** : MariaDB 12

## Lancer avec Docker

À la racine du projet :

```bash
docker compose up --build
```

- **Application** : http://localhost:8080 (frontend ; l’API est proxée en `/api`)
- **API** : http://localhost:8080/api/health (via le même host, pas de CORS)
- **MariaDB** : `localhost:3306`, user `toys`, password `toys`, base `toys_academy`

Le frontend (nginx) reverse-proxy les appels `/api` vers le backend, donc une seule origine pour le navigateur.

---

## Partie « Application Web » — Fait / Non fait

### Base (à faire en premier)

| Ref | Description | Statut |
|-----|-------------|--------|
| **W1** | Modèle de domaine : Article, Abonné, Box, Campagne (paramètres) | ✅ Fait (backend : Article, Subscriber, User ; BDD : article, subscriber, user. Box/Campagne en BDD retirés pour l’instant, à remettre avec l’optimisation) |
| **W2** | Gestion articles (admin) : ajout d’un article (1) | ✅ Fait — `POST /api/admin/articles` |
| **W3** | Catalogue : affichage paginé (10 par page) des articles disponibles (2) | ✅ Fait — `GET /api/articles?page=&per_page=` |
| **W4** | Gestion abonnés : inscription avec tranche d’âge et préférences (6) ; cookie pour réutiliser les infos | ⚠️ Partiel — API `POST /api/subscribers` (création/mise à jour par email). Cookie côté front non fait |
| **W5** | Liste abonnés (admin) : affichage abonnés + tranche d’âge + préférences (7) | ✅ Fait — `GET /api/subscribers` |
| **W6** | Campagne : paramétrage campagne (poids max par box) (9) | ❌ Non fait — pas d’optimisation pour l’instant |
| **W7** | Composition : envoi des données à la brique d’optimisation + récupération des résultats (10) | ❌ Non fait |
| **W8** | Affichage des box composées (admin) : liste des articles par box, score, poids, prix (11) | ❌ Non fait |
| **W9** | Consultation box abonné : voir sa box (validée) en renseignant son email (13) | ❌ Non fait |
| **W10** | Responsive : interfaces utilisables sur mobile (priorité back-office) | ❌ Non fait — à faire côté front |
| **W11** | Back-office : URL dédiée sans auth (pour le démo) | ⚠️ À faire côté front (route dédiée type `/back-office`) ; API admin existe sans auth |

### Avancé (après chaîne de base cohérente)

| Ref | Description | Statut |
|-----|-------------|--------|
| **W12** | Filtrage catalogue : par catégorie, tranche d’âge, état (3) | ❌ Non fait |
| **W13** | Modification d’un article (admin), pré-remplissage ; interdire si article dans une box validée (4) | ⚠️ Partiel — `PUT /api/admin/articles/{id}` en place ; pas de vérification « déjà dans box validée » |
| **W14** | Code-barre / QR : association à un article, recherche par scan/saisie (5) | ⚠️ Partiel — champ `barcode` en BDD et en API ; pas d’endpoint/recherche dédiée |
| **W15** | Modification des préférences abonné (email pour retrouver le profil) (8) | ❌ Non fait — pas de `PUT/PATCH` abonné par email |
| **W16** | Validation des box (admin) : validation individuelle, retrait du stock, historique (12) | ❌ Non fait |
| **W17** | Historique box d’un abonné (14) | ❌ Non fait |
| **W18** | Historique global (admin) : campagnes, synthèse (15) | ❌ Non fait |
| **W19** | Authentification : différencier abonné / gestionnaire (16) | ❌ Non fait |
| **W20** | Tableau de bord (admin) : stats stock, abonnés actifs, score moyen (17) | ❌ Non fait |

### Super avancé (si temps)

| Ref | Description | Statut |
|-----|-------------|--------|
| **W21** | Bon de préparation : document imprimable par box (18) | ❌ Non fait |
| **W22** | Notification email (ex. MailCatcher) quand la box est prête (19) | ❌ Non fait |

---

### Routes API en place

| Méthode | Route | Rôle |
|--------|--------|------|
| GET | `/api/health` | Health check |
| GET | `/api/reference` | Catégories, tranches d’âge, états (formulaires) |
| GET | `/api/articles` | Catalogue paginé (`page`, `per_page`) |
| GET | `/api/articles/{id}` | Détail article |
| POST | `/api/admin/articles` | Ajout article |
| PUT | `/api/admin/articles/{id}` | Modification article |
| DELETE | `/api/admin/articles/{id}` | Suppression article |
| GET | `/api/subscribers` | Liste abonnés (admin) |
| POST | `/api/subscribers` | Inscription / mise à jour abonné (par email) |

---

## Structure

```
web/
  backend/          # PHP Slim (API) — service web-backend
  frontend/         # Vue 3 + Vite + Tailwind 4 — service web-frontend
docker-compose.yml  # web-backend, web-frontend, db
database/           # schema.sql (init MariaDB)
```

## Dev local (sans Docker)

- **Backend** : dans `web/backend/`, `composer install` puis `php -S 0.0.0.0:8080 -t public/`
- **Frontend** : dans `web/frontend/`, `npm install` puis `npm run dev` (Vite proxy `/api` → `http://localhost:8080`)
