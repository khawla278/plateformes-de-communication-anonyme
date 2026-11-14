# Plateforme de Communication Anonyme

## Description

Application web permettant aux utilisateurs de communiquer de manière anonyme à travers une plateforme simple, intuitive et sécurisée.

Le projet repose sur une architecture séparant le frontend et le backend afin de faciliter la maintenance, l'évolution et la gestion des fonctionnalités.

## Technologies utilisées

### Frontend
- Angular
- TypeScript
- HTML5 / CSS3

### Backend
- Laravel
- API REST

### Base de données
- MySQL

## Architecture du projet

Le projet est organisé en deux parties principales :

```text
plateformes-de-communication-anonyme/
│
├── backend/          # API REST développée avec Laravel
│
└── frontend/         # Interface utilisateur développée avec Angular
```

## Installation et exécution

### 1. Backend – Laravel

Accéder au dossier backend :

```bash
cd backend
```

Installer les dépendances :

```bash
composer install
```

Configurer le fichier `.env` avec les informations de connexion à la base de données.

Générer la clé de l'application :

```bash
php artisan key:generate
```

Exécuter les migrations :

```bash
php artisan migrate
```

Démarrer le serveur Laravel :

```bash
php artisan serve
```

Backend disponible par défaut sur :

http://127.0.0.1:8000

### 2. Frontend – Angular

Accéder au dossier frontend :

```bash
cd frontend
```

Installer les dépendances :

```bash
npm install
```

Démarrer l'application :

```bash
ng serve
```

Frontend disponible sur :

http://localhost:4200/

## Fonctionnalités principales

- Communication anonyme entre utilisateurs.
- Interface web interactive.
- Échange de données entre Angular et Laravel via une API REST.
- Gestion des données avec MySQL.



## Licence

Ce projet est développé dans un cadre académique.
