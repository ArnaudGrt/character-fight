<?php
    function chargerClasse($className){
        require $className.'.php';
    }

    spl_autoload_register('chargerClasse');

    session_start();

    if(isset($_GET['deconnexion'])){
      session_destroy();
      header('Location: .');
      exit();
    }

    if(isset($_SESSION['perso'])){
      $perso = $_SESSION['perso'];
    }

    $db = new PDO('mysql:host=localhost;dbname=POO_PHP', 'root', 'root');

    $manager = new PersonnageManager($db);

    if(isset($_POST['creer']) && isset($_POST['nom'])){
        $perso = new Personnage(['nom' => $_POST['nom']]);

        if(!$perso->nomValid()){
            $message = 'Le nom n\'est pas valide !';
            unset($perso);
        }
        elseif($manager->exist($perso->nom())){
            $message = 'Le nom du personnage est déjà pris !';
            unset($perso);
        }
        else{
            $manager->add($perso);
        }
    }
    elseif(isset($_POST['utiliser']) && isset($_POST['nom'])){
        if($manager->exist($_POST['nom'])){
            $perso = $manager->get($_POST['nom']);
        }
        else{
            $message = 'Ce personnage n\'existe pas !';
        }
    }
    elseif(isset($_GET['frapper'])){
      if(!isset($perso)){
        $message = 'Merci de créer un personnage ou bien de vous identifier !';
      }
      else{
        if(!$manager->exist((int) $_GET['frapper'])){
          $message = 'Le personnage que vous voulez frapper n\'existe pas !';
        }
        else{
          $persoAFrapper = $manager->get((int) $_GET['frapper']);
          $retour = $perso->frapper($persoAFrapper);

          switch($retour){
            case Personnage::CEST_MOI:
              $message = 'Mais... pourquoi voulez-vous vous frapper ?';
            break;

            case Personnage::PERSONNAGE_FRAPPE:
              $message = 'Le personnage a bien été frappé !';

              $manager->update($perso);
              $manager->update($persoAFrapper);
            break;

            case Personnage::PERSONNAGE_TUE:
              $message = 'Vous avez tué ce personnage !';

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
    <p>Nombre de personnages créés : <?= $manager->count() ?></p>
    <?php 
        if(isset($message)) { echo '<p>'.$message.'</p>'; }

        if(isset($perso)){
          ?>
          <p><a href="?deconnexion=1">Déconnexion</a></p>
          <fieldset>
            <legend>Mes informations</legend>
            <p>
            Nom : <?= htmlspecialchars($perso->nom()) ?><br>
            Dégâts : <?= $perso->degats() ?>
            </p>
          </fieldset>

          <fieldset>
            <legend>Qui frapper ?</legend>
            <p>
    <?php
      $persos = $manager->getList($perso->nom());

      if(empty($persos)){
        echo 'Personne à frapper !';
      }
      else{
        foreach($persos as $unPerso){
          echo '<a href="?frapper=', $unPerso->id(), '">', htmlspecialchars($unPerso->nom()), '</a> (dégâts : ', $unPerso->degats(), ')<br>';
        }
      }
      ?>
            </p>
          </fieldset>
      <?php
        }
        else{
      ?>
        <form action="" method="post">
          <p>
            Nom : <input type="text" name="nom" maxlength="50" />
            <input type="submit" value="Créer ce personnage" name="creer" />
            <input type="submit" value="Utiliser ce personnage" name="utiliser" />
          </p>
        </form>
      <?php
        }
    ?>
  </body>
</html>
<?php
  if(isset($perso)){
    $_SESSION['perso'] = $perso;
  }
?>