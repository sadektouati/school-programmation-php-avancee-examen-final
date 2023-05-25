<?php
// On utilise le module de validation (Validator) de Symfony
use Symfony\Component\Validator\Validation;

class Controleur 
{
    protected $modele;
    protected $gabarit;
    protected $params;
    protected $validateur;
    protected $messagesUI = [];

    function __construct($modele, $module, $action, $params)
    {
        if(class_exists($modele)) {
            $this->modele = new $modele();
        }
        $this->gabarit = new HtmlGabarit($module, $action);
        $this->gabarit->affecter('page', $module);
        $this->params = $params;
        // Instance du validateur Symfony
        $this->validateur = Validation::createValidator();
        // Comme les paramètres de messages d'erreurs sont utilisés souvent, on les 
        // gère dans le constructeur de base.
        if(isset($this->params['msg'])) {
            $this->gabarit->affecter('erreur', $this->messagesUI[$this->params['msg']]);
        }
    }

    function __destruct()
    {
       $this->gabarit->genererVue(); 
    }

    // Action par défaut : donc méthode obligatoire
    public function index() 
    {
    }
}