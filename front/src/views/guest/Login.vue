<script setup>
import { ref } from 'vue'
import axios from '@/axios'
import { useRouter } from 'vue-router'
import {saveToken, saveRole} from '@/utils'

const router = useRouter()

const email = ref('')
const password = ref('')
const errorMessage = ref('')

async function handleLogin() {
    try {
        const response = await axios.post("/login", {
            email: email.value,
            password: password.value
        })

        console.log("Réponse API :", response.data)

        // On récupère le token
        const token = response.data.access_token

        // On le stocke dans localStorage
        //localStorage.setItem("access_token", token)
        saveToken(token)
        
        saveRole(response.data.role)
        // Redirection après login
        router.push("/")

    } catch (error) {
        console.error(error)
        errorMessage.value = error.response?.data?.detail || "Erreur de connexion"
    }
}
</script>

<template>
<div class="container">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="row w-75">
            <div class="col-md-6 bg-success">
            </div>
        <div class="col-md-6 p-5">
            
            <h3 class="text-center">Login</h3>
            <form @submit.prevent="handleLogin">
                <div class="form-group mb-3">
                    <input v-model="email" type="email" placeholder="Email" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <input v-model="password" type="password" placeholder="Password" class="form-control">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success">Log In</button>
                </div>
            </form>

        </div>
    </div>
    </div>
</div>
</template>