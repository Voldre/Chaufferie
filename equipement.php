<?php

/* L'équivalent de SCOPE_IDENTIFY() en SQL Server (Transact-SQL) 
    c'est LAST_INSERT_ID() pour MySQL !

    Entre nous, le nom est beaucoup plus explicite!

    Cette fonction va chercher le dernier ID qui a été généré automatiquement.
    Donc on parle lors d'un INSERT la plupart du temps

    Et même mieux! EN PHP avec la calsse PDO, il existe une fonction !

    $db->lastInsertId();

*/

session_start();

if(isset($_POST['IMMO'])){
    $_SESSION['IMMO'] = $_POST['IMMO'];
}

require("header.php");
                            // DB, "TH","proprete",  IMMO
function AJOUT_MESURE_POUR_EQPT($db, $type_mesure, $eqpt_ID){
    $reponse = $db->prepare('INSERT INTO MESURE(TYPE) VALUES(?)'); // On ajoute la mesure
    $reponse->execute(array($type_mesure));

    $last_ID = $db->lastInsertId(); // On récupère l'ID de cette mesure
        // Merci la classe PDO ! ^_^
    
    $reponse->closeCursor();


    $reponse = $db->prepare('INSERT INTO MESURE_PAR_EQPT(ID_MESURE,ID_EQPT) VALUES(?,?)');
    $reponse->execute(array($last_ID,$eqpt_ID)); // Et on ajoute la liaison entre l'ID mesure et l'IMMO de l'équipement

    $reponse->closeCursor();
}


echo "<h2>Page dédiée à la modification de mesures d'un équipement</h2>";


if(isset($_POST['change_param_eqpt'])){

    $_POST['batiment_eqpt'] = htmlspecialchars($_POST['batiment_eqpt']);
    $_POST['local_eqpt'] = htmlspecialchars($_POST['local_eqpt']);

    $requete = $db->prepare('UPDATE EQPT SET BATIMENT = ? , LOCAL = ? , TYPE = ? WHERE ID = ?');
    $requete->execute(array($_POST['batiment_eqpt'],$_POST['local_eqpt'],$_POST['type_eqpt'],$_POST['IMMO']));
    $requete->closeCursor();
    
    echo "<p>Les paramètres ont bien été changé avec succès!</p>";
}

    if(isset($_POST['new_eqpt'])){  // Lors de la création :

        $_POST['batiment_eqpt'] = htmlspecialchars($_POST['batiment_eqpt']);
        $_POST['local_eqpt'] = htmlspecialchars($_POST['local_eqpt']);
        
        $requete = $db->prepare('INSERT INTO EQPT(ID,BATIMENT,LOCAL,TYPE) VALUES(?,?,?,?)');
        // try{
        $requete->execute(array($_POST['IMMO'],$_POST['batiment_eqpt'],$_POST['local_eqpt'],$_POST['type_eqpt']));
        
        echo "<p>L'équipement N°<b>".$_POST['IMMO']." \"".$_POST['batiment_eqpt']." - ".$_POST['local_eqpt']."\"</b> a bien été créé !</p>";

        // Equipement créé, on y ajoute tous les relevés de mesures de bases (création des paramètres par défaut de ce type d'EQPT)
        // Ex : par défaut, tous les adou possèdent un TH, un compteur d'eau, un Niveau sel OU saumure  et  un niveau de propreté

        if($_POST['type_eqpt'] == "sel" OR $_POST['type_eqpt'] == "saumure"){
                                                                            // ajoute "sel" ou "saumure" aux mesures, s'adapte
            $liste_mesures_par_defaut = array('TH','compteur_eau','proprete',$_POST['type_eqpt']);
        }
        if($_POST['type_eqpt'] == "chaudiere"){
            $liste_mesures_par_defaut = array();
        }
                // CREATE IN DB, TELLE MESURE ++ TELLES MESURE_PAR_EQPT
        // if($_POST['type'] == "sel" ...)

        foreach($liste_mesures_par_defaut as $type_mesure){ // Pour chaque nouveau paramètre (mesure) :
            AJOUT_MESURE_POUR_EQPT($db, $type_mesure, $_POST['IMMO']); // #ID_EQPT
        }

        /*
        }catch (Exception $e)
        {
        //echo $e->getMessage(); // SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicata du champ '   ' pour la clef 'PRIMARY'

        echo "<p class=\"red\">²équipement existe déjà, veuillez utiliser le formulaire de modification d'équipement ci-dessus.</p>";
        }  */

        $requete->closeCursor();



    }

    $reponse = $db->prepare('SELECT * FROM EQPT WHERE ID = ?');
    $reponse->execute(array($_SESSION['IMMO']));
    $data_eqpt = $reponse->fetch();
    $reponse->closeCursor();

    echo "<p>Type de mon équipement : <b>".$data_eqpt['TYPE']."</b>, bâtiment et emplacement de mon équipement : <b>".$data_eqpt['BATIMENT']." - ".$data_eqpt['LOCAL']."</b>, IMMO : <b>".$data_eqpt['ID']."</b> </p>";

    ?>
    <h4>Vous pouvez associer de nouvelles mesures à votre équipement, ou en retirer :</h4>

    <p>Liste des mesures existantes actuellement :</p>
    <?php
        $requete = $db->prepare('SELECT MESURE.TYPE AS "Type_mesure" FROM MESURE
                                    INNER JOIN MESURE_PAR_EQPT AS MPE ON MESURE.ID = MPE.ID_MESURE
                                    WHERE MPE.ID_EQPT = ?');
            $requete->execute(array($_SESSION['IMMO']));
            
         while($data = $requete->fetch()){
            $mesures[] = $data['Type_mesure'];
                echo "<p> - ".$data['Type_mesure']."<br/> </p>";
        }
        $requete->closeCursor();
    ?>
    
    <form method="post" action="">


    <p>Souhaitez-vous supprimer une mesure? Si oui, sélectionnez-la :<select name="type_mesure_delete">
    <?php foreach($mesures as $value){
        echo "<option value=\"".$value."\">$value</option>";
        } ?>
    </select></p>

    <input type="submit" name="delete" value="Supprimer la mesure" />
    </form>


    <form method="post" action="">
    <?php
    if($data_eqpt['TYPE'] == "sel" OR $data_eqpt['TYPE'] == "saumure"){ // Si c'est un Adou
        ?>
        <p>Souhaitez-vous ajouter un nouveau type de mesure? Si oui, sélectionnez-la :<select name="type_mesure_add">
        <option value="TH">TH</option>
        <option value="compteur_eau">Compteur d'eau</option>
        <option value="saumure">Niveau Saumure</option>
        <option value="sel">Niveau Sel</option>
        <option value="proprete">Propreté</option>
        </select></p>
        <?php
    }
    else if($_POST['type'] == "chaudiere"){

    }

    ?>
    <input type="submit" name="add" value="Ajouter la mesure" />
    </form>

    <?php 
    if(isset($_POST['suppr_eqpt'])){  // Si modification, alors possibilité de le supprimer      
    ?>
 
    <?php
}

if(isset($_POST['delete'])){

    // Ce fut long et fastidieux mais j'y suis parvenu!
    // Les requêtes peuvent être écrites de plusieurs façons
    // Et il faut trouver la plus adéquate!
        // les SELECT dans un WHERE c'est sous côté! ^_^

    // On supprime les mesures qui ont une liaison (MPE) avec un équipement X
    $reponse = $db->prepare('DELETE FROM MESURE
    WHERE ID IN (SELECT ID_MESURE FROM MESURE_PAR_EQPT MPE WHERE MPE.ID_EQPT = ?) 
            AND TYPE = ?');
    $reponse->execute(array($data_eqpt['ID'],$_POST['type_mesure_delete']));

    $reponse->closeCursor();

    // On peut maintenant supprimer TOUTES LES LIAISONS MESURE/EQPT 
    // qui ne sont donc pas rattaché à une mesure existante (car DELETE précédent)

        // En gros, on supprime toutes les liaisons là où la valeur ID_MESURE de MPE
        // n'apparaît dans aucune ID de MESURE

    $reponse = $db->query('DELETE FROM MESURE_PAR_EQPT
            WHERE ID_MESURE NOT IN (SELECT ID FROM MESURE)');
    $reponse->execute(); // BOOUM !

    echo "<meta http-equiv='refresh' content='0'>"; // Obliger de refresh / reset car ça se fait mal

}    

if(isset($_POST['add'])){
    AJOUT_MESURE_POUR_EQPT($db, $_POST['type_mesure_add'], $data_eqpt['ID']);
    echo "<meta http-equiv='refresh' content='0'>"; // Obliger de refresh / reset car ça se fait mal

}