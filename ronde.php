<?php

/*
Dans les SELECT WHERE (SELECT ...), il faut faire super attention à bien fermer
toutes les parenthèses!

Ex : 
$requete = $db->prepare('SELECT * FROM EQPT 
            WHERE ID NOT IN(SELECT ID FROM EQPT, EQPT_PAR_RONDE EPR
                                WHERE ID = EPR.ID_EQPT AND EPR.ID_MODELE_RONDE = ?');
    
       Là, c'est mal fermé, il manque une parenthèse à la fin ! Faut fermer le NOT IN()
*/

session_start();

// DB, "TH","proprete",  IMMO
function AJOUT_EQPT_POUR_RONDE($db, $eqpt_ID, $modele_ID){
    $reponse = $db->prepare('INSERT INTO EQPT_PAR_RONDE(ID_EQPT,ID_MODELE_RONDE) VALUES(?,?)'); // On ajoute la mesure
    $reponse->execute(array($eqpt_ID,$modele_ID));

    $reponse->closeCursor();
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


if(isset($_POST['new_modele_rondes'])){

    $reponse = $db->prepare('INSERT INTO MODELE_RONDE(NOM) VALUES(?)'); // On ajoute la mesure
    $reponse->execute(array($_POST['nom_modele_rondes']));

    $reponse->closeCursor();

    echo "<p>La ronde \"".$_POST['nom_modele_rondes']."\" a été créé avec succès, 
            son numéro est <b>\"".$db->lastInsertId()."\"</b>";

    $_SESSION['ID_Modele'] = $db->lastInsertId();
}
else if(isset($_POST['modele_rondes'])){
    $_SESSION['ID_Modele'] = $_POST['modele_rondes'];
}

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


echo "<h2>Page dédiée à la modification des modèles de rondes</h2>";

$reponse = $db->prepare('SELECT * FROM MODELE_RONDE WHERE ID = ?');
$reponse->execute(array($_SESSION['ID_Modele']));
$data_modele = $reponse->fetch();
$reponse->closeCursor();

echo "<p>Ronde N°".$data_modele['ID']." \"".$data_modele['NOM']."\"</p>";

?>
<h4>Vous pouvez associer de nouveaux équipements à votre ronde, ou en retirer :</h4>

<p>Liste des équipements traités actuellement :</p>
<?php
    $requete = $db->prepare('SELECT EQPT.ID num_eqpt, EQPT.TYPE type, EQPT.LOCAL lieu, EQPT.BATIMENT batiment FROM EQPT_PAR_RONDE EPR
                                INNER JOIN EQPT ON EQPT.ID = EPR.ID_EQPT
                                INNER JOIN MODELE_RONDE ON MODELE_RONDE.ID = EPR.ID_MODELE_RONDE
                                WHERE MODELE_RONDE.ID = ?');
        $requete->execute(array($_SESSION['ID_Modele']));
        
    // $data['num_eqpt'] contient le numéro IMMO de l'équipement

        while($data = $requete->fetch()){
        $equipements[$data['num_eqpt']]["type"] = $data['type'];
        $equipements[$data['num_eqpt']]["local"] = $data['lieu'];
        $equipements[$data['num_eqpt']]["batiment"] = $data['batiment'];
        $eqpt[$data['num_eqpt']] = $data['type'].": ".$data['batiment']." : ".$data['lieu'];
        
        echo "<p> - ".$eqpt[$data['num_eqpt']]."<br/> </p>";
    }
    $requete->closeCursor();
?>

<form method="post" action="">


<p>Souhaitez-vous supprimer un équipement? Si oui, sélectionnez-le :<select name="eqpt_delete">
<?php foreach($eqpt as $key => $value){
    echo "<option value=\"".$key."\">".$value."</option>";
    } ?>
</select></p>

<input type="submit" name="delete" value="Supprimer l'équipement du modèle" />
</form>


<form method="post" action="">

<h3>Souhaitez-vous ajouter un ou des nouveau(x) équipement(s) ?<br/>
Si oui, sélectionnez-les : </h3>

<p>- Par bâtiment : <input type="texte" name="add_batiment" placeholder="B8"/></p>

<?php
// Liste des équipements HORS modèle de la ronde
$requete = $db->prepare('SELECT * FROM EQPT 
            WHERE ID NOT IN(SELECT ID FROM EQPT, EQPT_PAR_RONDE EPR
                                WHERE ID = EPR.ID_EQPT AND EPR.ID_MODELE_RONDE = ?
                            )');
    $requete->execute(array($_SESSION['ID_Modele']));

while($data = $requete->fetch()){
$liste_eqpt_out_modele[$data['ID']]["type"] = $data['TYPE']; 
$liste_eqpt_out_modele[$data['ID']]["local"] = $data['LOCAL'];
$liste_eqpt_out_modele[$data['ID']]["batiment"] = $data['BATIMENT'];
$liste_eqpt_out_modele_full[$data['ID']] =  $data['TYPE']." : ".$data['BATIMENT']." : ".$data['LOCAL'];
}
?>

<p>- Par équipement : <select name="eqpt_add">
<?php   foreach($liste_eqpt_out_modele_full as $key => $value){
    echo "<option value=\"".$key."\">$value</option>";
    } ?>
</select></p>
<input type="submit" name="add" value="Ajouter le ou les équipement(s)" />
</form>

<?php
/*
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
*/  

if(isset($_POST['add'])){
    AJOUT_EQPT_POUR_RONDE($db, $_POST['eqpt_add'], $_SESSION['ID_Modele']);
    echo "<meta http-equiv='refresh' content='0'>"; // Obliger de refresh / reset car ça se fait mal


    // GERER le cas où on add par bâtiment
}