<template>
    <div class="container">

<div class="d-flex justify-content-center align-items-center vh-100">

    <div class="row w-75 ">
        <div class="col-md-6 bg-success">
        </div>
        <div class="col-md-6 p-5">
        <h3 class="text-center">Register </h3>
        <form @submit.prevent="onSubmit">
            <div class="form-group mb-3">
                <input type="text" v-model="form.email" name="email" placeholder="Email" class="form-control">
            </div>
            <div class="form-group mb-3">
                <input type="text" v-model="form.phone" name="phone" placeholder="Phone" class="form-control">
            </div>
            <div class="form-group mb-3">
                <input type="password" v-model="form.password" name="password" placeholder="Password" class="form-control">
            </div>
            <div class="form-group mb-3">
                <input type="password" v-model="form.confirm_password" name="confirm_password" placeholder="Password Confirmation" class="form-control">
            </div>
            <div class="form-group mb-3" >
                <input type="hidden"  name="role" :value="role" placeholder="Role" class="form-control">
            </div>
            <div class="text-center">
                <button :disabled="loading" type="submit" class="btn btn-success">
                {{ loading ? "Enregistrement..." : "Register" }}
              </button>
            </div>
            <p class="py-3">Si vous êtes inscrite, veuillez <router-link to="/login">cliquer ici</router-link></p>
        </form>
    </div>
   
    </div>

</div>
</div>
    </template>
<script setup>
import {useRouter} from 'vue-router'
import { ref, onMounted } from 'vue'
import api from "@/axios"; 


const route = useRouter()
const role = ref('')
role.value = localStorage.getItem('role')
console.log(role.value);
const form = ref({
  email: "",
  phone: "",
  password: "",
  confirm_password: "",
});

const loading = ref(false);
const errorMsg = ref("");
const successMsg = ref("");

// Changez l'URL si votre backend attend /api/register ou /web/register
const API_ENDPOINT = "/register"; 
function validate() {
  errorMsg.value = "";
  if (!form.value.email) return (errorMsg.value = "Email requis");
  if (!form.value.phone) return (errorMsg.value = "Phone requis");
  if (!form.value.password) return (errorMsg.value = "Mot de passe requis");
  if (form.value.password !== form.value.confirm_password) return (errorMsg.value = "Les mots de passe ne correspondent pas");
  return true;
}

async function onSubmit() {
  if (!validate()) return;
  loading.value = true;
  errorMsg.value = "";
  successMsg.value = "";
  try {
    // Prépare payload : inclure role si présent
    const payload = {
      email: form.value.email,
      phone: form.value.phone,
      password: form.value.password,
      confirm_password: form.value.confirm_password,
      role: role.value || undefined,
    };

    // Envoi en JSON — axios avec withCredentials s'occupera des cookies
    const res = await api.post(API_ENDPOINT, payload);

    // 200/201 attendu ; adapte selon ton backend
    if (res.status === 200 || res.status === 201) {
      successMsg.value = res.data.message || "Inscription réussie";
      // si backend renvoie cookie HttpOnly, il est déjà posé (axios withCredentials)
      console.log(res.data)
      // redirige vers login ou dashboard
      setTimeout(() => {
        route.push("/login");
      }, 900);
    } else {
      errorMsg.value = res.data?.message || `Erreur serveur (${res.status})`;
    }
  } catch (err) {
    // axios errors: err.response, err.request, err.message
    if (err.response) {
      // message côté serveur
      errorMsg.value = err.response.data?.message || JSON.stringify(err.response.data) || err.message;
    } else {
      errorMsg.value = err.message || "Erreur réseau";
    }
  } finally {
    loading.value = false;
  }
}
</script>

