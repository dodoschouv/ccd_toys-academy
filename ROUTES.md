# Routes API en place

| Méthode | Route | Rôle |
|---------|-------|------|
| GET | `/api/health` | Health check |
| POST | `/api/auth/login` | Connexion (body : email, password) → retourne { token, user } JWT |
| POST | `/api/auth/register` | Inscription avec mot de passe (body : email, password, first_name, last_name, child_age_range, preferences) → crée abonné + user, retourne { token, user } |
| GET | `/api/auth/me` | Utilisateur courant (header Authorization: Bearer \<token\>) ; optionnellement avec subscriber |
| GET | `/api/reference` | Catégories, tranches d'âge, états (formulaires) |
| GET | `/api/articles` | Catalogue paginé (page, per_page) ; filtres optionnels : category, age_range, state |
| GET | `/api/articles/{id}` | Détail article |
| POST | `/api/admin/articles` | Ajout article |
| PUT | `/api/admin/articles/{id}` | Modification article |
| DELETE | `/api/admin/articles/{id}` | Suppression article |
| GET | `/api/subscribers` | Liste abonnés (admin) |
| GET | `/api/subscribers/by-email` | Récupérer un abonné par email (query : email) — pour pré-remplir le formulaire de modification |
| GET | `/api/subscribers/box` | W9 — Box validées de l'abonné (query : email) : liste des box avec score, poids, prix, articles ; 404 si email inconnu |
| POST | `/api/subscribers` | Inscription / mise à jour abonné (par email) |
| GET | `/api/admin/dashboard` | W20 — Stats tableau de bord : { stock, subscribers_count, average_score } |
| GET | `/api/admin/history` | W18 — Historique global : campagnes avec boxes_count, articles_count, average_score (box validées) |
| GET | `/api/admin/campaigns` | Liste des campagnes |
| POST | `/api/admin/campaigns` | Création campagne (body : max_weight_per_box en grammes) |
| POST | `/api/admin/campaigns/{id}/compose` | Lance la composition (articles + abonnés + campagne) → optimisation → enregistrement des box en brouillon ; retourne { score, boxes_count } |
| GET | `/api/admin/campaigns/{id}/boxes` | Liste des box composées de la campagne : par box : abonné, score, poids total, prix total, liste des articles (id, designation, category, age_range, state, price, weight) |
| POST | `/api/admin/boxes/{id}/validate` | W16 — Valide une box individuellement : change le statut de "draft" à "validated", met à jour validated_at, vérifie que les articles ne sont pas déjà dans une autre box validée |
