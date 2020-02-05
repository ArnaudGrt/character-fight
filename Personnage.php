<?php
    class Personnage{
        // ATTRIBUTS
        private $_id;
        private $_degats;
        private $_nom;

        // CONSTANTE
        const CEST_MOI = 1; // Si l'on se frappe soi-même
        const PERSONNAGE_TUE = 2; // Si le personnage est tué
        const PERSONNAGE_FRAPPE = 3; // Lorsque l'on frappe un personnage

        // CONSTRUCTEUR

        public function __construct(array $donnees){
            $this->hydrate($donnees);
        }

        // ACCESSEURS

        public function id(){
            return $this->_id;
        }

        public function degats(){
            return $this->_degats;
        }

        public function nom(){
            return $this->_nom;
        }

        // MUTATEURS

        public function setId($id){
            $id = (int)$id;

            if($id > 0){
                $this->_id = $id;
            }
        }

        public function setDegats($degats){
            $degats = (int)$degats;

            if($degats >= 0 && $degats <= 100){
                $this->_degats = $degats;
            }
        }

        public function setNom($nom){
            if(is_string($nom)){
                $this->_nom = $nom;
            }
        }

        // METHODE

        public function hydrate(array $donnees){
            foreach($donnees as $key => $value){
                $method = 'set'.ucfirst($key);

                if(method_exists($this, $method)){
                    $this->$method($value);
                }
            }
        }

        public function frapper(Personnage $perso1){
            if($perso1->id() == $this->id()){
                return self::CEST_MOI;
            }

            return $perso1->recevoirDegats();
        }

        public function recevoirDegats(){
            $this->_degats += 5;

            if($this->_degats >= 100){
                return self::PERSONNAGE_TUE;
            }

            return self::PERSONNAGE_FRAPPE;
        }

        public function nomValid(){
            return !empty($this->_nom);
        }
    }
?>