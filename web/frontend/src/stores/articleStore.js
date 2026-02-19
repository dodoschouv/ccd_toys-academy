import { defineStore } from 'pinia'
import api from '../api/index.js'

export const useArticleStore = defineStore('article', {
    state() {
        return {
            articles: [],
            loading: false,
        }
    },

    actions: {
        async fetchArticles() {
            if (this.articles.length > 0) return;

            this.loading = true;
            try {
                const response = await api.get('/api/articles');

                this.articles = response.data;

            } catch (error) {
                console.error('Erreur lors de la récupération des articles:', error);
            } finally {
                this.loading = false;
            }
        },

        async addArticle(nouveauArticle) {
            try {
                const response = await api.post('/api/articles', nouveauArticle);
                this.articles.unshift(response.data);
                return response.data;
            } catch (error) {
                console.error('Erreur lors de l\'ajout de l\'article:', error);
                throw error;
            }
        }
    }
});