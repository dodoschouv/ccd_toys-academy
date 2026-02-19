# Toys Academy Xx_BTS_xX — CCD 2026


Application web de gestion d’articles et d’abonnés, avec composition de box optimisée.

**Application en live** : http://docketu.iutnc.univ-lorraine.fr:8082

## Stack

- **Frontend** : Vue 3 + Vite + Tailwind CSS 4 (nginx)
- **Backend** : PHP 8.5 Slim (API REST)
- **Optimisation** : Java 17 (composition des box)
- **Base de données** : MariaDB 12

## Lancer le projet

```bash
docker compose up --build
```

- **Application** : http://localhost:8080  
- **API** : http://localhost:8080/api/health  
- **MariaDB** : `localhost:3306`, user `toys`, password `toys`, base `toys_academy`

---

## Liens vers les README détaillés

### Backend — [web/backend/README.md](web/backend/README.md)

```
web/backend/
├── public/
│   └── index.php
├── src/
│   ├── Application/
│   │   ├── CreateArticle.php
│   │   ├── CreateCampaign.php
│   │   ├── DeleteArticle.php
│   │   ├── GetReferenceData.php
│   │   ├── GetValidatedBoxesForSubscriberByEmail.php
│   │   ├── ListArticles.php
│   │   ├── ListBoxesForCampaign.php
│   │   ├── ListCampaigns.php
│   │   ├── ListSubscribers.php
│   │   ├── RunComposition.php
│   │   ├── SaveSubscriber.php
│   │   ├── UpdateArticle.php
│   │   └── ValidateBox.php
│   ├── Application/Port/
│   │   ├── ArticleRepository.php
│   │   ├── BoxRepository.php
│   │   ├── CampaignRepository.php
│   │   ├── OptimisationService.php
│   │   ├── SubscriberRepository.php
│   │   └── UserRepository.php
│   ├── Domain/
│   │   ├── Article.php
│   │   ├── ArticleCategory.php
│   │   ├── ArticleState.php
│   │   ├── AgeRange.php
│   │   ├── Box.php
│   │   ├── Campaign.php
│   │   ├── Subscriber.php
│   │   └── User.php
│   └── Infrastructure/
│       ├── Http/
│       │   ├── AdminAuthMiddleware.php
│       │   ├── ArticleController.php
│       │   ├── AuthController.php
│       │   ├── BoxController.php
│       │   ├── CampaignController.php
│       │   ├── DashboardController.php
│       │   ├── HistoryController.php
│       │   ├── HttpOptimisationService.php
│       │   ├── ReferenceController.php
│       │   └── SubscriberController.php
│       └── Persistence/
│           ├── PdoArticleRepository.php
│           ├── PdoBoxRepository.php
│           ├── PdoCampaignRepository.php
│           ├── PdoSubscriberRepository.php
│           └── PdoUserRepository.php
├── composer.json
└── Dockerfile
```

### Frontend — [web/frontend/README.md](web/frontend/README.md)

```
web/frontend/
├── public/
├── src/
│   ├── api/
│   │   └── index.js
│   ├── components/
│   │   ├── Header.vue
│   │   └── Footer.vue
│   ├── router/
│   │   └── index.js
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

### Optimisation — [optimisation/README.md](optimisation/README.md)

```
optimisation/
├── src/main/java/com/example/
│   ├── Main.java
│   ├── Article.java
│   ├── Abonne.java
│   ├── Box.java
│   ├── Categorie.java
│   ├── Etat.java
│   └── TrancheAge.java
├── 01_exemple/
├── 02_pb_simples/
├── 03_pb_complexes/
│   └── generate_pb.py
├── pom.xml
└── Dockerfile
```