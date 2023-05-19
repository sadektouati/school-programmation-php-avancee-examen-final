<?php
class TacheControleur extends Controleur
{
    function __construct($modele, $module, $action, $params)
    {
        if(!isset($_SESSION['utilisateur'])) {
            Utilitaire::nouvelleRoute('utilisateur/index');
        }
        
        parent::__construct($modele, $module, $action, $params);
    }

    /**
     * Méthode invoquée par défaut si aucune action n'est indiquée
     */
    public function index()
    {
        // Par défaut on affiche les tâches
        $this->gabarit->affecterActionParDefaut('tout');
        $this->tout();

    }

    public function tout() {
        $taches = $this->modele->toute($_SESSION['utilisateur']->uti_id, $_GET['etat']??null);
        $this->gabarit->affecter('taches', $taches);
        $this->gabarit->affecter('selection', $_GET['etat']??-1);
    }

    public function basculer() {
        
        $this->modele->basculeTache($_SESSION['utilisateur']->uti_id, $_GET["tid"]);
        //Redirige vers la page des taches
        Utilitaire::nouvelleRoute('tache/tout');
    }

    public function ajouter() {
        $texte = $_POST['tac_texte'];
        $identifiant = $this->modele->ajouter($_SESSION['utilisateur']->uti_id, $texte);
        if(is_numeric( $identifiant)){
        //Redirige vers la page des taches
        Utilitaire::nouvelleRoute('tache/tout');
        }else{
            //en doit gerer l'erreur ici.. mais c'est pas demandé...
            echo 'il y a un probleme';
        }
    }

}
