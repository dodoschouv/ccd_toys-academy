// src/stores/articleStore.js
import { defineStore } from 'pinia'
import api from '../api/index.js'

// Avec baseURL = '/api' (Docker), il faut appeler '/articles'. Sans baseURL (dev), il faut '/api/articles'.
const articlesPath = import.meta.env.VITE_API_URL ? '/articles' : '/api/articles'

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
                const response = await api.get(articlesPath, {
                    params: { page, per_page: perPage }
                });

                this.articles = response.data.data ?? [];
                this.total = response.data.total ?? 0;
                this.currentPage = page;
                this.itemsPerPage = perPage;
            } catch (error) {
                console.error('Erreur lors de la récupération des articles:', error);
                this.articles = [];
                this.total = 0;
            } finally {
                this.loading = false;
            }
        },

        changePage(newPage) {
            this.fetchArticles(newPage, this.itemsPerPage);
        }
    }
});