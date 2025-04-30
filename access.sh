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
VALUES (1, 'AI Chatbot', 'Building with an AI chatbot', 'AI', 5, '2024-07-22 08:06:50', 'kas210009');
"

PID,Name,Description,Tag,Capacity,Date_posted,NetID