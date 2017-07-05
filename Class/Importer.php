<?php


require "../Class/Logger.php";
require "../Class/DBmysqli.php";


class Importer 
{ 
    private $fp; 
    private $parse_header; 
    private $header; 
    private $delimiter; 
    private $length;
    private $path;

    //-------------------------------------------------------------------- 
    function __construct(/*$path,$file_name, $parse_header=false, $delimiter=",", $length=8000*/)
    { 
          $this->path = $path;
       // $this->fp = fopen($file_name, "r");
       // $this->parse_header = $parse_header;
       // $this->delimiter = $delimiter;
       // $this->length = $length;
       // $this->lines = $lines;

        /*
        if ($this->parse_header)
        { 
           $this->header = fgetcsv($this->fp, $this->length, $this->delimiter); 
        } 
        */
    } 
    //-------------------------------------------------------------------- 
    function __destruct() 
    { 
        if ($this->fp) 
        { 
            fclose($this->fp); 
        } 
    } 
    //-------------------------------------------------------------------- 
    function parse($max_lines=0) 
    { 
        //if $max_lines is set to 0, then get all the data 

        $data = array(); 

        if ($max_lines > 0) 
            $line_count = 0; 
        else 
            $line_count = -1; // so loop limit is ignored 

        while ($line_count < $max_lines && ($row = fgetcsv($this->fp, $this->length, $this->delimiter)) !== FALSE) 
        { 
            if ($this->parse_header) 
            { 
                foreach ($this->header as $i => $heading_i) 
                { 
                    $row_new[$heading_i] = $row[$i]; 
                } 
                $data[] = $row_new; 
            } 
            else 
            { 
                $data[] = $row; 
            } 

            if ($max_lines > 0) 
                $line_count++; 
        } 
        return $data; 
    } 
    //-------------------------------------------------------------------- 

    // ulozi cilova data (url) do souboru
    function save($stock, $startdate, $enddate)
    {
        try {
            $actual = strftime("%Y_%m_%d", Time());
            $urlData = ('http://www.google.com/finance/historical?output=csv&q=' . $stock . '&startdate=' . $startdate . '&enddate=' . $enddate);
            $contentFile = file_get_contents($urlData);

            $fileName = $actual . '.txt';
            $file = fopen('../Files/' . $fileName, "a");
            echo('Soubor ' . $fileName . ' otevren/vytvoren.<br />');

            fwrite($file, $contentFile);
            echo('Soubor ' . $fileName . ' ulozen. <br />');
            echo "Data pro akcii: ";
            echo($stock . ' za obdobi od: ' . $startdate . ' do ' . $enddate . '<br /n>');
            echo 'Obsah souboru: ';
            echo($contentFile);
            fclose($file);
            $logger = new Logger();
            $logger->info("Data stazeny. Titul: ".$stock . "url adresa: " . 'http://www.google.com/finance/historical?output=csv&q=' . $stock . '&startdate=' . $startdate . '&enddate=' . $enddate  );
            }
        catch(Exception $e)
            {
            $logger = new Logger();
            $logger->error("Data nestazena. " . $e->getMessage() );
            }

    }
    function saveData()
    {
        //inicializace loggeru
        $logger = new Logger();
        //Nacteni dat z tabulky TITUL a k tomu docteni info o burze
        $object = new DBmysqli();
        $result = $object->query('SELECT T.NAZEV, T.KOD, B.URL, B.BURZA_ID, T.TITUL_ID, T.URL_TITULU
                                  FROM TITUL T, BURZA B
                                  WHERE T.BURZA_ID = B.BURZA_ID
                                  AND T.STAHOVAT =1');

        /*
         *Nacteni datumu od a datumu do, dle kterych se budou nacitat data o akciich. Od datumu odecitam jeden den,
         *protoze google poskytuje data se spozdenim a new york burza uzavira az ve 22:00
         */
        $dateFrom = date("Y-m-d");
        $dateFrom_arr=explode('-',$dateFrom);
        $dateFrom=Date("Y-m-d",mktime(0,0,0,$dateFrom_arr[1],$dateFrom_arr[2]-5,$dateFrom_arr[0]));

        $dateTo = date('Y-m-d');
        $dateTo_arr=explode('-',$dateTo);
        $dateTo=Date("Y-m-d",mktime(0,0,0,$dateTo_arr[1],$dateTo_arr[2]-1,$dateTo_arr[0]));

        //Pro kazdy radek z tabulky TITUL vemu prislusna data a podle typu burzy provedu stazeni a parse dat
        foreach($result as $row=>$value) {
            echo('<b>' .'Nacten titul z databaze: ' . $row . ': NAZEV:  ' . $value[0] . ' , KOD: ' . $value[1] . ' , URL: ' . $value[2] . ' , burza id: ' . $value[3] . ', TITUL ID: ' . $value[4] . ', URL_TITULU: ' . $value[5] . '</b>' . '<br />');
            $titulNAZEV = $value[0];
            $titulKOD = $value[1];
            $burzaURL = $value[2];
            $burzaID = $value[3];
            $titulID = $value[4];
            $urlTitulu = $value[5];


            switch ($burzaID) {
                case 1:
                    echo 'titul je zpracovavan kodem pro burzu New York:' . '<br />';
                    
                    $url = str_replace("*titul", $titulKOD, $burzaURL);
                    $url = str_replace("*dateFrom", $dateFrom, $url);
                    $url = str_replace("*dateTo", $dateTo, $url);

                    echo 'url: ' . $url . '<br />';
                    $log = "Pripavuji stazeni titulu: " . $titulKOD;
                    $logger->info($log);
                    $rowNum = 1;

                    $contentData = fopen($url, "r");
                    if($contentData) {

                        while (($titleValues = fgetcsv($contentData, 1000, ",")) !== FALSE) {
                            echo 'cislo radku z datoveho souboru: ' . $rowNum . '<br />';
                            if ($rowNum > 1) {
                                echo 'titul nazev: ' . $titulNAZEV . '<br />';
                                echo $titulKOD . '<br />';
                                echo $titleValues[0] . '<br />';
                                echo strtoupper($titleValues[0]) . '<br />';
                                echo $titulUNIXDate = strtotime(strtoupper($titleValues[0])) . '<br />';
                                echo $titulDate = date('Y-m-d', $titulUNIXDate) . '<br />';
                                echo $titleValues[1] . '<br />';
                                echo $titleValues[2] . '<br />';
                                echo $titleValues[3] . '<br />';
                                echo $titleValues[4] . '<br />';
                                echo $titleValues[5] . '<br />';
                                $object = new DBmysqli();
                                $count = "SELECT TITUL_ID FROM DATA
                                      WHERE TITUL_ID=" . $titulID . "
                                      AND DATE = '" . $titulDate . "'";
                                echo $count . '<br />';
                                $titulDataCounter = $object->count($count);
                                echo $titulDataCounter . '<br />';
                                if ($titulDataCounter == 0) {
                                    $insert = "INSERT INTO DATA (DATA_ID, TITUL_ID, OPEN, CLOSE, HIGH, LOW, VOLUME, DATE)
                                          VALUES (NULL, " . $titulID . ", '" . $titleValues[1] . "','" . $titleValues[4] . "','" . $titleValues[2] . "','" . $titleValues[3] . "','" . $titleValues[5] . "','" . $titulDate . "')";
                                    echo $insert . '<br />';
                                    $object->insert($insert);
                                    $log = 'Stazen titul ' . $titulKOD . '. Americka burza.';
                                    $log .= "\r\n";
                                    $log .= 'URL: ' . $url;
                                    $log .= "\r\n";
                                    $log .= 'INSERT: ' . $insert;
                                    $log .= "\r\n";
                                    $log .= 'Data: OPEN=' . $titleValues[1] . ',CLOSE=' . $titleValues[4] . ',MAX=' . $titleValues[2] . ',MIN=' . $titleValues[3] . ',OBJEM=' . $titleValues[5] . ',DATUM=' . $titulDate;
                                    $logger->info($log);
                                }
                                if ($titulDataCounter > 0) {
                                    $log = 'Data na Americke burze pro titul ' . $titulKOD . ' jsou jiz stazena ze dne: ' . $titulDate . ' Novejsi nejsou k dispozici';
                                    $log .= "\r\n";
                                    $log .= 'URL: ' . $url;
                                    $logger->info($log);
                                }
                            }

                            $rowNum = $rowNum + 1;
                        }
                    } else {
                        $log = 'Error: nepodarilo se stahnou soubor z GOOGLE';
                        $log .= "\r\n";
                        $log .= 'URL: ' . $url;
                        $logger->error($log);
                    }
                    if($rowNum == 1){
                        $log = 'Data na Americke burze pro titul: '.$titulKOD.' nejsou k dispozici.';
                        $log .="\r\n";
                        $log .= 'URL: '.$url;
                        $logger->info($log);
                    }
                    break;

                case 2: // RM system, parsovani URL typu: http://www.rmsystem.cz/vysledky/historie-obchodovani/jednotlive-cenne-papiry?ticker=BAACEZ
                    echo 'titul je zpracovavan kodem 2 (parsovani URL pro historii daneho titulu) pro Prazskou burzu:' . '<br />';
                    echo 'titulNAZEV: ' . $titulNAZEV . '<br />';
                    echo 'titulKOD: ' . $titulKOD . '<br />';
                    echo 'burzaURL: ' . $burzaURL . '<br />';
                    echo 'burzaID: ' . $burzaID . '<br />';
                    echo 'titulID: ' . $titulID . '<br />';
                    echo 'urlTitulu: ' . $urlTitulu . '<br />';

                    $source = "URL"; // URL/FILE - zda zdroj dat bude URL nebo FILE na filesystemu - FILE je pro nacteni historie, default pro denni spousteni je URL
                                      // to FILE je delany pro to, ze se na strance http://www.rmsystem.cz/vysledky/historie-obchodovani/jednotlive-cenne-papiry?ticker=BAACEZ
                                      // zada rozmezi datumu treba rok, zdroj html stranky se ulozi do file, uploadne se a zde se jen zada nazev a vse z nej se nacte a co neni v db, tak se vlozi
                    if ($source == "URL") {
                        $urTituluProBurzuID2 = $burzaURL . '/vysledky/historie-obchodovani/jednotlive-cenne-papiry?ticker=' . $titulKOD;
                        echo 'volane URL titulu: ' . $urTituluProBurzuID2 . '<br />';
                        $UrlData = file_get_contents($urTituluProBurzuID2);
                        //echo 'Source code stranky:#' . $UrlData . '#';
                    } else {
                        $filenamePvo = "../Files/BAACEZ2.html";
                        $handlePvo = fopen($filenamePvo, "r");
                        $UrlData = fread($handlePvo, filesize($filenamePvo));
                        fclose($handlePvo);
                        //echo 'Nacteny obsah souboru:#' . $UrlData . '#';
                    }

                    // kontrola, zda byla nactena spravna stranka, tzn. stranka s historii titulu
                    if ((stripos($UrlData,"<base href=\"http://www.rmsystem.cz/index.php\" />")<>"") || ($UrlData == "")) {
                        echo 'NEBYLA NACTENA SPRAVNA STRANKA, ZPRACOVANI SE NEPROVADI.'; // skocilo to na rm system index stranku nebo url nic nevratila, vypisu jen chyb. zpravu a jdu dal
                    } else {
                        $pos = stripos($UrlData,"Datum");
                        //echo 'pos:#' . $pos . '#';
                        $posEnd = stripos($UrlData,"Copyright (c)");
                        $part = substr($UrlData,$pos,$posEnd - $pos);
                        //echo 'part:#' . $part . '#';

                        // ve smycce se vezmou vsechny pritomne dny a hodnoty pro kazdy den vlozim do db, kdyz tam jeste nejsou a pokud jsou, tak zkontroluji, zda data odpovidaji tem v DB a pokud jsou rozdilne, zapisi to do logu a jdu na dalsi den
                        $pos = stripos($part,"<td class=\"tleft\">");
                        while ($pos <> "") {
                            $part = substr($part,$pos + 18,10000000);
                            //echo 'part:#' . $part . '#';

                            // datum
                            $index = stripos($part,"</td>");
                            $datum = substr($part,0,$index);
                            echo '<br />' . 'datum:#' . $datum . '#';
                            // DD
                            $index = stripos($datum,".");
                            $DD = substr($datum,0,$index);
                            //echo 'DD:#' . $DD . '#<br />';
                            // MM
                            $datumPart = substr($datum,$index+1,100);
                            $index = stripos($datumPart,".");
                            $MM = substr($datumPart,0,$index);
                            //echo 'MM:#' . $MM . '#<br />';
                            // YYYY
                            $index = stripos($datumPart,".");
                            $YYYY = substr($datumPart,$index+1,100);
                            //echo 'YYYY:#' . $YYYY . '#<br />';

                            // open
                            $index = stripos($part,"<td>");
                            $part = substr($part,$index+4,10000000);
                            //echo 'part:#' . $part . '#';
                            $open = substr($part,0,stripos($part,"</td>"));
                            $open = str_replace(",",".",$open);
                            $open = str_replace(" ","",$open);
                            echo 'open:#' . $open . '#';

                            // min
                            $index = stripos($part,"<td>");
                            $part = substr($part,$index+4,10000000);
                            //echo 'part:#' . $part . '#';
                            $min = substr($part,0,stripos($part,"</td>"));
                            $min = str_replace(",",".",$min);
                            $min = str_replace(" ","",$min);
                            echo 'min:#' . $min . '#';

                            // max
                            $index = stripos($part,"<td>");
                            $part = substr($part,$index+4,10000000);
                            //echo 'part:#' . $part . '#';
                            $max = substr($part,0,stripos($part,"</td>"));
                            $max = str_replace(",",".",$max);
                            $max = str_replace(" ","",$max);
                            echo 'max:#' . $max . '#';

                            // close
                            $index = stripos($part,"<td>");
                            $part = substr($part,$index+4,10000000);
                            //echo 'part:#' . $part . '#';
                            $close = substr($part,0,stripos($part,"</td>"));
                            $close = str_replace(",",".",$close);
                            $close = str_replace(" ","",$close);
                            echo 'close:#' . $close . '#';

                            // volume
                            $index = stripos($part,"<td"); // to je objem v Kc, to me nezajima, necham odriznout
                            $part = substr($part,$index+10,10000000);
                            $index = stripos($part,"<td>");
                            $part = substr($part,$index+4,10000000);
                            //echo 'part:#' . $part . '#';
                            $volume = substr($part,0,stripos($part,"</td>"));
                            $volume = str_replace(",",".",$volume);
                            $volume = str_replace(" ","",$volume);
                            $volume = $volume * 1000; // je to v tisicich, tak to prevedu na normalni cislo v jednotkach kusu
                            echo 'volume:#' . $volume . '#';

                            // insert dat kdyz v db neni, pokud je, vytahnu si hodnoty a porovnaji se a pri rozdilu se to zapise do logu, ale nic se neupdatuje
                            $object = new DBmysqli();
                            $count = "SELECT TITUL_ID FROM DATA
                                          WHERE TITUL_ID=".$titulID."
                                          AND DATE = '" . $YYYY . "-" . $MM . "-" . $DD . "'";
                            echo "count:" . $count . '<br />';
                            $titulDataCounter = $object->count($count);
                            echo "titulDataCounter:" . $titulDataCounter . '<br />';
                            $emailDate = $YYYY."-".$MM."-".$DD;
                            //if ($source == "URL") {
                            if ($titulDataCounter==0)
                            {
                                echo "provadi se insert, data pro dany titul a den jeste v DB nejsou.<br />";
                                $date = $YYYY."-".$MM."-".$DD;
                                $insert = "INSERT INTO DATA (DATA_ID, TITUL_ID, OPEN, CLOSE, HIGH, LOW, VOLUME, DATE)
                                       VALUES (NULL, ".$titulID.", ".$open.",".$close.",".$max.",".$min.",".$volume.",'". $date."')";
                                echo $insert . '<br />';
                                $object->insert($insert);
                                $log = 'Stazen titul '.$titulKOD.'. RM System.';
                                $log .="\r\n";
                                $log .= 'URL: '.$urTituluProBurzuID2;
                                $log .="\r\n";
                                $log .='INSERT: '.$insert;
                                $log .="\r\n";
                                $log .='Data: OPEN='.$open.',CLOSE='.$close.',MAX='.$max.',MIN='.$min.',OBJEM='.$volume.',DATUM='.$date;
                                $log .="\r\n";
                                $log .="\r\n";
                                $logger->info($log);
                            } else {
                                $log = 'Data na RM Systemu pro titul '.$titulKOD.' jsou jiz stazena a to ze dne '.$emailDate.' Novejsi nejsou k dispozici.';
                                $log .="\r\n";
                                $log .= 'URL: '.$urTituluProBurzuID2;
                                $log .="\r\n";
                                $log .="\r\n";
                                $logger->info($log);
                                echo "data pro dany titul a den jiz v DB jsou, nic se neprovadi.<br />";
                            }
                            //}

                            // hledam, zda jsou ve stringu data pro dalsi den, pokud ano, smycka while jede znova, pokud ne, konci se a jde se na dalsi titul
                            $pos = stripos($part,"<td class=\"tleft\">");
                        }
                    }
                    echo '<br />';
                    break;

                case 999: //  RM System - kod pro ziskavani dat z URL typu: http://www.rmsystem.cz/akcie-11392-cez. Bylo opusteno, protoze je to nespolehlive - o pulnoci je smazan z teto stranky udaj CLOSE
                    // misto toho vytvoren kod, ktery data ziskava z URL pro historii daneho titulu
                    echo 'titul je zpracovavan kodem 999 (parsovani URL pro dany titul) pro Prazskou burzu (bylo opusteno):' . '<br />';
                    echo 'titulNAZEV: ' . $titulNAZEV . '<br />';
                    echo 'titulKOD: ' . $titulKOD . '<br />';
                    echo 'burzaURL: ' . $burzaURL . '<br />';
                    echo 'burzaID: ' . $burzaID . '<br />';
                    echo 'titulID: ' . $titulID . '<br />';
                    echo 'urlTitulu: ' . $urlTitulu . '<br />';
                    
                    $urTituluProBurzuID2 = $burzaURL . $urlTitulu;
                    echo 'volane URL titulu: ' . $urTituluProBurzuID2 . '<br />'; 
                    
                    $UrlData = file_get_contents($urTituluProBurzuID2);

                    $pos1 = stripos($UrlData,"<div class=\"col1\">");
                    $pos2 = stripos($UrlData,"<div id=\"detailcp-graf\">");
                    $part1 = substr($UrlData,$pos1,$pos2-$pos1);

                    // ted uz jen vyhledat dle nadpisu spravna cisla a i datum z druhe casti a vlozit do databaze
                    // kurz
                    $indexStart = stripos($part1," kurz:");
                    $part = substr($part1,$indexStart,1500);
                    $indexEnd = stripos($part,",") + 2;
                    $open = substr($part,25,$indexEnd-24);
                    $open = str_replace(",",".",$open);
                    $open = str_replace(" ","",$open);
                    echo 'oteviraci kurz:#' . $open . '#<br />';
                    // minimum
                    $indexStart = stripos($part,"Minimum:");
                    $part = substr($part,$indexStart,1500);
                    $indexEnd = stripos($part,",") + 2;
                    $min = substr($part,27,$indexEnd-26);
                    $min = str_replace(",",".",$min);
                    $min = str_replace(" ","",$min);
                    echo 'minimum:#' . $min . '#<br />';
                    // objem ks
                    $indexStart = stripos($part,"Objem (ks):");
                    $part = substr($part,$indexStart,1500);
                    $indexEnd = stripos($part,"EasyClick lot:") + 2;
                    $objem = substr($part,30,$indexEnd-51);
                    $objem = str_replace(" ","",$objem);
                    echo 'objem:#' . $objem . '#<br />';
                    // maximum
                    $indexStart = stripos($part,"Maximum:");
                    $part = substr($part,$indexStart,1500);
                    $indexEnd = stripos($part,">Po");
                    $max = substr($part,27,$indexEnd-45);
                    $max = str_replace(",",".",$max);
                    $max = str_replace(" ","",$max);
                    echo 'maximum:#' . $max . '#<br />';
                    // close
                    $indexStart = stripos($part," kurz:");
                    $part = substr($part,$indexStart,1500);
                    $indexEnd = stripos($part,"pustn");
                    $close = substr($part,25,$indexEnd-47);
                    $close = str_replace(",",".",$close);
                    $close = str_replace(" ","",$close);
                    echo 'close:#' . $close . '#<br />';

                    // ziskani datumu posledniho obchodu
                    $indexStart = stripos($part1,"%)</strong></p><p>");
                    $part = substr($part1,$indexStart+18,50);
                    $indexEnd = stripos($part,"</p>");
                    $part = substr($part,0,$indexEnd);
                    $indexStart = stripos($part," ");
                    $datum = substr($part,$indexStart+1,100);
                    $datum = str_replace(" ","",$datum);
                    echo 'datum:#' . $datum . '#<br />';
                    // DD
                    $index = stripos($datum,".");
                    $DD = substr($datum,0,$index);
                    echo 'DD:#' . $DD . '#<br />';
                    // MM
                    $datumPart = substr($datum,$index+1,100);
                    $index = stripos($datumPart,".");
                    $MM = substr($datumPart,0,$index);
                    echo 'MM:#' . $MM . '#<br />';
                    // YYYY
                    $index = stripos($datumPart,".");
                    $YYYY = substr($datumPart,$index+1,100);
                    echo 'YYYY:#' . $YYYY . '#<br />';

                    // insert
                    $object = new DBmysqli();
                    $count = "SELECT TITUL_ID FROM DATA
                                      WHERE TITUL_ID=".$titulID."
                                      AND DATE = '" . $YYYY . "-" . $MM . "-" . $DD . "'";
                    echo "count:" . $count . '<br />';
                    $titulDataCounter = $object->count($count);
                    echo "titulDataCounter:" . $titulDataCounter . '<br />';
                    $emailDate = $YYYY."-".$MM."-".$DD;
                    if ($titulDataCounter==0)
                    {
                        echo "provadi se insert, data pro dany titul a den jeste v DB nejsou.<br />";
                        $date = $YYYY."-".$MM."-".$DD;
                        $insert = "INSERT INTO DATA (DATA_ID, TITUL_ID, OPEN, CLOSE, HIGH, LOW, VOLUME, DATE)
                                   VALUES (NULL, ".$titulID.", ".$open.",".$close.",".$max.",".$min.",".$objem.",'". $date."')";
                        echo $insert . '<br />';
                        $object->insert($insert);
                        $log = 'Stazen titul '.$titulKOD.'. RM System.';
                        $log .="\r\n";
                        $log .= 'URL: '.$urTituluProBurzuID2;
                        $log .="\r\n";
                        $log .='INSERT: '.$insert;
                        $log .="\r\n";
                        $log .='Data: OPEN='.$open.',CLOSE='.$close.',MAX='.$max.',MIN='.$min.',OBJEM='.$objem.',DATUM='.$date;
                        $log .="\r\n";
                        $log .="\r\n";
                        $logger->info($log);
                    } else {
                        $log = 'Data na RM Systemu pro titul '.$titulKOD.' jsou jiz stazena a to ze dne '.$emailDate.' Novejsi nejsou k dispozici.';
                        $log .="\r\n";
                        $log .= 'URL: '.$urTituluProBurzuID2;
                        $log .="\r\n";
                        $log .="\r\n";
                        $logger->info($log);
                        echo "data pro dany titul a den jiz v DB jsou, nic se neprovadi.<br />";
                    }
                    break;

            }

            echo ' ' . '<br />';
        }
    }


}

?>