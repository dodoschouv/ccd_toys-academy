import { defineStore } from 'pinia'
import api from '../api/index.js'

export const useArticleStore = defineStore('article', {
    state: () => ({
        articles: [],
        total: 0,
        loading: false,
        currentPage: 1,
        itemsPerPage: 10,
        // Nouvelles variables pour les listes déroulantes
        references: {
            categories: {},
            age_ranges: {},
            states: {}
        }
    }),

    actions: {
        async fetchArticles(page = 1, perPage = 10) {
            this.loading = true;
            try {
                const response = await api.get('/articles', {
                    params: { page, per_page: perPage } // Attention, ton back utilise per_page
                });
                this.articles = response.data.data;
                this.total = response.data.total;
                this.currentPage = page;
                this.itemsPerPage = perPage;
            } catch (error) {
                console.error('Erreur fetchArticles:', error);
            } finally {
                this.loading = false;
            }
        },

        changePage(newPage) {
            this.fetchArticles(newPage, this.itemsPerPage);
        },

        // --- NOUVELLES ACTIONS ---

        // 1. Récupérer les données de référence pour le formulaire
        async fetchReferences() {
            try {
                const response = await api.get('/reference');
                this.references = response.data;
            } catch (error) {
                console.error('Erreur lors de la récupération des références:', error);
            }
        },

        // 2. Ajouter un article
        async addArticle(articleData) {
            try {
                await api.post('/admin/articles', articleData);
                // Si ça marche, on recharge la page 1 du catalogue pour voir le nouvel article
                await this.fetchArticles(1, this.itemsPerPage);
                return true;
            } catch (error) {
                console.error('Erreur lors de l\'ajout de l\'article:', error);
                throw error; // On renvoie l'erreur pour l'afficher dans le composant
            }
        }
    }
});