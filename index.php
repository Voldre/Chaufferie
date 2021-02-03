<?php

// Ctrl + F5 pour rafraichir la page avec les propriétés CSS


/*
120km a 60km/h
90km à 80km/h

Autrement dit, on roule 120/(120+90) (57.1%) du temps à 60km/h et (42.9%) 90/(90+120) % du temps à 80km/h

V moyenne = 120/(120+90)  *60    +   90/(90+120)    * 80 = 34.28 +  34.28
*/

/*

2 premiers mois : +20%
pdt 3 mois : -30%
puis 6e mois : +10%

fluctuation moyenne = +x ou -x % étalé sur 6 mois.

((1*1.2^2)*0.7^3)*1.1 = 54.33%

racine sixième de 54.33%

= 90.3%

Soit -9.67% par mois en moyenne, ou *90.3%

Voilà vic, logique que racine d'un truc entre 0 et 1 donne un nombre plus gros, ex : racine(0.25) = 0.5
*/

/*
100F/300F : 

*/


session_start();

include("header.php");

// Récupération de tous les modèles de rondes
$reponse = $db->query('SELECT ID, NOM FROM MODELE_RONDE');
while($data = $reponse->fetch()){
    $modeles_rondes[$data['ID']] = $data['NOM'];
}
$reponse->closeCursor();

?>

<div class="main">

    <div class="main">

    <h1>Tableau de bord : Responsable Chaufferie</h1>

    <?php 
    if(!isset($_POST['IMMO_modif'])){
        echo "<h2>Vous pouvez créer un équipement :</h2>";
    }
    else{ 
        
        try{
        $requete = $db->prepare('SELECT * FROM EQPT WHERE ID = ?');
        $requete->execute(array($_POST['IMMO_modif']));
        $data = $requete->fetch();
        $requete->closeCursor();
        echo "<h2>Vous pouvez modifier l'équipement N°".$data['ID']." de type \"".$data['TYPE']."\"<br/></h2>"; 
        ?>
        <form method="post" action="">
        <p>Pour supprimer l'équipement, cliquez sur le bouton suivant :<input type="submit" value="Supprimer" name="supprimer"/></p>
            <h5>Attention, si vous le supprimez par erreur vous devrez le recréer</h5>
        </form>
        <?php
        }catch(Exception $e){
            echo $e->getMessage();
            echo "<br/><p>L'équipement N°".$_POST['IMMO_modif']." n'existe pas</p>";
        }
    } 
    ?>

    <form method="post" action="equipement.php">
    <p>Type équipement :<select name="type_eqpt">
    <option value="sel">Adoucisseur sel</option>
    <option value="saumure">Adoucisseur saumure</option>
    <option value="chaudiere">Chaudière</option>
    <option value="pompe">Pompe</option>
    <!--<option value="valeur3">Valeur 3</option>-->
    </select></p>

    <?php
    if(!isset($_POST['IMMO_modif'])){
        ?>
        <p>Bâtiment de l'équipement :<input type="text" name="batiment_eqpt" placeholder="8B..."/>
        <p>Emplacement/Localisation de l'équipement :<input type="text" name="local_eqpt" placeholder="Local 3B-64..."/>
        <p>IMMO :<input type="number" name="IMMO" placeholder="110757..." />
        <br/>
        <input type="submit" name="new_eqpt" value="Créer mon équipement"/>
        <?php
    }else{ 
        ?>    
        <p>Bâtiment de l'équipement :<input type="text" name="batiment_eqpt" placeholder="8B..." value="<?= $data['BATIMENT']?>" />
        <p>Emplacement/Localisation de l'équipement :<input type="text" name="local_eqpt" placeholder="Local 3B-64..."  value="<?= $data['LOCAL']?>" />
        
        <input type="hidden" name="IMMO" value=<?= $_POST['IMMO_modif']?> />
        <br/> <?php
        echo "<input type=\"submit\" name=\"change_param_eqpt\" value=\"Modifier mon équipement\"/>"; } 
    ?>

    </form>

    <!-- <h4>Vous pouvez créer un type de mesure :</h4>
        On ne crée pas de "type" de mesures. C'est moi qui les prédéfinis dans une liste déroulante
        En effet, on ne crée pas "souvent" de nouvelles mesures, donc ça, c'est à la mano, y a pas une table de données consacrée à ça
        Ex : TH, Compteur Eau, Saumure, Sel, Propreté, ...
    -->


    <h3>Pour modifier (ou supprimer) un équipement, veuillez saisir son numéro IMMO :</h3>

    <form method="post">
        <input type="number" name="IMMO_modif" placeholder="110757..." />
        <input type="submit" name="modif_eqpt" value="Modifier mon équipement" />
    </form>


    <h3>Pour ajouter (ou supprimer) des mesures à un équipement, veuillez saisir son numéro IMMO :</h3>

    <form method="post" action="equipement.php">
        <input type="number" name="IMMO" placeholder="110757..." />
        <input type="submit" name="modif_eqpt" value="Modifier les mesures de mon équipement" />
    </form>




<br/><br/>




    <h2>Vous pouvez créer ou modifier un modèle de rondes</h2>
    <form method="post" action="ronde.php">
        <p>Je crée un modèle de rondes s'appelant ...<input type="texte" name="nom_modele_rondes" placeholder="Rondes B8 Adou..." />
        <input type="submit" name="new_modele_rondes" value="Créer un modèle de rondes" />
    </form>
    <form method="post" action="ronde.php">
    <p>J'ouvre le modèle de rondes suivant...<select name="modele_rondes">
        <?php 
        foreach($modeles_rondes as $key => $value){
            // On enregistre le numéro de la ronde en option, et affiche son nom
            echo "<option value=\"".$key."\">".$value."</option>";
        } ?>
    </select></p>
    <input type="submit" value="Ouvrir le modèle de rondes"/>
    </form>

</div>

<h4>Vous pouvez réaliser une ronde</h4>

<p>Choisissez le modèle de rondes que vous souhaitez effectuer...</p>




</body>
</html>