<script>
import Dropdown from '@/components/Dropdown.vue'
import logo from '@/assets/images/logo.png'
import axios from "@/axios.js";
import {onMounted, ref} from "vue";
export default {
  name: 'Navbar',
  components: { Dropdown },
  setup(){
    const token = localStorage.getItem('access_token')
    const count = ref('')
    const fetchUnReadCountMessage = async () =>{
      const r = await axios.get(`/messages/unread/${JSON.parse(localStorage.getItem('data')).user.id}`,{
        headers:{
          Authorization:`Bearer ${localStorage.getItem('token')}`
        }
      })
      count.value = await r.data.unread_messages_count
      console.log('Compteur',count.value)
    }
    onMounted(()=>{
      //fetchUnReadCountMessage()
    })
    return {
      token,
      logo,
      count
    }
  }
}
</script>

<template>
  <div class="container  m-auto">
    <nav class="navbar navbar-expand-lg bg-body-light " data-bs-theme="dark">
      <div class="container-fluid px-5">
        <router-link to="/" class="navbar-brand text-dark" :class="{ active: $route.path === `/` }" >
          <img :src="logo"  alt="Logo" width="80px"><span class="text-success">E-GP</span></router-link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
<!--            <li class="nav-item">
              <router-link to="/"  class="nav-link text-dark" :class="{ active: $route.path === `/` }">Home</router-link>
            </li>-->
          </ul>
            <!-- <li class="ms-2" v-if="token">
              <router-link to="/messages">
                <span class="icon-count" v-if="count>0">{{count}}</span>
                <span v-else></span>
                <i class="fa fa-envelope fa-2x text-success"></i>
              </router-link>
            </li> -->
            <Dropdown />
        </div>
      </div>
    </nav>
  </div>

</template>

<style scoped>
/* Import Font Dancing Script */
@import url(https://fonts.googleapis.com/css?family=Dancing+Script);

* {
  margin: 0;
}

body {
  background-color: #e8f5ff;
  font-family: Arial;
  overflow: hidden;
}

/* NavbarTop */
.navbar-top {
  background-color: #fff;
  color: #333;
  box-shadow: 0px 4px 8px 0px grey;
  height: 70px;
}

.title {
  font-family: 'Dancing Script', cursive;

}

.navbar-top ul {
  float: right;
  list-style-type: none;
  margin: 0;
  overflow: hidden;
  padding: 18px 50px 0 40px;
}

.navbar-top ul li {
  float: left;
}

.navbar-top ul li a {
  color: #333;
  padding: 14px 16px;
  text-align: center;
  text-decoration: none;
}

div#navbarSupportedContent a{
  position: relative;
}

.icon-count {
  background-color: #ff0000;
  color: #fff;
  font-size: 11px;
  padding: 2px;
  /*position: relative;*/
  border-radius: 100px;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  top: -14px;
  left: 17px;
}


/* End */

/* Sidenav */
.sidenav {
  background-color: #fff;
  color: #333;
  border-bottom-right-radius: 25px;
  height: 86%;
  left: 0;
  overflow-x: hidden;
  padding-top: 20px;
  position: absolute;
  top: 70px;
  width: 250px;
}

.profile {
  margin-bottom: 20px;
  margin-top: -12px;
  text-align: center;
}

.profile img {
  border-radius: 50%;
  box-shadow: 0px 0px 5px 1px grey;
}

.name {
  font-size: 20px;
  font-weight: bold;
  padding-top: 20px;
}

.job {
  font-size: 16px;
  font-weight: bold;
  padding-top: 10px;
}

.url, hr {
  text-align: center;
}

.url hr {
  margin-left: 20%;
  width: 60%;
}

.url a {
  color: #ebeced;
  display: block;
  font-size: 20px;
  margin: 10px 0;
  padding: 6px 8px;
  text-decoration: none;
}

.url a:hover, .url .active {
  background-color: #313b42;
  border-radius: 28px;
  color: #f8f5f5;
  margin-left: 14%;
  width: 65%;
}
/* End */

/* Main */
.main {
  margin-top: 2%;
  margin-left: 29%;
  font-size: 28px;
  padding: 0 10px;
  width: 58%;
}

.main h2 {
  color: #333;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 24px;
  margin-bottom: 10px;
}

.main .card {
  background-color: #fff;
  border-radius: 18px;
  box-shadow: 1px 1px 8px 0 grey;
  height: auto;
  margin-bottom: 20px;
  padding: 20px 0 20px 50px;
}

.main .card table {
  border: none;
  font-size: 16px;
  height: 270px;
  width: 80%;
}

.edit {
  position: absolute;
  color: #e7e7e8;
  right: 14%;
}

.social-media {
  text-align: center;
  width: 90%;
}

.social-media span {
  margin: 0 10px;
}

.fa-facebook:hover {
  color: #4267b3 !important;
}

.fa-twitter:hover {
  color: #1da1f2 !important;
}

.fa-instagram:hover {
  color: #ce2b94 !important;
}

.fa-invision:hover {
  color: #f83263 !important;
}

.fa-github:hover {
  color: #161414 !important;
}

.fa-whatsapp:hover {
  color: #25d366 !important;
}

.fa-snapchat:hover {
  color: #fffb01 !important;
}

/* End */
</style>