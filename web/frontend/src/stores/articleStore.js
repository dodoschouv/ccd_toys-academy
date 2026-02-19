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
        itemsPerPage: 10,
        filters: {
            category: null,
            age_range: null,
            state: null
        }
    }),

    actions: {
        async fetchArticles(page = 1, perPage = 10, filters = null) {
            this.loading = true;
            const f = filters ?? this.filters;
            if (filters) this.filters = f;
            try {
                const params = { page, per_page: perPage };
                if (f.category) params.category = f.category;
                if (f.age_range) params.age_range = f.age_range;
                if (f.state) params.state = f.state;
                const response = await api.get(articlesPath, { params });

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
        },

        setFilters(newFilters) {
            this.filters = { ...this.filters, ...newFilters };
            this.fetchArticles(1, this.itemsPerPage, this.filters);
        },

        resetFilters() {
            this.filters = { category: null, age_range: null, state: null };
            this.fetchArticles(1, this.itemsPerPage, this.filters);
        },

        setItemsPerPage(perPage) {
            this.fetchArticles(1, perPage, this.filters);
        }
    }
});