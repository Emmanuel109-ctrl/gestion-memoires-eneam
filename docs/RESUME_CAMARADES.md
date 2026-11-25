# Bibliothèque des mémoires ENEAM — résumé pour l'équipe

## En une phrase

Une application Laravel qui permet aux étudiants de l'ENEAM de déposer leurs mémoires, à la bibliothèque de vérifier les dossiers, et à l'administration de piloter le fonds documentaire.

## Ce qui fonctionne aujourd'hui

- Connexion par **matricule à 8 chiffres** (étudiants) ou par **e-mail** (personnel).
- Inscription d'un compte étudiant avec un matricule unique.
- Trois espaces avec chacun son tableau de bord :
  - **Administration** : volumétrie, répartition par statut, top filières, activité récente, derniers dépôts.
  - **Bibliothèque** : file d'attente, compteurs par statut, dépôts récents.
  - **Étudiant** : matricule, compteurs personnels, étapes du dépôt.
- Gestion des **années académiques** (création, activation).
- **Vérification documentaire** : ouverture d'un dossier, cases de conformité, décision (`valider`, `demander une correction`, `rejeter`) avec motif obligatoire.
- Comptes désactivables : un compte inactif ne peut plus se connecter et ses sessions sont fermées.

## Comptes de démonstration

Mot de passe commun : `password`

| Rôle | Identifiant |
|---|---|
| Administrateur | `admin@eneam.bj` |
| Bibliothécaire | `biblio@eneam.bj` |
| Étudiant | `20240001` |
| Étudiant | `20240002` |

## Lancer le projet

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

Puis ouvrir <http://localhost:8145>.

La base est configurée pour MySQL sur le port **3307** (XAMPP). Si votre installation utilise un autre port, adaptez `DB_PORT` dans `.env`.

## Règles à respecter

1. **Matricule = exactement 8 chiffres.** Pas de lettres, pas de tirets, pas de slash. La règle est appliquée à l'inscription et vérifiée par les tests.
2. **Ne jamais rendre `role` mass-assignable.** On l'attribue explicitement (`forceFill`, factory state ou propriété).
3. **Un motif est obligatoire** pour une décision `a_corriger` ou `rejete`.
4. **Ne pas réintroduire le champ encadreur** : il est hors périmètre.
5. **Pas d'accès à la base institutionnelle de l'école.** Le matricule est déclaré par l'étudiant puis contrôlé par la bibliothèque.

## Vérifier que tout est bon

```powershell
php artisan test
```

Attendu : **47 tests verts**.

```powershell
npm run build
```

Attendu : build Vite sans erreur.

## Ce qui reste à faire

- Écran de **dépôt de mémoire** (upload PDF privé, calcul du hash anti-doublon).
- **Catalogue** et fiche mémoire avec lecteur PDF.
- Recherche plein texte (l'index FULLTEXT est déjà prévu en base).
- Gestion des **utilisateurs** et des **filières** depuis l'administration.
- Exploitation des tables déjà créées : versions, favoris, historique de lecture, notifications.
- Export / statistiques.

## Où lire la suite

- `docs/RESUME_LOT1.md` : détail technique du lot 1.
- `docs/MAQUETTE_IRREGULARITES.md` : écarts entre la maquette et l'application.
- `README.md` : installation et comptes de démonstration.
