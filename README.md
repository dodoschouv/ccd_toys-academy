# Toys Academy — CCD 2026

Application web (gestion articles, abonnés, box) + brique d’optimisation, en Docker.

## Stack

- **Web** : PHP 8.2 Slim (API) + Vue 3 + Vite + Tailwind CSS 4
- **Optimisation** : Python 3.12 + FastAPI
- **Base de données** : MariaDB 11

## Lancer avec Docker

À la racine du projet :

```bash
docker compose up --build
```

- **Appli web** : http://localhost:8080 (SPA Vue + API en `/api`)
- **API health** : http://localhost:8080/api/health
- **Optimisation** : http://localhost:8000
- **MariaDB** : `localhost:3306`, user `toys`, password `toys`, base `toys_academy`

## Dev local (sans tout Dockeriser)

- **Backend API** : dans `web/backend/`, `composer install` puis `php -S 0.0.0.0:8080 -t public/`
- **Frontend** : dans `web/frontend/`, `npm install` puis `npm run dev` (Vite proxy vers `http://localhost:8080` pour `/api`)

## Structure

```
web/
  backend/          # PHP Slim (API)
  frontend/         # Vue 3 + Vite + Tailwind 4
optimization/       # Python FastAPI (composition des box)
docker-compose.yml  # web, optimization, MariaDB
```
