<?php

    // Parse the '10.txt' Gutenberg KJV text
    // to gather wordcount statistics, etc

$book_translate=array(
    'The Old Testament of the King James Version of the Bible' => '',
    'The First Book of Moses:  Called Genesis' => 'Gen',
    'The Second Book of Moses:  Called Exodus' => 'Exo',
    'The Third Book of Moses:  Called Leviticus' => 'Lev',
    'The Fourth Book of Moses:  Called Numbers' => 'Num',
    'The Fifth Book of Moses:  Called Deuteronomy' => 'Deu',
    'The Book of Joshua' => 'Jos',
    'The Book of Judges' => 'Judg',
    'The Book of Ruth' => 'Rth',
    'The First Book of Samuel' => '1Sa',
    'The Second Book of Samuel' => '2Sa',
    'The First Book of the Kings' => '1Ki',
    'The Second Book of the Kings' => '2Ki',
    'The First Book of the Chronicles' => '1Ch',
    'The Second Book of the Chronicles' => '2Ch',
    'Ezra' => 'Eza',
    'The Book of Nehemiah' => 'Neh',
    'The Book of Esther' => 'Est',
    'The Book of Job' => 'Job',
    'The Book of Psalms' => 'Psa',
    'The Proverbs' => 'Pro',
    'Ecclesiastes' => 'Ecc',
    'The Song of Solomon' => 'SS',
    'The Book of the Prophet Isaiah' => 'Isa',
    'The Book of the Prophet Jeremiah' => 'Jer',
    'The Lamentations of Jeremiah' => 'Lam',
    'The Book of the Prophet Ezekiel' => 'Ezk',
    'The Book of Daniel' => 'Dan',
    'Hosea' => 'Hos',
    'Joel' => 'Joe',
    'Amos' => 'Amo',
    'Obadiah' => 'Obd',
    'Jonah' => 'Jon',
    'Micah' => 'Mic',
    'Nahum' => 'Nah',
    'Habakkuk' => 'Hab',
    'Zephaniah' => 'Zep',
    'Haggai' => 'Hag',
    'Zechariah' => 'Zch',
    'Malachi' => 'Mal',
    'The New Testament of the King James Bible' => '',
    'The Gospel According to Saint Matthew' => 'Mat',
    'The Gospel According to Saint Mark' => 'Mar',
    'The Gospel According to Saint Luke' => 'Luk',
    'The Gospel According to Saint John' => 'Jn',
    'The Acts of the Apostles' => 'Act',
    'The Epistle of Paul the Apostle to the Romans' => 'Rom',
    'The First Epistle of Paul the Apostle to the Corinthians' => '1Co',
    'The Second Epistle of Paul the Apostle to the Corinthians' => '2Co',
    'The Epistle of Paul the Apostle to the Galatians' => 'Gal',
    'The Epistle of Paul the Apostle to the Ephesians' => 'Eph',
    'The Epistle of Paul the Apostle to the Philippians' => 'Phi',
    'The Epistle of Paul the Apostle to the Colossians' => 'Col',
    'The First Epistle of Paul the Apostle to the Thessalonians' => '1Th',
    'The Second Epistle of Paul the Apostle to the Thessalonians' => '2Th',
    'The First Epistle of Paul the Apostle to Timothy' => '1Ti',
    'The Second Epistle of Paul the Apostle to Timothy' => '2Ti',
    'The Epistle of Paul the Apostle to Titus' => 'Tit',
    'The Epistle of Paul the Apostle to Philemon' => 'Phm',
    'The Epistle of Paul the Apostle to the Hebrews' => 'Heb',
    'The General Epistle of James' => 'Jam',
    'The First Epistle General of Peter' => '1Pe',
    'The Second General Epistle of Peter' => '2Pe',
    'The First Epistle General of John' => '1Jo',
    'The Second Epistle General of John' => '2Jo',
    'The Third Epistle General of John' => '3Jo',
    'The General Epistle of Jude' => 'Jud',
    'The Revelation of Saint John the Devine' => 'Rev',
);

    $bible=array();

    $fp=fopen("kjv-bible.txt","r");

    $book='';
    $verse='';
    $blank=0;
    while ($linecr=fgets($fp))
    {
        $line=trim($linecr);
        if ($line=="")
        {
            $blank++;
            continue;
        }
        if ($blank>3)
        {
            $blank=0;
            if (substr($line,0,4)=="End ")
                break;
            if (!array_key_exists($line,$book_translate))
                die("ERROR: '$line' is not in translate table\n");
            $book=$book_translate[$line];
            $verse='';
            echo "$book ";
            if ($book)
                $bible[$book]=array();
            continue;
        }
        $blank=0;
        if (!$book) continue;
        foreach (explode(' ',$line) as $word)
        {
            if (preg_match('/^[0-9]+:[0-9]+$/',$word,$match))
            {
                $verse=$match[0];
                $bible[$book][$verse]=array();
                continue;
            }
            if ($verse)
                $bible[$book][$verse][]=$word;
        }
    }

    fclose($fp);
    echo "\n";

    file_put_contents("kjv-bible.php",'<'."?php\n".'$bible='.var_export($bible,true).";\n");

    $wordcount=array();
    foreach ($bible as $book => $verses)
    {
        foreach ($verses as $verse => $words)
        {
            $wordcount[$book.' '.$verse]=count($words);
        }
    }

    file_put_contents("kjv-wordcount-verses.php",'<'."?php\n".'$wordcount='.var_export($wordcount,true).";\n");

    $wordcount=array();
    foreach ($bible as $book => $verses)
    {
        foreach ($verses as $verse => $words)
        {
            $split=explode(':',$verse);
            $chapter=$split[0];
            $index=$book.' '.$chapter;
            if (!array_key_exists($index,$wordcount))
                $wordcount[$index]=0;
            $wordcount[$index]+=count($words);
        }
    }

    file_put_contents("kjv-wordcount-chapters.php",'<'."?php\n".'$wordcount='.var_export($wordcount,true).";\n");


