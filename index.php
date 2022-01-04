<?php
 
function chargerClasse($classname)
{
    require $classname.'.php';
}
 
spl_autoload_register('chargerClasse');
 
session_start();
 
if (isset($_GET['deconnexion'])){
    session_destroy();
    header('Location: .');
    exit();
}
 
if (isset($_SESSION['perso'])){
    $perso = $_SESSION['perso'];
}
 
$db = new PDO('mysql:host=localhost;dbname=jeu','root','');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
 
$manager = new Manager($db);
 
if (isset($_POST['creer']) && isset($_POST['nom'])){
    $perso = new Etudiant(['nom' => $_POST['nom']]);
     
    if (!$perso->nomValide()){
        $message = 'Le nom choisi est invalide.';
        unset($perso);
    } elseif ($manager->exists($perso->nom())){
        $message = 'Le nom du Etudiant est déjà pris.';
        unset($perso);
    } else {
        $manager->add($perso);
    }
     
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])){
    if ($manager->exists($_POST['nom']))
    {
        $perso = $manager->get($_POST['nom']);
    } else {
        $message = 'Ce Etudiant n\'existe pas !';
    }
     
} elseif (isset($_GET['frapper'])){
     
    if (!isset($perso)){
        $message = 'Merci de créer un Etudiant ou de vous identifier.';
    } else {
        if (!$manager->exists((int) $_GET['frapper'])){
            $message = 'Le Etudiant que vous voulez frapper n\'existe pas!';
        } else {
             
            $persoAFrapper = $manager->get((int) $_GET['frapper']);
            $retour = $perso->frapper($persoAFrapper);
             
            switch($retour)
            {
                case Etudiant::CEST_MOI :
                    $message = 'Mais... pouquoi voulez-vous vous frapper ???';
                    break;
                case Etudiant::Etudiant_FRAPPE :
                    $message = 'Le Etudiant a bien été frappé !';
                     
                    $perso->gagnerExperience();
                     
                    $manager->update($perso);
                    $manager->update($persoAFrapper);
                     
                    break;
                case Etudiant::Etudiant_TUE;
                    $message = 'Vous avez tué ce Etudiant !';
                     
                    $perso->gagnerExperience();
 
                    $manager->update($perso);
                    $manager->delete($persoAFrapper);
                     
                    break;
            }
        }
    }
}
 
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TP : Mini jeu de combat</title>
        <meta charset="utf-8" />
    </head>
    <body>
     
        <p> Nombre de Etudiants créés : <?= $manager->count() ?></p>
    <?php
        if (isset($message)){
            echo '<p>'. $message . '</p>';
        }
         
        if (isset($perso)){
        ?>
            
         
            <fieldset>
                <legend>Mes informations</legend>
                <p>
                    Nom : <?=  htmlspecialchars($perso->nom()) ?><br />
                    Dégâts : <?= $perso->degats() ?>
                    Expérience : <?= $perso->experience() ?>
                    Niveau : <?= $perso->niveau() ?>
                </p>
            </fieldset>
            <fieldset>
                <legend>Qui frapper?</legend>
                <p>
                    <?php
                     
                    $persos = $manager->getList($perso->nom());  
                    if (empty($persos)) {
                        echo 'Personne à frapper!';
                    } else {
                        foreach($persos as $unPerso){
                            echo '<a href="?frapper='.$unPerso->id().'">'.htmlspecialchars($unPerso->nom()).
                            '</a> (dégâts : '.$unPerso->degats().', expérience : '.$unPerso->experience().
                            ', niveau : '.$unPerso->niveau().')<br />';
                             
                        }
                    }
                     
                    ?>
                </p>
            </fieldset>
             
            <p><a href="?deconnexion=1">Déconnexion</a></p>
        <?php
 
        } else {
             
    ?>
            <form action="" method = "post">
                <p>
                    Nom : <input type="text" name="nom" maxlength="50" />
                    <input type="submit" value = "Créer ce Etudiant" name="creer" />
                    <input type="submit" value = "Utiliser ce Etudiant" name="utiliser" />
                </p>
            </form>
    <?php
        }
    ?>
     
     
     
    </body>
</html>
<?php
if (isset($perso)){
    $_SESSION['perso'] = $perso;
}
?>