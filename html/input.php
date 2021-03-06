<?php
        $dbs_path = $_SERVER['DOCUMENT_ROOT'] . "/../../dbs/weather.sqlite";
        $dbs_auth = "YouShallPass";

        if(isset($_POST['authentification']))
        {
                $esp_auth = filter_input(INPUT_POST,'authentification',FILTER_SANITIZE_STRING);

                if($dbs_auth === $esp_auth)
                {

                        if(isset($_POST['temperatur']) && isset($_POST['humidity']) && isset($_POST['illumination']) )
                        {
                                $timestamp = date('Y-m-d H:i:sa');

                                $temperatur = round(filter_input(INPUT_POST,'temperatur',FILTER_SANITIZE_STRING),2);
                                $humidity = round(filter_input(INPUT_POST,'humidity',FILTER_SANITIZE_STRING), 2);
                                $illumination = round(filter_input(INPUT_POST,'illumination',FILTER_SANITIZE_STRING), 2);
                                //echo 'Uhrzeit      = ' . $timestamp ."\n";
                                //echo "Temperatur   = " . $temperatur . " *C\n";
                                //echo "Humidity     = " . $humidity . " %\n";
                                //echo "Illumination = " . $illumination . " lx\n";

                                //echo "OPEN Database\n";
                                $db = new SQLite3($dbs_path,SQLITE3_OPEN_READWRITE);

                                if($db)
                                {
                                        //echo "Yes, we are connected\n";
                                        try {
                                                //$db->enableExceptions(true);
                                                $sql = "INSERT INTO data (timestamp,temperatur,humidity,illumination) values(datetime('now','localtime'),:temp,:humi,:illu)";
                                                //echo $sql . '\n';
                                                $statement = $db->prepare($sql);

                                                $statement->bindValue(':temp', $temperatur, SQLITE3_FLOAT);
                                                $statement->bindValue(':humi', $humidity, SQLITE3_FLOAT);
                                                $statement->bindValue(':illu', $illumination, SQLITE3_FLOAT);
                                                //echo $db->lastErrorMsg() . '\n';
                                                $statement->execute();
                                                echo 'Insert of Temp = ' . $temperatur . ' and Humi = ' . $humidity;
                                        }
                                        catch(Exception $e)
                                        {
                                                echo 'Caught exception: ' . $e->getMessage() . '\n';
                                        }
                                }
                                else
                                {
                                        echo '<Could not open Database!\n';
                                        echo 'Error: ' . $db->lastErrorMsg() . '\n';
                                }
                                //echo 'CLOSE Database\n';
                                $db->close();
                        }
                        else if( isset($_POST['DhtError']) && isset($_POST['tslError']) ) 
                        {       
                                $esp_error = date("F d, Y h:i:s A", time())."\t";
                                $esp_error = $esp_error.filter_input(INPUT_POST,'DhtError',FILTER_SANITIZE_STRING)."\t";
                                $esp_error = $esp_error.filter_input(INPUT_POST,'tslError',FILTER_SANITIZE_STRING);

                                $filename = '/var/www/html/log_error.txt';
                                file_put_contents($filename, $esp_error."\n", FILE_APPEND | LOCK_EX);
                        }
                        else{                                
                                echo 'No POST Data received\n';
                                echo 'var_dump(_POST) = [\n';
                                var_dump($_POST);
                                echo ']\n';
                        }

                }

        }
?>