<?php
   //richiamo pagina per accesso con utente e password
   //require ('access.php');
   //richiamo pagina con configurazione db e accesso ad esso
   require ('config.php');
   //Parametri di connessione al  DB mysql
   $verb = mysql_connect($hostname,$db_user,$db_pass) or die ("Non riesco a contattare il server");
   mysql_select_db($database) or die ("Non riesco a connettermi al DB");
   //Stili e intestazione pagina

   echo'
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    ';

   echo '<head>
	    <meta http-equiv="content-type" content="text/html; charset=windows-1252"></meta>
	    <title>XBMC libviewer</title>
            <style type="text/css">
	    .centrato{			text-align:center;}
            .normale{ 			font-size:11px;
		       			font-family:monospace;}
            .titolo{ 			font-size:11px;
					font-family:monospace; }
	    .big{			font-size:13px;
					font-family:monospace; }
	    a:link { 			color:black;
					font-size:11px;
					font-family:monospace; }
	    a:visited { 		color:black;
					font-size:11px;
					font-family:monospace; }
	    .path_txt span {		display:none;}
            .path_txt:hover span {	display:table;
            				table-layout:fixed;
	    				font-size:11px;
	    				color:grey;
		        		width:400; }
            </style>
         </head>';
   //Leggo il db e creo la tabella
   echo '<body>';
   echo '<table style="width:500px;">
   ';
   //creo i link in alto alla tabella alle iniziali dei film
   $elenco = array_merge(range('A', 'Z'), range('0', '9'));
   echo "<tr><td colspan = '2' style='border: 1px dashed black; border-collapse:collapse;' class='normale'>";
   echo '<img src="mozzomicro.gif" alt="Logo"></img><b> M0220 XBMC libviewer</b>
   <br /><a href="films.php">  Films</a>
   <b><a href="series.php">* Serie TV</a></b>
   <a href="?action=logout">ESCI X</a><br />';
   if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=ultime_serie; };
   echo "<br /><p class='centrato'>Stai visualizzando:";
   if ($page == "ultime_serie") {
   echo "<b>Ultimi Episodi Aggiunti</b>";
   }
   else {
   echo "<b>".$page."</b>";
   };
   echo "<br />";
   foreach ($elenco as $i) {
            if ($i=='0') {
            echo "<br />";
            };
            echo '<a href="series.php?page='.$i.'">'.$i.' </a>';
   };
   echo "  <a href='series.php?page=ultime_serie'>Ultimi Episodi Aggiunti</a></p>";
   echo "</td></tr>";
   //Vedo in quale pagina sono e seleziono i record che mi interessano, se ho cliccato su ultime serie faccio un'altra query   
   if ($page != 'ultime_serie') {
   $result = mysql_query("SELECT * FROM tvshow WHERE c00 LIKE '".$page."%' OR c00 LIKE '".strtolower($page)."%' ORDER BY c00 ASC",$verb);
   while ($row_proz = mysql_fetch_array($result))
     {
     $img = $row_proz['c06'];
     list($imgpart1, $imgpart2) = explode('http://thetvdb.com/banners/posters/', $img);
     list($imgpart2) = explode('</thumb', $imgpart2);
     $imgpart2 = 'http://thetvdb.com/banners/posters/'.$imgpart2;
     $trailer = $row_proz['c19'];
     list($trailerpart1, $trailerpart2) = explode('videoid=', $trailer);
     $votostr = substr($row_proz['c04'], 0, 1);
     $votostrreale = substr($row_proz['c04'], 0, 3);
     $votostr = "<b>".$votostrreale."</b>";
     //riempio la tabella con i film prelevati tramite query, sostituendo original con w32 faccio in modo che le locandine
     //visualizzate siano piccole, se ci clicco le linco a quelle grandi
     echo '
                    <tr>
                        <td class="normale" style="width: 100px; border: 1px dashed black; border-collapse:collapse;"><a href="'.$imgpart2.'" target="_blank" title="Clicca per visualizzare la locandina in formato grande"><img src="'.str_replace('banners/','banners/_cache/',$imgpart2).'" width="100" height="150" alt="'.$imgpart2.'"></img></a>
                        </td>
                        <td class="normale" style="width: 400px; display: inline-block; border: 1px dashed black; border-collapse:collapse;"><p><b>'.$row_proz['c00'].'</b><br />
                           Genere: '.$row_proz['c08'].'<br />
			   Voto: '.$votostr.'<br />
                           '.$row_proz['c01'].'<br /><br />
        		   '.$row_proz['c22'];
			   //trovo tutti gli episodi dalla tabella episode che appartengono a quella serie
			   $result_episode = mysql_query("SELECT * FROM  episode  WHERE ".$row_proz['idShow']." = idShow ORDER BY c12 *1, c13 *1",$verb);
                           while ($row_proz_episode = mysql_fetch_array($result_episode))
			     {
			     //reperisco le info del titolo dalla tabella episodeview
			     echo '<span class="path_txt">';
			     $result_episodeview = mysql_query("SELECT * FROM  episodeview  WHERE ".$row_proz_episode['idEpisode']." = idEpisode",$verb);//
			     $row_proz_episodeview = mysql_fetch_array($result_episodeview);
			     echo '<a href="series.php?page='.$row_proz['c00'].'">'.$row_proz['c00'].'</a> ';
    			     echo $row_proz_episode['c12'].'x'.$row_proz_episode['c13'].'-'.$row_proz_episode['c00'].'
                             <span>'.
                             $row_proz_episodeview['strPath'].$row_proz_episodeview['strFileName'].'
                             </span></span><br />';
			     }
      echo '		   <br /><br /></p>
			</td>
                    </tr>'  ;
     }
   }
   else {
   echo '<tr><td style="width: 500px; display: inline-block; border:1px dashed black; border-collapse:collapse;"  class="normale">';
   $result_episodeview = mysql_query("SELECT * FROM episodeview ORDER BY dateAdded DESC LIMIT 50",$verb);
   while ($row_proz_episodeview = mysql_fetch_array($result_episodeview))
    {
     $result = mysql_query("SELECT * FROM tvshow WHERE ".$row_proz_episodeview['idShow']." = idShow",$verb);
     $row_proz = mysql_fetch_array($result);
     echo '<a href="series.php?page='.$row_proz['c00'].'">'.$row_proz['c00'].'</a> ';
     echo $row_proz_episodeview['c12'].'x'.$row_proz_episodeview['c13'].'-'.$row_proz_episodeview['c00'].'
     <br />';
    };
   echo "</td></tr>";
   };
   mysql_free_result($result);
   echo '</table>';
   //icona w3c validator
   echo '<p>
   <a href="http://validator.w3.org/check?uri=referer"><img
     src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
   </p>';
   //javascript per l'opzione leggi tutto
   echo "
   <script type='text/javascript' src='jquery.min.js'></script>
   <script type='text/javascript' src='readmore.js'></script>
   <script type='text/javascript'>
   $('#info').readmore({
     maxHeight: 150,
     afterToggle: function(trigger, element, expanded) {
       if(! expanded) {
         $('html, body').animate( { scrollTop: element.offset().top }, {duration: 100 } );
       }
     }
   });
   $('p').readmore({maxHeight: 135});
   </script>
   </body>
   </html>";
   mysql_close($verb);
?>
