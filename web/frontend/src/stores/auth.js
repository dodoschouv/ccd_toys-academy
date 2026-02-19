import { defineStore } from 'pinia'
import api from '../api/index.js'

export const useAuthStore = defineStore('auth', {
    state(){
        return{
            email: null,
            password: null,
        }
    }, 

    actions: {
        
    }
});