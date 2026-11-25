# Irrégularités relevées dans la maquette

Maquette analysée : `C:\Users\Emmanuel\Downloads\maquette\maquette` (18 pages HTML + `assets/`).

Ce document liste les écarts entre la maquette, le site officiel de l'ENEAM et les règles retenues pour l'application. Il sert de base de décision : chaque point indique une recommandation.

## 1. Identité visuelle

| # | Irrégularité | Recommandation |
|---|---|---|
| 1.1 | La maquette utilise **Inter** et un bleu `#2563EB`, alors que l'application doit s'intégrer au site ENEAM (Montserrat, bleu institutionnel). | Appliquer Montserrat et la palette `eneam` (`#1e40af` → `#3b82f6`) — fait dans l'application. |
| 1.2 | Le CSS de la maquette force `--radius: 4px` et supprime les ombres (`--shadow: none`), ce qui donne un rendu très plat et éloigné des cartes institutionnelles. | Conserver des rayons modérés et une ombre douce (`shadow-soft`). |
| 1.3 | Le logo utilisé dans la maquette est un fichier local `logo_fond_transparent.png` (108×108), différent du logo officiel du site. | Utiliser le logo officiel `public/images/logo-eneam.png`. |

## 2. Rôles et parcours

| # | Irrégularité | Recommandation |
|---|---|---|
| 2.1 | La maquette prévoit **4 rôles** (`étudiant`, `secrétaire`, `validateur`, `administrateur`) alors que l'application en gère 3. | Ne pas élargir le modèle maintenant : `secrétaire` est couvert par l'espace bibliothèque/admin, `validateur` par `bibliothecaire`. |
| 2.2 | Un écran « consultation externe » (`memoire-externe.html`) annonce un accès **Public**, mais la page affiche un shell connecté « Visiteur externe · Accès limité » et restreint la navigation. | Trancher explicitement : soit un vrai catalogue public, soit un accès authentifié. Ne pas laisser une promesse « public » sans parcours public. |
| 2.3 | Le champ **encadreur** apparaît dans certains écrans de dépôt. | Il a été retiré du périmètre. Ne pas le réintroduire sans décision explicite. |

## 3. Matricule et comptes

| # | Irrégularité | Recommandation |
|---|---|---|
| 3.1 | Les placeholders de matricule ne précisent pas le format attendu (8 chiffres). | Indiquer « 12345678 » et la règle « exactement 8 chiffres » — fait dans les écrans d'inscription. |
| 3.2 | Les exemples de la maquette montrent des noms et des compteurs différents d'un écran à l'autre. | Utiliser un jeu de démonstration unique et cohérent (6 mémoires, 2 étudiants). |
| 3.3 | Aucun écran ne montre le cas d'un compte désactivé. | Prévoir le blocage à la connexion et la fermeture de session (`users.actif`). |

## 4. Chiffres et libellés incohérents

| # | Irrégularité | Recommandation |
|---|---|---|
| 4.1 | Le tableau de bord étudiant affiche des volumes qui ne correspondent pas au nombre de mémoires réellement listés. | Calculer tous les compteurs depuis la base, sans valeur codée en dur. |
| 4.2 | Le compteur de notifications ne correspond pas au nombre d'éléments affichés. | Alimenter les compteurs depuis les données réelles. |
| 4.3 | Certains libellés mélangent « validé », « publié » et « consultable ». | Utiliser un vocabulaire unique : statut du mémoire ≠ visibilité au catalogue. |

## 5. Liens et fichiers manquants

| # | Irrégularité | Recommandation |
|---|---|---|
| 5.1 | `memoire-detail.html` pointe vers `assets/docs/memoire.pdf`, fichier absent de la maquette. | Ne pas livrer de lien mort : prévoir un PDF de démonstration ou désactiver le bouton. |
| 5.2 | `notifications.html` redirige immédiatement les utilisateurs desktop vers `dashboard.html`. | Supprimer la redirection et afficher réellement la page, ou assumer que les notifications sont un panneau du tableau de bord. |
| 5.3 | Le menu `.bottom-nav` est défini dans le CSS mais n'est affiché par aucune page. | Soit l'implémenter sur mobile, soit retirer le code mort. |

## 6. Formulaire de validation

| # | Irrégularité | Recommandation |
|---|---|---|
| 6.1 | L'écran validateur ne présente aucune case de **conformité documentaire**. | Ajouter les contrôles `conforme_version_papier` et `format_conforme` — fait dans l'application. |
| 6.2 | Le motif de rejet n'est pas clairement obligatoire. | Rendre le motif obligatoire pour `a_corriger` et `rejete`. |
| 6.3 | Aucune trace de la date de décision. | Stocker `date_decision` (nullable tant que la décision n'est pas prise). |

## 7. Accessibilité et finitions

| # | Irrégularité | Recommandation |
|---|---|---|
| 7.1 | Plusieurs boutons sont des `div` ou des liens sans libellé accessible. | Utiliser des boutons/liens natifs avec texte ou `aria-label`. |
| 7.2 | Les contrastes de textes secondaires sont faibles sur les fonds gris clair. | Renforcer le contraste (au minimum `text-slate-500` sur fond blanc). |
| 7.3 | Les états de focus sont peu visibles. | Ajouter un anneau de focus bleu (`ring-eneam-500`). |

## Synthèse

La maquette est une bonne base de composition, mais elle n'est pas directement transposable :

- elle ne respecte pas la charte du site ENEAM ;
- elle promet des parcours (public, notifications, lecteur PDF) que le code ne couvre pas encore ;
- elle contient des liens morts et des chiffres incohérents.

L'application retient la **structure** et les **écrans clés**, mais corrige la charte, les rôles, les statuts et les données de démonstration.
