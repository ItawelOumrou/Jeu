<?php
class Manager
{
    private $_db;
     
    public function __construct($db)
    {
        $this->setDb($db);
    }
     
    public function add(Etudiant $perso)
    {
        $q = $this->_db->prepare('INSERT INTO Etudiants (nom) VALUES (:nom);');
        $q->bindValue(':nom', $perso->nom());
        $q->execute();
         
        $perso->hydrate([
            'id'=>$this->_db->lastInsertId(),
            'degats' => 0,
            'experience' => 0,
            'niveau' => 1,
            'nbCoups' => 0]);
    }
     
    public function count()
    {
        return $this->_db->query('SELECT COUNT(*) FROM Etudiants')->fetchColumn();
    }
     
    public function delete(Etudiant $perso)
    {
        $this->_db->exec('DELETE FROM Etudiants WHERE id = '.$perso->id());
    }
     
    public function exists($info)
    {
        if (is_int($info))
        {
            return (bool)$this->_db->query('SELECT COUNT(*) FROM Etudiants WHERE id = '.$info)->fetchColumn();
        }
         
        $q = $this->_db->prepare('SELECT COUNT(*) FROM Etudiants WHERE nom = :nom');
        $q -> execute([':nom' => $info]);
         
        return (bool) $q->fetchColumn();
    }
     
    public function get($info)
    {
        if (is_int($info))
        {
            $q = $this->_db->query('SELECT id, nom, degats, experience, niveau FROM Etudiants WHERE id = '.$info);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
             
            return new Etudiant($donnees);
        }
         
        $q = $this -> _db ->prepare('SELECT id, nom, degats, experience, niveau FROM Etudiants WHERE nom = :nom');
        $q->execute([':nom' => $info]);
        $donnees = $q->fetch(PDO::FETCH_ASSOC);
         
        return new Etudiant($donnees);
    }
     
    public function getList($nom)
    {
        $persos = [];
 
        $q  =  $this->_db->prepare('SELECT id, nom, degats, experience, niveau FROM Etudiants WHERE nom <> :nom ORDER BY nom');
        $q->execute([':nom'=>$nom]);
 
        while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
        {
            $persos[] = new Etudiant($donnees);
        }
        return $persos;
    }
     
    public function update(Etudiant $perso)
    {
        $q  =  $this->_db->prepare('UPDATE Etudiants SET degats = :degats, experience = :experience, niveau = :niveau WHERE id = :id');
        $q->bindValue(':degats',$perso->degats(), PDO::PARAM_INT);
        $q->bindValue(':experience',$perso->experience(), PDO::PARAM_INT);
        $q->bindValue(':niveau',$perso->niveau(), PDO::PARAM_INT);
        $q->bindValue(':id',$perso->id(), PDO::PARAM_INT);
        $q->execute();
    }
     
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }
     
}