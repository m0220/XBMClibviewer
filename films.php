<?php
   //richiamo pagina per accesso con utente e password
   //require ('access.php');
   //richiamo pagina con configurazione db e accesso ad esso
   require ('config.php');
   //Parametri di connessione al  DB mysql
   $verb = mysql_connect($hostname,$db_user,$db_pass) or die ("Non riesco a contattare il server");
   mysql_select_db($database) or die ("Non riesco a connettermi al DB");
   //Stili e intestazione pagina
   echo                                         '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                                                 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                                                 <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
                                                 <head>
                                                 <meta http-equiv="content-type" content="text/html; charset=windows-1252"></meta>
                                                 <title>XBMC libviewer</title>
                                                 <link href="style.css" rel="stylesheet" type="text/css" />
                                                 </head>
                                                 <body>
                                                 <table style="width:500px;">
                                                 <tr>
                                                 <td colspan = "2" style="border: 1px dashed black; border-collapse:collapse;" class="normale">
                                                 <img src="mozzomicro.gif" alt="Logo"></img><b> M0220 XBMC libviewer</b>
                                                 <br />';
   echo 				        '<b><a href="films.php">*Films</a></b>
   						 <a href="series.php">  Serie TV</a>
   						 <a href="?action=logout">ESCI X</a>
						 <br />';
   $elenco = array_merge(range('A', 'Z'), range('0', '9'));
   if (isset($_GET["page"])) {
     $page = htmlspecialchars($_GET["page"]);
   } elseif (isset($_GET["pageid"])) {
       $pageid = htmlspecialchars($_GET["pageid"]);
       $page = '-1';
   } else {
       $page = 'ultimi_film';
   };
   echo 				       '<br />
						<p class="centrato">Stai visualizzando:';
   if ($page == "ultimi_film") {
     echo 				       '<b>Ultimi Films Aggiunti</b>';
   } elseif ($page == "tutti_film") {
       echo 				       '<b>Elenco di tutti i films</b>';
   } elseif ($page != '-1') {
       echo 				       '<b>'.$page.'</b>';
   }
     else {
       echo 				       '<b>Un signolo film</b>';
   }
   echo 				       '<br />';
   foreach ($elenco as $i) {
     if ($i=='0') {
       echo	 			       '<br />';
     };
     echo 				       '<a href="films.php?page='.$i.'">'.$i.' </a>';
   };
   echo 				       '<a href="films.php?page=ultimi_film">Ultimi Films Aggiunti</a>
						<br />
           					<a href="films.php?page=tutti_film">Elenco di tutti i Films</a>
						</p>
						<br />';
   //Parte suddivisione pagine
   if ($page == 'tutti_film') {
     $x_pag = 100;
     } else {
         $x_pag = 15;
     }
   $num_pag = $_GET['num_pag'];
   if (!$num_pag) {
     $num_pag = 1;
   }
   $first = ($num_pag - 1) * $x_pag;
   $tot_pag = 1;
   //Vedo in quale pagina sono e assegno la query alla variabile $result
   if (($page != 'ultimi_film') and ($page != 'tutti_film') and ($page != '-1')) {
     $result = mysql_query("SELECT * FROM movie WHERE c00 LIKE '".$page."%' OR c00 LIKE '".strtolower($page)."%' ORDER BY c00 ASC LIMIT ".$first.", ".$x_pag."",$verb);
     $tot_pag = ceil((mysql_num_rows(mysql_query("SELECT * FROM movie WHERE c00 LIKE '".$page."%' OR c00 LIKE '".strtolower($page)."%'"))) / $x_pag);
   } elseif ($page == 'ultimi_film') {
       $result = mysql_query("SELECT tabkey.idFile, movie.*, files.dateAdded FROM
       (SELECT movie.idFile FROM movie UNION SELECT files.idFile FROM files)
       AS tabkey LEFT JOIN movie on tabkey.idFile = movie.idFile LEFT JOIN files
       on tabkey.idFile = files.idFile WHERE movie.c00 is not null ORDER BY dateAdded DESC LIMIT 5",$verb);
       $tot_pag = 0;
   } elseif ($page != '-1') {
       $result = mysql_query("SELECT * FROM movie ORDER BY c00 ASC LIMIT ".$first.", ".$x_pag."",$verb);
       $tot_pag = ceil((mysql_num_rows(mysql_query("SELECT * FROM movie"))) / $x_pag);
   } else {
       $result = mysql_query("SELECT * FROM movie WHERE idMovie LIKE '".$pageid."'",$verb);
       $tot_pag = 0;
   }
   echo					       '<p class="centrato">';
   $all_rows = mysql_num_rows($result);
   $all_pages = ceil($all_rows / $x_pag);
   if (($page != 'ultimi_film') and ($tot_pag > 0)){
     echo 'Pagina:';
   } else {
       echo '<br />';
   }
   for($x = 1; $x < $tot_pag+1; $x++){
     if ($x == $num_pag){
       echo 				       '<b>*<a href="films.php?page='.$page.'&amp;num_pag='.$x.'">'.$x.'</a></b>';
     } else {
         echo 				       '<a href="films.php?page='.$page.'&amp;num_pag='.$x.'">'.$x.'</a>';
     }
   }
   echo                                        '</p>
						<p>
                                                <a href="javascript:history.go(-1)">Torna indietro</a>
                                                </p>
                                                </td>
                                                </tr>';
   if ($page != 'tutti_film') {
     while ($row_proz = mysql_fetch_array($result)) {
       $img = $row_proz['c08'];
       echo $row_proz['dateadded'];
       list($imgpart1, $imgpart2) = explode('">http://', $img);
       list($imgpart2) = explode('</thumb', $imgpart2);
       $imgpart2 = 'http://'.$imgpart2;
       $trailer = $row_proz['c19'];
       list($trailerpart1, $trailerpart2) = explode('videoid=', $trailer);
       $votostr = substr($row_proz['c05'], 0, 1);
       $votostrreale = substr($row_proz['c05'], 0, 3);
       $votostr = "<b>".$votostrreale."</b>";
       //riempio la tabella con i film prelevati tramite query, sostituendo original con w32 faccio in modo che le locandine
       //visualizzate siano piccole, se ci clicco le linco a quelle grandi
       echo 				      '<tr>
                        			<td class="normale" style="width: 100px; word-wrap: break-word; word-break: break-all; border: 1px dashed black; border-collapse:collapse;"><a href="'.$imgpart2.'" target="_blank" title="Clicca per visualizzare la locandina in formato grande"><img src="'.str_replace('original','w92',$imgpart2).'" width="100" height="150" alt="'.$imgpart2.'"></img></a>
                        			</td>
                        			<td class="normale" style="width: 400px; display: inline-block; border: 1px dashed black; border-collapse:collapse;"><p><b>'.$row_proz['c00'].'</b>
						<br />
                           			Genere: '.$row_proz['c14']." - Anno: ".$row_proz['c07']." - Regia di: ".$row_proz['c15'].'
						<br />
			   			Voto: '.$votostr.'
						<br />
                           			<a target="_blank" href="https://www.youtube.com/watch?v='.$trailerpart2.'">Trailer film</a>
						<br />
                           			'.$row_proz['c01'].'
						<br />
						<br />
			   			'.$row_proz['c22'].'
						</p>
                        			</td>
                    				</tr>';
     }
   } else {
       echo 				       '<tr>
						<td style="width: 500px; display: inline-block; border:1px dashed black; border-collapse:collapse;"  class="normale">';
       $i = (($num_pag-1)*$x_pag)+1;
       while ($row_proz = mysql_fetch_array($result)) {
         echo 					$i.'. <a href="films.php?pageid='.$row_proz['idMovie'].'">'.$row_proz['c00'].'</a>
						<br />';
         $i++;
       };
       echo 				       '</td>
						</tr>';
   };
   mysql_free_result($result);
   echo 				       '</table>';
   //icona w3c validator
   echo 				       '<p>
    						<a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
   						</p>';
   //javascript per l'opzione leggi tutto
   echo                                        '<script type="text/javascript" src="jquery.min.js"></script>
                                                <script type="text/javascript" src="readmore.js"></script>
                                                <script type="text/javascript">
                                                $("#info").readmore({
                                                maxHeight: 150,
                                                afterToggle: function(trigger, element, expanded) {
                                                if(! expanded) {
                                                $("html, body").animate( { scrollTop: element.offset().top }, {duration: 100 } );
                                                }
                                                }
                                                });
                                                $("p").readmore({maxHeight: 135});
                                                </script>
                                                </body>
                                                </html>';
   mysql_close($verb);
?>
