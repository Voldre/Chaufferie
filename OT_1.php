
<?php

//Pas "in_array()" quand on recherche des KEY, in_array = des valeurs! Donc "array_key_exists()"

/*
    ATTRIBUTION D'UN CHECK PAR DEFAUT SELON LA VALEUR ENREGISTREE PRECEDEMMENT :

                        'check' entre apostrophe évite de mettre ça en gros string
                        pour bien sortir un <input type=... name=... value=.... checked >

    <?php if($data['Autoris'] == 1){ $valueOui = 'checked'; $valueNon = null; }
          else if($data['Autoris'] == 0){ $valueOui = null; $valueNon = 'checked'; }
          else{ $valueOui= ""; $valueNon = ""; } // Si la case n'a jamais été cochée
    ?> 
        <input type="radio" name="AUTORISATIONS_PARTICULIERES" value=1 <?=$valueOui?> > Oui
        <input type="radio" name="AUTORISATIONS_PARTICULIERES" value=0 <?=$valueNon?> > Non

Refresh obligatoire dans le code PHP (où on veut) :   echo "<meta http-equiv='refresh' content='0'>";

*/

session_start();

require("header.php");


require("OT_1_content.php");

// Page suivante ?

$requete = $db->prepare('SELECT Type, COUNT(*) AS Count FROM OT_RP_EPI, Risques_Precautions_EPI WHERE ID_RP_EPI = ID AND ID_OT = ? GROUP BY Type');
$requete->execute(array( $_SESSION['OT_ID']));
$nb_categories = 0;
while($data = $requete->fetch()){
$nb_categories++;    
}
if($nb_categories >= 3){
echo "<br/><form action=\"OT_2.php\"> <p><input type=\"submit\" value =\"Page suivante\" > </p></form>";
}

    ?>      </div>  <!-- Fin du bloc de questions, passage au bloc de signatures -->


</body>
</html>