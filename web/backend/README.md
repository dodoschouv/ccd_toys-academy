# Backend — Toys Academy

API REST PHP (Slim 4) pour la gestion des articles, abonnés, campagnes et box. Authentification JWT pour les routes admin.

## Stack

- PHP 8.5
- Slim 4
- PDO (MariaDB)
- firebase/php-jwt
- cURL (appel service d’optimisation)

## Structure

```
web/backend/
├── public/
│   └── index.php          # Point d’entrée, routes, wiring
└── src/
    ├── Application/       # Cas d’usage (CreateArticle, RunComposition, ValidateBox, etc.)
    ├── Application/Port/  # Interfaces (repositories, OptimisationService)
    ├── Domain/            # Article, Box, Campaign, Subscriber, User, etc.
    └── Infrastructure/
        ├── Http/          # Contrôleurs + AdminAuthMiddleware + HttpOptimisationService
        └── Persistence/    # Pdo*Repository
```

## Variables d’environnement

| Variable | Description |
|----------|-------------|
| `DATABASE_URL` | DSN MySQL (ex. `mysql://toys:toys@db:3306/toys_academy`) |
| `OPTIMIZATION_URL` | URL du service d’optimisation (ex. `http://optimisation:80`) |
| `JWT_SECRET` | Secret pour les JWT |
| `CORS_ORIGIN` | Origine CORS (ex. `http://localhost:8080`) |

## Lancer en local

```bash
cd web/backend
composer install
php -S 0.0.0.0:8080 -t public/
```

## Routes API

### Publiques

| Méthode | Route | Rôle |
|--------|--------|------|
| GET | `/api/health` | Health check |
| POST | `/api/auth/login` | Connexion → JWT |
| POST | `/api/auth/register` | Inscription → JWT |
| GET | `/api/auth/me` | Utilisateur courant (Bearer) |
| GET | `/api/reference` | Catégories, tranches d’âge, états |
| GET | `/api/articles` | Catalogue paginé + filtres |
| GET | `/api/articles/{id}` | Détail article |
| GET | `/api/subscribers/by-email?email=` | Abonné par email |
| GET | `/api/subscribers/box?email=` | Box validées de l’abonné |
| POST | `/api/subscribers` | Inscription / mise à jour abonné |

### Admin (JWT `role=admin`)

| Méthode | Route | Rôle |
|--------|--------|------|
| GET | `/api/subscribers` | Liste abonnés |
| GET | `/api/admin/dashboard` | Stats (stock, abonnés, score moyen) |
| GET | `/api/admin/history` | Historique campagnes (box validées, articles, score moyen) |
| GET | `/api/admin/campaigns` | Liste campagnes |
| POST | `/api/admin/campaigns` | Création campagne |
| POST | `/api/admin/campaigns/{id}/compose` | Composition (appel optimisation) |
| GET | `/api/admin/campaigns/{id}/boxes` | Box de la campagne |
| POST | `/api/admin/boxes/{id}/validate` | Valider une box |
| POST | `/api/admin/articles` | Ajout article |
| PUT | `/api/admin/articles/{id}` | Modification article |
| DELETE | `/api/admin/articles/{id}` | Suppression article |

## Lien avec l’optimisation

Le backend appelle **POST** `{OPTIMIZATION_URL}/api/compute` avec un CSV (articles, abonnés, paramètres) et reçoit un CSV (score + composition). Implémentation dans `HttpOptimisationService`, utilisé par `RunComposition`.
