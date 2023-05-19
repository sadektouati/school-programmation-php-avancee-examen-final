<?php
class TacheModele extends AccesBd
{
    /**
     * Ajouter Tache
     * @param int $uti_id identifiant usager.
     * @param string $tache texte de la tache.
     */
    public function ajouter($uti_id, $tache)
    {
        $res = $this->creer("INSERT INTO tache(tac_texte, tac_date_ajout, tac_uti_id_ce) VALUES (:tac_texte, NOW(), :tac_uti_id_ce)"
                        , [
                            'tac_texte' => $tache, 
                            'tac_uti_id_ce' => $uti_id
                        ]);
        return $res;
    }

    /**
     * Retrouver toutes les taches
     * @param int $uti_id identifiant usager.
     * @param string $etat etat de la tache confirmé ou non ou null
     */
    public function toute($uti_id, $etat = null)
    {
        $req = "select tac_id, tac_texte, tac_accomplie, date_format(tac_date_ajout, '%d %M %Y, %T') tac_date_ajout from tache where tac_uti_id_ce = :tac_uti_id_ce";
        $params = ['tac_uti_id_ce' => $uti_id];

        if($etat !== null){
            $req .= " and tac_accomplie = :tac_accomplie";
            $params['tac_accomplie'] = $etat;
        }
        
        $req .= " order by tac_date_ajout desc";
        $res = $this->lireTout($req, $params);
        return $res;
    }

    /**
     * Modifier une tache
     * @param int $uti_id identifiant usager.
     * @param string $tac_id id de la tache
     */
    public function basculeTache($uti_id, $tac_id)
    {
        $res = $this->modifier("UPDATE tache SET tac_accomplie = not tac_accomplie WHERE tac_uti_id_ce=:tac_uti_id_ce and tac_id=:tac_id"
                        , 
                        [
                            'tac_uti_id_ce' => $uti_id,
                            'tac_id'  => $tac_id
                    ]);
        return $res;
    }
}