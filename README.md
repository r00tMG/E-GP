# 🚚 Application de Gestion & Réservation de GP avec Services IA

## 📌 Contexte
Dans le secteur du transport partagé (GP – Gratuité Partielle), les clients cherchent à **réserver des kilos disponibles auprès des voyageurs (GP)** pour transporter leurs marchandises de manière simple, rapide et fiable.  
Ce projet vise à proposer une **plateforme complète (Web + API + IA)** intégrant réservation, paiement sécurisé et services intelligents basés sur le Machine Learning.

---

## 🎯 Objectifs du Projet
- Réserver des kilos disponibles chez un GP.  
- Sécuriser la réservation via un paiement en ligne.  
- Améliorer l’expérience client grâce à l’IA :  
  - Prédire la disponibilité et la demande.  
  - Recommander le meilleur GP (prix, proximité, fiabilité).  
  - Anticiper les risques d’annulation/non-paiement.  
  - Optimiser les prix de façon dynamique.  

---

## 🧩 Architecture
### Couches principales :
1. **Data Layer** → collecte et gestion des données (réservations, GP, clients, paiements).  
2. **ML Layer** → entraînement & inférence des modèles IA.  
3. **API REST (FastAPI)** → exposition des fonctionnalités et modèles IA.  
4. **Frontend (React)** → interface client (réservation, paiement) & GP (gestion disponibilité).  
5. **Déploiement (Docker + CI/CD)** → automatisation, scalabilité, monitoring.  

---

## 🛠️ Stack Technique
- **Backend** : [FastAPI](https://fastapi.tiangolo.com/), PostgreSQL, SQLAlchemy, Alembic  
- **Frontend** : React ou Flutter (selon choix mobile/web)  
- **Data/IA** : Python (Pandas, NumPy, Scikit-learn, XGBoost, LightGBM, TensorFlow/PyTorch si besoin)  
- **Paiement** : [Stripe](https://stripe.com/)  
- **Déploiement** : Docker, GitHub Actions (CI/CD), Heroku / AWS / GCP (selon besoin)  
- **Monitoring** : Prometheus, Grafana, Logging centralisé  

---

## 🚀 Installation & Lancement
### 1. Cloner le projet
```bash
git clone https://github.com/r00tMG/E-GP.git
cd E-GP
cp .env-example .env
```
### 2. Backend (API FastAPI)
## Prérequis

- Python 3.11 installé sur votre machine

- pip

- virtualenv (optionnel mais recommandé)

- Docker et Docker Compose (si vous utilisez Docker)

## Option 1 : Lancement avec Python
```bash
cd back
# Linux / Mac
python -m venv .venv
source .venv/bin/activate
# Windows
python -m venv .venv
.venv\Scripts\activate

pip install -r requirements.txt
alembic upgrade head   # migration base de données
uvicorn main:app --reload
```
## Option 2 : Lancement avec Docker
```bash
docker compose up -d  #Lancement de tous les services définis dans le docker-compose.yml
docker ps #Vérifiez les services
docker compose down # arrêter les services docker

```

### Accès :

- API : http://localhost:8000/docs
- Base Postgres : localhost:5432
- PgAdmin : http://localhost:5050

### 3. Frontend (React)
```bash
cd front
npm install
npm start
```
