# Résumé du lot 1 — Bibliothèque des mémoires ENEAM

## Objectif du lot

Poser un socle fiable pour la bibliothèque numérique des mémoires de l'ENEAM :

- authentification par **matricule à 8 chiffres** pour les étudiants ;
- authentification par **e-mail** pour le personnel ;
- trois espaces distincts (`admin`, `bibliothecaire`, `etudiant`) ;
- premiers écrans métier : tableaux de bord, référentiel des années, vérification documentaire ;
- identité visuelle alignée sur le site officiel de l'ENEAM.

## Fonctionnalités livrées

### Authentification et comptes

- Connexion par matricule **ou** adresse e-mail via un champ unique `identifiant`.
- Inscription publique réservée aux étudiants.
- Matricule validé par l'expression `^[0-9]{8}$` : exactement 8 chiffres, ni lettre, ni tiret, ni slash.
- Matricule unique en base, utilisé comme identifiant de connexion.
- Le champ `role` n'est **pas** mass-assignable : une inscription ne peut pas fabriquer un administrateur.
- Compte désactivable via `users.actif` :
  - la connexion est refusée ;
  - les sessions déjà ouvertes sont fermées par le middleware `EnsureAccountIsActive`.

### Tableaux de bord

| Espace | Contenu |
|---|---|
| Administration | volumétrie du fonds, répartition par statut, top filières, activité récente, derniers dépôts, référentiels |
| Bibliothèque | file d'attente, indicateurs par statut (dont « à corriger » et « rejetés »), derniers dépôts |
| Étudiant | hero personnalisé, matricule mis en avant, compteurs personnels, étapes du dépôt |

### Référentiels

- Gestion des **années académiques** : création, activation unique.
- Gestion des **filières** : code et nom uniques, utilisés par les mémoires.
- Jeu de données de démonstration : 6 mémoires, validations associées, 2 étudiants, 1 administrateur, 1 bibliothécaire.

### Vérification documentaire

- File de validation triée par ancienneté.
- Fiche de contrôle avec :
  - identité de l'étudiant et matricule ;
  - filière, année, type de diplôme, date de dépôt ;
  - résumé et fichier PDF ;
  - cases de conformité (`conforme_version_papier`, `format_conforme`) ;
  - décision : `valide`, `a_corriger` ou `rejete` ;
  - motif obligatoire en cas de correction ou de rejet.
- Une décision met à jour le statut du mémoire et crée une trace dans `validations`.

## Schéma de données

### Tables métier

- `users` : `name`, `email`, `password`, `role`, `matricule`, `actif`.
- `filieres` : `nom` (unique), `code`, `description`.
- `annees_academiques` : `libelle`, `date_debut`, `date_fin`, `active`.
- `memoires` : métadonnées, fichier PDF, hash unique, statut, confidentialité, embargo, filière, année, auteur.
- `validations` : `memoire_id`, `validateur_id` nullable (`nullOnDelete`), statut, motif, conformité, `date_decision` nullable.

### Statuts d'un mémoire

`soumis` → `en_verification` → `valide`

Variantes : `a_corriger` (nouvelle version attendue), `rejete` (non retenu).

### Tables préparées pour les lots suivants

- `versions_memoires` : historique des PDF déposés (`memoire_id` + `numero_version` uniques).
- `favoris` : mémoires mis de côté par un utilisateur.
- `historiques_lecture` : traçage des consultations.
- `notifications` : notifications Laravel.

## Sécurité

- Mots de passe hachés (Breeze).
- Middleware `role` sur les routes sensibles.
- Middleware `EnsureAccountIsActive` sur le groupe `web`.
- Validation stricte du matricule et de son unicité.
- `role` exclu de `$fillable`, attribution explicite via `forceFill` ou états de factory.
- Clés étrangères protectrices :
  - `validations.memoire_id` → `cascadeOnDelete` ;
  - `validations.validateur_id` → `nullOnDelete` ;
  - `memoires.etudiant_id` → `nullOnDelete` ;
  - `memoires.filiere_id` / `annee_id` → `restrictOnDelete`.
- Aucun accès à une base institutionnelle : le matricule est déclaré puis contrôlé par la bibliothèque.

## Identité visuelle

- Palette Tailwind `eneam` construite autour du bleu institutionnel (`#1e40af` → `#3b82f6`).
- Police **Montserrat**.
- Logo officiel dans `public/images/logo-eneam.png`.
- Layout invité en deux panneaux : présentation institutionnelle + formulaire.
- Layout applicatif : barre latérale blanche, en-tête translucide, cartes `shadow-soft`, footer institutionnel.
- Textes réécrits pour éviter les formulations génériques.

## Vérifications

```powershell
php artisan migrate:fresh --seed
npm run build
php artisan test
```

Résultat attendu : migrations et seeders sans erreur, build Vite réussi, **47 tests verts**.

## Limites connues

- Pas encore d'écran de dépôt de mémoire ni de catalogue public.
- Pas de lecteur PDF intégré.
- Les tables `versions_memoires`, `favoris`, `historiques_lecture` et `notifications` sont prêtes mais pas encore exploitées par l'interface.
- Pas d'analyse anti-plagiat (volontairement hors périmètre).
- Le matricule reste auto-déclaré : la vérification institutionnelle est un contrôle humain de la bibliothèque.
