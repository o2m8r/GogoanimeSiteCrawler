<?php
#
#   Gogoanime Site Crawler
#
#   Usage:  php gogoanime.php <starting episode> <ending episode> "<anime title>" <file name(optional)>
#   example:  php gogoanime.php 1 2 "boruto-naruto-next-generations" links.txt
#
#

error_reporting(0);


unset($argv[0]);
$episode_start = trim($argv[1]);
$episode_end = trim($argv[2]);
$anime_title = str_replace(' ','-',strtolower(trim($argv[3])));
$filename = str_replace(' ','-',strtolower(trim($argv[4])));

if(empty($episode_start) || empty($episode_end) || empty($anime_title)){
    echo "\nEnter anime title: ";
    $anime_title = str_replace(' ','-',strtolower(trim(fgets(STDIN, 1024))));
    echo "Enter starting episode: ";
    $episode_start = trim(fgets(STDIN, 1024));
    echo "Enter ending episode: ";
    $episode_end = trim(fgets(STDIN, 1024));
    echo "Enter filename(Hit enter to skip): ";
    $filename = str_replace(' ','-',strtolower(trim(fgets(STDIN, 1024))));
}


$anime_episode_main_download_links = array();

function follow_links($url,$domain,$title,$i){

    global $filename;
    if( isset($filename) && $filename != ""){global $myfile;}
    
    $doc = new DOMDocument();
    $doc->loadHTML(file_get_contents($url));
    
    $linklist = $doc->getElementsByTagName("a");
    
    if($domain == "vidstream"){
        echo "\n==============================================================\nTitle: ".$title."\nEpisode #: ".$i."\nLinks: \n";
        if( isset($filename) && $filename != ""){
            $text_file_head = "\n==============================================================\nTitle: ".$title."\nEpisode #: ".$i."\nLinks: \n";
            $txt = $text_file_head;
            fwrite($myfile, $txt);
        }
    }

    foreach( $linklist as $link){
        $l = $link->getAttribute("href");
        if($domain == "gogoanime"){
            
            if (substr($l, 0, 20) == "https://vidstream.co"){
                return $l;
            }
        }
        if($domain == "vidstream"){
            
            if (substr($l, 0, 27) == "https://video.xx.fbcdn.net/"){
                $fbcdn = "[+]fbcdn: ".$l."\n";
                if( isset($filename) && $filename != ""){
                    $txt = $fbcdn;
                    fwrite($myfile, $txt);
                }
                echo "[+]fbcdn: ".$l."\n";
            }
            if (substr($l, 0, 20) == "https://openload.co/"){
                $openload = "\n[+]openload: ".$l."\n";
                if( isset($filename) && $filename != ""){
                    $txt = $openload;
                    fwrite($myfile, $txt);
                }
                echo "\n[+]openload: ".$l."\n";
            }
            if (substr($l, 0, 20) == "https://thevideo.me/"){
                $thevideo = "\n[+]thevideo: ".$l."\n";
                if( isset($filename) && $filename != ""){
                    $txt = $thevideo;
                    fwrite($myfile, $txt);
                }
                echo "\n[+]thevideo: ".$l."\n";
            }
            if (substr($l, 0, 25) == "http://www.mp4upload.com/"){
                $mp4upload = "\n[+]mp4upload: ".$l."\n";
                if( isset($filename) && $filename != ""){
                    $txt = $mp4upload;
                    fwrite($myfile, $txt);
                }
                echo "\n[+]mp4upload: ".$l."\n";
            }
        
        } 

    

        
    }
    
}
        
function gogoanime_crawl($title,$start,$end){

    global $anime_episode_main_download_links;
    
    for($i = $start; $i<=$end; $i++){
        $start_link = "https://www1.gogoanime.se/".$title."-episode-".$i;
        
        $anime_episode_main_download_links[] = follow_links($start_link,"gogoanime",$title,$i);
        
    }

    $i = $start;
    
    foreach($anime_episode_main_download_links as $vidstream_links){
        follow_links($vidstream_links,"vidstream",$title,$i);
        $i++;
    }
    

}
if( is_string($anime_title) && isset($anime_title) && !is_array($anime_title) && is_numeric($episode_start) && isset($episode_start) && !is_array($episode_start) && is_numeric($episode_end) && isset($episode_end) && !is_array($episode_end)){
    
    echo "Checking anime existence: ";
    $site = 'https://www1.gogoanime.se/'.$anime_title.'-episode-'.$episode_start;

    $doc = new DOMDocument();
    $doc->loadHTML(file_get_contents($site));
    $tags = $doc->getElementsByTagName("h1");

    foreach( $tags as $tag){
        $check_anime_existence = $tag->getAttribute("class");
        if($check_anime_existence == "entry-title"){
            echo "Anime does not exists. :(\nCheck the anime title on the site\n";
        }else{
            if( isset($filename) && $filename != ""){
                $myfile = fopen($filename, "w") or die("Unable to open file!");
            }
            echo "Anime exists. :)\nLoading. . .\n";
            
            gogoanime_crawl($anime_title,$episode_start,$episode_end);
        }
    }
}else{
    echo "\n???\n";
}
if( isset($filename) && $filename != ""){
    fclose($myfile);
}
?>

