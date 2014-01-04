<?php
// Get ESV version text and email it

    function curl_get_contents($url)
    {
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        $contents=curl_exec($ch) or die('CURL ERROR: '.curl_error($ch));
        curl_close($ch);
        return($contents);
    }

    if (empty($argv[1])) die('Please specify email address'."\n");
    $email=$argv[1];

    date_default_timezone_set('America/New_York');

    $year=date('Y');

    $file="dbr-plan-$year.php";
    if (!file_exists($file))
        system("php DailyChapters.php >/dev/null");

    if (!file_exists($file))
        die("Unable to generate $file");

    require $file;

    $last_email=0;
    if (file_exists('lastemail'))
        $last_email=file_get_contents('lastemail');

    $baseurl='http://www.esvapi.org/v2/rest/passageQuery?key=IP&passage=';

    $current=time();
    foreach ($plan as $date => $chapters)
    {
        $dateval=strtotime($date);

        if ($dateval<=$last_email)
            continue;

        if ($dateval>$current)
            break;

        $subj='Daily Bible Reading for '.$date;
        $msg=$subj."\n\n";
        foreach ($chapters as $chapter)
        {
            $texturl=$baseurl.urlencode($chapter).'&output-format=plain-text';
            $mp3url=$baseurl.urlencode($chapter).'&output-format=mp3';

            $msg.='Chapter: '.$chapter."\nAudio: $mp3url\n\n";


            $text=curl_get_contents($texturl);
            $msg.=$text."\n\n\n\n";
        }

        mail($email,$subj,$msg);

        file_put_contents('lastemail',$dateval);
        break;
    }

