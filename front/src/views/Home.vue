<script setup>
import Navbar from "@/components/Navbar.vue";
import {useRouter} from 'vue-router'
import {ref, onMounted, reactive} from "vue";
import axios from "@/axios";

const router = useRouter();
const token = ref(localStorage.getItem("access_token"));
const role = ref(localStorage.getItem('role'))
const annonces = ref([]);
const loading = ref(false)
const error = ref('')
const success = ref('')

// Champs de recherche
const searchDateDepart = ref('');
const searchDateArrivee = ref('');
const searchOrigin = ref('');
const searchDestination = ref('');

function selectRole(r) {
  localStorage.setItem('role', r);
  router.push('/register');
}

async function fetchAnnonces() {
  try {
    if (!token.value) {
      const response = await axios.get("/home");
      annonces.value = response.data.annonces;
    } else {
      const response = await axios.get("/annonces", {
        headers: { Authorization: `Bearer ${token.value}` },
        params: {
          search_date_depart: searchDateDepart.value || undefined,
          search_date_arrivee: searchDateArrivee.value || undefined,
          search_origin: searchOrigin.value || undefined,
          search_destination: searchDestination.value || undefined,
        }
      });
      annonces.value = response.data.annonces;
      console.log(annonces.value);
    }
  } catch (error) {
    console.error("Erreur lors du chargement des annonces:", error);
  }
}
function formatDate(isoDate) {
  if (!isoDate) return '';
  const date = new Date(isoDate);
  return date.toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  });
}
function formatTime(isoDate) {
  const date = new Date(isoDate);
  return `${date.getHours().toString().padStart(2,"0")}:${date.getMinutes().toString().padStart(2,"0")}`;
}
function getDuration(dateStart, dateEnd) {
  if (!dateStart || !dateEnd) return '';
  
  const start = new Date(dateStart);
  const end = new Date(dateEnd);
  const diffMs = end - start; 
  
  if (diffMs < 0) return 'Invalid dates';
  
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
  const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
  
  return `${diffHours}h:${diffMinutes.toString().padStart(2, '0')}`;
}
// Recherche déclenchée au clic du bouton
function searchAnnonces() {
  fetchAnnonces();
}


// form state
const form = reactive({
  origin: '',
  date_depart: '',
  destination: '',
  date_arrivee: '',
  description:'',
  prix_du_kilo: null,
  prix_par_piece: null,
  kilos_disponibles: null,
});



// helper pour fermer modal bootstrap programatiquement
function closeBootstrapModal(modalId = 'staticBackdrop') {
  // bootstrap doit être chargé sur la page (bootstrap.bundle.js)
  const el = document.getElementById(modalId)
  if (!el) return
  const bs = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)
  bs.hide()
}

// submit handler
async function handleSubmit() {
  error.value = ''
  success.value = ''

  // petite validation côté client
  if (!form.origin || !form.destination || !form.date_depart || !form.date_arrivee || !form.description) {
    error.value = "Veuillez remplir les champs de départ/arrivée et les dates."
    return
  }

  // build payload — adapte les noms de champs attendus par ton API
  const payload = {
    origin: form.origin,
    date_depart: form.date_depart,
    destination: form.destination,
    description: form.description,
    date_arrivee: form.date_arrivee,
    prix_du_kilo: form.prix_du_kilo,
    prix_par_piece: form.prix_par_piece,
    kilos_disponibles: form.kilos_disponibles,
  };
  loading.value = true
  try {
    const headers = {}
    if (token.value) headers['Authorization'] = `Bearer ${token.value}`

    // si ta route est /api/annonces ou /annonces adapte l'URL
    const res = await axios.post('/annonces', payload, { headers })

    // succès
    success.value = 'Annonce publiée avec succès.'
    // reset form si tu veux
    Object.keys(form).forEach(k => form[k] = (typeof form[k] === 'number' ? null : ''))

    // fermer le modal
    closeBootstrapModal()
    // rafraîchir la liste
    await fetchAnnonces();
  } catch (err) {
    // traitement d'erreurs
    if (err.response && err.response.data) {
      // si Laravel renvoie des erreurs de validation
      const data = err.response.data
      if (data.errors) {
        // concatène les messages de validation
        error.value = Object.values(data.errors).flat().join(' — ')
      } else if (data.message) {
        error.value = data.message
      } else {
        error.value = JSON.stringify(data)
      }
    } else {
      error.value = err.message || 'Erreur réseau'
    }
  } finally {
    loading.value = false
  }
}


onMounted(() => {
  fetchAnnonces();
});



</script>
<template>
  <Navbar />
    <div>
      <router-view>
        <div class="container corde rounded-circle"></div>
    <div class="container-fluid px-0">
      <div class="container m-auto p-5">
        <div class="w-75 m-auto m-5 p-5 text-center">
          <h3>
            <span class="text-success">Publiez</span> vos annonces,
            <span class="text-success">Gérez</span> les réservations, et
            <span class="text-success">Connectez-vous</span> rapidement avec vos clients
            grâce à <em class="text-success">E-GP</em>
          </h3>
        </div>
        <div class="container">
          <div class="row m-auto px-3 py-3   ">
            <div class="row m-auto" v-if="!token">
              <div class="col-md-2 m-auto text-center">
                <!-- <router-link to="/search/annonces" class="btn btn-success text-center shadow-sm rounded-5 mt-4 p-3">
                  Commencer
                </router-link> -->

                <p class="text-center"><button @click="selectRole('gp')" style="width: 103.86px;"
                                      class="btn btn-sm btn-success rounded-4">Je suis GP</button></p>
            <p class="text-center"><button @click="selectRole('client')"
                                      class="btn btn-sm btn-success rounded-4">Je suis Client</button ></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid mt-3 bg-light">
      <div class="container m-auto p-5">
    <div class="container">
        <div class="row m-auto px-4 py-5 background  shadow-sm rounded-3 border border-success ">
            <div class="row">
                <div class="col-md-10 row">
                    <div class="col-md-3">
                        <label for="floatingInputGroup1">Départ le</label>
                        <input type="date" v-model="searchDateDepart"
                               class="form-control rounded-4 border border-success p-3" id="floatingInputGroup1"
                               placeholder="">
                    </div>
                    <div class="col-md-3">
                        <label for="floatingInputGroup1">Arrivée le</label>
                        <input type="date" v-model="searchDateArrivee"
                               class="form-control rounded-4 border border-success p-3" id="floatingInputGroup1"
                               placeholder="">
                    </div>
                    <div class="col-md-3">
                        <label class="label">Origine</label>
                        <select v-model="searchOrigin" class="form-select capitalSelect rounded-4 border border-success p-3" id="originSelect">
                            <option value="">Chargement...</option>
                        </select>


                    </div>
                    <div class=" col-md-3">
                        <label class="label">Destination</label>
                        <select v-model="searchDestination" class="form-select capitalSelect rounded-4 border border-success p-3"
                                id="destinationSelect">
                            <option value="">Chargement...</option>
                        </select>

                    </div>
                </div>
                <div class="col-md-2 m-auto">
                    <button @click="searchAnnonces" class="btn btn-success rounded-5 mt-4 p-3" type="button"
                            data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                             class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                        Trouver un GP
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>

    <div class="container">
    <div class="w-75 m-auto">
        

        <div class="container p-5 bg-light">
    <div class="mb-5 d-flex justify-content-between">
      <h3>{{ token ? 'Liste des annonces' : 'Annonces récentes' }}</h3>
      
      <button v-if="token && role === 'gp'" type="button" class="btn btn-success"
            data-bs-toggle="modal" data-bs-target="#staticBackdrop">
      Publier
    </button>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static"
         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form @submit.prevent="handleSubmit">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Publier une annonce</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <div class="container p-5 border border-success rounded-3">
                <div class="mb-3">
                  <div class="row">
                    <div class="col-md-6">
                      <label class="form-label">Origine</label>
                      <input v-model="form.origin" class="form-control border-success bg-light" required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Date Départ</label>
                      <input v-model="form.date_depart" type="datetime-local" class="form-control border-success bg-light" required />
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <div class="row">
                    <div class="col-md-6">
                      <label class="form-label">Destination</label>
                      <input v-model="form.destination" class="form-control border-success bg-light" required />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Date Arrivée</label>
                      <input v-model="form.date_arrivee" type="datetime-local" class="form-control border-success bg-light" required />
                    </div>
                  </div>
                </div>

                <div class="mb-3">
                  <div class="row">
                    <div class="col-md-4">
                      <label class="form-label">Prix / Kg</label>
                      <input v-model.number="form.prix_du_kilo" type="number" min="0" step="0.1" class="form-control border-success bg-light" />
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Prix / Pièce</label>
                      <input v-model.number="form.prix_par_piece" type="number" min="0" step="0.1" class="form-control border-success bg-light" />
                    </div>
                    <div class="col-md-4">
                      <label class="form-label">Kg disponibles</label>
                      <input v-model.number="form.kilos_disponibles" type="number" min="0" step="0.1" class="form-control border-success bg-light" />
                    </div>
                  </div>
                </div>
                <div class="mb-3">
  <label class="form-label">Description</label>
  <textarea v-model="form.description"
            rows="4"
            class="form-control border-success bg-light"
            placeholder="Décris l'annonce (taille du chargement, particularités, contact...)" required></textarea>
</div>
               

                <div v-if="error" class="alert alert-danger">{{ error }}</div>
                <div v-if="success" class="alert alert-success">{{ success }}</div>
              </div>
            </div>

            <div class="modal-footer">
              <button :disabled="loading" type="submit" class="btn btn-success">
                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Publier
              </button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    </div>
    <div v-if="annonces.length > 0">
    <div  v-for="annonce in annonces" :key="annonce.id" class="bg-white mb-3 p-3 shadow m-auto border-success border rounded-3">
      <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <div>
          <img class="rounded-circle" src="#" width="40px" height="40px" alt="Photo Profile">
          {{ annonce.gp.email }}
        </div>
        <div>
          <p  class="text-muted mt-1">Publiée il y a {{ annonce.timeAgo }}</p>
        </div>
      </div>
      
      <div class="row p-0">
        <div class="col-md-10 p-0">
          <div class="card-body border-success rounded-start-2 shadow border">
            <div class="row p-3">
              <div class="col-md-4 d-flex flex-column justify-content-center align-items-center">
                <h6>Départ</h6>
                <h4 class="text-success">{{ formatDate(annonce.date_depart) }}</h4>
                <h4 class="text-success">{{ formatTime(annonce.date_depart) }}</h4>
                <div>{{ annonce.origin }}</div>
              </div>
              <div class="col-md-4 d-flex flex-column justify-content-center align-items-center">
                <p class="btn btn-sm btn-success text-white w-100 my-0 rounded-4">
                  {{ getDuration(annonce.date_depart, annonce.date_arrivee) }}
                </p>
              </div>
              <div class="col-md-4 d-flex flex-column justify-content-center align-items-center">
                <h6>Arrivée</h6>
                <h4 class="text-success">{{ formatDate(annonce.date_arrivee) }}</h4>
                <h4 class="text-success">{{ formatTime(annonce.date_arrivee) }}</h4>
                <div>{{ annonce.destination }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-2 border-success border rounded-start-2 text-center">
          <h4 class="mt-2">Poids</h4>
          <div><span class="text-muted" v-if="annonce.kilos_disponibles > 0">Disponible</span><span class="text-muted" v-else>Indisponible</span></div>
          <div><strong>{{ annonce.kilos_disponibles }} Kg</strong></div>
          <router-link :to="`/annonce/${annonce.id}`" class="btn btn-sm btn-success rounded-4">Détails</router-link>
        </div>
      </div>
    </div>
  </div>
    <div v-else class="text-center">Aucune annonce n'est disponible</div>
  </div>
        
    </div>
</div>
      </router-view>
    </div>
</template>


<style>
.corde {
    box-sizing: border-box;
    position: absolute;
    width: 896px;
    height: 100vh;
    left: -404px;
    top: -65px;
    border: 1px solid rgba(188, 189, 237, 0.5);
    z-index: -1;
}

.rounded-circle {
    border-radius: 50% !important;
}
</style>