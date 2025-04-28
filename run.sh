# do chmod +x run.sh first!
docker compose -f 'docker-compose.yml' up -d --build 


        $conn = new mysqli($host, $dbuser, $dbpass, $dbname);
        $sql = "SELECT NetID, Pass FROM STUDENT WHERE NetID = '$username' AND Pass = '$password'";
        $result = $conn->query($sql);