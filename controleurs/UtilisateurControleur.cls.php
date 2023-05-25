<?php
// [MODIF HORS COURS]
// Nous utilisons le module Validator de Symfony
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UtilisateurControleur extends Controleur
{
    // [MODIF HORS COURS]
    // Les messages d'erreurs de ce contrôleur : les codes préfixés par _ sont de mon 
    // invention, les autres sont ceux retournés par MySQL.
    protected $messagesUI = [
        '1062'   =>  "Un utilisateur avec ce courriel existe déjà.",
        '_1000'  =>  "Il faut être authentifié pour accéder à cette page.",
        '_1010'  =>  "Vous avez été déconnecté.",
        '_1999'  =>  "Le nom ne peut être vide.",
        '_2000'  =>  "Courriel non-valide.",
        '_2010'  =>  "Mot de passe pas assez long (10 caractères sans espaces au moins).",
        '_2020'  =>  "Les deux saisies de mot de passe ne sont pas égales.",
        '_2030'  =>  "Votre compte a été créé avec succès ; vous receverez un message de confirmation par courriel.",
        '_3000'  =>  "Votre compte est confirmé."
    ];

    function __construct($modele, $module, $action, $params)
    {
        // Si l'utilisateur est connecté on le dirige directement dans la page 'catégories'
        if(isset($_SESSION['utilisateur'])) {
            Utilitaire::nouvelleRoute('/tache/tout');
        }
        
        parent::__construct($modele, $module, $action, $params);
    }

    /**
     * Méthode invoquée par défaut si aucune action n'est indiquée
     */
    public function index()
    {
        // Par défaut on affiche le formulaire de connexion  : aucune autre action 
        // n'est requise pour le moment

    }

    public function nouveau()
    {
        // On affiche le formulaire de création de compte : aucune autre action 
        // n'est requise pour le moment
        
    }

    /**
     * Vérifier la connexion d'un utilisateur
     */
    public function connexion()
    {
        $courriel = $_POST['uti_courriel'];
        $mdp = $_POST['uti_mdp'];

        $utilisateur = $this->modele->un($courriel);

        $erreur = false;
        if(!$utilisateur || !password_verify($mdp, $utilisateur->uti_mdp)) {
            $erreur = "Combinaison courriel/mot de passe erronée";
        }
        // else if($utilisateur->uti_confirmation != '') {
        //     $erreur = "Compte non confirmé : vérifiez vos courriels";
        // }

        if(!$erreur) {
            // Sauvegarder l'état de connexion
            $_SESSION['utilisateur'] = $utilisateur;
            // Rediriger vers categorie/tout
            Utilitaire::nouvelleRoute('/tache/tout');
        }
        else {
            $this->gabarit->affecter('erreur', $erreur);
            $this->gabarit->affecterActionParDefaut('index');
            $this->index([]);
        }
    }

    /**
     * Supprimer la connexion d'un utilisateur (en détruisant la variable de session associée)
     */
    public function deconnexion()
    {
        unset($_SESSION['utilisateur']);
        Utilitaire::nouvelleRoute('/utilisateur/index/msg=_1010');
    }

    /**
     * Ajouter un utilisateur
     */
    public function ajouter()
    {
        // Valider la saisie de l'utilisateur
        $nomErreurs = $this->validateur->validate($_POST['uti_nom'], [new NotBlank()]);
        $courrielErreurs = $this->validateur->validate($_POST['uti_courriel'], [new NotBlank(), new Email()]);
        $mdpErreurs = $this->validateur->validate($_POST['uti_mdp'], [new NotBlank(), new Regex(['pattern'=>'/^\S{8,}$/'])]);
        $mdp2Erreurs = $this->validateur->validate($_POST['uti_mdp2'], [new EqualTo($_POST['uti_mdp'])]);
        $erreurValidation = false;

        if(count($nomErreurs)>0) {
            $erreurValidation = '_1999';
        }
        else if(count($courrielErreurs)>0) {
            $erreurValidation = '_2000';
        }
        else if(count($mdpErreurs)>0) {
            $erreurValidation = '_2010';
        }
        else if(count($mdp2Erreurs)>0) {
            $erreurValidation = '_2020';
        }

        // S'il y a erreur de formulaire, on réaffiche le formulaire de création
        // de compte avec le message d'erreur adéquat
        if($erreurValidation) {
            // On injecte le nom et l'adresse courriel dans le gabarit pour pouvoir l'afficher dans le formulaire
            $this->gabarit->affecter('uti_nom', $_POST['uti_nom']);
            $this->gabarit->affecter('uti_courriel', $_POST['uti_courriel']);
            $this->gabarit->affecter('erreur', $this->messagesUI[$erreurValidation]);
            $this->gabarit->affecterActionParDefaut('nouveau');
            $this->nouveau();
        }
        
        // Sinon, on a passé la validation...
        else {
            // Ajouter le nouvel utilisateur (dont les valeurs sont reçues par POST) dans la BD
            $res = $this->modele->ajouter($_POST);
            // S'il y a erreur à insérer un nouvel utilisateur, on réaffiche le 
            // formulaire avec le bon message d'erreur qui vient de MySQL
            if(is_string($res) && str_starts_with($res, '/msg=')) {
                $this->gabarit->affecter('uti_nom', $_POST['uti_nom']);
                $this->gabarit->affecter('uti_courriel', $_POST['uti_courriel']);
                $this->gabarit->affecter('erreur', $this->messagesUI[explode('=',$res)[1]]);
                $this->gabarit->affecterActionParDefaut('nouveau');
                $this->nouveau();
            }
            // Sinon, tout va bien, on envoie le courriel de confirmation au nouvel utilisateur
            else {
                $this->envoyerMessageConfirmationCompte($res['courriel'],$res['cc']);
                // On peut aussi appeler la méthode index dans le même contrôleur
                // mais ce n'est pas une bonne pratique pour éviter la soumission répétée
                // du formulaire (si l'utilisateur raffraîchit la page)
                Utilitaire::nouvelleRoute('/utilisateur/index/msg=_2030');
            }
        }
    }

    /**
     * Code rudimentaire pour confirmer l'adresse courriel de l'utilisateur
     * (En particulier, on suppose que le CC est vraiment UNIQUE ! Sinon, il faut 
     * travailler un peu plus en comparant courriel et CC : mais il faut trouver
     * alors un moyen de transmettre l'adresse courriel dans le lien envoyé par
     * courriel sans compromettre cette adresse pour vos utilisateurs... à réfléchir ;-))
     */
    public function confirmer() {
        $cc = $this->params['cc'];
        $this->modele->confirmer($cc);
        $this->gabarit->affecter('erreur', $this->messagesUI['_3000']);
        $this->gabarit->affecterActionParDefaut('index');
        $this->index();
    }

    /**
     * Envoyer un message de confirmation
     * @param string $courriel Adresse courriel de l'utilisateur
     * @param string $cc Code de confirmation à joindre dans le lien de ce message
     */
    private function envoyerMessageConfirmationCompte($courriel, $cc) {
        $sujet = "Confirmation de votre compte Memo";
        $message = "
        <html>
            <head>
                <title>Confirmation de votre compte Memo</title>
            </head>
            <body>
                <p>Votre compte est presque prêt !</p>
                <p>
                    Avant de pouvoir l'utiliser, vous devez confirmer votre 
                    adresse courriel ; il suffit de cliquer le lien suivant :
                </p>
                <p>
                    <a href='/utilisateur/confirmer/cc=".$cc."'>utilisateur/confirmer/cc=".$cc."</a>
                </p>
            </body>
        </html>
        ";
        $entetes[] = 'MIME-Version: 1.0';
        $entetes[] = 'Content-type: text/html; charset=utf-8';
        $entetes[] = 'From: Équipe Memo <admin@memo.com>';
        // Localement, par défaut vous n'avez pas de serveur SMTP installé. Il y a
        // plusieurs solutions pour simuler ou configurer un serveur de mail, mais
        // le plus simple pour illustrer cet exemple est d'installer un utilitaire
        // qui simule SMTP comme "Papercut-SMTP" : https://github.com/ChangemakerStudios/Papercut-SMTP
        mail($courriel, $sujet, $message, implode("\r\n", $entetes));
    }
}
