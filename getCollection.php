<?php
    include_once('query.php');
    //error_reporting(0);
    phpQuery::newDocumentFile('collection.txt');
    $companies = pq('.zm-item-title a');
    $collectioncount = sizeof($companies);
    $count = 1;
    foreach($companies as $key){
        $link = pq($key) -> attr('href');
        $text = pq($key) -> text();
        //$order = "mkdir content/'".$text."'";
        //system($order);
        getAllpage($link,$text,$count,$collectioncount);
        echo "\n";
        $count ++;

    }
    function getAllpage($url,$name,$count,$collectioncount){
        echo "start getting collection ".$name."......\n";
        echo "get answer list ......";
        $maxPage = getMaxpage($url);
        echo "successfully\n";
        echo "getting page 1 of ".$maxPage."......";
        $html = file_get_contents("http://www.zhihu.com".$url);
        echo "successfully\n";
        for ($i = 2; $i < $maxPage; $i++){
            echo "getting page ".$i." of ".$maxPage."......";
            $html = $html . file_get_contents("http://www.zhihu.com".$url."?page=".$i);
            //getOnePage($html,$text,$i);
            echo "successfuly\n";
        }
        echo "\ngenerating html .....";
        $pagecontent = '<html><head><meta charset = "utf8"></head><body>';
        phpQuery::newDocument($html);
        $content = pq('div.zm-item');
        foreach($content as $answer){
            $title = pq($answer) -> find('h2') -> find('a') -> text();
            $text = pq($answer) -> find('textarea.content') ->text();
            $title = '<p><b>'.$title.'</b></p><br>';
            $pagecontent = $pagecontent.$title.$text.'<hr />';
        }
        $pagecontent = $pagecontent.'</body></html>';
        $filename = "content/".$count.".html";
        echo "successfully\n";
        echo "saving html ......";
        saveHtml($filename,$pagecontent);
        echo "successfully\n";
        echo "\ncollection ".$name." get!\n";
        echo $count." collection get successfuly, totaly ".$collectioncount."\n";
    }

    function saveHtml($filename,$content){
        $of = fopen($filename,'w');
        if($of){
            fwrite($of,$content);
        }
        fclose($of);
    }
    function getMaxpage($url){
        $firstPage = file_get_contents("http://www.zhihu.com".$url);
        $rule  = '/page\=(\d)+/';
        preg_match_all($rule,$firstPage,$result);
        //echo sizeof($result);
        for($i = 0; $i < sizeof($result[0]); $i ++){
            $result[0][$i] = substr($result[0][$i],5);
        }
        $maxpage = 1;
        foreach($result[0] as $key){
            if ($key > $maxpage){
                $maxpage = $key;
            }
        }

        return  $maxpage;
    }
    function getTitle(){

        $qusurl = pq($answer) -> find('span') -> find('a')-> attr('href');
        $qusurl = preg_replace('/\/ans.*/','',$qusurl);
        $qusfullurl = "http://zhihu.com".$qusurl;
        $question = file_get_contents($qusfullurl);
        phpQuery::newDocument($question);
        $title = pq('title') -> text();
        echo 'starting get quesion '.$title.'\n';
        $title = preg_replace('/\s\-\s知乎/','',$title);
    }

?>