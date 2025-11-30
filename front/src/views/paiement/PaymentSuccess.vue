<script setup>
import Navbar from '@/components/Navbar.vue';

</script>
<template>
    <Navbar />
    <div class="container w-50 justify-content-center vh-100">
  <div class="container border border-success rounded-4 shadow p-5 ">
    <h1 class="text-center">Merci pour votre réservation !</h1>
    <p class="text-center" >Votre paiement a été reçu avec succès.</p>
    <!--<p class="text-center">Vous pouvez générer votre facture en cliquant sur le bouton ci-dessous :</p>
     <p class="text-center"><button @click="onUploads" class="btn btn-sm btn-outline-success">Générer</button></p>
    <p class="text-center"><a :href="invoice_url" target="_blank" v-if="invoice_url">Lien de téléchargement...</a></p> -->
  </div>
</div>
</template>