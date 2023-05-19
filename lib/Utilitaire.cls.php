<?php
class Utilitaire
{
    /**
     * Rediriger vers une route dynamique.
     */
    public static function nouvelleRoute($route="")
    {
        header('Location: '.BASE_SERVEUR.$route);
    }

    /**
     * Rediriger vers une route statique.
     * @param string $erreur Code d'erreur HTTP
     * @param array $info Tableau contenant les codes d'erreurs générés par les librairies de code 
     * (pas utilisé ici, mais illustre ce qui est possible)
     */
    public static function nouvelleRouteErreur($erreur='400', $info=[])
    {
        header('Location: '.BASE_SERVEUR.'pages-erreurs/'.$erreur.'.html?'.$info[0].'/'.$info[1].'/'.$info[2]);
    }

    /**
     * Convertit le tableau des paramètres d'URL d'un tableau numérique à un tableau associatif (en décortiquant les chaînes de paramètres)
     * Transforme ça : ['p1=v1', 'p2', 'p3=']
     * En ça : ['p1'=>'v1', 'p2'=>true, 'p3'=>'']
     * 
     * @param array $params Tableau des paramètres passés en URL
     * @return array Tableau associatif de ces même paramètres
     */
    public static function decortiquerParams($params)
    {
        $paramsAssoc = null;
        if($params && count($params)>0) {
            $paramsAssoc = [];
            foreach ($params as $param) {
                $paramTab = explode('=', $param);
                $paramsAssoc[$paramTab[0]] = (isset($paramTab[1]))?$paramTab[1]:true;
            }
        }
        return $paramsAssoc;
    }
}