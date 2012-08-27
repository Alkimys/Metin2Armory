<p>
    <h2>Rechercher un joueur</h2>
    <form action="index.php?p=character_search" method="post">
        <label for="perso">Trouver un joueur :</label><br />
        <input type="text" size="30" name="perso" />
        <br /><br />
        <center><input type="submit" class="button" value="Trouver" /></center>
    </form>
</p>

<?php

if(!empty($_POST['perso']) or !empty($_GET['n']))  //Si et seulement si on a une requ�te ou un changement de page
{

    //Soit on lance la recherche, soit on navigue dedans
    if(isset($_POST['perso'])) {$perso=$_POST['perso'];}
    else {$perso=$_GET['n'];}
    
    if(!empty($perso))
    {$name = '%'.mysql_real_escape_string($perso).'%';}
    else {$name = "%"; }
    
    mysql_select_db('player'); // On s�l�ctionne la base de donn�es
    $retour = mysql_query("SELECT COUNT(*) AS nb_perso FROM player WHERE name LIKE '$name' AND name NOT IN(SELECT mName FROM common.gmlist)"); // On recherche le nombre de r�sultats
    $donnees = mysql_fetch_array($retour); // On l'affecte � un array.
    
    //On donne le Nombre total de joueurs
    $total = $donnees['nb_perso'];
    //On donne le Rang � partir du quel on affiche le classement
    if(isset($_GET['r']) and $_GET['r']>0){$rang=$_GET['r'];} else {$rang=0;}
    //On donne le Nombre de joueurs � afficher dans le classement
    $nb_persos_page = 2;
    //On donne la variable pour la Page suivante
    $next_rang=$rang+$nb_persos_page;
    //On donne la variable pour la Page pr�c�dente
    $prev_rang=$rang-$nb_persos_page;

    //On refait une requ�te, celle-ci sans demander un nombre total mais pour demander des valeurs.
    $requette2 = ("SELECT * FROM player WHERE name LIKE '$name' AND name NOT IN(SELECT mName FROM common.gmlist) ORDER By name DESC LIMIT $rang, $nb_persos_page"); 
    $sql2 = mysql_query($requette2) or die('Erreur SQL !<br />'.$requette2.'<br />'.mysql_error()); 
echo '
<table align="center">
  <tr>
    <th>Rang</th>
    <th>Pseudo</th>
    <th>Level</th>
    <th>Acc&egrave;s Page Perso</th>
  </tr>';

    while(($data = mysql_fetch_array($sql2))) //Tant qu'il y a des lignes dans la requ�te, dans la limite du nombre total de joueur et de celui par page.
        {
            $rang++;//On incr�mente le nombre de rang
            $name = $data['name']; //On demande le nom
            $level = $data['level']; //On demande le niveau
    //On affiche la ligne du tableau
    echo "
          <tr>
            <td>$rang</td>
            <td>$name</td>
            <td>$level</td>
            <td><a href='index.php?p=character&name=$name'>> ></a></td>
          </tr>
    ";

        }
  //Si le total est �gal � 0, on note qu'il n'y a pas de r�sultats.
  if ($total == '0') {
    echo'
      <tr>
        <td colspan="4"><div align="center">Aucun r&eacute;sultat </div></td>
      </tr>
    ';
  }
  echo '
</table>
<div align="center">
  ';
if ($prev_rang >= '0') { //On affiche la page pr�c�dente si (le rang actuellement affich� - le nombre de persos par page) est sup�rieur ou �gal � 0.
echo "<a href='index.php?p=character_search&amp;r=$prev_rang&amp;n=$perso'>Page pr&eacute;c&eacute;dente</a> |";
}

if ($next_rang < $total) { //On affiche la page suivante si le total est sup�rieur au rang actuellement affich�
echo"| <a href='index.php?p=character_search&amp;r=$next_rang&amp;n=$perso'>Page suivante</a>";
}

    if ($total == '0') { //Si le total est �gal � 0
echo"</div>"; }
elseif ($total == '1') { //Si il est �gal � 1
echo"<p><center><em style='font-family:Stardos Stencil;'>Total : 1 joueur trouv&eacute;.</em></center></p>
</div>"; }
else { //Sinon
  echo"<p><center><em style='font-family:Stardos Stencil;'>Total : $total joueur(s) trouv&eacute;(s).</em></center></p>
</div>"; }

}//On ferme le 'si' du d�but
?>