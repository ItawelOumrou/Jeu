<?php
class Etudiant
{
    private $_id;
    private $_nom;
    private $_degats;
    private $_experience;
    private $_niveau;
     
    const CEST_MOI = 1;
    const Etudiant_TUE = 2;
    const Etudiant_FRAPPE = 3;
     
    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }
     
 
    public function frapper(Etudiant $perso)
    {
        if ($this->id() == $perso->id()){
            return self::CEST_MOI;
        }
         
         
        return $perso->recevoirDegats($this->niveau() * 5);
    }
 
    public function recevoirDegats($force)
    {
        $this->setDegats($this->degats() + $force);      
        if ($this->degats() >= 100){
            return self::Etudiant_TUE;
        }
        return self::Etudiant_FRAPPE;
    }
     
    public function gagnerExperience(){
        $this->setExperience($this->experience() + $this->niveau() * 5);
         
        if ($this->experience() >= 100){
            $this->setNiveau($this->niveau() + 1);
            $this->setExperience(0);
        }
    }
     
    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value)
        {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }
     
    public function id()
    {
        return $this->_id;
    }
     
    public function nom()
    {
        return $this->_nom;
    }
     
    public function degats()
    {
        return $this->_degats;
    }
     
    public function experience(){
        return $this->_experience;
    }
     
    public function niveau()
    {
        return $this->_niveau;
    }
     
    
    public function setId($id)
    {
        $id = (int) $id;
        if ($id >= 0) {
            $this->_id = $id;
        }
    }
     
    public function setNom($nom)
    {
        $nom = strip_tags($this->_nom);
        if (is_string($nom)) {
            $this->_nom = $nom;
        }
    }
     
    public function setDegats($degats)
    {
        $degats = (int) $degats;
        //if ($degats >= 0 && $degats <= 100) {
            $this->_degats = $degats;
        //}
    }
     
    public function setExperience($experience)
    {
        $experience = (int) $experience;
        //if ($experience >= 0 && $experience <= 100) {
            $this->_experience = $experience;
        //}
    }
     
    public function setNiveau($niveau)
    {
        $niveau = (int) $niveau;
        if ($niveau >= 0 && $niveau <= 100) {
            $this->_niveau = $niveau;
        }
    }
   
     
    public function nomValide()
    {
        return !(empty($this->_nom));
    }
}