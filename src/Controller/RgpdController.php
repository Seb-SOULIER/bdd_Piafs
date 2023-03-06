<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RgpdController extends AbstractController
{
    #[Route('/app/rgpd/privacy', name: 'app_rgpd_privacy')]
    public function privacy(): Response
    {
        $privacy =
"Politique de confidentialite
Le site web https://www.lespiafsactifs.fr est détenu par Les Piafs Actifs, qui est un contrôleur de données de vos données personnelles.

Nous avons adopté cette politique de confidentialité, qui détermine la manière dont nous traitons les informations collectées par https://www.lespiafsactifs.fr, qui fournit également les raisons pour lesquelles nous devons collecter certaines données personnelles vous concernant. Par conséquent, vous devez lire cette politique de confidentialité avant d'utiliser le site web de https://www.lespiafsactifs.fr.

Nous prenons soin de vos données personnelles et nous nous engageons à en garantir la confidentialité et la sécurité.

Les informations personnelles que nous collectons :
Lorsque vous visitez le https://www.lespiafsactifs.fr, nous recueillons automatiquement certaines informations sur votre appareil, notamment des informations sur votre navigateur web, votre adresse IP, votre fuseau horaire et certains des cookies installés sur votre appareil. En outre, lorsque vous naviguez sur le site, nous recueillons des informations sur les pages web ou les produits individuels que vous consultez, sur les sites web ou les termes de recherche qui vous ont renvoyé au site et sur la manière dont vous interagissez avec le site. Nous désignons ces informations collectées automatiquement par le terme "informations sur les appareils". En outre, nous pourrions collecter les données personnelles que vous nous fournissez (y compris, mais sans s'y limiter, le nom, le prénom, l'adresse, les informations de paiement, etc.) lors de l'inscription afin de pouvoir exécuter le contrat.

Pourquoi traitons-nous vos données ?
Notre priorité absolue est la sécurité des données des clients et, à ce titre, nous ne pouvons traiter que des données minimales sur les utilisateurs, uniquement dans la mesure où cela est absolument nécessaire pour maintenir le site web. Les informations collectées automatiquement sont utilisées uniquement pour identifier les cas potentiels d'abus et établir des informations statistiques concernant l'utilisation du site web. Ces informations statistiques ne sont pas autrement agrégées de manière à identifier un utilisateur particulier du système.

Vous pouvez visiter le site web sans nous dire qui vous êtes ni révéler d'informations, par lesquelles quelqu'un pourrait vous identifier comme un individu spécifique et identifiable. Toutefois, si vous souhaitez utiliser certaines fonctionnalités du site web, ou si vous souhaitez recevoir notre lettre d'information ou fournir d'autres détails en remplissant un formulaire, vous pouvez nous fournir des données personnelles, telles que votre e-mail, votre prénom, votre nom, votre ville de résidence, votre organisation, votre numéro de téléphone. Vous pouvez choisir de ne pas nous fournir vos données personnelles, mais il se peut alors que vous ne puissiez pas profiter de certaines fonctionnalités du site web. Par exemple, vous ne pourrez pas recevoir notre bulletin d'information ou nous contacter directement à partir du site web. Les utilisateurs qui ne savent pas quelles informations sont obligatoires sont invités à nous contacter via contact@lespiafsactifs.fr.

Vos droits :
Si vous êtes un résident européen, vous disposez des droits suivants liés à vos données personnelles :

Le droit d'être informé.
Le droit d'accès.
Le droit de rectification.
Le droit à l'effacement.
Le droit de restreindre le traitement.
Le droit à la portabilité des données.
Le droit d'opposition.
Les droits relatifs à la prise de décision automatisée et au profilage.
Si vous souhaitez exercer ce droit, veuillez nous contacter via les coordonnées ci-dessous.

En outre, si vous êtes un résident européen, nous notons que nous traitons vos informations afin d'exécuter les contrats que nous pourrions avoir avec vous (par exemple, si vous passez une commande par le biais du site), ou autrement pour poursuivre nos intérêts commerciaux légitimes énumérés ci-dessus. En outre, veuillez noter que vos informations pourraient être transférées en dehors de l'Europe, y compris au Canada et aux États-Unis.

Liens vers d'autres sites web :
Notre site web peut contenir des liens vers d'autres sites web qui ne sont pas détenus ou contrôlés par nous. Sachez que nous ne sommes pas responsables de ces autres sites web ou des pratiques de confidentialité des tiers. Nous vous encourageons à être attentif lorsque vous quittez notre site web et à lire les déclarations de confidentialité de chaque site web susceptible de collecter des informations personnelles.

Sécurité de l'information :
Nous sécurisons les informations que vous fournissez sur des serveurs informatiques dans un environnement contrôlé et sécurisé, protégé contre tout accès, utilisation ou divulgation non autorisés. Nous conservons des garanties administratives, techniques et physiques raisonnables pour nous protéger contre tout accès, utilisation, modification et divulgation non autorisés des données personnelles sous son contrôle et sa garde. Toutefois, aucune transmission de données sur Internet ou sur un réseau sans fil ne peut être garantie.

Divulgation légale :
Nous divulguerons toute information que nous collectons, utilisons ou recevons si la loi l'exige ou l'autorise, par exemple pour nous conformer à une citation à comparaître ou à une procédure judiciaire similaire, et lorsque nous pensons de bonne foi que la divulgation est nécessaire pour protéger nos droits, votre sécurité ou celle d'autrui, enquêter sur une fraude ou répondre à une demande du gouvernement.

Informations de contact :
Si vous souhaitez nous contacter pour comprendre davantage la présente politique ou si vous souhaitez nous contacter concernant toute question relative aux droits individuels et à vos informations personnelles, vous pouvez envoyer un courriel à contact@lespiafsactifs.fr.

";

        return $this->json(
            $privacy,
        );
    }

    #[Route('/app/rgpd/terms', name: 'app_rgpd_terms')]
    public function terms(): Response
    {
        $terms =

" MENTIONS LEGALES
1 - Édition du site
En vertu de l'article 6 de la loi n° 2004-575 du 21 juin 2004 pour la confiance dans l'économie numérique, il est précisé aux utilisateurs du site internet https://www.lespiafsactifs.fr/mobile-app l'identité des différents intervenants dans le cadre de sa réalisation et de son suivi:

Propriétaire du site : Les Piafs Actifs - Contact : contact@lespiafsactifs.fr 07.56.91.12.17 - Adresse : 1 cours Lemercier 17100 Saintes.

Identification de l'entreprise : Association Les Piafs Actifs au capital social de € - SIREN : 911969202 - RCS ou RM : - Adresse postale : 1 cours Lemercier 17100 Saintes - [Consignes : ajoutez ici le lien hypertexte vers la page de vos conditions générales de vente si vous en avez une]

Directeur de la publication : Les Piafs Actifs - Contact : contact@lespiafsactifs.fr.

Hébergeur : Autre

Délégué à la protection des données : Les Piafs Actifs - contact@lespiafsactifs.fr

Autres contributeurs : Réalisée par Cod4Y - Sébastien SOULIER, développeur web

2 - Propriété intellectuelle et contrefaçons.
Les Piafs Actifs est propriétaire des droits de propriété intellectuelle et détient les droits d’usage sur tous les éléments accessibles sur le site internet, notamment les textes, images, graphismes, logos, vidéos, architecture, icônes et sons.

Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable de Les Piafs Actifs.

Toute exploitation non autorisée du site ou de l’un quelconque des éléments qu’il contient sera considérée comme constitutive d’une contrefaçon et poursuivie conformément aux dispositions des articles L.335-2 et suivants du Code de Propriété Intellectuelle.

3 - Limitations de responsabilité.
Les Piafs Actifs ne pourra être tenu pour responsable des dommages directs et indirects causés au matériel de l’utilisateur, lors de l’accès au site https://www.lespiafsactifs.fr/mobile-app.

Les Piafs Actifs décline toute responsabilité quant à l’utilisation qui pourrait être faite des informations et contenus présents sur https://www.lespiafsactifs.fr/mobile-app.

Les Piafs Actifs s’engage à sécuriser au mieux le site https://www.lespiafsactifs.fr/mobile-app, cependant sa responsabilité ne pourra être mise en cause si des données indésirables sont importées et installées sur son site à son insu.

Des espaces interactifs (espace contact ou commentaires) sont à la disposition des utilisateurs. Les Piafs Actifs se réserve le droit de supprimer, sans mise en demeure préalable, tout contenu déposé dans cet espace qui contreviendrait à la législation applicable en France, en particulier aux dispositions relatives à la protection des données.

Le cas échéant, Les Piafs Actifs se réserve également la possibilité de mettre en cause la responsabilité civile et/ou pénale de l’utilisateur, notamment en cas de message à caractère raciste, injurieux, diffamant, ou pornographique, quel que soit le support utilisé (texte, photographie …).

4 - CNIL et gestion des données personnelles.
Conformément aux dispositions de la loi 78-17 du 6 janvier 1978 modifiée, l’utilisateur du site https://www.lespiafsactifs.fr/mobile-app dispose d’un droit d’accès, de modification et de suppression des informations collectées. Pour exercer ce droit, envoyez un message à notre Délégué à la Protection des Données : Les Piafs Actifs - contact@lespiafsactifs.fr.

Pour plus d'informations sur la façon dont nous traitons vos données (type de données, finalité, destinataire...), lisez notre https://www.lespiafsactifs.fr/politique-confidentialite. [Consignes : ajoutez ici le lien hypertexte vers votre politique de confidentialité]

5 - Liens hypertextes et cookies
Le site https://www.lespiafsactifs.fr/mobile-app contient des liens hypertextes vers d’autres sites et dégage toute responsabilité à propos de ces liens externes ou des liens créés par d’autres sites vers https://www.lespiafsactifs.fr/mobile-app.

La navigation sur le site https://www.lespiafsactifs.fr/mobile-app est susceptible de provoquer l’installation de cookie(s) sur l’ordinateur de l’utilisateur.

Un \"cookie\" est un fichier de petite taille qui enregistre des informations relatives à la navigation d’un utilisateur sur un site. Les données ainsi obtenues permettent d'obtenir des mesures de fréquentation, par exemple.

Vous avez la possibilité d’accepter ou de refuser les cookies en modifiant les paramètres de votre navigateur. Aucun cookie ne sera déposé sans votre consentement.

Les cookies sont enregistrés pour une durée maximale de 12 mois.

Pour plus d'informations sur la façon dont nous faisons usage des cookies, lisez notre https://www.lespiafsactifs.fr/politique-confidentialite. [Consignes : ajoutez ici le lien hypertexte vers votre politique de confidentialité ou vers votre politique de cookies si vous en avez une (c'est le cas si vous utilisez Complianz)]

6 - Droit applicable et attribution de juridiction.
Tout litige en relation avec l’utilisation du site https://www.lespiafsactifs.fr/mobile-app est soumis au droit français. En dehors des cas où la loi ne le permet pas, il est fait attribution exclusive de juridiction aux tribunaux compétents de Saintes.


";

        return $this->json(
            $terms,
        );
    }
}
