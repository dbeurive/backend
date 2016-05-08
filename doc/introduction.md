# Introduction

Le choix de l’implémentation de la couche d’accès aux données revêt une importance capitale. Deux techniques sont couramment mises en oeuvre :

* Le mapping objet-relationnel (ORM). Cette technique établit une correspondance entre le « paradigme objet » (utilisé en conception logicielle), et le « paradigme relationnel » (utilisé pour la conception de la base de données relationnelle). 
* L’utilisation de procédures stockées. Cette technique repose sur l’utilisation du langage SQL et d’un langage de programmation embarqué sur le serveur de la base.

Ces deux techniques présentent des avantages et des inconvénients.

# L’utilisation d’un ORM

L’utilisation de l’approche orientée objet (ORM) se heurte rapidement à la problématique du « mélange des données ».

Tôt ou tard, il devient indispensable de « joindre » les données. Dans le paradigme relationnel, on parle de « jointures ». Pour répondre à ce besoin sans sortir du paradigme objet, deux stratégies sont couramment utilisées :

* **Première technique**: CRUD (create, read, update, delete). Cette technique consiste à considérer la base de données comme un simple « réservoir d’entités ». Une relation directe est établie entre des entités logicielles (représentées par des « objets ») et des groupes de tables. S’il est nécessaire de « mélanger » des entités, cette action n’est pas effectuée par le SGBDR (via une jointure exprimée en SQL), mais par le code de l’application : on parle « de jointures en PHP ».
* **Deuxième technique**: La jointure est exprimée dans un « formalisme objet » qui est ensuite converti en SQL (par l'ORM) et transmise à la base pour exécution. Un exemple de ce formalisme est le DQL (de Doctrine). 

La première technique (CRUD) présente de sérieux inconvénients :

* Cette technique est extrêmement inefficiente. D’une part, contrairement au SGBDR, l’application ne dispose pas des index. D’autre part, les algorithmes implémentés dans le SGBDR sont beaucoup plus performants (et compliqués) que les algorithmes triviaux (à base de boucles imbriquées) implémentés dans l’application. 
* Cette technique rend extrêmement difficile la compréhension des opérations effectuées sur les données (par comparaison aux mêmes opérations exprimées en SQL). Le SQL permet d’exprimer de façon synthétique des requêtes. En revanche, « l’équivalent programme », à base de boucles imbriquées, peut très rapidement devenir très difficile à lire.
* Le volume de donnée qui transite via le réseau est considérablement plus important, car les tris ne sont pas effectués en amont du transfert, par le SGBDR.

> Note : bien que cela puisse sembler hallucinant, il n’est pas si rare de croiser des entreprises qui ont choisi d’implémenter cette technique. 

La deuxième technique permet de contourner les défauts (fatals) de la première. Un formalisme objet permet d’exprimer une opération de jointure sur les « entités logicielles » (les objets). L’ORM se chargera ensuite de convertir cette expression en SQL qui sera adressé au SGBDR.

Toutefois, lorsque l’on en arrive à utiliser ce genre de formalisme, on commence à se dire que l’on pourrait tout aussi bien exprimer la jointure directement en SQL:

* Il est courant de voir des développeurs placer _systématiquement_ les requêtes SQL en commentaires, devant les « expressions objet ». En effet, une requête SQL est plus facile à lire qu’une longue suite d’appels de méthodes. Pourquoi, dans ces conditions, compliquer le code ?
* L’utilisation du formalisme objet ne dispense pas l’utilisateur d’une excellente connaissance du SQL et du fonctionnement du SGBDR sous-jacent. La couche logicielle de l’ORM ne va pas réécrire l’expression à votre place pour l’améliorer…

L’un des avantages avancés pour justifier l’utilisation des ORM est « l’indépendance du code vis-à-vis du SGBDR ». Typiquement, on vous promet que vous serez en mesure de migrer de MySql vers Oracle sans changer une ligne de code, et sans avoir besoin de réécrire votre modèle de données (car certains ORM sont capables de générer le modèle de données associé à une description « objet » en fonction de la base utilisée).

Certes, cette possibilité est bien réelle. Mais _avez-vous déjà été obligé de changer de SGBDR du jour au lendemain, toutes choses égales par ailleurs ?_

On ne décide pas de changer de SGBDR « _ex nihilo_ ». Ce besoin est associé à des contraintes fonctionnelles ou environnementales (le plus souvent : fonctionnelles **ET** environnementales).

Le besoin de changer de base est en général associé à des contraintes qui vous obligent à changer votre modèle de donnée, votre code ou votre architecture. Dans ces conditions, l’argument de l’indépendance du code vis-à-vis de la base, même s’il est vrai, ne s’applique pas.

> Par exemple :
>
> Votre entreprise passe un gros contrat avec un très gros client extrêmement exigeant.
>
> * Vous devez anticiper une forte montée en charge sur l’ensemble de votre plateforme (et donc sur votre SGBDR également).
> * Vous devez revoir votre politique de sécurisation des données, car votre client ne tolérera pas la moindre corruption de donnée.
> * Vous devez anticiper une forte accélération des demandes d’évolution, pour satisfaire ce client stratégique.
>
> Vous aurez peut-être besoin de migrer de MySql vers Oracle.
>
> Mais pensez-vous qu’il vous suffira de « traduire » votre schéma d’un SGBDR à un autre pour atteindre l’objectif ? 
>
> Probablement pas. L’adaptation de votre plateforme informatique ne se limitera pas à un simple changement de SGBRD.
> 
> * Il sera probablement nécessaire de revoir une partie de l’architecture générale de votre plateforme. Cette adaptation entrainera peut-être la modification du schéma de la base.
> * Il sera probablement nécessaire de revoir la façon dont vous manipulez les données. Cela entrainera la modification de certaines requêtes.
> * Il sera peut-être nécessaire de modifier ou d’étendre votre modèle de données en vue des évolutions fonctionnelles envisagées.
>
> Bref, vous aurez probablement à vous pencher sérieusement sur le modèle de données et l’utilisation de votre SGBDR.
> 
> * La « traduction automatique » du schéma et des requêtes présente un certain intérêt. Mais, à lui seul, ce mécanisme n’est pas suffisant.  Mais, surtout, les enjeux stratégiques sont ailleurs.
> * Votre ORM ne fera pas mieux qu’un DBA expérimenté. Pour des projets sérieux, en particulier, il serait inconcevable de se passer d’un BDA pour assurer la migration. Or un DBA est parfaitement en mesure de traduire un schéma, ainsi que toutes les requêtes associées. En revanche, un DBA n’est pas censé modifier du code applicatif. Il ne touchera pas aux « expressions objets ». _L’utilisation d’un ORM vous privera donc de l’intervention d’un DBA_.
 
Il faut noter la démarche pragmatique de certains micro-ORM. Contrairement aux ORM « poids lourds », tels que Doctrine (avec le DQL), certains micro-ORM ne prétendent pas remplacer le SQL.

Ainsi, [Idiorm](https://github.com/j4mie/idiorm/):

> * Makes simple queries and simple CRUD operations completely painless.
> * Gets out of the way when more complex SQL is required.

Ou [Redbean](http://www.redbeanphp.com/index.php?p=/querying):

> One of the biggest mistakes people make with ORM tools is to try to accomplish everything with objects (or beans). They forget SQL is a very powerful tool as well. Use SQL if you are merely interested in generating reports or lists.

# L'utilisation des procédures stockées

L’utilisation des procédures présente de nombreux avantages :

* Les opérations sur les données étant effectuées par le SGBDR, ces dernières sont particulièrement optimisées. Le SGBDR a accès aux index et ses algorithmes sont hyper optimisés. Il est impossible de faire mieux avec du code applicatif.
* Le volume de données qui transite entre le SGBDR et l’application est réduit au strict nécessaire.
* L’écriture et la maintenance de toute la couche d’accès aux données peuvent être assurées par des DBA.
* Les développeurs accèdent à la base via une interface procédurale. Le code est indépendant du SGBDR.

Mais, elle présente également des inconvénients :

* Sur certains SGBDR, l’écriture de procédures stockées utilise un formalisme « préhistorique ». Ce formalisme d’un autre âge rend l’implémentation de procédures stockées pénible. C’est le cas, en particulier pour MySql.
* Sur certains SGBDR, le debug des procédures stockées est un calvaire. C’est le cas, en particulier pour MySql.
* L’utilisation des procédures stockées tend à « charger » le SGBDR. Déporter une partie des traitements sur l’applicatif peut s’avérer être une démarche pertinente. En effet, le SGBDR constitue souvent un « goulot d’étranglement », alors que l’applicatif est conçu de façon à supporter la montée en charge. Toutefois, il convient de bien estimer ce qui **doit** être réalisé par le SGBDR de ce qui **peut** être délégué à l’applicatif.
* Quand, un beau jour, une procédure stockée « plante », il peut être très difficile de trouver la raison de ce plantage. Contrairement au code applicatif - relativement accessible -, l’environnement du SGBDR est assez opaque.
