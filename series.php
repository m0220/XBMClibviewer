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
   echo                                         '<a href="films.php">  Films</a>
                                                 <b><a href="series.php"> *Serie TV</a></b>
                                                 <a href="?action=logout">ESCI X</a>
                                                 <br />';
   $elenco = array_merge(range('A', 'Z'), range('0', '9'));
   if (isset($_GET["page"])) {
       $page = htmlspecialchars($_GET["page"]);
   } elseif (isset($_GET["pageid"])) {
       $pageid = htmlspecialchars($_GET["pageid"]);
       $page = '-1';
   } else {
       $page = 'ultime_serie';
   }
   echo 					'<br />
            					 <p class="centrato">Stai visualizzando:';
   if ($page == "ultime_serie") {
      echo 					'<b>Ultimi Episodi Aggiunti</b>';
   }  elseif ($page == "tutte_serie") {
        echo 					'<b>Elenco di tutte le serie</b>';
   }  elseif ($page != '-1') {
        echo					'<b>'.$page.'</b>';
   }  else {
        echo	 				'<b>Una signola serie</b>';
   }
   echo 					'<br />';
   foreach ($elenco as $i) {
     if ($i=='0') {
       echo 					'<br />';
     };
     echo	 				'<a href="series.php?page='.$i.'">'.$i.' </a>';
   };
   echo 					'<a href="series.php?page=ultime_serie">Ultimi Episodi Aggiunti</a>
						 <br />
           					 <a href="series.php?page=tutte_serie">Elenco di tutte le Serie</a>
						 </p>
						 <br />';
   //Parte suddivisione pagine
   if ($page == 'tutte_serie'){
      $x_pag = 20;
   }  else {
        $x_pag = 20;
   }
   $num_pag = $_GET['num_pag'];
   if (!$num_pag){
      $num_pag = 1;
   }
   $first = ($num_pag - 1) * $x_pag;
   $tot_pag = 1;
   //Vedo in quale pagina sono e seleziono i record che mi interessano, se ho cliccato su ultime serie faccio un'altra query
   if (($page != 'ultime_serie') and ($page != 'tutte_serie')) {
     if ($page != '-1') {
        $result = mysql_query("SELECT * FROM tvshow WHERE c00 LIKE '".$page."%' OR c00 LIKE '".strtolower($page)."%' ORDER BY c00 ASC LIMIT ".$first.", ".$x_pag."",$verb);
        $tot_pag = ceil((mysql_num_rows(mysql_query("SELECT * FROM tvshow WHERE c00 LIKE '".$page."%' OR c00 LIKE '".strtolower($page)."%'")))/$x_pag);
     }  else {
          $result = mysql_query("SELECT * FROM tvshow WHERE idShow LIKE '".$pageid."' LIMIT ".$first.", ".$x_pag."",$verb);
          $tot_pag = 0;
     }
     $all_rows = mysql_num_rows($result);
     $all_pages = ceil($all_rows / $x_pag);
     echo                                      '<p class="centrato">';
     if (($page != 'ultime_serie') and ($tot_pag > 0)){
        echo  					'Pagina:';
     }  else {
          echo                                  '<br />';
     }
     for($x = 1; $x < $tot_pag+1; $x++){
       if ($x == $num_pag){
          echo  				'<b>*<a href="series.php?page='.$page.'&amp;num_pag='.$x.'">'.$x.'</a></b>';
       }  else {
            echo				'<a href="series.php?page='.$page.'&amp;num_pag='.$x.'">'.$x.'</a>';
       }
     }
     echo 					'</p>
						 <p>
						 <a href="javascript:history.go(-1)">Torna indietro</a>
						 </p>
						 </td>
						 </tr>';
     while ($row_proz = mysql_fetch_array($result)) {
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
                        			 <td class="normale" style="width: 100px; word-wrap: break-word; word-break: break-all; border: 1px dashed black; border-collapse:collapse;">
                                                 <a href="'.$imgpart2.'" target="_blank" title="Clicca per visualizzare la locandina in formato grande"><img src="'.str_replace('banners/','banners/_cache/',$imgpart2).'" width="100" height="150" alt="'.$imgpart2.'"></img></a>
                        			 </td>
                        			 <td class="normale" style="width: 400px; display: inline-block; border: 1px dashed black; border-collapse:collapse;">
						 <p>
						 <b>'.$row_proz['c00'].'</b>
  						 <br />
				                 Genere: '.$row_proz['c08'].'<br />
			   			 Voto: '.$votostr.'<br />
                           			 '.$row_proz['c01'].'
                                                 <br />
						 <br />
			 	  	 	 '.$row_proz['c22'];
       //trovo tutti gli episodi dalla tabella episode che appartengono a quella serie
       $result_episode = mysql_query("SELECT * FROM  episode  WHERE ".$row_proz['idShow']." = idShow ORDER BY c12 *1, c13 *1",$verb);
       while ($row_proz_episode = mysql_fetch_array($result_episode)) {
	 //reperisco le info del titolo dalla tabella episodeview
         echo			 	        '<span class="path_txt">';
	 $result_episodeview = mysql_query("SELECT * FROM  episodeview  WHERE ".$row_proz_episode['idEpisode']." = idEpisode",$verb);//
         $row_proz_episodeview = mysql_fetch_array($result_episodeview);
	 echo 					'<a href="series.php?page='.$row_proz['c00'].'">'.$row_proz['c00'].'</a> ';
    	 echo			 		 $row_proz_episode['c12'].'x'.$row_proz_episode['c13'].'-'.$row_proz_episode['c00'].'
                             			 <span>'.
                             			 $row_proz_episodeview['strPath'].$row_proz_episodeview['strFileName'].'
                             			 </span>
   						 </span>
						 <br />';
       }
       echo '		   			 <br />
						 <br />
						 </p>
						 </td>
                    				 </tr>'  ;
     }
   }  else {
	echo                                  '<p class="centrato">';
        if ($page == 'tutte_serie') {
          $result = mysql_query("SELECT * FROM tvshow ORDER BY c00 ASC LIMIT ".$first.", ".$x_pag."",$verb);
          $tot_pag = ceil((mysql_num_rows(mysql_query("SELECT * FROM tvshow ORDER BY c00 ASC")))/$x_pag);
          if (($page != 'ultime_serie') and ($tot_pag > 0)) {
            echo                                'Pagina:';
          } else {
              echo                              '<br />';
          }

          for($x = 1; $x < $tot_pag+1; $x++) {
            if ($x == $num_pag) {
              echo 				'<b>*<a href="series.php?page='.$page.'&amp;num_pag='.$x.'">'.$x.'</a></b>';
	    } else {
              echo 				'<a href="series.php?page='.$page.'&amp;num_pag='.$x.'">'.$x.'</a>';
	    }
          }
          echo '				 </p>

                                                 <p>
                                                 <a href="javascript:history.go(-1)">Torna indietro</a>
                                                 </p>
                                                 </td>
                                                 </tr>
						 <tr>
						 <td style="width: 500px; display: inline-block; border:1px dashed black; border-collapse:collapse;"  class="normale">';
          while ($row_proz = mysql_fetch_array($result)) {
            echo 			        '<a href="series.php?pageid='.$row_proz['idShow'].'">'.$row_proz['c00'].'</a> '.'
          					 <br />';
          };
        }  else {
             echo                               '<br />
						 </p>
                                                 <p>
                                                 <a href="javascript:history.go(-1)">Torna indietro</a>
                                                 </p>
                                                 </td>
                                                 </tr>
						 <tr>
						 <td style="width: 500px; display: inline-block; border:1px dashed black; border-collapse:collapse;"  class="normale">';
             $result_episodeview = mysql_query("SELECT * FROM episodeview ORDER BY dateAdded DESC LIMIT 20",$verb);
             while ($row_proz_episodeview = mysql_fetch_array($result_episodeview)) {
               $result = mysql_query("SELECT * FROM tvshow WHERE ".$row_proz_episodeview['idShow']." = idShow",$verb);
               $row_proz = mysql_fetch_array($result);
               echo				'<a href="series.php?pageid='.$row_proz['idShow'].'">'.$row_proz['c00'].'</a> ';
               echo 				 $row_proz_episodeview['c12'].'x'.$row_proz_episodeview['c13'].'-'.$row_proz_episodeview['c00'].'
         					 <br />';
             }
        }
        echo 					'</td>
						 </tr>';
   };
   mysql_free_result($result);
   echo 					'</table>';
   //icona w3c validator
   echo 					'<p>
   					 	 <a href="http://validator.w3.org/check?uri=referer"><img
					         src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>
					         </p>';
   //javascript per l'opzione leggi tutto
   echo 				        '<script type="text/javascript" src="jquery.min.js"></script>
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
