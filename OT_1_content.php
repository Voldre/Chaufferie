<?php

$liste_RP_EPI = array();
$liste_sur_OT_RP_EPI = array(); // A bien définir au cas où l'OT soit nouveau donc aucun RP_EPI sur l'OT

$reponse = $db->query('SELECT * FROM Risques_Precautions_EPI');


while($data = $reponse->fetch() ){
$liste_RP_EPI[$data['ID']] = $data;
}
$reponse->closeCursor();
        
        // Récupération des ID utilisés par l'OT
$requete = $db->prepare('SELECT ID_RP_EPI FROM OT_RP_EPI WHERE ID_OT = ?');
$requete->execute(array( $_SESSION['OT_ID']));

while($data = $requete->fetch() ){
$liste_sur_OT_RP_EPI[] = $data['ID_RP_EPI'];
}


$requete->closeCursor();
?>



<h3>OT N°<?= $_SESSION['OT_ID']?></h3>

<?php $reponse = $db->prepare('SELECT DESIGNATION FROM OT WHERE ID = ?');
        $reponse->execute(array($_SESSION['OT_ID']));
        $data = $reponse->fetch();
        $reponse->closeCursor(); ?>
<h4>Ordre de travail : <?= $data['DESIGNATION'] ?></h4>



<h4>N° d'infirmerie : 1010 &nbsp;&nbsp;&nbsp; N° pompier : 18  &nbsp;&nbsp;&nbsp; 
    Poste surveillance : 34452 &nbsp;&nbsp;&nbsp;   Accueil : 112 &nbsp;&nbsp;&nbsp;  SAMU : 15</h4>



    <!--SAISIE DES DIFFERENTS PARAMETRES DE SECURITE-->

    <h3 style="text-align : center;">Evaluation sécurité pour autorisation d'intervention</h3>

        <div class="main">

<form action="OT_1.php" method="post"> <!-- Ajout de action pour bien MàJ les cases cochées -->

<?php       // SAISIE DES QUESTIONS LISTE

$liste_tableau = ["Proprio_Inter","Inter"];


foreach($liste_tableau as $cible_global){

    if($cible_global == "Proprio_Inter"){
        echo "<h4>Propriétaire de l'équipement et intervenants</h4> 
        <h5>- - Chaussures de sécurité obligatoires - -</h5>";
    } else if($cible_global =="Inter"){
        echo "<h4>Intervenants</h4> 
        <h5>- - Vêtements couvrants obligatoires - -</h5>";
    }

    echo "<div class=\"liste0\">";

    $liste_types = array("risques" => "risque","precautions" => "precaution","epi" => "epi");

    // Définir les emplacements des espaces pour que les lignes correspondent :
    $liste_spaces = array("Anoxie", "Travail en hauteur","Détection azote à proximité","Balisage zone","Oxygènomètre","Protection travailleur isolé");

    // Liste des cases en dessous des quelles il faut tracer une ligne
    $liste_blocs = array(1003,1005,1010,1012);

    foreach($liste_types as $key_type => $value_type){ // Optimisaton pour 1 boucle pour chaque type
   ?>
    <div class="liste">
        <h4><b><?= strtoupper($key_type) ?></b></h4>
        <?php
                                // <=> ID
        foreach($liste_RP_EPI as $key => $value){   // Optimisation pour 1 boucle pour chaque paramètre (check)
            foreach($value as $key2 => $value2){    // <=> "nom champ, valeur champ"
                if($key2 == "Type" && $value2 == $key_type && $liste_RP_EPI[$key]['Cible'] == $cible_global){


                    if($key_type == "epi" && $cible_global == "Inter"){
                        break; // On traite séparément cette colonne
                        // Donc on sort pour pas la traiter normalement
                    }
                        // Si l'ID actuel est DANS LISTE d'ID rattachés à l'OT, alors
                                                                    // Si pas dans la colonne EPI INTER (car particulier)
                        if( in_array($key,$liste_sur_OT_RP_EPI) ){ 
                            echo "<p><b><input type=\"checkbox\" name=$key value=1 checked >"; // checked by default!
                        } else{ echo "<p><input type=\"checkbox\" name=$key value=1 >"; 
                        }
                        echo "<label>".$liste_RP_EPI[$key]['NOM']."</label></b></p>";

                        if(in_array($liste_RP_EPI[$key]['NOM'],$liste_spaces)){ 
                            // Si le nom de du RP_EPI apparaît dedans, alors :
                            echo "<p> &nbsp; </p>";
                            if($liste_RP_EPI[$key]['NOM'] == "Balisage zone"){
                                for($i=1; $i <=3; $i++){
                                    echo "<p> &nbsp; </p>"; // *3 +1
                                }
                            }
                            if($liste_RP_EPI[$key]['NOM'] == "Protection travailleur isolé"){
                                for($i=1; $i <=4; $i++){
                                    echo "<p> &nbsp; </p>"; // *4 +1
                                }
                            }
                        }
                            // Tracer une ligne?
                        if( in_array($liste_RP_EPI[$key]['ID'],$liste_blocs) && $cible_global == "Inter" ){
                            echo "________________________________";
                        }

                    }
                }
            } 
            // Si on est dans la case EPI INTER, on la remplie à la main
        if($key_type == "epi" && $cible_global == "Inter"){

            for($i=0; $i <= 2; $i++){

                if($i != 0){    // Si on a est pas dans la colonne 1, on ajoute Visière puis Casque / Casquette
                    echo "________________________________";
                }

                if( in_array(1017,$liste_sur_OT_RP_EPI) ){ 
                    echo "<p><label><input type=\"checkbox\" name=1017 value=1 checked >"; // checked by default!
                } else{ echo "<p><label><input type=\"checkbox\" name=1017 value=1 >"; 
                }
                echo $liste_RP_EPI[1017]['NOM']."</label></p>";

                if( in_array(1018,$liste_sur_OT_RP_EPI) ){ 
                    echo "<p><label><input type=\"checkbox\" name=1018 value=1 checked >"; // checked by default!
                } else{ echo "<p><label><input type=\"checkbox\" name=1018 value=1 >"; 
                }
                echo $liste_RP_EPI[1018]['NOM']."</label></p>";


                if($i != 0){    // Si on a est pas dans la colonne 1, on ajoute Visière puis Casque / Casquette
                    if( in_array(1018+$i,$liste_sur_OT_RP_EPI) ){ 
                        echo "<p><label><input type=\"checkbox\" name=".(1018+$i)." value=1 checked >"; // checked by default!
                    } else{ echo "<p><label><input type=\"checkbox\" name=".(1018+$i)." value=1 >"; 
                    }
                    echo $liste_RP_EPI[1018+$i]['NOM']."</label></p>";
     
                }
            }

        }

    echo "</div>"; // de <div class="liste">
    } // Fin du foreach des 3 types 

 echo "</div><br/><br/>"; // Saut de ligne pour la transition des 2 tableaux (des 2 cibles)

} // Fin bloc foreach de cible_global 


    // SAISIE DES QUESTIONS SEPAREES
    
$liste_reponses_OT = array();


$reponse = $db->prepare('SELECT AUTORISATIONS_PARTICULIERES AS Autoris, PLAN_PREV AS PP, RISQUES_BIOLOGIQUES AS RB,
                        RISQUES_BIOLOGIQUES_DESIGNATION AS RBD, RISQUES_BIOLOGIQUES_ACCES AS RBA
                             FROM OT WHERE ID = ?');
$reponse->execute(array($_SESSION['OT_ID']));

$data = $reponse->fetch();
$reponse->closeCursor();

     //print_r($data);


    
?>      <div class="page_break"></div>  <!-- MàJ Janvier 2021 : -->
        <br/> <!-- Espaces utile car l'impression PDF donne saut de page -->

<h3>Autorisations particulières :
    <?php if($data['Autoris'] == 1){ $valueOui = 'checked'; $valueNon = null; }
          else if($data['Autoris'] == 0){ $valueOui = null; $valueNon = 'checked'; }
          else{ $valueOui= null; $valueNon = null; } // Si la case n'a jamais été cochée
    ?> 
        <input type="radio" name="AUTORISATIONS_PARTICULIERES" value=1 <?=$valueOui?> > Oui
        <input type="radio" name="AUTORISATIONS_PARTICULIERES" value=0 <?=$valueNon?> > Non
</h3>

<p><br/> Si oui :</p>
    <?php if($data['PP'] == "particulier"){ $valueOui = 'checked'; $valueNon = null; }
            else if($data['PP'] == "annuel"){ $valueOui = null; $valueNon = 'checked'; }
            else{ $valueOui= null; $valueNon = null; } // Si la case n'a jamais été cochée
        ?> 
<label>
    Plan de prévention particulier....<input type="radio" name="PLAN_PREV" value="particulier"<?=$valueOui?> >
    Plan de prévention annuel....<input type="radio" name="PLAN_PREV" value="annuel" <?=$valueNon?> >
</label>

<br/><br/>

<h3>Risques biologiques :</h3>      <br/> <!-- MàJ Janvier 2021, saut de ligne pour aérer -->
    <?php if($data['RB'] == 1){ $valueOui = 'checked'; $valueNon = null; }
            else if($data['RB'] == 0){ $valueOui = null; $valueNon = 'checked'; }
            else{ $valueOui= null; $valueNon = null; } // Si la case n'a jamais été cochée
    ?> 
    <label>Autre bâtiment visité dans la journée (risque de contamination croisée) :<input type="radio" name="RISQUES_BIOLOGIQUES" value=1 <?=$valueOui?> >Oui  
                                                                                <input type="radio" name="RISQUES_BIOLOGIQUES" value=0 <?=$valueNon?> >Non </br>
       Si oui, lequel :<input type="text" name="RISQUES_BIOLOGIQUES_DESIGNATION" value=<?php echo $data['RBD']; // Même si null ?> >.

       <?php if($data['RBA'] == 1){ $valueOui = 'checked'; $valueNon = null; }
            else if($data['RBA'] == 0){ $valueOui = null; $valueNon = 'checked'; }
            else{ $valueOui= ""; $valueNon = ""; } // Si la case n'a jamais été cochée
        ?> 
        Accès autorisé :<input type="radio" name="RISQUES_BIOLOGIQUES_ACCES" value=1 <?=$valueOui?> >Oui  
                        <input type="radio" name="RISQUES_BIOLOGIQUES_ACCES" value=0 <?=$valueNon?> >Non </br>
    </label>

<br/><br/>


<?php
// Récupération des signatures déjà faites :
          
$requete = $db->prepare('SELECT SIGNATURE_PROPRIO_DEBUT AS SPD, SIGNATURE_INTERVENANT_DEBUT AS SI FROM OT WHERE ID = ?');
$requete->execute(array($_SESSION['OT_ID']));

$data = $requete->fetch();

if(isset($data['SPD']) OR isset($data['SI'])){
    echo "<p class=\"red\">Les réponses ne peuvent plus être mises à jour, le document a déjà été signé par au moins une personne.</p>";
    echo "<br/><br/><br/>";
}else{   ?>
<input type="submit" name="questions" value="Mettre à jour toutes les réponses">  

<?php   }   ?>

</form> 



<?php  
        
// QUESTIONS

if(isset($_POST['questions'])){

    // Remplissage de la table OT

    if(!isset($_POST['AUTORISATIONS_PARTICULIERES']) && !isset($_POST['RISQUES_BIOLOGIQUES'])){
        $_SESSION['message'][] = "<p class=\red\">Erreur, l'autorisation particulière ou le risque biologique n'a pas été coché (oui ou non)</p>";
    }
    else{
        if($_POST['AUTORISATIONS_PARTICULIERES'] == 0){
            $_POST['PLAN_PREV'] = null;
        }

        if($_POST['RISQUES_BIOLOGIQUES'] == 0){
            $_POST['RISQUES_BIOLOGIQUES_DESIGNATION'] = null;
            $_POST['RISQUES_BIOLOGIQUES_ACCES'] = null;
        }
        else{
            if(strlen($_POST['RISQUES_BIOLOGIQUES_DESIGNATION']) < 1){
                $_POST['RISQUES_BIOLOGIQUES_DESIGNATION'] = "A définir";
            }
            else{
                $_POST['RISQUES_BIOLOGIQUES_DESIGNATION'] = htmlspecialchars($_POST['RISQUES_BIOLOGIQUES_DESIGNATION']);
            }
        }

        $requete = $db->prepare('UPDATE OT SET AUTORISATIONS_PARTICULIERES = ?, PLAN_PREV = ?, RISQUES_BIOLOGIQUES = ? ,
                            RISQUES_BIOLOGIQUES_DESIGNATION = ? , RISQUES_BIOLOGIQUES_ACCES = ? WHERE  ID = ?');

        $requete->execute(array($_POST['AUTORISATIONS_PARTICULIERES'],  $_POST['PLAN_PREV'],    $_POST['RISQUES_BIOLOGIQUES'],
                            $_POST['RISQUES_BIOLOGIQUES_DESIGNATION'], $_POST['RISQUES_BIOLOGIQUES_ACCES'], $_SESSION['OT_ID']));
        $requete->closeCursor();

        $_SESSION['message'][] = "<p>L'OT a bien été complété avec les questions saisies!</p>";
    }

    // Remplissage de la table OT_RP_EPI

                // On vide la table pour supprimer les cases éventuellements décochés
    $reponse = $db->prepare('DELETE FROM OT_RP_EPI WHERE ID_OT = ?');
    $reponse->execute(array($_SESSION['OT_ID']));
    $reponse->closeCursor();

    
    // On insère toutes les cases cochés dans la table

    foreach($_POST as $key => $value){
        if( (int) $key != 0){ // Si la key en nombre est différente de 0, c'est qu'il s'agit bien d'un nombre

            $requete = $db->prepare('INSERT INTO OT_RP_EPI(ID_OT, ID_RP_EPI) VALUES(?,?)');
            $requete->execute(array( $_SESSION['OT_ID'], $key )); // On envoi les ID en int
            $requete->closeCursor();

        }
    }

        // Si une case "Aucun Truc" a été coché, on retire les liaisons qui auraient pu être cochées
            // Ex : je coche "Aucune précaution" pour EPI, et "Masque adapté" par erreur, alors "Masque adapté" 
            // ne sera pas ajouté à la liste. Car "Aucune précaution" est prioritaire et a été coché

        $liste_aucun = array(1000 => "risques", 1001 => "precautions", 1002 => "epi");
            // Liste des 3 catégories et définition du numéro ID correspondant à l'absence "no_type".
            
            // Optimisation du code en réduisant les 3 requêtes No_risques, No_precautions et No_epi en une seule :
        foreach($liste_aucun as $key => $value){
            //Pas "in_array()" quand on recherche des KEY, in_array = des valeurs! Donc "array_key_exists()"
            if(array_key_exists($key,$_POST)){ // Si dans les valeurs des ID envoyés, 1000, 10001 ou/et 1002 apparaissent :
                $_SESSION['message'][] = "<p>Vous avez cochée la case \"Aucun(e) $value\", aucun(e) $value ne sera donc saisi dans l'OT.</p>";
                $requete = $db->prepare("DELETE T1 FROM OT_RP_EPI As T1, Risques_Precautions_EPI As T2
                                            WHERE ID_OT = ? AND ID_RP_EPI = T2.ID
                                                AND T2.Type = ?      AND  ID_RP_EPI != ?");
                $requete->execute(array($_SESSION['OT_ID'], $value, $key)); // On supprime tous les ID du type 
                $requete->closeCursor();                               // sauf la case "Aucun de ce type", normal
            }
        }

    //  print_r($_POST);

    // Vérification qu'au moins une case a été cochée par catégorie

    $isCheck = true;
    $nb_categories = 0;

    $requete = $db->prepare('SELECT Type, COUNT(*) AS Count FROM OT_RP_EPI, Risques_Precautions_EPI WHERE ID_RP_EPI = ID AND ID_OT = ? GROUP BY Type');
        // COUNT(COUNT(*)): "Je compte le nombre de groupement fait
            // * = Les items,   COUNT(*) = Je compte les items par groupe (car GROUP BY)
            // Donc COUNT(COUNT(*)) = Je compte les groupes
        


    $requete->execute(array( $_SESSION['OT_ID']));


    while($data = $requete->fetch()){
        $nb_categories++;    
        }
    if($nb_categories < 3){
        $isCheck = false;
    }

    if(!$isCheck){ // Si une catégorie contient 0 élément coché
        
        $_SESSION['message'][] = "<p class=\"red\">Vous n'avez pas cochée une seule case dans ".(3-$nb_categories)." catégorie(s) parmi \"Risques\", \"Precautions\" et \"EPI\".</p>";

        // On vide la table car la saisie comporte des erreurs
        $reponse = $db->prepare('DELETE FROM OT_RP_EPI WHERE ID_OT = ?');
        $reponse->execute(array($_SESSION['OT_ID']));
        $reponse->closeCursor();

        $_SESSION['message'][]  = "<p class=\"red\">Veuillez ressaisir vos données dans le formulaire en cochant au moins une case par catégorie (Risques, Précautions, EPI).</p>";
    }
    else{ // Si toutes les catégories possèdent au moins un paramètre (de coché), alors
        $_SESSION['message'][] = "<p>L'OT a bien été associé à tous les risques, précautions et EPI sélectionnés.</p>";
    }

    echo "<meta http-equiv='refresh' content='0'>"; // Obliger de refresh / reset car ça se fait mal
}
?>