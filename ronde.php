<?php

session_start();


function SHOW_LISTE_MESURES_PAR_EQPT($db, $eqpt_ID){

    $reponse = $db->prepare('SELECT * FROM MESURE INNER JOIN MESURE_PAR_EQPT MPE
                                            ON MESURE.ID = MPE.ID_MESURE
                                WHERE MPE.ID_EQPT = ?');
            $reponse->execute(array($eqpt_ID));

    $mesure = array(); // Important d'initialiser / réinitialiser

    while($data = $reponse->fetch()){
        $mesure[$data['ID']]["type"] = $data['TYPE'];
        $mesure[$data['ID']]["valeur"] = $data['VALEUR'];
        }
    $reponse->closeCursor();

    $nb_on_line = 0;
    foreach($mesure as $mesure_ID => $contenu){

        if($nb_on_line == 4){
            echo "</div><div class=\"liste\">";
            $nb_on_line = 0;
        }
        echo "<p>".$contenu['type']."&nbsp; &nbsp; &nbsp; 
        <input type=\"number\" value=".$contenu['valeur']." name=".$mesure_ID." /></p>";
        $nb_on_line++;


    }
}

require("header.php");

if(isset($_POST['go_ronde'])){
    
    $_SESSION['ID_Modele'] = $_POST['mon_modele_ronde'];

    // Récupération de tous les équipements de CE MODELE DE RONDE
    $reponse = $db->prepare('SELECT * FROM EQPT INNER JOIN EQPT_PAR_RONDE EPR
                                        ON EQPT.ID = EPR.ID_EQPT
                                WHERE EPR.ID_MODELE_RONDE = ?');
        $reponse->execute(array($_POST['mon_modele_ronde']));

    while($data = $reponse->fetch()){
    $liste_eqpt[$data['ID']]["type"] = $data['TYPE']; 
    $liste_eqpt[$data['ID']]["local"] = $data['LOCAL'];
    $liste_eqpt_[$data['ID']]["batiment"] = $data['BATIMENT'];
    $liste_eqpt_full[$data['ID']] =  $data['ID']." : ".$data['TYPE']." : ".$data['BATIMENT']." : ".$data['LOCAL'];
    }
?>

    <h2>Vous êtes dans une ronde issue du Modèle N°<?=$_POST['mon_modele_ronde']?></h2>
    
    <form method="post" action="">
    
    <?php
    foreach($liste_eqpt_full as $eqpt_ID => $eqpt_nom){ 
        echo "<h4>".$eqpt_nom."</h4><div class=\"liste\">";
            SHOW_LISTE_MESURES_PAR_EQPT($db, $eqpt_ID);
        echo "</div>";
    } ?>

    <p><input type="submit" name="ronde_faite" value="Transmettre les données"/></p>
    </form>
<?php
}

if(isset($_POST['ronde_faite'])){

    // Insertion de toutes les valeurs dans la table Mesure

    foreach($_POST as $key => $value){
        if(is_int($key)){
            $reponse = $db->prepare('UPDATE MESURE SET VALEUR = ? WHERE ID = ?');
            $reponse->execute(array($value,$key));
        }
    }
    echo "<p>Les mesures ont bien été envoyées!</p>";
    
}