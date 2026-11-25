# Accès, identités et données — décisions de cadrage

Ce document fixe le modèle d'accès retenu pour la bibliothèque numérique des mémoires ENEAM. Il sert de référence avant l'implémentation du lot 2.

## 1. Décisions actées

- Pas d'API/SSO fournie par l'école.
- Les étudiants ENEAM sont vérifiés à partir d'un **annuaire local** (`etudiants_eneam`), alimenté par un fichier fourni par la scolarité ou le professeur.
- Pas de paiement en ligne pour l'instant.
- Les visiteurs non-ENEAM consultent le catalogue et la page de garde, sans connexion.
- Le texte intégral est réservé aux étudiants ENEAM authentifiés, selon la confidentialité et l'embargo.
- La connexion se fait par **matricule + mot de passe** dans un premier temps. Le passage au code par e-mail (OTP) reste prévu, sans refonte du modèle.
- **Le site commence par une page d'accueil publique**, pas par la connexion.
- **Pas d'inscription publique.** Les comptes du personnel sont créés par l'administration. Les étudiants activent leur compte à partir de l'annuaire.
- Les rapports de stage fournis sont utilisés comme **données de test**, présentés comme des mémoires de soutenance.
- Ton éditorial : pas de « Bonjour Administrateur » ni de « Bonjour [nom] ». On utilise des libellés institutionnels : « Tableau de bord — Administration », « Espace bibliothèque », « Espace étudiant ».

## 1 bis. Page d'accueil et parcours public

L'entrée du site est `/` : une page d'accueil publique aux couleurs de l'ENEAM.

Elle contient :

- l'en-tête institutionnel (logo, nom, navigation) ;
- un hero avec la recherche et deux actions : **Explorer le catalogue** et **Se connecter** ;
- quelques mémoires récents ou mis en avant ;
- les chiffres clés du fonds documentaire ;
- une explication claire des droits d'accès (étudiant ENEAM / visiteur) ;
- un footer institutionnel.

Parcours :

1. `/` — accueil public ;
2. `/catalogue` — catalogue public ;
3. `/catalogue/{memoire}` — fiche + page de garde ;
4. `/login` — connexion étudiants/personnel ;
5. `/premiere-connexion` — activation du compte étudiant (si retenue) ;
6. `/dashboard` — espace adapté au rôle après connexion.

## 2. Pourquoi séparer l'annuaire des comptes

Ne pas créer un compte `users` pour chaque étudiant de l'école. On garde :

- `etudiants_eneam` : l'annuaire officiel, potentiellement grand, synchronisé périodiquement.
- `users` : uniquement les personnes qui se sont réellement connectées (étudiants, bibliothécaires, administrateurs).

Avantages :

- pas de milliers de comptes fantômes ;
- mise à jour de l'annuaire sans toucher aux comptes ;
- radiation simple : `etudiants_eneam.actif = false` bloque la connexion ;
- données personnelles limitées et centralisées.

### Schéma proposé pour `etudiants_eneam`

| Champ | Type | Remarque |
|---|---|---|
| `id` | clé primaire | |
| `matricule` | string(8), unique | exactement 8 chiffres |
| `nom` | string | nom et prénoms |
| `email` | string, unique | e-mail associé au matricule |
| `filiere` | string, nullable | ou clé vers `filieres` |
| `promotion` | string, nullable | ex. `2024-2025` |
| `actif` | boolean | permet de bloquer un étudiant |
| `source` | string | ex. `import-2026` |
| `synced_at` | timestamp | date du dernier import |

## 3. Création des comptes et connexion

### Personnel

- Les comptes `admin` et `bibliothecaire` sont créés par l'administration (ou par les seeders pour la démo).
- Il n'existe pas de page d'inscription pour le personnel.
- Le personnel se connecte sur la **même page de connexion** que les étudiants, avec son adresse e-mail professionnelle.
- Après connexion, il est redirigé vers son tableau de bord selon son rôle.

### Étudiants

Deux options sont possibles. L'option A est recommandée.

**Option A — Activation du compte (recommandée)**

1. L'administration importe l'annuaire `etudiants_eneam`.
2. L'étudiant ouvre `/premiere-connexion`.
3. Il saisit son **matricule** et l'**e-mail présent dans l'annuaire**.
4. Si les deux correspondent, il définit son mot de passe.
5. Le compte `users` est créé avec `role = etudiant`.
6. Ensuite, il se connecte normalement avec matricule + mot de passe.

Ce n'est pas une inscription publique : seuls les étudiants présents dans l'annuaire peuvent activer un compte.

**Option B — Comptes créés par l'administration**

- L'administration importe l'annuaire et génère les comptes avec un mot de passe temporaire.
- L'étudiant se connecte et change son mot de passe.
- Plus simple à coder, mais moins sûr : les mots de passe temporaires circulent mal.

### Connexion

- Champ unique `identifiant` : matricule pour les étudiants, e-mail pour le personnel.
- Vérification de `users.actif` **et** de `etudiants_eneam.actif` pour les étudiants.
- Limitation du nombre de tentatives.
- Message d'erreur générique pour ne pas révéler l'existence d'un matricule.
- Redirection automatique vers le tableau de bord correspondant au rôle.

### Évolution OTP

Le modèle est prévu pour remplacer plus tard le mot de passe par un code à 6 chiffres envoyé à l'e-mail de l'annuaire. Les tables et les écrans restent compatibles.

## 4. Matrice d'accès aux mémoires

| Profil | Catalogue | Page de garde | Texte intégral | Téléchargement |
|---|---|---|---|---|
| Visiteur non connecté | Oui, notices `valide` + `public` | Oui (image) | Non | Non |
| Étudiant ENEAM | Oui | Oui | Oui si `public` ou `interne`, `valide`, hors embargo | Oui si `telechargement_autorise` |
| Auteur du mémoire | Oui, y compris ses brouillons | Oui | Oui | Oui |
| Bibliothécaire | Oui, tous les mémoires | Oui | Oui | Oui, accès journalisé |
| Administrateur | Oui, tous | Oui | Oui | Oui |
| Externe autorisé (plus tard) | Oui | Oui | Oui, document et durée limités | Non, sauf autorisation |

### Règles de confidentialité

- `public` : consultable par tout étudiant ENEAM connecté.
- `interne` : réservé aux étudiants ENEAM et au personnel.
- `restreint` : auteur + bibliothèque + administrateur, ou autorisation explicite.
- `date_embargo` future : texte intégral bloqué pour tous, sauf bibliothèque/admin ; la notice indique la date de disponibilité.
- Seuls les mémoires `valide` apparaissent dans le catalogue public. Les statuts `soumis`, `en_verification`, `a_corriger`, `rejete` restent visibles à l'auteur et au personnel.

## 5. Configuration technique des accès

### Stockage

- PDF dans `storage/app/private/memoires/{id}/` — jamais dans `public/`.
- Page de garde générée dans `storage/app/private/couvertures/{id}.jpg` (ou équivalent).
- Aucun accès direct par URL au fichier complet.
- Téléchargement via une route controller + `Storage::download()` ou `response()->file()`.

### Routes

| Route | Accès |
|---|---|
| `GET /catalogue` | public |
| `GET /catalogue/{memoire}` | public, notice + page de garde |
| `GET /catalogue/{memoire}/page-de-garde` | public, image uniquement |
| `GET /catalogue/{memoire}/lire` | authentifié + politique d'accès |
| `GET /catalogue/{memoire}/telecharger` | authentifié + politique + `telechargement_autorise` |
| `POST /catalogue/{memoire}/demande-acces` | public (externe) |

### Politiques

`MemoirePolicy` avec au minimum :

- `viewAny` : catalogue ;
- `view` : notice ;
- `read` : texte intégral ;
- `download` : téléchargement ;
- `update` : dépôt/modification par l'auteur ;
- `validate` : bibliothécaire/admin ;
- `delete` : admin.

### Journalisation

- `historiques_lecture` : qui a lu quoi, quand (utilisateurs connectés).
- Journal séparé ou colonnes dédiées pour les téléchargements.
- Les accès du personnel sont traçables.

### Sécurité

- Ne jamais envoyer le PDF complet à un visiteur non autorisé : il pourrait le conserver.
- URL signées et expiration courte pour les téléchargements.
- Filigrane avec nom/matricule pour les étudiants (option recommandée).
- Validation du type MIME, de la taille, du hash SHA-256.
- Limitation de débit sur la connexion, l'inscription et les téléchargements.

## 6. Données personnelles : précautions

> Ceci n'est pas un avis juridique. Au Bénin, le traitement des données personnelles relève du Code du numérique et de l'APDP. Faites valider l'usage par le professeur ou l'école.

- Les noms, e-mails et matricules sont des **données personnelles**.
- Demandez une **autorisation écrite** au professeur ou à l'école pour constituer l'annuaire et l'utiliser dans le projet.
- Limitez les champs au strict nécessaire : matricule, nom, e-mail, filière, promotion.
- Ne versionnez **jamais** l'annuaire réel ni les PDF dans Git.
- Pour les démonstrations publiques, utilisez des données **anonymisées** ou fictives.
- Informez les étudiants de l'usage prévu et de la durée de conservation.
- Supprimez ou anonymisez les données à la fin du projet.
- Ne publiez pas les mémoires sans l'accord de leurs auteurs.

## 7. Où déposer les données

### Annuaire

Créer un fichier CSV UTF-8 ici :

```
database/data/etudiants_eneam.csv
```

Modèle : `database/data/etudiants_eneam.example.csv`.

Le fichier `database/data/etudiants_eneam.csv` existe déjà avec la ligne d'en-tête : il suffit d'ajouter les lignes.

Format attendu (UTF-8, séparateur `;` ou `,`) :

```csv
matricule;nom;email;filiere;promotion;actif
20240001;Awa HOUNKPE;awa.hounkpe@eneam.bj;Informatique de Gestion;2024-2025;1
```

Règles :

- `matricule` : exactement 8 chiffres, au format texte (pour conserver les zéros initiaux) ;
- `nom` : nom et prénoms sur une seule colonne ;
- `email` : e-mail associé au matricule ;
- `filiere` et `promotion` : facultatifs ;
- `actif` : `1` ou `0`.

Ce fichier est ignoré par Git. Il peut aussi être placé hors du dépôt, par exemple :

```
C:\Users\Emmanuel\Documents\eneam-data\etudiants_eneam.csv
```

### Mémoires

Les PDF peuvent rester dans `C:\Users\Emmanuel\Downloads\Mémoires de stage` et être copiés ensuite dans :

```
storage/app/private/memoires/
```

Avec un manifeste :

```
database/data/memoires.csv
```

Modèle : `database/data/memoires.example.csv`.

## 8. Priorités

1. **Lot 2A — Accès et catalogue** : annuaire, inscription vérifiée, catalogue, fiche mémoire, page de garde, politiques d'accès.
2. **Lot 2B — Dépôt et validation** : upload, hash, versions, workflow, notifications.
3. **Lot 3 — Lecture et espace personnel** : lecteur, favoris, historique, statistiques.
4. **Lot 4 — Accès externe** : demande d'autorisation, puis éventuellement paiement.
