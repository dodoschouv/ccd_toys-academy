// src/stores/articleStore.js
import { defineStore } from 'pinia'
import api from '../api/index.js'

const articlesPath = import.meta.env.VITE_API_URL ? '/articles' : '/api/articles'
const referencePath = import.meta.env.VITE_API_URL ? '/reference' : '/api/reference'
const adminArticlesPath = import.meta.env.VITE_API_URL ? '/admin/articles' : '/api/admin/articles'

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
        },
        references: {
            categories: {},
            age_ranges: {},
            states: {}
        }
    }),

    actions: {
        async fetchArticles(page = 1, perPage = 10, filters = null) {
            this.loading = true
            const f = filters ?? this.filters
            if (filters) this.filters = { ...this.filters, ...filters }
            try {
                const params = { page, per_page: perPage }
                if (f.category) params.category = f.category
                if (f.age_range) params.age_range = f.age_range
                if (f.state) params.state = f.state

                const response = await api.get(articlesPath, { params })
                this.articles = response.data.data ?? []
                this.total = response.data.total ?? 0
                this.currentPage = page
                this.itemsPerPage = perPage
            } catch (error) {
                console.error('Erreur fetchArticles:', error)
                this.articles = []
                this.total = 0
            } finally {
                this.loading = false
            }
        },

        changePage(newPage) {
            this.fetchArticles(newPage, this.itemsPerPage)
        },

        setFilters(newFilters) {
            this.filters = { ...this.filters, ...newFilters }
            this.fetchArticles(1, this.itemsPerPage, this.filters)
        },

        resetFilters() {
            this.filters = { category: null, age_range: null, state: null }
            this.fetchArticles(1, this.itemsPerPage, this.filters)
        },

        setItemsPerPage(perPage) {
            this.fetchArticles(1, perPage, this.filters)
        },

        async fetchReferences() {
            try {
                const response = await api.get(referencePath)
                this.references = response.data ?? this.references
            } catch (error) {
                console.error('Erreur lors de la récupération des références:', error)
            }
        },

        async addArticle(articleData) {
            try {
                await api.post(adminArticlesPath, articleData)
                await this.fetchArticles(1, this.itemsPerPage, this.filters)
                return true
            } catch (error) {
                console.error('Erreur lors de l\'ajout de l\'article:', error)
                throw error
            }
        }
    }
})