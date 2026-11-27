# Bibliothèque des mémoires — ENEAM

Application Laravel de gestion et de consultation des mémoires de l'École Nationale d'Économie Appliquée et de Management (ENEAM).

## Fonctionnalités

- **Accueil public** : présentation de la bibliothèque, chiffres du fonds et derniers mémoires.
- **Catalogue public** : recherche, filtres par filière et année, notices et pages de garde accessibles sans compte.
- **Activation étudiante encadrée** : pas d'inscription libre ; l'étudiant active son compte avec son matricule et l'e-mail présent dans l'annuaire ENEAM.
- Connexion par **matricule à 8 chiffres** pour les étudiants, par **e-mail** pour le personnel, sur une page unique.
- Trois rôles : `admin`, `bibliothecaire`, `etudiant`, chacun avec son tableau de bord.
- **Accès différencié** : page de garde publique ; texte intégral réservé aux étudiants authentifiés et au personnel, selon la confidentialité et l'embargo. Les PDF restent dans le stockage privé.
- **Journalisation** des consultations et téléchargements dans `historiques_lecture`.
- Middleware `role` pour protéger les routes métier.
- Middleware `EnsureAccountIsActive` : un compte désactivé (`users.actif = false`) ne peut plus se connecter et ses sessions sont fermées.
- Référentiel des **filières** et des **années académiques**.
- **Vérification documentaire** : file d'attente, fiche de contrôle, cases de conformité et décisions (`valide`, `a_corriger`, `rejete`) avec motif obligatoire. Chaque décision est conservée dans un historique consultable par l'administration et l'étudiant concerné.
- Cycle de vie d'un mémoire : `soumis` → `en_verification` → `valide` (+ `a_corriger`, `rejete`).
- **Dépôt étudiant** : envoi de PDF privés, suivi de statut, retour des corrections et conservation des versions précédentes.
- **Notifications internes** : alertes lors des nouveaux dépôts et des décisions de validation, avec suivi des notifications lues/non lues.
- **Comptes du personnel** : création, activation et désactivation des comptes par l'administration.
- **Suivi des décisions** : historique conservé à chaque passage en validation, consultable par l'administration et par l'étudiant pour ses propres dépôts, avec export CSV filtrable réservé à l'administration.
- **Espaces de travail par rôle** : suggestions et historique côté étudiant, file priorisée côté bibliothèque, indicateurs d'usage et de qualité du fonds côté administration.
- **Gestion des mémoires** : recherche par titre/auteur et filtre par statut pour le personnel autorisé.
- **Import** de l'annuaire étudiant et des mémoires depuis des fichiers CSV locaux, avec copie des PDF en privé et génération des pages de garde.
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

Le site est ensuite disponible sur <http://127.0.0.1:8145>.

Adapter `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` et `DB_PASSWORD` dans `.env`
si votre MySQL n'utilise pas la configuration XAMPP par défaut.

### Importer l'annuaire et les mémoires

Les fichiers réels sont **ignorés par Git** (`database/data/*.csv`, PDF privés).
Des modèles sont fournis : `database/data/etudiants_eneam.example.csv` et
`database/data/memoires.example.csv`.

```powershell
# Annuaire des étudiants (CSV : matricule;nom;email;filiere;promotion;actif)
php artisan eneam:importer-etudiants --dry-run
php artisan eneam:importer-etudiants

# Mémoires (manifeste CSV + dossier des PDF)
php artisan eneam:importer-memoires --dry-run --source="C:\chemin\vers\les\pdf"
php artisan eneam:importer-memoires --source="C:\chemin\vers\les\pdf"
```

L'import des mémoires copie les PDF dans `storage/app/private/memoires`, calcule
leur empreinte SHA-256 et génère une page de garde dans `storage/app/private/couvertures`.

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

Pour tester l'activation : ouvrir `/premiere-connexion` avec le matricule `20249999`
et l'e-mail `premiere.connexion@eneam.bj`, puis choisir un mot de passe.

## Règles métier

- Un matricule contient **exactement 8 chiffres** (`^[0-9]{8}$`).
- Le matricule est unique et sert d'identifiant de connexion.
- **Aucune inscription publique** : un compte étudiant ne peut être activé que si le
  couple matricule + e-mail correspond à une fiche active de `etudiants_eneam`.
- Les comptes du personnel sont créés par l'administration.
- Le champ `role` n'est pas mass-assignable : il est attribué explicitement côté serveur.
- Une décision `a_corriger` ou `rejete` exige un motif.
- Un étudiant retiré de l'annuaire (fiche inactive) ne peut plus se connecter.
- La page de garde est publique ; le PDF complet n'est jamais exposé directement.
- Le texte intégral suit la confidentialité (`public`, `interne`, `restreint`) et l'embargo.
- Toute lecture ou tout téléchargement authentifié est journalisé.

## Tests

```powershell
php artisan test
```

## Documentation

- [`docs/ACCES_ET_DONNEES.md`](docs/ACCES_ET_DONNEES.md) — matrice d'accès, annuaire, activation et précautions juridiques.
- [`docs/RESUME_LOT1.md`](docs/RESUME_LOT1.md) — détail technique du lot 1.
- [`docs/RESUME_CAMARADES.md`](docs/RESUME_CAMARADES.md) — résumé pour l'équipe.
- [`docs/MAQUETTE_IRREGULARITES.md`](docs/MAQUETTE_IRREGULARITES.md) — écarts relevés dans la maquette.

## Évolutions restantes

- Exports consolidés CSV des mémoires et statistiques pour les bilans administratifs (l'historique des décisions est déjà exportable).
- Notifications par e-mail (en complément des notifications internes), après configuration et validation du service SMTP.
- Exploitation réelle : annuaire fourni par la scolarité, autorisation écrite pour les données des étudiants, sauvegardes et déploiement HTTPS.
