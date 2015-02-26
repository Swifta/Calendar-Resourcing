<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
        
<title>Calendar Resourcing WebApp</title>



</head>

<body style="background: url(back.jpg) ;
    border: 0.5px solid black;">
    
    <div style="background-color:#ffffff; margin-left: 250px;padding: 10px; margin-right: 250px;margin-bottom: 100px;margin-top: 65px;padding-bottom: 20px;
         filter: alpha(Opacity=50);
         opacity: 0.5;">
            
        
        
       <?php
            
                                                session_start();
                                                         

if(isset($_GET['code'])){


$code =  $_GET['code'];

}
else{
//authorization                         
                        $data2= "https://accounts.google.com/o/oauth2/auth?"; 

                        $data2 .="response_type=code&";
                        $data2 .="redirect_uri=" . urlencode("http://localhost/calendarresource/index.php") ."&";

                        $data2 .="client_id=272873981630-eietl5b2fmkjcb5s1h2ep6j3d71li1so.apps.googleusercontent.com" ."&";

                        $data2 .="scope=" . urlencode("https://apps-apis.google.com/a/feeds/calendar/resource/") ."&"; #orgunit user
                    

                        $data2 .="approval_prompt=force&";
                        $data2 .="state=kay&";
                        $data2 .="access_type=offline&"; 

                        $data2 .="include_granted_scopes=true"; #true
                        $datea = $data2;
                        
                        header("Location: $datea");

}


$url = 'https://accounts.google.com/o/oauth2/token';
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
curl_setopt($ch, CURLOPT_FAILONERROR, false);  
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded'
));

curl_setopt($ch, CURLOPT_POSTFIELDS,
    'code=' . urlencode($code) . '&' .
    'client_id=' . urlencode('272873981630-eietl5b2fmkjcb5s1h2ep6j3d71li1so.apps.googleusercontent.com') . '&' .
    'client_secret=' . urlencode('zSyfwHqnXbLsOlkxSio_L96-') . '&' .
    'redirect_uri=' . urlencode('http://localhost/calendarresource/index.php') . '&' .
    'grant_type=authorization_code'
);


// Send the request & save response to $resp
$resp = curl_exec($ch);

        //$_SESSION['access_token'] ;
	//$_SESSION['refresh_token'] ;


$json = json_decode($resp, true);

if(!isset($json['access_token'])){
    $access_token = $_SESSION['access_token'];
    $refresh_token = $_SESSION['refresh_token'];
}else{
    $access_token = $json['access_token'];
    $refresh_token = $json['refresh_token'];
}
// Close request to clear up some resources
curl_close($ch);


if (isset($access_token)){
	
	$_SESSION['access_token'] = $access_token;
	$_SESSION['refresh_token'] = $refresh_token;

//echo "Access token generated";
        ?>
    
    <div style="height:auto; margin-left: 150px;margin-right:50px;width: auto;">
            
        
           <form action="" method="POST" enctype="multipart/form-data" style="margin-left:3px;width:auto ;padding:7px;margin-top:1px;margin-right:2px;border: 1px solid #fff;border-radius: 3px; font-family:HelveticaNeue-Light, Helvetica Neue Light, Helvetica Neue, Lucida Grande;font-weight:300px;text-align: left;text-decoration: none;">
            <div >
                <h1>Calendar Resource Creator</h1>    </br>
    
        <h3>This application can only be used by Google Apps Administrators. </h3>
        </br>
    </br>
               
            </div>
                
            <label style="float: left;width: 200px;">Domain name: </label>
            <input type="text" name="dom"style="float: left; margin-left: 20px;width: 200px;"/>
            <br><br>
            <label for="file">Select the CSV file to be uploaded: </label><input type="file" name="csv"/>
            <br><br>
            <input type="submit" name="submit" value="Submit">
        </form>
      
    
<?php
    if(isset($_REQUEST['submit'])){
            
        $target_file5 = "output/";

                   $dom = $_POST['dom'];
                   $target_file5 = $target_file5 . basename( $_FILES['csv']['name']); 
                   
    
     if(move_uploaded_file($_FILES['csv']['tmp_name'], $target_file5)) {
        echo "The file ".  basename( $_FILES['csv']['name']). 
        " has been uploaded".'</br>';
        $x=0;
        $file=fopen("$target_file5","r");
        
         while(!feof($file)){
             ++$x;
             $access = $_SESSION['access_token'];
             
                $res  = fgets($file);
                trim($res);
                $res = preg_replace("/[\\n\\r]+/", "", $res);

                $res2 = (array) explode(",",$res);
               $resID = trim($res2[0]);//resourceID
               $resCname = trim($res2[1]);//resourceCommonName
               $resDesc = trim($res2[2]);//resourceID
               $resType = trim($res2[3]);//resourceID
               
               
               
               
               $data3 = "<atom:entry xmlns:atom='http://www.w3.org/2005/Atom' xmlns:apps='http://schemas.google.com/apps/2006'>".
               "<apps:property name='resourceId' value='$resID'/>".
               "<apps:property name='resourceCommonName' value='$resCname'/>".
               "<apps:property name='resourceDescription' value='$resDesc'/>".
               "<apps:property name='resourceType' value='$resType'/>".
                "</atom:entry>";
               
            


                                        $urla = 'https://apps-apis.google.com/a/feeds/calendar/resource/2.0/'.$dom.'?alt=json';
                                        $cha = curl_init($urla);

                                        curl_setopt($cha, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt ( $cha , CURLOPT_VERBOSE , 1 );
                                        curl_setopt ( $cha , CURLOPT_HEADER , 1 );
                                        curl_setopt($cha, CURLOPT_FAILONERROR, false);
                                        curl_setopt($cha, CURLOPT_SSL_VERIFYPEER, false);
                                        curl_setopt($cha, CURLOPT_CUSTOMREQUEST, 'POST');
                                        curl_setopt($cha, CURLOPT_CONNECTTIMEOUT ,0);
                                        curl_setopt($cha, CURLOPT_TIMEOUT, 400);
                                        curl_setopt($cha, CURLOPT_HTTPHEADER, array(
                                                        'Content-type: application/atom+xml',
                                                        'Authorization:Bearer '.$access

                                        ));


                                       curl_setopt($cha, CURLOPT_POSTFIELDS, $data3);

                                           $response = curl_exec($cha);
                    $error = curl_error($cha);
                    $result = array( 'header' => '', 
                                     'body' => '', 
                                     'curl_error' => '', 
                                     'http_code' => '',
                                     'last_url' => '');


                    if ( $error != "" )
                    {
                        $result['curl_error'] = $error;
                        echo $result['curl_error'];
                    }

                    $header_size = curl_getinfo($cha,CURLINFO_HEADER_SIZE);
                    $result['header'] = substr($response, 0, $header_size);
                    $result['body'] = substr( $response, $header_size );
                    $result['http_code'] = curl_getinfo($cha,CURLINFO_HTTP_CODE);
                    $result['last_url'] = curl_getinfo($cha,CURLINFO_EFFECTIVE_URL);
                   
                    
                    $xmll = json_decode($result['body'], true);



                    if($result['http_code']=="201"){

                        //echo "Request Created Successfully".'<br>';
                            
                       $val =  count($xmll['entry']['apps$property']);
                       for($i=0;$i<$val;$i++){
                           
                          //echo $xmll['entry']['apps$property'][$i]['name']." = ".$xmll['entry']['apps$property'][$i]['value'].'<br>';
                           
                            
                       }
                            //echo "<br>";
                           
                             
                        }else{

                            echo "An error occurred";
                        }

                                           curl_close($cha);

                    
               
             
             
                }
          fclose($file);
          if($result['http_code']=="201"){
          echo $x." resources created Successfully"."<BR>";
          //echo "Finished!"; 
          }
         }
      }

}else{echo "Access NOT granted";}

?> 
    
        

      </div>       
        

    
    
   </div>
   
</body>
</html>