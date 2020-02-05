<?php
    class PersonnageManager{
        // ATTRIBUTS
        private $_db;

        // CONSTRUCTEUR
        public function __construct($db){
            $this->setDb($db);
        }

        // MUTATEURS

        public function setDb($db){
            $this->_db = $db;
        }

        // METHODES

        public function add(Personnage $perso1){
            $req = $this->_db->prepare('INSERT INTO personnages(nom, degats) VALUES(:nom, :degats)');
            $req->execute(array(
                'nom' => $perso1->nom(),
                'degats' => 0
            ));
            
            $perso1->hydrate([
              'id' => $this->_db->lastInsertId(),
              'degats' => 0,
            ]);
        }

        public function update(Personnage $perso1){
            $req = $this->_db->prepare('UPDATE personnages SET degats = :degats WHERE id = :id');

            $req->bindValue(':degats', $perso1->degats(), PDO::PARAM_INT);
            $req->bindValue(':id', $perso1->id(), PDO::PARAM_INT);

            $req->execute();
        }

        public function delete(Personnage $perso1){
            return $this->_db->query('DELETE FROM personnages WHERE id = '.$perso1->id());
        }

        public function getList($nom){
            $persos = [];

            $req = $this->_db->prepare('SELECT * FROM personnages WHERE nom <> :nom ORDER BY nom');
            $req->execute([':nom' => $nom]);

            while($donnees = $req->fetch(PDO::FETCH_ASSOC)){
                $persos[] = new Personnage($donnees);
            } 

            return $persos;
        }

        public function get($infos){
            if(is_int($infos)){
                $req = $this->_db->query('SELECT * FROM personnages WHERE id = '.$infos);
                $donnees = $req->fetch(PDO::FETCH_ASSOC);

                return new Personnage($donnees);
            }
            else{
                $req = $this->_db->prepare('SELECT * FROM personnages WHERE nom = :nom');
                $req->execute([':nom' => $infos]);

                return new Personnage($req->fetch(PDO::FETCH_ASSOC));
            }
        }

        public function count(){
            return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
        }

        public function exist($infos){
            if (is_int($infos)){
              return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id = '.$infos)->fetchColumn();
            }
            
            $req = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom = :nom');
            $req->execute([':nom' => $infos]);
            
            return (bool) $req->fetchColumn();
        }
    }
?>