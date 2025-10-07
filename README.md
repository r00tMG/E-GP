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
```
### 2. Backend (API FastAPI)
```bash
cd backend
python -m venv .venv
source .venv/bin/activate   # (Linux/Mac)
.venv\Scripts\activate      # (Windows)

pip install -r requirements.txt
alembic upgrade head   # migration base de données
uvicorn app.main:app --reload
```

### 3. Frontend (React)
```bash
cd front
npm install
npm start
```
