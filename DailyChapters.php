<?php

    // Create a daily bible reading plan
    // Target is consistent average number of words/day

    date_default_timezone_set('America/New_York');

    require 'kjv-wordcount-chapters.php';

    $year='2014';
    if (!empty($argv[1]))
        $year=$argv[1];

    $daylist=array();
    $start=strtotime("Jan 1 $year");
    $end=strtotime("Dec 31 $year");
    $day=$start;
    while ($day <= $end)
    {
        $dayofweek=date("w",$day);
        if ($dayofweek) // not sunday
            $daylist[]=$day;
        $day=$day+24*3600;
    }

    $daycount=count($daylist);

    $totalwords=0;
    foreach ($wordcount as $chapter => $words)
        $totalwords+=$words;
    $chapters=count($wordcount);

    $wordsperday=$totalwords/$daycount;
    $chaptersperday=$chapters/$daycount;
    $wordsperchapter=$totalwords/$chapters;

    echo "There are $daycount days.\n";
    echo "There are $chapters chapters and $totalwords words.\n";
    echo "Average of $wordsperchapter words per chapter.\n";
    echo "Average of $chaptersperday chapters per day.\n";
    echo "Targeting $wordsperday words per day.\n";

    $chapterlist=array_keys($wordcount);
    // start with *next* year
    $words=0;
    $plan=array();
    $dayoffset=0;
    foreach ($daylist as $date)
    {
        $day=date("D M d Y",$date);
        $plan[$day]=array();

        $dayoffset++;
        $target=$wordsperday*$dayoffset;

        while ($words<$target)
        {
            $chapter=array_shift($chapterlist);
            $words+=$wordcount[$chapter];
            $plan[$day][]=$chapter;
        }
    }

    file_put_contents("dbr-plan-$year.php",'<'."?php\n".'$plan='.var_export($plan,true).";\n");


