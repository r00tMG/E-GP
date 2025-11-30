<script setup>
import Navbar from '@/components/Navbar.vue';

import { useRoute, useRouter } from 'vue-router';
import axios from "@/axios";
const route = useRoute();
const router = useRouter();

const reservationId = route.params.id;

function payerOrangeMoney() {
  router.push(`/paiement/orange-money/${reservationId}`);
}

function payerWave() {
  router.push(`/paiement/wave/${reservationId}`);
}


async function payerStripe() {
  try {
    const response = await axios.post("/stripe/create-checkout-session", {
      reservation_id: Number(reservationId) // voir point 2
    });
    window.location.href = response.data.checkout_url;
  } catch (err) {
    console.error("Erreur create-checkout-session:", err);
    console.error("response.data:", err?.response?.data);
    alert("Erreur: " + JSON.stringify(err?.response?.data));
  }
}

</script>

<template>
    <Navbar />
  <div class="container py-5 text-center">
    <h3>Choisissez votre moyen de paiement</h3>

    <div class="d-flex justify-content-center gap-4 mt-4">

      <button class="btn btn-warning btn-lg" @click="payerOrangeMoney">
        Orange Money
      </button>

      <button class="btn btn-primary btn-lg" @click="payerWave">
        Wave
      </button>

      <button class="btn btn-dark btn-lg" @click="payerStripe">
        Stripe (carte bancaire)
      </button>

    </div>
  </div>
</template>
