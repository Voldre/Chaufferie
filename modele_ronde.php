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


function AJOUT_EQPT_POUR_RONDE($db, $eqpt_ID, $modele_ID){

    if($eqpt_ID != 0){
    $reponse = $db->prepare('INSERT INTO EQPT_PAR_RONDE(ID_EQPT,ID_MODELE_RONDE) VALUES(?,?)
                            ');     // On ajoute la liaison si le nombre de liaison est de 0.
    $reponse->execute(array($eqpt_ID,$modele_ID));

    $reponse->closeCursor();
    }
}

require("header.php");



if(isset($_POST['new_modele_rondes'])){

    $_POST['nom_modele_rondes'] = htmlspecialchars($_POST['nom_modele_rondes']);

    $reponse = $db->prepare('INSERT INTO MODELE_RONDE(NOM) VALUES(?)');
    $reponse->execute(array($_POST['nom_modele_rondes']));

    $reponse->closeCursor();

    echo "<p>La ronde \"".$_POST['nom_modele_rondes']."\" a été créé avec succès, 
            son numéro est <b>\"".$db->lastInsertId()."\"</b>";

    $_SESSION['ID_Modele'] = $db->lastInsertId();
}
else if(isset($_POST['modele_rondes'])){
    $_SESSION['ID_Modele'] = $_POST['modele_rondes'];
}


echo "<h2>Page dédiée à la modification des modèles de rondes</h2>";

$reponse = $db->prepare('SELECT * FROM MODELE_RONDE WHERE ID = ?');
$reponse->execute(array($_SESSION['ID_Modele']));
$data_modele = $reponse->fetch();
$reponse->closeCursor();

echo "<p>Ronde N°<b>".$data_modele['ID']." \"".$data_modele['NOM']."\"</b></p>";
?>
    <form method="post">
    <p>Vous pouvez renommez la ronde :<input type="texte" name="new_nom" value="<?=$data_modele['NOM']?>" />
        <input type="submit" value="Changer le nom" name="change_nom"/></p>
    </form>
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
        $eqpt[$data['num_eqpt']] = $data['num_eqpt']." : ".$data['type'].": ".$data['batiment']." : ".$data['lieu'];
        
        echo "<div class=liste0><p>".$eqpt[$data['num_eqpt']]."<br/> </p></div>";
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

<p>- Par bâtiment : <input type="texte" name="add_batiment" placeholder="8B"/></p>

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
$liste_eqpt_out_modele_full[$data['ID']] =  $data['ID']." : ".$data['TYPE']." : ".$data['BATIMENT']." : ".$data['LOCAL'];
}
?>

<p>- Par équipement : <select name="eqpt_add">
    <option> &nbsp;  - &nbsp; &nbsp; - &nbsp; &nbsp; - &nbsp; &nbsp; -  &nbsp; &nbsp; - </option>
<?php   foreach($liste_eqpt_out_modele_full as $key => $value){
    echo "<option value=\"".$key."\">$value</option>";
    } ?>
    </select></p>
<input type="submit" name="add" value="Ajouter le ou les équipement(s)" />
</form>

<?php

if(isset($_POST['delete'])){

    $reponse = $db->prepare('DELETE FROM EQPT_PAR_RONDE
                WHERE ID_EQPT = ? AND ID_MODELE_RONDE = ?');
    $reponse->execute(array($_POST['eqpt_delete'],$_SESSION['ID_Modele']));

    $reponse->closeCursor();

    echo "<meta http-equiv='refresh' content='0'>"; // Obliger de refresh / reset car ça se fait mal

}  

if(isset($_POST['add'])){

    // ADD par équipement

    AJOUT_EQPT_POUR_RONDE($db, $_POST['eqpt_add'], $_SESSION['ID_Modele']);

    // ADD par bâtiment

    if(!empty($_POST['add_batiment'])){

        // On va sélectionner tous les équipements du bâtiment
        // Et ensuite on va retenir uniquement ceux n'étant pas déjà dans la ronde !

        $reponse = $db->prepare('SELECT ID, BATIMENT FROM EQPT
                            WHERE BATIMENT = ? AND ID NOT IN 
                                (SELECT ID_EQPT FROM EQPT_PAR_RONDE EPR
                                    WHERE EPR.ID_MODELE_RONDE = ?) ');

        $reponse->execute(array($_POST['add_batiment'],$_SESSION['ID_Modele']));
        
        $nb = 0;
        while($data = $reponse->fetch()){
            $liste_eqpt_batiment_non_use[$data['ID']] = $data['BATIMENT'];
            // on récupère les IMMO et les bâtiments
            $nb++;
        }
        $reponse->closeCursor();

        if($nb!= 0){
            foreach($liste_eqpt_batiment_non_use as $key => $value){
                $requete = $db->prepare('INSERT INTO EQPT_PAR_RONDE(ID_EQPT,ID_MODELE_RONDE) VALUES(?,?)');
                $requete->execute(array($key,$_SESSION['ID_Modele']));
            }
        }
        echo "<p>En ajoutant le bâtiment \"".$_POST['add_batiment']."\", vous avez inclus "
                .$nb." nouveau(x) équipement(s) au modèle.";
        echo "<meta http-equiv='refresh' content='5'>"; // 5s, obliger de refresh / reset car ça se fait mal
    } else{ echo "<meta http-equiv='refresh' content='0'>"; // Obliger de refresh / reset car ça se fait mal 
    }

}

if(isset($_POST['change_nom'])){
    $nom = htmlspecialchars($_POST['new_nom']);
    $requete = $db->prepare('UPDATE MODELE_RONDE SET NOM = ? WHERE ID = ?');
    $requete->execute(array($nom,$_SESSION['ID_Modele']));
    echo "<p>Le nom du modèle a bien été modifié</p>";
    echo "<meta http-equiv='refresh' content='1'>";
}