# Toys Academy — CCD 2026

[![CI/CD](https://github.com/dodoschouv/ccd_toys-academy/actions/workflows/ci.yml/badge.svg)](https://github.com/dodoschouv/ccd_toys-academy/actions/workflows/ci.yml)

🌐 **Application en live** : http://docketu.iutnc.univ-lorraine.fr:8082

Application web (gestion articles, abonnés). Optimisation des box prévue plus tard.


## Stack

- **Frontend** : Vue 3 + Vite + Tailwind CSS 4 (service dédié, nginx)
- **Optimisation** : Python 3.12 + FastAPI
- **Backend** : PHP 8.5 Slim (API, service dédié)
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
| **W4** | Gestion abonnés : inscription avec tranche d’âge et préférences (6) ; cookie pour réutiliser les infos | ✅ Fait — API `POST /api/subscribers` ; cookie `toys_academy_email` côté front après inscription ; pré-remplissage via `GET /api/subscribers/by-email?email=...` au chargement de la page Connexion/Inscription |
| **W5** | Liste abonnés (admin) : affichage abonnés + tranche d’âge + préférences (7) | ✅ Fait — `GET /api/subscribers` |
| **W6** | Campagne : paramétrage campagne (poids max par box) (9) | ✅ Fait — `GET /api/admin/campaigns`, `POST /api/admin/campaigns` (body : `max_weight_per_box`) |
| **W7** | Composition : envoi des données à la brique d’optimisation + récupération des résultats (10) | ✅ Fait — `POST /api/admin/campaigns/{id}/compose` |
| **W8** | Affichage des box composées (admin) : liste des articles par box, score, poids, prix (11) | ✅ Fait — `GET /api/admin/campaigns/{id}/boxes` |
| **W9** | Consultation box abonné : voir sa box (validée) en renseignant son email (13) | ✅ Fait — `GET /api/subscribers/box?email=...` |
| **W10** | Responsive : interfaces utilisables sur mobile (priorité back-office) | ✅ Fait — navbar avec menu burger (mobile), back-office en colonne/grille adaptative, tableaux avec défilement horizontal, grilles catalogue/accueil responsives |
| **W11** | Back-office : URL dédiée sans auth (pour le démo) | ⚠️ À faire côté front (route dédiée type `/back-office`) ; API admin existe sans auth |

### Avancé (après chaîne de base cohérente)

| Ref | Description | Statut |
|-----|-------------|--------|
| **W12** | Filtrage catalogue : par catégorie, tranche d’âge, état (3) | ✅ Fait — `GET /api/articles?category=&age_range=&state=` |
| **W13** | Modification d’un article (admin), pré-remplissage ; interdire si article dans une box validée (4) | ✅ Fait — `PUT /api/admin/articles/{id}` en place ; pas de vérification « déjà dans box validée » |
| **W14** | Code-barre / QR : association à un article, recherche par scan/saisie (5) | ⚠️ Partiel — champ `barcode` en BDD et en API ; pas d’endpoint/recherche dédiée |
| **W15** | Modification des préférences abonné (email pour retrouver le profil) (8) | ✅ Fait — `GET /api/subscribers/by-email?email=...` pour pré-remplir ; modification via `POST /api/subscribers` (mise à jour si email existe) |
| **W16** | Validation des box (admin) : validation individuelle, retrait du stock, historique (12) | ✅ Fait — `POST /api/admin/boxes/{id}/validate` |
| **W17** | Historique box d’un abonné (14) | ❌ Non fait |
| **W18** | Historique global (admin) : campagnes, synthèse (15) | ❌ Non fait |
| **W19** | Authentification : différencier abonné / gestionnaire (16) | ⚠️ Partiel — Connexion/inscription abonné avec JWT (POST `/api/auth/login`, `/api/auth/register`, GET `/api/auth/me`) ; token stocké côté front, envoyé en `Authorization: Bearer`. Pas encore de protection des routes admin par rôle |
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
| POST | `/api/auth/login` | Connexion (body : `email`, `password`) → retourne `{ token, user }` JWT |
| POST | `/api/auth/register` | Inscription avec mot de passe (body : `email`, `password`, `first_name`, `last_name`, `child_age_range`, `preferences`) → crée abonné + user, retourne `{ token, user }` |
| GET | `/api/auth/me` | Utilisateur courant (header `Authorization: Bearer <token>`) ; optionnellement avec `subscriber` |
| GET | `/api/reference` | Catégories, tranches d’âge, états (formulaires) |
| GET | `/api/articles` | Catalogue paginé (`page`, `per_page`) ; filtres optionnels : `category`, `age_range`, `state` |
| GET | `/api/articles/{id}` | Détail article |
| POST | `/api/admin/articles` | Ajout article |
| PUT | `/api/admin/articles/{id}` | Modification article |
| DELETE | `/api/admin/articles/{id}` | Suppression article |
| GET | `/api/subscribers` | Liste abonnés (admin) |
| GET | `/api/subscribers/by-email` | Récupérer un abonné par email (query : `email`) — pour pré-remplir le formulaire de modification |
| GET | `/api/subscribers/box` | W9 — Box validées de l'abonné (query : `email`) : liste des box avec score, poids, prix, articles ; 404 si email inconnu |
| POST | `/api/subscribers` | Inscription / mise à jour abonné (par email) |
| GET | `/api/admin/campaigns` | Liste des campagnes |
| POST | `/api/admin/campaigns` | Création campagne (body : `max_weight_per_box` en grammes) |
| POST | `/api/admin/campaigns/{id}/compose` | Lance la composition (articles + abonnés + campagne) → optimisation → enregistrement des box en brouillon ; retourne `{ score, boxes_count }` |
| GET | `/api/admin/campaigns/{id}/boxes` | Liste des box composées de la campagne : par box : abonné, score, poids total, prix total, liste des articles (id, designation, category, age_range, state, price, weight) |
| POST | `/api/admin/boxes/{id}/validate` | W16 — Valide une box individuellement : change le statut de "draft" à "validated", met à jour `validated_at`, vérifie que les articles ne sont pas déjà dans une autre box validée |

---

## Structure

```
web/
  backend/          # PHP Slim (API) — service web-backend
  frontend/         # Vue 3 + Vite + Tailwind 4 — service web-frontend
docker-compose.yml  # web-backend, web-frontend, db
database/           # schema.sql (init MariaDB)
```

## Dépendances backend (composer)

- **Avec Docker** : `composer install` est exécuté **à chaque build** de l'image (`docker compose up --build`). Aucune action à faire.
- **Sans Docker** : à la racine, lancer `./scripts/install-backend-deps.sh` (Linux/Mac/Git Bash) ou `.\scripts\install-backend-deps.ps1` (PowerShell), ou dans `web/backend/` : `composer install`.

## Dev local (sans Docker)

- **Backend** : dans `web/backend/`, `composer install` (inclut `firebase/php-jwt` pour l’auth) puis `php -S 0.0.0.0:8080 -t public/`. Optionnel : définir `JWT_SECRET` dans l’environnement (sinon une valeur par défaut est utilisée en dev).
- **Frontend** : dans `web/frontend/`, `npm install` puis `npm run dev` (Vite proxy `/api` → `http://localhost:8080`)
