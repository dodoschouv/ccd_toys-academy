// src/stores/articleStore.js
import { defineStore } from 'pinia'
import api from '../api/index.js'

export const useArticleStore = defineStore('article', {
    state: () => ({
        articles: [],
        total: 0,
        loading: false,
        currentPage: 1,
        itemsPerPage: 10
    }),

    actions: {
        async fetchArticles(page = 1, perPage = 10) {
            this.loading = true;
            try {
                // MODIFICATION ICI : On utilise juste '/articles'
                // Axios va ajouter la baseURL '/api' pour former '/api/articles'
                const response = await api.get('/articles', {
                    params: { page: page, perPage: perPage }
                });

                this.articles = response.data.data;
                this.total = response.data.total;

                this.currentPage = page;
                this.itemsPerPage = perPage;

            } catch (error) {
                console.error('Erreur lors de la récupération des articles:', error);
            } finally {
                this.loading = false;
            }
        },

        changePage(newPage) {
            this.fetchArticles(newPage, this.itemsPerPage);
        }
    }
});