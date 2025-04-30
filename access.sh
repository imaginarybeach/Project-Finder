docker exec -it project_finder_db mysql -u root -p
SHOW DATABASES;
USE `Project-Finder`;
SHOW TABLES;
SELECT * FROM STUDENT;
/Users/katy/Documents/GitHub/Project-Finder/sql/STUDENT.csv


docker volume create csv_volume
docker run -v csv_volume:/project_finder_db/Users/katy/Documents/GitHub/Project-Finder/sql/STUDENT.csv mysql:8.0


docker run -v csv_volume:/project_finder_db/Users/katy/Documents/GitHub/Project-Finder/sql/STUDENT.csv \
  -e MYSQL_ROOT_PASSWORD=rooty \
  mysql:8.0

docker cp /Users/katy/Documents/GitHub/Project-Finder/sql/STUDENT.csv project_finder_db:/var/lib/mysql-files
docker cp /Users/katy/Documents/GitHub/Project-Finder/sql/STUDENT.csv project_finder_db:/var/lib/mysql-files

docker exec -i project_finder_db mysql -u root -p'rooty' -e "
USE \`Project-Finder\`;
INSERT INTO PROJECT (PID, Name, Description, Tag, Capacity, Date_posted, NetID)
VALUES (2, 'Java REST API Project', 'Learning API', 'Java', 3, '2024-06-22 03:03:50', 'abc123456');
"
docker exec -i project_finder_db mysql -u root -p'rooty' -e "
USE \`Project-Finder\`;
LOAD DATA INFILE '/var/lib/mysql-files/STUDENT.csv'
INTO TABLE STUDENT
FIELDS TERMINATED BY ',' 
ENCLOSED BY '\"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(PID, Name, Description, Tag, Capacity, Date_posted, NetID);
"
PID,Name,Description,Tag,Capacity,Date_posted,NetID



INSERT INTO PROJECT (PID, Name, Description, Tag, Capacity, Date_posted, NetID)
VALUES (2, 'Restaurant App', 'Developing a full-stack web app for a restaurant', 'Web', 4, '2024-11-14 14:23:15', 'aaa000000')

INSERT INTO STUDENT (NetID, Pass, Email, Phone, Name, Pronouns)
VALUES ('aaa000000', 'password123', 'larry@yahoo.com', 1231231234, 'The Larry', 'cat/cat');
