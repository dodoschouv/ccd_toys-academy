# Frontend — Toys Academy

Interface Vue 3 pour le catalogue, l’inscription, la consultation des box et le back-office admin.

## Tutoriel
Pour consulter le tutoriel de l'application : [TUTORIEL.md](TUTORIEL.md).

## Stack

- Vue 3
- Vite 6
- Vue Router 4
- Pinia (+ persistedstate)
- Tailwind CSS 4
- Axios

## Structure

```
web/frontend/
├── public/
├── src/
│   ├── api/
│   │   └── index.js         # Client Axios (base URL, intercepteur JWT)
│   ├── components/
│   │   ├── Header.vue
│   │   └── Footer.vue
│   ├── router/
│   │   └── index.js         # Routes + garde admin pour /back-office
│   ├── stores/
│   │   ├── authStore.js
│   │   └── articleStore.js
│   ├── utils/
│   │   └── subscriberCookie.js
│   ├── views/
│   │   ├── HomeView.vue
│   │   ├── CatalogueView.vue
│   │   ├── MaBoxView.vue
│   │   ├── ConnexionView.vue
│   │   ├── ProfileView.vue
│   │   ├── SettingsView.vue
│   │   ├── BackOfficeView.vue
│   │   └── back-office/
│   │       ├── DashboardView.vue
│   │       ├── CampaignsView.vue
│   │       ├── SubscribersView.vue
│   │       ├── ArticlesView.vue
│   │       ├── AddArticleView.vue
│   │       └── HistoryView.vue
│   ├── App.vue
│   ├── main.js
│   └── style.css
├── index.html
├── package.json
├── vite.config.js
└── Dockerfile
```

## Routes

| Chemin | Accès |
|--------|--------|
| `/`, `/home` | Tous |
| `/catalogue` | Tous |
| `/ma-box` | Tous |
| `/connexion` | Tous |
| `/profil`, `/settings` | Tous |
| `/back-office` | Admin (redirige vers dashboard) |
| `/back-office/dashboard` | Admin |
| `/back-office/campaigns` | Admin |
| `/back-office/subscribers` | Admin |
| `/back-office/articles` | Admin |
| `/back-office/addarticle` | Admin |
| `/back-office/history` | Admin |

La garde `router.beforeEach` redirige vers `/home` si un non-admin accède à `/back-office/*`.

## Lancer en local

```bash
cd web/frontend
npm install
npm run dev
```

Le proxy Vite envoie `/api` vers le backend (voir `vite.config.js`).

## Build production

```bash
npm run build
```

Les fichiers dans `dist/` sont servis par nginx dans l’image Docker.
