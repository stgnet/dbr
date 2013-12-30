<?php

    // Create a daily bible reading plan
    // Target is consistent average number of words/day

    date_default_timezone_set('America/New_York');

    require 'kjv-wordcount-chapters.php';

    $total=0;
    foreach ($wordcount as $chapter => $words)
        $total+=$words;
    $chapters=count($wordcount);

    $wordsperday=$total/365;
    $chaptersperday=$chapters/365;
    $wordsperchapter=$total/$chapters;

    //echo "There are $chapters chapters and $total words.\n";
    //echo "Average of $wordsperchapter words per chapter.\n";
    //echo "Average of $chaptersperday chapters per day.\n";
    //echo "Targeting $wordsperday words per day.\n";

    $chapterlist=array_keys($wordcount);
    // start with *next* year
    $start=strtotime("Dec 31")+24*3600;
    $words=0;
    $plan=array();
    foreach (range(0,364) as $dayoffset)
    {
        $day=date("D M d Y",$start+24*3600*$dayoffset);
        $plan[$day]=array();

        $target=$wordsperday*($dayoffset+1);

        while ($words<$target)
        {
            $chapter=array_shift($chapterlist);
            $words+=$wordcount[$chapter];
            $plan[$day][]=$chapter;
        }
    }

    file_put_contents("dbr-plan.php",'<'."?php\n".'$plan='.var_export($plan,true).";\n");


