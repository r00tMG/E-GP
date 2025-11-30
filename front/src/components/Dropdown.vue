<script>
import {ref} from "vue";
import router from "@/router/index.js";

export default{
  name: "Dropdown",
  setup(){
    const data = ref({});
    const storedData = localStorage.getItem('data');
    const token = ref(localStorage.getItem("access_token"))
    if (storedData) {
      data.value = JSON.parse(storedData);
      //console.log(data.value)
      //console.log(data.value.user.role)
    } else {
      data.value = null;
    }
    const handleLogout = async () => {
      try {
        //console.log(token)
        localStorage.removeItem('access_token')
        localStorage.removeItem('roel')
        //console.log(token)
        await router.push('/');
      } catch (error) {
        //console.log('Salut')
        console.error('Logout failed:', error);
      }
    }
    return {
      data,
      handleLogout, 
      token
    }

  }
}
</script>

<template>

  <div class="dropdown ms-2">
    <button class="btn btn-transparent dropdown-toggle border-success" v-if="data && data.user" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <img class="#" :src="data.storage+'/'+data.user.photo_profile" width="30px" height="30px" alt="" >
      {{data.user.name}}
    </button>

    <button class="btn btn-transparent circle-button border  broder-dark dropdown-toggle" v-else type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-circle border border-successs rounded-circle" viewBox="0 0 16 16">
        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
      </svg>
    </button>
    <ul class="dropdown-menu" v-if="data && data.token">
      <li  class="text-center">
        <p class="dropdown-item">
          <img class="rounded-circle text-center border border-success" :src="data.storage+'/'+data.user.photo_profile" width="50px" height="50px" alt="">
        </p>
      </li>
      <li class="">
        <a class="dropdown-item" >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope me-2" viewBox="0 0 16 16">
            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
          </svg>
          {{data.user.email}}</a>
      </li>
      <li class="" >
        <router-link to="/profile"  v-if="data.user.role[0].name === 'GP' || data.user.role[0].name === 'Client' || data.user.role[0].name === 'admin'" class="dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill me-2" viewBox="0 0 16 16">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
          </svg>
          <span class="text-dark">Profile</span>
        </router-link>
        <router-link to="/users/index" v-if="data.user.role[0].name === 'admin'" class="dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill me-2" viewBox="0 0 16 16">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
          </svg>
          <span class="text-dark">Admin</span>
        </router-link>
      </li>

      <li class="dropdown-item ">
        <button @click="handleLogout" type="submit" class="text-body ms-0 bg-transparent border-0 p-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right me-2" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
          </svg>
          <span class="text-dark text-center">Déconnexion</span>
        </button>
      </li>
    </ul>
    <ul class="dropdown-menu" v-else>
      <li class="" >
        <router-link to="#" v-if="token" class="dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill me-2" viewBox="0 0 16 16">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
          </svg>
          <span class="text-dark">Profile</span>
        </router-link>
        
      </li>
      <li class="dropdown-item" v-if="token">
        <button  @click="handleLogout" type="submit" class="text-body ms-0 bg-transparent border-0 p-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right me-2" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
          </svg>
          <span class="text-dark text-center">Logout</span>
        </button>
      </li>
      <li class="dropdown-item" v-if="!token">
   
        <router-link  to="/login"  class="dropdown-item">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right me-2" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
            <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
          </svg>
          <span class="text-dark">Login</span>
        </router-link>
      </li>
      
   
    </ul>
  </div>
<!--  <label class="dropdown">
    <div class="dd-button bg-transparent" v-if="data && data.user">
      <img class="rounded-circle" :src="data.storage+'/'+data.user.photo_profile" width="30px" height="30px" alt="Photo Profile" >
      {{data.user.name}}
    </div>
    <div class="dd-button gap-1" v-else>
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
      </svg>
    </div>
    <input type="checkbox" class="dd-input" id="test">
    <ul class="dd-menu" v-if="data && data.token">
      <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
        <div class="mb-3">
          <img class="rounded-circle" :src="data.storage+'/'+data.user.photo_profile" width="50px" height="50px" alt="">
        </div>
        <div class="text-center">
          <p class="tx-16 fw-bolder">{{data.user.name}}</p>
          <p class="tx-12 text-dark">{{data.user.email}}</p>
        </div>
        <li class="dropdown-item py-2 " v-if="data.user.role === 'GP' || data.user.role === 'Client'">
          <router-link to="/profile/index" href="#" class="text-body ms-0">
            <i class="me-2 icon-md" data-feather="user"></i>
            <span class="text-dark">Profile</span>
          </router-link>
        </li>
        <li class="dropdown-item py-2">
          <button @click="handleLogout" type="submit" class="text-body ms-0 bg-transparent border-0 p-0">
            <i class="me-2 icon-md" data-feather="log-out"></i>
            <span class="text-dark">Déconnexion</span>
          </button>
        </li>
      </div>
    </ul>
    <ul class="dd-menu" v-else>
      <li>
        <router-link to="/login">Se connecter</router-link>
      </li>
    </ul>
  </label>-->
<!--  <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">

    <ul class="list-unstyled p-1">

      <li class="dropdown-item py-2">
        <a href="javascript:;" class="text-body ms-0">
          <i class="me-2 icon-md" data-feather="edit"></i>
          <span>Edit Profile</span>
        </a>
      </li>
      <li class="dropdown-item py-2">
        <a href="javascript:;" class="text-body ms-0">
          <i class="me-2 icon-md" data-feather="repeat"></i>
          <span>Switch User</span>
        </a>
      </li>

    </ul>
  </div>-->

</template>

<style scoped>
body {
  color: #000000;
  font-family: Sans-Serif;
  padding: 30px;
  background-color: #FFFFFF;
}
button.btn.dropdown-toggle {
  border: none !important;
  padding: 0px;
  margin-left: 20px;
}
a {
  text-decoration: none;
  color: #000000;
}

a:hover {
  color: #222222
}

/* Dropdown */

.dropdown {
  display: inline-block;
  position: relative;
}

.dd-button {
  display: inline-block;

  border-radius: 4px;
  padding: 10px 30px 10px 20px;
  background-color: #ffffff;
  cursor: pointer;
  white-space: nowrap;
}

.dd-button:after {
  content: '';
  position: absolute;
  top: 50%;
  right: 15px;
  transform: translateY(-50%);
  width: 0;
  height: 0;
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid black;
}

.dd-button:hover {
  background-color: #eeeeee;
}


.dd-input {
  display: none;
}

.dd-menu {
  position: absolute;
  top: 100%;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 0;
  margin: 2px 0 0 0;
  box-shadow: 0 0 6px 0 rgba(0,0,0,0.1);
  background-color: #ffffff;
  list-style-type: none;
}

.dd-input + .dd-menu {
  display: none;
}

.dd-input:checked + .dd-menu {
  display: block;
}

.dd-menu li {
  padding: 10px 20px;
  cursor: pointer;
  white-space: nowrap;
}

.dd-menu li:hover {
  background-color: #f6f6f6;
}

.dd-menu li a {
  display: block;
  margin: -10px -20px;
  padding: 10px 20px;
}

.dd-menu li.divider{
  padding: 0;
  border-bottom: 1px solid #cccccc;
}
</style>