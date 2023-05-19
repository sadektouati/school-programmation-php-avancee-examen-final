<?php
class AccesBd 
{
    private $pdo; // Objet de connexion PDO
    private $rp; // Objet de requête paramétrée PDO
    private $erreur = null;

    /**
     * Constructeur : initialise l'objet PDO
     * 
     */
    function __construct()
    {
        if(!isset($this->pdo)) {
            $options = [
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                    ];
            // [MODIF HORS COURS]
            // On gère les exceptions reliées à la connexion BD
            try {
                $this->pdo = new PDO("mysql:host=".BD_HOTE."; dbname=".BD_NOM."; charset=utf8",
                    BD_UTIL, BD_MDP, $options); 
            }
            // S'il y a erreur de connexion, on ne peut rien faire : diriger vers la page statique 
            // d'erreur HTTP 500 (on peut faire un travail plus granulaire ici, mais c'est juste un exemple)
            catch(PDOException $e) {
                Utilitaire::nouvelleRouteErreur('500', $e->errorInfo);
            }
        }
    }
        
    /**
     * Effectue une requête SQL
     *
     * @param string $reqSql Requête SQL paramétrée
     * @param array $params Tableau contenant les valeurs des paramètres à 
     *              passer à la requête au moment de son exécution
     * @return void
     */
    private function soumettre($req, $params) 
    {
        // [MODIF HORS COURS]
        // On gère les exceptions reliées à la requête MySQL
        try {
            $this->rp = $this->pdo->prepare($req);
            $this->rp->execute($params);
        }
        // S'il y a erreur on la sauvegarde dans la variable d'instance
        catch(PDOException $e) {
            $this->erreur = '/msg='.$e->errorInfo[1];
        }
    }

    /**
     * Effectue une requête SELECT et retourne un jeu d'enregistrements
     *
     * @param string $req Requête SQL de type SELECT
     * @param array $params Tableau contenant les valeurs des paramètres à 
     *              passer à la requête au moment de son exécution
     * 
     * @return object[] Tableau d'objets représentants les enregistrements
     */
    protected function lireTout($req, $params=[])
    {
        $this->soumettre($req, $params);
        return $this->rp->fetchAll();
    }
    
    /**
     * Effectue une requête SELECT et retourne un seul enregistrement
     *
     * @param string $req Requête SQL de type SELECT
     * @param array $params Tableau contenant les valeurs des paramètres à 
     *              passer à la requête au moment de son exécution
     * 
     * @return object Objet représentant l'enregistrement retourné
     */
    protected function lireUn($req, $params=[])
    {
        $this->soumettre($req, $params);
        return $this->rp->fetch();
    }

    /**
     * Effectue une requête INSERT
     *
     * @param string $req Requête SQL de type INSERT
     * @param array $params Tableau contenant les valeurs des paramètres à 
     *              passer à la requête au moment de son exécution
     * @return int Identifiant de l'enregistrement ajouté, ou false
     */
    protected function creer($req, $params=[]) 
    {
        $this->soumettre($req, $params);
        // [MODIF HORS COURS]
        // S'il y a erreur on la retourne
        if($this->erreur) {
            return $this->erreur;
        }
        return $this->pdo->lastInsertId();
    }

    /**
     * Effectue une requête UPDATE
     *
     * @param string $req Requête SQL de type UPDATE
     * @param array $params Tableau contenant les valeurs des paramètres à 
     *              passer à la requête au moment de son exécution
     * @return int Le nombre d'enregistrements modifiés
     */
    protected function modifier($req, $params=[])
    {
        $this->soumettre($req, $params);
        // [MODIF HORS COURS]
        // S'il y a erreur on la retourne
        if($this->erreur) {
            return $this->erreur;
        }
        return $this->rp->rowCount();
    }

    /**
     * Effectue une requête DELETE
     *
     * @param string $req Requête SQL de type DELETE
     * @param array $params Tableau contenant les valeurs des paramètres à 
     *              passer à la requête au moment de son exécution
     * @return int Le nombre d'enregistrements supprimés
     */
    protected function supprimer($req, $params=[])
    {
        // Simplement faire appel à la fonction modifier 
        // (puisque c'est la même implémentation)
        return $this->modifier($req, $params);
    }
}