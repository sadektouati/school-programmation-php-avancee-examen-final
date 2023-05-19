<?php
class UtilisateurModele extends AccesBd
{
    /**
     * Obtenir le détail d'un utilisateur
     * @param string $courriel Adresse courriel de l'utilisateur
     */
    public function un($courriel)
    {
        return $this->lireUn("SELECT * FROM utilisateur 
                                WHERE uti_courriel=:email"
                        , ['email'=>$courriel]);
    }

    /**
     * Ajouter un utilisateur
     * @param array $utilisateur Tableau contenant le détail d'un utilisateur.
     */
    public function ajouter($utilisateur)
    {
        $cc = uniqid('memo', true);
        $res = $this->creer("INSERT INTO utilisateur VALUES (0, :nom, :courriel, :mdp, NOW(), :cc)"
                        , [
                            'nom' => $_POST['uti_nom'], 
                            'courriel' => $_POST['uti_courriel'], 
                            'mdp' => password_hash($_POST['uti_mdp'], PASSWORD_DEFAULT),
                            'cc'  => $cc
                        ]);
        if(is_numeric($res)) {
            return ['courriel'=>$_POST['uti_courriel'], 'cc'=>$cc];
        }
        return $res;
    }

    /**
     * Modifier un utilisateur pour vider le code de confirmation.
     * @param string $cc Code de confirmation.
     */
    public function confirmer($cc)
    {
        $res = $this->modifier("UPDATE utilisateur SET uti_confirmation='' WHERE uti_confirmation=:cc"
                        , ['cc'  => $cc]);
        return $res;
    }
}