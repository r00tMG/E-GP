<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import Navbar from '@/components/Navbar.vue';
import axios from "@/axios"; // ton instance axios (avec baseURL si possible)

const route = useRoute();
const router = useRouter();
const annonceId = route.params.id;

const token = ref(localStorage.getItem("access_token") || null);

const annonce = ref(null);
const loading = ref(false);
const error = ref(null);

const submitting = ref(false);
const successMessage = ref('');

// Formulaire réservation
const kilosItems = ref([]);
const specialItems = ref([]);

const nbKgInput = ref(0); // champ rapide si tu veux un champ simple
const qtyInput = ref(1);

// utilitaires date/heure
function formatDate(isoDate) {
  if (!isoDate) return '';
  const d = new Date(isoDate);
  return d.toLocaleDateString("fr-FR", { day: "2-digit", month: "short", year: "numeric" });
}
function formatTime(isoDate) {
  if (!isoDate) return '';
  const d = new Date(isoDate);
  return `${d.getHours().toString().padStart(2,"0")}:${d.getMinutes().toString().padStart(2,"0")}`;
}

async function fetchAnnonce() {
  loading.value = true;
  error.value = null;
  try {
    const resp = await axios.get(`/annonce/${annonceId}`, {
      headers: token.value ? { Authorization: `Bearer ${token.value}` } : {}
    });
    // Ajuste selon la shape de ta réponse. Ici j'assume resp.data.annonce ou resp.data
    annonce.value = resp?.data?.annonce ?? resp?.data ?? null;
    if (!annonce.value) {
      throw new Error("Annonce introuvable dans la réponse");
    }
  } catch (err) {
    console.error("Erreur fetchAnnonce:", err);
    error.value = err?.response?.data?.detail || err.message || "Erreur lors de la récupération de l'annonce";
  } finally {
    loading.value = false;
  }
}

// Ajout / suppression d'items
function addKiloItem() {
  kilosItems.value.push({ item_name: "Marchandise", weight: 1 });
}
function removeKiloItem(index) {
  kilosItems.value.splice(index, 1);
}
function addSpecialItem() {
  specialItems.value.push({ item_name: "Spécial", quantity: 1 });
}
function removeSpecialItem(index) {
  specialItems.value.splice(index, 1);
}

// Calcul total estimé (localement, même si backend recalculera)
function estimateTotal() {
  if (!annonce.value) return 0;
  let total = 0;
  const prixKilo = Number(annonce.value.prix_du_kilo ?? annonce.value.prix_par_kilo ?? 0);
  const prixPiece = Number(annonce.value.prix_par_piece ?? 0);
  kilosItems.value.forEach(i => {
    const w = Number(i.weight) || 0;
    total += prixKilo * w;
  });
  specialItems.value.forEach(s => {
    const q = Number(s.quantity) || 0;
    total += prixPiece * q;
  });
  return total;
}

function validateBeforeSubmit() {
  error.value = null;
  if (!token.value) {
    error.value = "Vous devez être connecté en tant que client pour réserver.";
    return false;
  }
  if (!annonce.value) {
    error.value = "Annonce non chargée.";
    return false;
  }
  // Vérifier kilos disponibles
  const totalKgRequested = kilosItems.value.reduce((s, it) => s + (Number(it.weight) || 0), 0);
  const kilosDisponibles = Number(annonce.value.kilos_disponibles ?? annonce.value.kilos_disponibles ?? 0);
  if (totalKgRequested > kilosDisponibles) {
    error.value = `Poids demandé (${totalKgRequested} Kg) supérieur aux ${kilosDisponibles} Kg disponibles.`;
    return false;
  }
  // Vérifier entrées positives
  for (const it of kilosItems.value) {
    if (!it.item_name || Number(it.weight) <= 0) {
      error.value = "Chaque marchandise au kilo doit avoir un nom et un poids > 0.";
      return false;
    }
  }
  for (const s of specialItems.value) {
    if (!s.item_name || Number(s.quantity) <= 0) {
      error.value = "Chaque marchandise spéciale doit avoir un nom et une quantité > 0.";
      return false;
    }
  }
  return true;
}

// Soumission reservation
async function submitReservation(evt) {
  evt?.preventDefault();
  successMessage.value = '';
  if (!validateBeforeSubmit()) return;
  submitting.value = true;
  try {
    const payload = {
      annonce_id: Number(annonceId),
      items: kilosItems.value.map(i => ({
        item_name: i.item_name,
        weight: Number(i.weight)
      })),
      special_items: specialItems.value.map(s => ({
        item_name: s.item_name,
        quantity: Number(s.quantity)
      }))
    };
    //console.log("payload reservation:", payload);

    const resp = await axios.post("/reservations", payload, {
      headers: { Authorization: `Bearer ${token.value}` }
    });

    console.log("reservation resp:", resp);
    successMessage.value = resp?.data?.message || "Réservation créée avec succès";
    // Redirection possible vers une page de réservation / profil
    const reservationId = resp?.data?.reservation.reservation_id;
    router.push(`/paiement/${reservationId}`);
    // router.push('/mes-reservations')
  } catch (err) {
    console.error("Erreur création réservation:", err);
    error.value = err?.response?.data?.detail || err?.response?.data?.message || err.message || "Erreur lors de la création de la réservation";
  } finally {
    submitting.value = false;
  }
}

onMounted(() => {
  fetchAnnonce();
});
</script>

<template>
  <Navbar />
  <div>
    <router-view>
      <div class="container py-5">
        <div v-if="loading" class="text-center">Chargement de l'annonce...</div>
        <div v-if="error" class="alert alert-danger text-center">{{ error }}</div>
        <div v-if="annonce" class="d-flex justify-content-center">
          <div class="w-75 border border-success p-4 rounded-3 ">
            <div class="d-flex align-items-center mb-5">
              <img :src="annonce.gp?.profilePicture || '#'" class="border border-success p-4 rounded-circle me-3" style="width:80px;height:80px;object-fit:cover" alt="GP" />
              <div>
                <h4 class="mb-0">{{ annonce.gp?.email || 'GP' }}</h4>
                <small class="text-muted">{{ annonce.description || '' }}</small>
              </div>
            </div>

            <div class="mb-3">
              <h4 class="">Détails de l'annonce</h4>
              <div class="row">
                <div class="col-md-6">
                  <label class="form-label">Départ</label>
                  <input class="form-control border-success bg-light" readonly :value="annonce.origin" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date Départ</label>
                  <input class="form-control border-success bg-light" readonly :value="formatDate(annonce.date_depart) + ' à ' + formatTime(annonce.date_depart)" />
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row">
                <div class="col-md-6">
                  <label class="form-label">Arrivée</label>
                  <input class="form-control border-success bg-light" readonly :value="annonce.destination" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date Arrivée</label>
                  <input class="form-control border-success bg-light" readonly :value="formatDate(annonce.date_arrivee) + ' à ' + formatTime(annonce.date_arrivee)" />
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row">
                <div class="col-md-4">
                  <label class="form-label">Prix / Kg</label>
                  <input class="form-control border-success bg-light" readonly :value="annonce.prix_du_kilo ?? annonce.prix_par_kilo ?? '—'" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Prix / Pièce</label>
                  <input class="form-control border-success bg-light" readonly :value="annonce.prix_par_piece ?? '—'" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Kg disponibles</label>
                  <input class="form-control border-success bg-light" readonly :value="annonce.kilos_disponibles ?? 0" />
                </div>
              </div>
            </div>

            <hr />

            <form @submit="submitReservation">
              <h5>Marchandises au kilo</h5>
              <div v-if="kilosItems.length === 0" class="mb-2">
                <small class="text-muted">Aucun item ajouté. Ajoute au moins un item ou ajoute des marchandises spéciales.</small>
              </div>

              <div v-for="(it, idx) in kilosItems" :key="idx" class="row mb-2 align-items-end">
                <div class="col-md-6">
                  <label class="form-label">Désignation (kilo)</label>
                  <input v-model="it.item_name" type="text" class="form-control" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Poids (Kg)</label>
                  <input v-model.number="it.weight" type="number" min="0" step="0.1" class="form-control" />
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-danger" @click="removeKiloItem(idx)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash text-ligth" viewBox="0 0 16 16">
  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
  <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
</svg>
                  </button>
                </div>
              </div>

              <div class="mb-3">
                <button type="button" class="btn btn-outline-success me-2" @click="addKiloItem">+ Ajouter un item au kilo</button>
                <!-- quick add: champ simple -->
                <!-- <div class="d-inline-block ms-2">
                  <input v-model.number="nbKgInput" type="number" min="0" placeholder="Kg" class="form-control d-inline-block" style="width:120px" />
                  <button type="button" class="btn btn-outline-primary ms-1" @click="() => { if (nbKgInput>0) { kilosItems.push({item_name:'Marchandise', weight: nbKgInput}); nbKgInput=0 } }">Ajouter Kg</button>
                </div> -->
              </div>

              <hr />

              <h5>Marchandises spéciales</h5>
              <div v-for="(s, i) in specialItems" :key="i" class="row mb-2 align-items-end">
                <div class="col-md-6">
                  <label class="form-label">Désignation (spécial)</label>
                  <input v-model="s.item_name" type="text" class="form-control" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Quantité</label>
                  <input v-model.number="s.quantity" type="number" min="1" step="1" class="form-control" />
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-danger" @click="removeSpecialItem(i)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash text-light" viewBox="0 0 16 16">
  <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
  <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
</svg>
                  </button>
                </div>
              </div>

              <div class="mb-3">
                <button type="button" class="btn btn-outline-success me-2" @click="addSpecialItem">+ Ajouter un item spécial</button>
                <!-- <div class="d-inline-block ms-2">
                  <input v-model.number="qtyInput" type="number" min="1" placeholder="Qté" class="form-control d-inline-block" style="width:100px" />
                  <button type="button" class="btn btn-outline-primary ms-1" @click="() => { if (qtyInput>0) { specialItems.push({item_name:'Spécial', quantity: qtyInput}); qtyInput=1 } }">Ajouter Qté</button>
                </div> -->
              </div>
              <hr />
              <div class="mb-3 d-flex justify-content-between">
                <h4>Estimation total :</h4> 
                <h4>{{ estimateTotal() }} FCFA</h4>
              </div>

              <div v-if="successMessage" class="alert alert-success text-center">{{ successMessage }}</div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success w-100" :disabled="submitting">
                  {{ submitting ? 'En cours...' : 'Réserver' }}
                </button>
                <button type="button" class="btn btn-secondary" @click="router.back()">Annuler</button>
              </div>
            </form>

          </div>
        </div>

      </div>
    </router-view>
  </div>
</template>

<style scoped>
/* styles légers */
</style>

<!-- <script setup>
import Navbar from '@/components/Navbar.vue'

</script>
<template>
    <Navbar />
    <div>
        <router-view>
            <div class="container">
                <div class="d-flex justify-content-center align-items-center vh-100">
                    <div class="border border-success p-5 rounded-3">
                        <div class="container d-flex align-items-center ">
                            <img src="#" class="border border-success rounded-circle p-4 my-5 me-3"  />
                            <div class="">
                                <h3 class="mb-0"> John Doe</h3>
                                <p class="text-muted">Description</p>
                            </div>
                        </div>
                        <div class="container">
                            <form >
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Départ</label>
                                        <input class="form-control border-success  bg-light" readonly value="Dakar" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date Départ</label>
                                        <input class="form-control border-success  bg-light" readonly value="03 Nov 2025 à 15h" />
                                    </div>
                                </div>   
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Arrivée</label>
                                        <input class="form-control  border-success bg-light" readonly value="Casablanca" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Date Arrivée</label>
                                        <input class="form-control border-success  bg-light" readonly value="03 Nov 2025 à 15h" />
                                    </div>
                                </div>    
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Détails Marchandises</label>
                                        <input class="form-control  border-success bg-light" readonly value="Casablanca" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Prix/Kg</label>
                                        <input class="form-control border-success  bg-light" readonly value="70" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nombre Kg</label>
                                        <input type="number" placeholder="<70" class="form-control border-success bg-light"  />
                                    </div>
                                </div>   
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Marchandises Spécialee</label>
                                        <input class="form-control  border-success bg-light" value="Tissus" />
                                        <p>Casier judiciaire
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                            </svg>
                                            </p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Prix/Pièce</label>
                                        <input class="form-control border-success  bg-light" readonly value="100" />
                                    </div>
                                
                                    
                                </div>  
                                <div class="text-center">
                                    <button class="btn btn-success" style="width: 350px;">Réserver</button>
                                </div>
                            </form>  
                        </div>
                    </div>
                </div>
            </div>
        </router-view>
    </div>
</template> -->