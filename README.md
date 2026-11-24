# Bibliothèque des mémoires — ENEAM

Application Laravel de gestion et de consultation des mémoires de l'ENEAM.

## Fonctionnalités du lot 1

- Connexion par **matricule** pour les étudiants, par **e-mail** pour le personnel.
- Inscription publique réservée aux étudiants (matricule obligatoire et unique).
- Trois rôles : `admin`, `bibliothecaire`, `etudiant`, avec un tableau de bord dédié.
- Middleware `role` prêt pour protéger les futures routes métier.
- Interface entièrement en français (Breeze traduit, messages de validation FR).
- Référentiels filières et années académiques, jeu de mémoires de démonstration.

## Prérequis

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL / MariaDB (le projet est configuré sur le port **3307**)

## Installation

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
npm run build
php artisan serve --port=8145
```

Adapter `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` et `DB_PASSWORD` dans `.env`
si votre MySQL n'utilise pas la configuration XAMPP par défaut.

## Comptes de démonstration

| Rôle | Identifiant | Mot de passe |
|---|---|---|
| Administrateur | `admin@eneam.bj` | `password` |
| Bibliothécaire | `biblio@eneam.bj` | `password` |
| Étudiant | `ETU-2024-001` | `password` |
| Étudiant | `ETU-2024-002` | `password` |

Les étudiants peuvent aussi se connecter avec leur adresse e-mail (`etudiant@eneam.bj`,
`etudiant2@eneam.bj`).

## Tests

```powershell
php artisan test
```

## Prochaines étapes

- **Lot 2** : dépôt de mémoire (PDF privé, hash anti-doublon), workflow de validation, catalogue, recherche.
- **Lot 3** : lecteur PDF, embargo/confidentialité, statistiques, gestion des utilisateurs et des référentiels.
