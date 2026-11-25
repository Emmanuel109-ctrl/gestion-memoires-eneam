# Bibliothèque des mémoires — ENEAM

Application Laravel de gestion et de consultation des mémoires de l'École Nationale d'Économie Appliquée et de Management (ENEAM).

## Fonctionnalités

- Connexion par **matricule à 8 chiffres** pour les étudiants, par **e-mail** pour le personnel.
- Inscription publique réservée aux étudiants : matricule obligatoire, unique, composé de 8 chiffres.
- Trois rôles : `admin`, `bibliothecaire`, `etudiant`, chacun avec son tableau de bord.
- Middleware `role` pour protéger les routes métier.
- Middleware `EnsureAccountIsActive` : un compte désactivé (`users.actif = false`) ne peut plus se connecter et ses sessions sont fermées.
- Référentiel des **filières** et des **années académiques**.
- **Vérification documentaire** : file d'attente, fiche de contrôle, cases de conformité, décision (`valide`, `a_corriger`, `rejete`) avec motif obligatoire.
- Cycle de vie d'un mémoire : `soumis` → `en_verification` → `valide` (+ `a_corriger`, `rejete`).
- Interface en français, identité visuelle alignée sur le site officiel de l'ENEAM (Montserrat, palette bleue `eneam`, logo officiel).

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

Mot de passe commun : `password`

| Rôle | Identifiant |
|---|---|
| Administrateur | `admin@eneam.bj` |
| Bibliothécaire | `biblio@eneam.bj` |
| Étudiant | `20240001` |
| Étudiant | `20240002` |

Les étudiants peuvent aussi se connecter avec leur adresse e-mail (`etudiant@eneam.bj`,
`etudiant2@eneam.bj`).

## Règles métier

- Un matricule contient **exactement 8 chiffres** (`^[0-9]{8}$`).
- Le matricule est unique et sert d'identifiant de connexion.
- Le champ `role` n'est pas mass-assignable : il est attribué explicitement côté serveur.
- Une décision `a_corriger` ou `rejete` exige un motif.
- Le matricule est auto-déclaré ; la bibliothèque le vérifie lors du contrôle du dossier.

## Tests

```powershell
php artisan test
```

## Documentation

- [`docs/RESUME_LOT1.md`](docs/RESUME_LOT1.md) — détail technique du lot 1.
- [`docs/RESUME_CAMARADES.md`](docs/RESUME_CAMARADES.md) — résumé pour l'équipe.
- [`docs/MAQUETTE_IRREGULARITES.md`](docs/MAQUETTE_IRREGULARITES.md) — écarts relevés dans la maquette.

## Prochaines étapes

- **Lot 2** : dépôt de mémoire (PDF privé, hash anti-doublon), catalogue, recherche, fiche mémoire.
- **Lot 3** : lecteur PDF, embargo/confidentialité, notifications, favoris, historique de lecture, gestion des utilisateurs.
